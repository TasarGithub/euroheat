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
if ($_POST['action'] == '') {
    /*
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
    */
}

# /������

# �������

# /�������