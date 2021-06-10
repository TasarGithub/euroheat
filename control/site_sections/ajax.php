<?php

### ОТЛАДКА
# print_r($_GET);
# print_r($_POST);

# $a = unserialize($_POST['form']); print_r($a);

# sleep(5);

# защита от запроса c другого сайта
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт
header('Content-type: text/html; charset=windows-1251');

# подключаем и инициализируем класс для работы с БД через PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# подключаем конфиг
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# подключаем функции общего назначения для ajax-скриптов
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# проверка + нужная кодировка POST-переменных
preparePOSTVariables(); # print_r($_POST); exit;

# подготавливаем поля формы
if (!empty($_POST['params'])) parse_str($_POST['params'], $params); # print_r($params);

# ЛОГИКА
# провряем по названию, существует ли раздел
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
# провряем по url, существует ли раздел
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
# сохраняем информацию по разделу сайта при редактировании
elseif ($_POST['action'] == 'edit_item_submit') # для одного шаблона
{
    # проверка переменных
    if (empty($params['id'])) exit('не передан id');
    if (empty($params['site_sections_form_name'])) exit('не передан site_sections_form_name');
    if (empty($params['site_sections_form_url'])) exit('не передан site_sections_form_url');
    
    # full_url
    $fullURL = getFullURL(array('parent_id' => $params['site_sections_form_parent_id'], 'url' => $params['site_sections_form_url']));
    # имя нового файла с контентом
    $newFileName = getFileName(array('id' => $params['id'], 'full_url' => $fullURL));  # echo 'new file name: '.$newFileName.PHP_EOL;
    $fullPathToNewFileName = PATH_TO_PUBLIC_SITE_SECTIONS.$newFileName; # echo 'fullPathToNewFileName: '.$fullPathToNewFileName.PHP_EOL;
    # имя старого файла с контентом
    $oldFileName = getOldFileName1($params['id']); # echo 'old file name: '.$oldFileName.PHP_EOL;
    $fullPathToOldFileName = PATH_TO_PUBLIC_SITE_SECTIONS.$oldFileName; # echo 'full path to file name: '.$fullPathToOldFileName.PHP_EOL;
    # сохраняем информацию в бд
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
    # удаляем старый файл
    # echo 'file_exists: '.file_exists($fullPathToOldFileName).PHP_EOL;
    # echo 'oldFileName: '.$oldFileName.PHP_EOL;
    # echo 'newFileName: '.$newFileName.PHP_EOL;
    if (file_exists($fullPathToOldFileName) && $oldFileName != $newFileName)
    { 
        unlink($fullPathToOldFileName);
        # echo 'removed'.PHP_EOL;
    }
    # сохраняем контент в файл
    saveContentToFile($fullPathToNewFileName, $params['site_sections_form_html_code_1']);
} # /сохраняем информацию по разделу сайта при редактировании

# /ЛОГИКА

# ФУНКЦИИ

# /ФУНКЦИИ