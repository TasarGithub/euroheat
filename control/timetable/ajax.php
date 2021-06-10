<?php

### �������
# print_r($_GET);
# print_r($_POST);

# $a = unserialize($_POST['form']); print_r($a);

# sleep(5);

# ������ �� ������� c ������� �����
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# ��������� ���������, ������� ����� �������� javascript'� ajax-������
header('Content-type: text/html; charset=windows-1251');

# ���������� � �������������� ����� ��� ������ � �� ����� PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# ���������� ������
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# ���������� ������� ������ ���������� ��� ajax-��������
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# �������� + ������ ��������� POST-����������
preparePOSTVariables(); # print_r($_POST); exit;

# �������������� ���� �����
if (!empty($_POST['params'])) parse_str($_POST['params'], $params); # print_r($params);

# ������

# �������� ����������
if ($_POST['action'] == 'getTimetable') {
    # ������� ����������
    showTimetable();
}

# ��������� ���������
elseif ($_POST['action'] == 'saveChanges') {
    if (empty($_POST['eventsAndDates'])) exit('<div style="color:red;font-weight:bold;text-align:center;font-size:24px;margin-top:25px">����������, ������� ��� ������� ���� ���, ���� � �����.</div>');

    # echo $_GET['eventsAndDates'];
    # if (saveChanges()) echo '<span style="color:blue;font-weight:bold">���������</span> &nbsp;';
    # else echo '<span style="color:red;font-weight:bold">�� ���������</span> &nbsp;';

    # ��������� ���������
    saveChanges();

    # ������� ����������
    showTimetable();
} # /��������� ���������

# ������� ����
elseif ($_POST['action'] == 'remove_date') {
    if (empty($_POST['date_id'])) exit('<div style="color:red;font-weight:bold;text-align:center;font-size:24px;margin-top:25px">�� ������� id ��������� ����.</div>');

    # ������� ����
    removeDate();
}

# /������

# �������

# ������� ����������
function showTimetable()
{
    global $dbh;

    $_ = getTimetable(); # echo '<pre>'.(print_r($_, true)).'</pre>';
    $_c = count($_);
    if (!empty($_)) {
        for ($i=0;$i<$_c;$i++) {
            $optionsForShowsSelect = getShowsForSelect($_[$i]['show_id']);
            $optionsForPlacesSelect = getPlacesForSelect($_[$i]['place_id']);

            # ������� select � ��� ������ ��� �����������
            # � �������� �������� ������ ���� ������� ���
            if ($_[$i]['place_id'] == 1) $class = ' class="hidden"';
            else unset($class);

            $result .= '
            <div class="well timetable">
                <div class="form-group timetable" data-item-id="'.$_[$i]['id'].'">
					<b>����</b><!-- (��.��.����)-->:
					&nbsp; <input type="text" name="timetable_event_date" class="form-control timetable_event_date" value="'.$_[$i]['date_formatted'].'" />
					&nbsp;&nbsp;&nbsp;
					<b>�����</b>:
					&nbsp; <input type="text" name="timetable_event_time" class="form-control timetable_event_time" value="'.$_[$i]['time'].'" maxlength="5" />
					&nbsp;&nbsp;&nbsp;
					<b>��������</b>: &nbsp;
					<select name="timetable_event_place_id" class="timetable_place_id form-control">
					'.$optionsForPlacesSelect.'
					</select>

                    <span'.$class.'>
                    &nbsp;&nbsp;&nbsp;
                    <b>���:</b> &nbsp;
                    <select name="timetable_event_name" class="timetable_event_id form-control">
                    '.$optionsForShowsSelect.'
                    </select>
                    </span>

                    <a title="������� ����" href="#" class="time_remove_item">
                        <i class="fa fa-trash-o size_18"></i>
                    </a>
                </div>
            </div>
			';
        }
        echo $result;
    }
} # /������� ����������

# �������� ����������
function getTimetable() {
    global $dbh;

    unset($sqlModifier);

    # ���������� �� ����� ���������� (��������) "�������"
    if ($_POST['place'] == 1) $sqlModifier = ' and place_id = 1 ';
    # ���������� �� ����� ���������� (��������) "�������� �����������"
    elseif ($_POST['place'] == 2) $sqlModifier = ' and place_id = 2 ';

    # ���������� �� ���
    if (!empty($_POST['show'])) $sqlModifier .= ' and show_id = :show_id ';

    $sql = "
	select id,
	       show_id,
	       place_id,
		   date_format(date,'%e.%m.%Y') as date_formatted,
		   date_format(date,'%H:%i') as time
	from ".DB_PREFIX."timetable
	where 1 ".$sqlModifier."
	order by date
	"; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    # ���������� �� ���
    if (!empty($_POST['show'])) $sth->bindValue(':show_id', $_POST['show'], PDO::PARAM_INT);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            if (!empty($_)) return $_;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /�������� ����������

# �������� ������ ���, ��������� OPTIONS ��� SELECT'�
function getShowsForSelect($showID)
{
    global $dbh;

    $sql = "
	select id, name
	from ".DB_PREFIX."shows_circus
	order by isnull(element_order_listing),
	         element_order_listing,
	         name
	"; # echo $sql."<hr />";
    $result = $dbh->prepare($sql);
    try {
        if ($result->execute()) {
            $_ = $result->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            $_c = count($_);
            if (!empty($_)) {
                $result = '';
                for ($i=0;$i<$_c;$i++) {
                    # selected
                    if ($_[$i]['id'] == $showID) $selected = ' selected="selected"';
                    else unset($selected);

                    $result .= '<option value="'.$_[$i]['id'].'"'.$selected.'>'.$_[$i]['name'].'</option>';
                }
            }

            return $result;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /�������� ������ ���, ��������� OPTIONS ��� SELECT'�

# �������� ������ ���� ���������� �������������, ��������� OPTIONS ��� SELECT'�
function getPlacesForSelect($showID)
{
    global $dbh;

    $sql = "
	select id, name
	from ".DB_PREFIX."places
	order by name
	"; # echo $sql."<hr />";
    $result = $dbh->prepare($sql);
    try {
        if ($result->execute()) {
            $_ = $result->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            $_c = count($_);
            if (!empty($_)) {
                $result = '';
                for ($i=0;$i<$_c;$i++) {
                    # selected
                    if ($_[$i]['id'] == $showID) $selected = ' selected="selected"';
                    else unset($selected);

                    $result .= '<option value="'.$_[$i]['id'].'"'.$selected.'>'.$_[$i]['name'].'</option>';
                }
            }

            return $result;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /�������� ������ ���� ���������� �������������

# ��������� ��������� ��� ������� ���� � ���������
function saveChanges()
{
    # �������� ����������
    if (empty($_POST['eventsAndDates'])) return;

    global $dbh;

    # print_r($_POST); exit;

    # print_r($_POST['eventsAndDates']); exit;

    # �������� �� ������� ��� � ����������
    # �������� ������ �� ������� ';'
    $eventsAndDates = rtrim($_POST['eventsAndDates'], ';'); # echo $eventsAndDates; exit;
    $eventsAndDates = explode(';', $eventsAndDates); # print_r($eventsAndDates); exit;
    if (!empty($eventsAndDates)
        and is_array($eventsAndDates)) {
        # ������� ������� � �����������
        clearTimeTable();

        foreach($eventsAndDates as $eventAndDate) {
            if (!empty($eventAndDate)) {
                $item = explode('*', $eventAndDate); # print_r($item); echo '<hr />';
                # place + '*' + date + '*' + time + '*' + (!show ? '' : show) + ';';
                # example: 2*18.11.2015*19:00*2;1*19.12.2015*14:00*;1*19.12.2015*19:00*;
                if (!empty($item[0]) && !empty($item[1]) && !empty($item[2])) {
                    # echo 'event_id: '.$item[0].', date: '.$item[1].', time: '.$item[2].', stage: '.$item[3].'<br />';

                    $place = $item[0];

                    $date = date("Y-m-d", strtotime($item[1])); // echo $date.'<br />';
                    $date .= ' '.$item[2]; # echo $date.'<br />';

                    $show = $item[3];

                    # ��������� ����������
                    saveNewData(array(
                        'show' => $show,
                        'place' => $place,
                        'date' => $date
                    ));
                }
            }
        }

        return 1;
    } # /�������� �� ������� ��� � ����������
} # /��������� ��������� ��� ������� ���� � ���������

# ��������� ����������
function saveNewData($array)
{
    # print_r($array); echo '<hr>'; exit;

    # �������� ����������
    # if (empty($array['show'])) return;
    if (empty($array['place'])) return;
    if (empty($array['date'])) return;

    global $dbh;

    # ��� �������� �� ��������� ���
    if ($array['place'] == 1) $array['show'] = null;

    # ��������� ����� ������ � ���������� � �����
    $sql = "
    insert into ".DB_PREFIX."timetable
    (
     show_id,
     place_id,
     date
    )
    values
    (
     :show_id,
     :place_id,
     :date
    )
    "; # echo '<pre>'.$sql."</pre><hr />event: ".$event.', date: '.$date.'<hr />';

    $result = $dbh->prepare($sql);

    $result->bindValue(':show_id', !empty($array['show']) ? $array['show'] : null);
    $result->bindParam(':place_id', $array['place'], PDO::PARAM_INT);
    $result->bindParam(':date', $array['date'], PDO::PARAM_STR);

    try {
        if ($result->execute()) {
            return 1;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /��������� ����������

# ������� ������� � �����������
function clearTimeTable()
{
    global $dbh;

    unset($sqlModifier);

    # ���������� �� ����� ���������� (��������) "�������"
    if ($_POST['place'] == 1) $sqlModifier = ' and place_id = 1 ';
    # ���������� �� ����� ���������� (��������) "�������� �����������"
    elseif ($_POST['place'] == 2) $sqlModifier = ' and place_id = 2 ';

    # ���������� �� ���
    if (!empty($_POST['show'])) $sqlModifier .= ' and show_id = :show_id ';

    # ������� ������ ������, ���� ����
    $sql = "
	delete from ".DB_PREFIX."timetable
	where 1 ".$sqlModifier."
	"; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    # ���������� �� ���
    if (!empty($_POST['show'])) $sth->bindValue(':show_id', $_POST['show'], PDO::PARAM_INT);
    try {
        if ($sth->execute()) return 1;
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /������� ������� � �����������

# ������� ����
function removeDate()
{
    global $dbh;

    # ������� ������ ������, ���� ����
    $sql = "
	delete from ".DB_PREFIX."timetable
	where id = :id
	"; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindValue(':id', $_POST['date_id'], PDO::PARAM_INT);
    try {
        if ($sth->execute()) return 1;
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /������� ����

# /�������