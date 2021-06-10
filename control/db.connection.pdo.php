<?php

include($_SERVER['DOCUMENT_ROOT'].'/app/db.connection.pdo.php');

/*

# настройки БД mysql
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PWD', '');
define('DB_PREFIX', '');
define('DB_SHOW_ERRORS', 1);

### соединяемся с БД: http://ru2.php.net/manual/en/book.pdo.php
try
{
	$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PWD);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE); # described in "Zend PHP 5 Certifcation Study Guide", pages 156-157
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); # described in the same source, page 157 (default error mode is PDO::ERRMODE_SILENT) - PDO will not emit any warnings or errors
}
catch(PDOException $e)
{
	# echo $e->getMessage()."<br />";
	# exit;
	exit("Сайт временно не доступен. Пожалуйста, зайдите попозже. Благодарим за внимание."); #
}

*/