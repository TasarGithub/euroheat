<?php # назначение: конфиг клиентской части сайта

echo "<br>";
echo "config".PHP_EOL;

# настройки вывода ошибок в php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); # E_ALL, E_ERROR, E_WARNING, E_PARSE, E_NOTICE, 0



# глобальные настройки

define('DOMAIN', $_SERVER['SERVER_NAME']);

define('DOMAIN_SHORT', str_replace('www.', '', $_SERVER['SERVER_NAME']));

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].'/euroheater.ru'); 



define('MVC_PATH', DOCUMENT_ROOT.'/app/mvc/'); # echo MVC_PATH."<hr />";

define('PATH_TO_TEMPLATES', DOCUMENT_ROOT.'/app/templates/'); # echo TEMPLATES_PATH."<hr />";

define('PATH_TO_TEMPLATES_SHORT', '/app/templates/'); 
#echo TEMPLATES_PATH."<hr />";

define('PATH_TO_SITE_SECTIONS', DOCUMENT_ROOT.'/app/site_sections/'); 
#echo PATH_TO_PUBLIC_SITE_SECTIONS."<hr />";

define('PATH_TO_SITE_SECTIONS_SHORT', '/app/site_sections/'); # echo PATH_TO_PUBLIC_SITE_SECTIONS."<hr />";



# переменные шаблонизатора

$GLOBALS['tpl_year'] = date('Y');