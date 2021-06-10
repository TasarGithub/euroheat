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
# ��������, ���������� �� ���������� �� ��������
if ($_POST['action'] == 'check_item_for_existence_by_name') {
    $sql = '
    select id
    from `'.DB_PREFIX.'photos`
    where h1 = :name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':name', $_POST['name']);
    $sth->execute();
    if ($_ = $sth->fetchColumn()) {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
elseif ($_POST['action'] == 'search') {
    $sql = '
    select t1.id,
           t1.name,
           t1.url,
           t1.image,
           t1.is_showable,
           t2.url as photoalbum_url,
           t2.name as photoalbum_name
    from '.DB_PREFIX.'photos as t1
    left outer join photoalbums as t2
        on t2.id = t1.photoalbum_id
    where t1.name like :q
    order by t2.name,
             length(t1.name),
             t1.name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
	$q = '%'.$_POST['q'].'%';
	$sth->bindParam(':q', $q);
    $sth->execute();
    if ($_ = $sth->fetchAll()) {
        $_c = count($_);
        $rows = array();
        for ($i=0;$i<$_c;$i++) {
            # ������
            $link = '<a href="/foto/'.$_[$i]['photoalbum_url'].'/'.$_[$i]['url'].'/" target="_blank">��������</a>';
            
            # is_showable
            if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
            else unset($trClass);
            
            $rows[] = '
            <tr'.$trClass.'>
                <td class="center vertical_middle">
                    <a class="block" href="/control/photoalbums/?action=editItem&itemID='.$_[$i]['id'].'">
                        <i class="fa fa-edit size_18"></i>
                    </a>
                </td>
                <td class="center vertical_middle">'.$link.'</td>
                <td>'.$_[$i]['name'].'</td>
                <td class="center">'.$_[$i]['photoalbum_name'].'</td>
                <td class="center vertical_middle">
                    <a class="block" title="������� ����������" href="/control/photoalbums/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'���������� ����� ������ ������������. ������� ����������?\')">
                        <i class="fa fa-trash-o size_18"></i>
                    </a>
                </td>
            </tr>
            ';
        }
        if (empty($rows)) $result .= '� ������� �� ����� �� ���� ����������.';
        else {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);
            
            $result .= '
            <div id="resultSet">
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
                <tr>
                    <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                    <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                    <th class="center vertical_middle">��������</th>
                    <th class="center vertical_middle" style="width:150px">����������</th>
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