<?php

### �������
# print_r($_GET);
# print_r($_POST);

# $a = unserialize($_POST['form']); print_r($a);

# sleep(5);

# ������ �� ������� c ������� �����
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER'])) exit('');

# ��������� ���������, ������� ����� �������� javascript'� ajax-������
header('Content-type: text/html; charset=windows-1251');

# ���������� � �������������� ����� ��� ������ � �� ����� PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# ���������� ������
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# ���������� ������� ������ ���������� ��� ajax-��������
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');
include($_SERVER['DOCUMENT_ROOT'].'/control/#library/functions.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# �������� + ������ ��������� POST-����������
preparePOSTVariables(); # print_r($_POST); exit;

# �������������� ���� �����
if (!empty($_POST['params'])) parse_str($_POST['params'], $params); # print_r($params);

# ������
if ($_POST['action'] == 'search')
{
    $sql = '
    select id,
           name,
           feedback,
           votes_plus,
           votes_minus,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
           date_format(date_add,"%Y") as date_add_year,
           is_published
    from '.DB_PREFIX.'feedback
    where name like :q
          or feedback like :q
          or date_format(date_add, "%d.%m.%Y") like :q
    order by date_add desc
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
            if (!empty($_[$i]['id'])) $link = '<a href="/otzyvy/'.$_[$i]['id'].'/" target="_blank">��������</a>';
            else $link = '&nbsp;';
            
            # is_published
            if (empty($_[$i]['is_published'])) $trClass = ' class="item_hidden"';
            else unset($trClass);
            
            $rows[] = '
            <tr'.$trClass.'>
                <td class="center vertical_middle">
                    <a class="block" href="/control/feedbacks/?action=editItem&itemID='.$_[$i]['id'].'">
                        <i class="fa fa-edit size_18"></i>
                    </a>
                </td>
                <td class="center vertical_middle">'.$link.'</td>
                <td class="nowrap vertical_middle">'.$_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].'</td>
                <td class="vertical_middle">'.$_[$i]['name'].'</td>
                <td class="vertical_middle">'.cutText($_[$i]['feedback'], 113).'</td>
                <td class="center vertical_middle">
                    <a class="block" title="������� �����" href="/control/feedbacks/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'����� ����� ������ ������������. ������� �����?\')">
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
                    <th class="center vertical_middle">����</th>
                    <th class="center vertical_middle">���</th>
                    <th class="center vertical_middle">�����</th>
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