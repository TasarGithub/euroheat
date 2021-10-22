<?php # назначение: конфиг админской части сайта



# настройки вывода ошибок в php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); # E_ALL, E_ERROR, E_WARNING, E_PARSE, E_NOTICE, 0



# глобальные настройки

define('DOMAIN', $_SERVER['SERVER_NAME']);

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']); # echo SITE_PATH."<hr />";

# путь к шаблонам админской части

define('PATH_TO_CMS_TEMPLATES', DOCUMENT_ROOT.'/control/#templates/'); # echo PATH_TO_CMS_TEMPLATES."<hr />";

# путь к шаблонам клиентской части

define('PATH_TO_PUBLIC_TEMPLATES', DOCUMENT_ROOT.'/app/templates/'); # echo PATH_TO_PUBLIC_TEMPLATES."<hr />";

define('PATH_TO_PUBLIC_TEMPLATES_SHORT', '/app/templates/'); # echo PATH_TO_PUBLIC_TEMPLATES_SHORT."<hr />";

define('PATH_TO_PUBLIC_CSS', DOCUMENT_ROOT.'/public/css/'); # echo PATH_TO_PUBLIC_CSS."<hr />";

define('PATH_TO_PUBLIC_CSS_SHORT', '/public/css/'); # echo PATH_TO_PUBLIC_CSS."<hr />";

#define('PATH_TO_PUBLIC_CSS_OLD', '/home/u39721/kranauto.ru/www/_styles/'); # echo PATH_TO_PUBLIC_CSS."<hr />";

#define('PATH_TO_PUBLIC_CSS_OLD_SHORT', 'http://www.kranauto.ru/_styles/'); # echo PATH_TO_PUBLIC_CSS."<hr />";

define('PATH_TO_PUBLIC_JS', DOCUMENT_ROOT.'/public/js/'); # echo PATH_TO_PUBLIC_JS."<hr />";

define('PATH_TO_PUBLIC_JS_SHORT', '/public/js/'); # echo PATH_TO_PUBLIC_JS."<hr />";

define('PATH_TO_PUBLIC_SITE_SECTIONS', DOCUMENT_ROOT.'/app/site_sections/'); # echo PATH_TO_PUBLIC_SITE_SECTIONS."<hr />";

define('PATH_TO_PUBLIC_SITE_SECTIONS_SHORT', '/app/site_sections/'); # echo PATH_TO_PUBLIC_SITE_SECTIONS."<hr />";



# переменные шаблонизатора

$GLOBALS['tpl_year'] = date('Y');