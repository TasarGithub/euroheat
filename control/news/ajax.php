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
# ��������, ���������� �� ������� �� ��������
if ($_POST['action'] == 'check_item_for_existence_by_name')
{
    $sql = '
    select id
    from '.DB_PREFIX.'news
    where h1 = :name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':name', $_POST['name']);
    $sth->execute();
    if ($_ = $sth->fetchColumn())
    {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
# ��������, ���������� �� ������� �� ����
if ($_POST['action'] == 'check_item_for_existence_by_date_add')
{
    $sql = '
    select id
    from '.DB_PREFIX.'news
    where date_add = :date_add
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    # ������������ ���� �� ���� datepicker � mysql datetime:
    if (!empty($_POST['date_add'])) $date = date("Y-m-d H:i:s", strtotime($_POST['date_add'].' 05:00:00'));
    $sth->bindParam(':date_add', $date);
    $sth->execute();
    if ($_ = $sth->fetchColumn())
    {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
elseif ($_POST['action'] == 'search')
{
    $sql = '
    select id,
           date_add,
           date_format(date_add, "%d.%m.%Y") as date_add_formatted,
           date_format(date_add, "%d.%m.%Y") as date_add_formatted_2,
           h1,
           is_showable
    from '.DB_PREFIX.'news
    where h1 like :q
          or date_format(date_add, "%d.%m.%Y") like :q
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
	$q = '%'.$_POST['q'].'%';
	$sth->bindParam(':q', $q);
    $sth->execute();
    if ($_ = $sth->fetchAll())
    {
        $_c = count($_);
        $rows = array();
        for ($i=0;$i<$_c;$i++)
        {
            # ������
            if (!empty($_[$i]['date_add_formatted_2'])) $link = '<a href="/novosti/'.$_[$i]['date_add_formatted_2'].'/" target="_blank">��������</a>';
            else $link = '&nbsp;';
            
            # is_showable
            if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
            else unset($trClass);
            
            $rows[] = '
            <tr'.$trClass.'>
                <td class="center vertical_middle">
                    <a class="block" href="/control/news/?action=editItem&itemID='.$_[$i]['id'].'">
                        <i class="fa fa-edit size_18"></i>
                    </a>
                </td>
                <td class="center vertical_middle">'.$link.'</td>
                <td><span style="color:#ababab;padding-right:10px">'.$_[$i]['date_add_formatted'].'</span>'.$_[$i]['h1'].'</td>
                <td class="center vertical_middle">
                    <a class="block" title="������� �������" href="/control/news/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'������� ����� ������� ������������. ������� �������?\')">
                        <i class="fa fa-trash-o size_18"></i>
                    </a>
                </td>
            </tr>
            ';
        }
        if (empty($rows)) $result .= '� ������� �� ������ �� ���� �������.';
        else
        {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);
            
            $result .= '
            <div id="resultSet">
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
                <tr>
                    <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                    <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                    <th class="center vertical_middle">��������� h1</th>
                    <th class="center vertical_middle" style="width:100px;white-space:nowrap">��������</th>
                </tr>
                '.$rows.'
            </table>
            </div>
            ';
        }
        echo $result;
    }
    else echo '�� ������� &quot;'.$_POST['q'].'&quot; ������ �� �������.';
}

# /������

# �������

# /�������