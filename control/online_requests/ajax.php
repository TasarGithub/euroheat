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
# провряем, существует ли новость по названию
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

# /ЛОГИКА

# ФУНКЦИИ

# /ФУНКЦИИ