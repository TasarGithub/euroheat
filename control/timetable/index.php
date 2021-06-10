<?php 
# ������ ������� ��� ������ � ����������� (������� timetable)
# romanov.egor@gmail.com; 2015.10.16

# ���������� ���� �������
include('../loader.control.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '����������';
$GLOBALS['public_url'] = '/raspisanie/';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������

$GLOBALS['tpl_title'] .= '';
$GLOBALS['tpl_h1'] = '���������� ('.$dbh->query('select count(1) from '.DB_PREFIX.'timetable')->fetchColumn().')';
$GLOBALS['tpl_content'] = showItems();

# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ������� ����� ��� ����������
function showItems($count = null)
{
    global $dbh;

    $lastShowID = getLastShowID();
    $optionsForShowsSelect = getShowsForSelect($lastShowID);
    $optionsForPlacesSelect = getPlacesForSelect($lastShowID);

    # ������� ���������� ������� � ��������
    $timetable1Count = $dbh->query('select count(1) from '.DB_PREFIX.'timetable where place_id = 1')->fetchColumn();
    # ������� ���������� ������� �� �����������
    $timetable2Count = $dbh->query('select count(1) from '.DB_PREFIX.'timetable where place_id = 2')->fetchColumn();

    # ���� ������� ���������� �� �������� (�/��� �� ���)
    # �������
    if ($_GET['place'] == 1) $active1 = ' class="active"';
    # �����������
    elseif ($_GET['place'] == 2) {
        $active2 = " class='active'";
        # ��������� � ������� ���������� �� ���
        $sql = '
        select distinct t1.show_id as id,
               t2.name,
               (select count(1) from '.DB_PREFIX.'timetable where place_id = 2 and show_id = t1.show_id) as items_count
        from '.DB_PREFIX.'timetable as t1
        left outer join shows_circus as t2
        on t2.id = t1.show_id
        where t1.place_id = 2
        '; # echo '<pre>'.$sql."</pre><hr />";
        $tmp = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($tmp, true)).'</pre>'; # exit;
        if (!empty($tmp)) {
            $tmpResult = array();
            foreach ($tmp as $item) {
                # selected
                $active = $_GET['show'] == $item['id'] ? ' class="active"' : '';

                $tmpResult[] = '<a href="/control/timetable/?place=2&show='.$item['id'].'"'.$active.'>'.$item['name'].' ('.$item['items_count'].')</a>';
            }
            $sortingByShow = '
            <!-- ���������� �� ��� -->
            <div class="sorting"><b>���������� �� ���:</b> '.
            implode(' <span style="color:#ccc">|</span> ', $tmpResult).
            '</div>
            <!-- /���������� �� ��� -->';
        }
    } # /���� ������� ���������� �� �������� (�/��� �� ���)

    $result = '
	<script type="text/javascript" src="/control/timetable/index.js?v=1.4"></script>

    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="'.$GLOBALS['public_url'].'" target="_blank">http://'.$_SERVER['SERVER_NAME'].$GLOBALS['public_url'].'</a>
    </div>

    <br style="clear:both" />

    <!-- ���������� �� �������� -->
    <div class="sorting"><b>����������:</b>
    <a href="/control/timetable/?place=1"'.$active1.'>������� ('.$timetable1Count.')</a> <span style="color:#ccc">|</span>
    <a href="/control/timetable/?place=2"'.$active2.'>����������� ('.$timetable2Count.')</a>
    </div>
    '.$sortingByShow.'
    <!-- /���������� �� �������� -->

    <div class="center" style="margin-top:10px;margin-bottom:15px">
        <a href="#" class="timetable_add_date_button"><button class="btn btn-success" type="button"><i class="fa fa-plus-square" style="margin-right:3px"></i> �������� ����</button></a>

        &nbsp;&nbsp;&nbsp;

        <button class="btn btn-primary submit_button timetable_save_changes inline" type="submit">��������� ���������</button>
    </div>

    <div id="time_table_list"></div>

    <div id="template_add_date" style="display:none">
        <div class="well timetable">
            <div class="form-group timetable" data-item-id="'.$_[$i]['id'].'">
                <b>����</b><!-- (��.��.����)-->:
                &nbsp; <input type="text" name="timetable_event_date" class="form-control timetable_event_date" value="'.$_[$i]['date_formatted'].'" />
                &nbsp;&nbsp;&nbsp;
                <b>�����</b>:
                &nbsp;<input type="text" name="timetable_event_time" class="form-control timetable_event_time" value="'.$_[$i]['time'].'" maxlength="5" />
                &nbsp;&nbsp;&nbsp;
                <b>��������</b>: &nbsp;
                <select name="timetable_event_place_id" class="timetable_place_id form-control">
                '.$optionsForPlacesSelect.'
                </select>
                <span>
                &nbsp;&nbsp;&nbsp;
                <b>���:</b> &nbsp;
                <select name="timetable_event_name" class="timetable_event_id form-control">
                '.$optionsForShowsSelect.'
                </select>
                &nbsp;&nbsp;&nbsp;
                <a title="������� ����" href="#" class="time_remove_item">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
                </span>
            </div>
        </div>
    </div>
    ';

    return $result;
} # /������� ����� ��� ����������

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

# �������� ID ���������� ���
function getLastShowID()
{
    global $dbh;

    $sql = "
	select max(id)
	from ".DB_PREFIX."shows
	"; # echo $sql."<hr />";
    $result = $dbh->prepare($sql);
    try {
        if ($result->execute()) {
            $_ = $result->fetchColumn(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            if (!empty($_)) return $_;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /�������� ID ���������� ���

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

# /����������