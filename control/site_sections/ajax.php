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
# �������� �� ��������, ���������� �� ������
if ($_POST['action'] == 'check_item_for_existence_by_name')
{
    # id
    if (!empty($_POST['id'])) $idCondition = ' and id != :id ';
    
    $sql = '
    select id
    from '.DB_PREFIX.'site_sections
    where name = :name
          '.$idCondition.'
          and parent_id = :parent_id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':name', $_POST['name']);
    # id
    if (!empty($_POST['id'])) $sth->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $sth->bindParam(':parent_id', $_POST['parent_id']);
    $sth->execute();
    if ($_ = $sth->fetchColumn()) {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
# �������� �� url, ���������� �� ������
elseif ($_POST['action'] == 'check_item_for_existence_by_url')
{
    # id
    if (!empty($_POST['id'])) $idCondition = ' and id != :id ';
    
    $sql = '
    select id
    from '.DB_PREFIX.'site_sections
    where url = :url
          '.$idCondition.'
          and parent_id = :parent_id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':url', $_POST['url']);
    # id
    if (!empty($_POST['id'])) $sth->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $sth->bindParam(':parent_id', $_POST['parent_id']);
    $sth->execute();
    if ($_ = $sth->fetchColumn())
    {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
# ��������� ���������� �� ������� ����� ��� ��������������
elseif ($_POST['action'] == 'edit_item_submit') # ��� ������ �������
{
    # �������� ����������
    if (empty($params['id'])) exit('�� ������� id');
    if (empty($params['site_sections_form_name'])) exit('�� ������� site_sections_form_name');
    if (empty($params['site_sections_form_url'])) exit('�� ������� site_sections_form_url');
    
    # full_url
    $fullURL = getFullURL(array('parent_id' => $params['site_sections_form_parent_id'], 'url' => $params['site_sections_form_url']));
    # ��� ������ ����� � ���������
    $newFileName = getFileName(array('id' => $params['id'], 'full_url' => $fullURL));  # echo 'new file name: '.$newFileName.PHP_EOL;
    $fullPathToNewFileName = PATH_TO_PUBLIC_SITE_SECTIONS.$newFileName; # echo 'fullPathToNewFileName: '.$fullPathToNewFileName.PHP_EOL;
    # ��� ������� ����� � ���������
    $oldFileName = getOldFileName1($params['id']); # echo 'old file name: '.$oldFileName.PHP_EOL;
    $fullPathToOldFileName = PATH_TO_PUBLIC_SITE_SECTIONS.$oldFileName; # echo 'full path to file name: '.$fullPathToOldFileName.PHP_EOL;
    # ��������� ���������� � ��
    $sql = '
    update '.DB_PREFIX.'site_sections
    set parent_id = :parent_id,
        name = :name,
        url = :url,
        full_url = :full_url,
        title = :title,
        keywords = :keywords,
        description = :description,
        navigation = :navigation,
        full_navigation = :full_navigation,
        h1 = :h1,
        file_name_1 = :file_name_1,
        footeranchor = :footeranchor,
        right_menu_services = :right_menu_services,
        is_showable = :is_showable
    where id = :id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':parent_id', $params['site_sections_form_parent_id']);
    $sth->bindParam(':name', $params['site_sections_form_name']);
    $params['site_sections_form_url'] = str_replace('/', '', $params['site_sections_form_url']);
    $sth->bindParam(':url', $params['site_sections_form_url']);
    $sth->bindParam(':full_url', $fullURL);
    $sth->bindParam(':title', $params['site_sections_form_title']);
    $sth->bindParam(':keywords', $params['site_sections_form_keywords']);
    $sth->bindParam(':description', $params['site_sections_form_description']);
    $sth->bindParam(':navigation', $params['site_sections_form_navigation']);
    # full_navigation
    if (empty($params['site_sections_form_full_navigation'])) $params['site_sections_form_full_navigation'] = null;
    $sth->bindParam(':full_navigation', $params['site_sections_form_full_navigation']);
    $sth->bindParam(':file_name_1', $newFileName);
    # footeranchor_id
    if ($params['site_sections_form_footeranchor'] == '') $params['site_sections_form_footeranchor'] = null;
    $sth->bindParam(':footeranchor', $params['site_sections_form_footeranchor']);
    # right_menu_services
    if ($params['site_sections_form_right_menu_services'] == '') $params['site_sections_form_right_menu_services'] = null;
    $sth->bindParam(':right_menu_services', $params['site_sections_form_right_menu_services']);
    # h1
    $sth->bindParam(':h1', $params['site_sections_form_h1']);
    $isShowable = $params['site_sections_form_is_showable'] == 'on' ? 1 : null;
    $sth->bindParam(':is_showable', $isShowable);
    $sth->bindParam(':id', $params['id'], PDO::PARAM_INT);
    try { if ($sth->execute()) echo 'success'; }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    # ������� ������ ����
    # echo 'file_exists: '.file_exists($fullPathToOldFileName).PHP_EOL;
    # echo 'oldFileName: '.$oldFileName.PHP_EOL;
    # echo 'newFileName: '.$newFileName.PHP_EOL;
    if (file_exists($fullPathToOldFileName) && $oldFileName != $newFileName)
    { 
        unlink($fullPathToOldFileName);
        # echo 'removed'.PHP_EOL;
    }
    # ��������� ������� � ����
    saveContentToFile($fullPathToNewFileName, $params['site_sections_form_html_code_1']);
} # /��������� ���������� �� ������� ����� ��� ��������������

# /������

# �������

# /�������