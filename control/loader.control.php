<?php

# запуск сессий
# session_start();

# echo '<pre>'.(print_r($_SESSION, true)).'</pre>';

# echo '<!-- '.$_SERVER['REQUEST_URI'].' -->';

# unset($_SESSION['auth']);

### отладка
# print_r($_GET);
# print_r($_POST);
# print_r($_FILES);
# echo '<pre>'.(print_r($_COOKIE, true)).'</pre>';

# конфиг
include('config.control.php');

# класс шаблонизатор на базе php
$tpl = new templates();

# класс защиты пользовательских данных
$defence = new defence;

# настройки mysql и инициализация соединения: http://ru2.php.net/manual/en/book.pdo.php
include('db.connection.pdo.php');

# подключаем функции общего назначения для ajax-скриптов
include('functions.common.ajax.php');

# выводим главный шаблон
if ($_SERVER['REQUEST_URI'] == '/control/')
{
    header('Location: /control/online_requests/');
    # header('Location: /control/online_requests/');
}

# загрузка классов "на лету"
function __autoload($class_name)
{
	$filename = strtolower($class_name); 
	# echo "filename: ".$filename."<hr />";
	# для не-MVC классов
	$file = DOCUMENT_ROOT.'/control/#library/'.$filename.'.php';
	
    /* # отладка
	echo 'file: '.$file.' exists: '.file_exists($file).'<br />';
	# */
   
	if (file_exists($file)) include($file);
	else return;
} # /загрузка классов "на лету"
