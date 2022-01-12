<?php

# запуск сессий
session_start();


// echo '<pre>'.(print_r($_SESSION, true)).'</pre>';

// echo '<!-- '.$_SERVER['REQUEST_URI'].' -->';

# unset($_SESSION['auth']);

### отладка
// print_r($_GET);
// print_r($_POST);
// print_r($_FILES);
// echo '<pre>'.(print_r($_COOKIE, true)).'</pre>';

# конфиг
include('config.php');
echo "<br>";
echo "ssfdfsdfs".PHP_EOL;
# класс "реестр": в нем хранятся экземпляры других классов
$registry = new registry;
echo "<br>";
echo "ssfdfsdfs".PHP_EOL;
# настройки mysql и инициализация соединения: http://ru2.php.net/manual/en/book.pdo.php

echo "include('db.connection.pdo.php');";

include('db.connection.pdo.php');
// echo "dbh: ";
$registry->set('dbh', $dbh);

// echo "dbh: ";


# класс шаблонизатор на базе php
include(DOCUMENT_ROOT.'/app/library/templates.php');
$tpl = new templates();
$registry->set('tpl', $tpl);

# класс "маршрутизатор"
$router = new router($registry);
$router->setPath(MVC_PATH);
$registry->set('router', $router);
$router->showRouterInfo = 1; # вывести отладочную информацию
#$router->showRouterInfo = 0; # вывести отладочную информацию

   echo "<br/>";
     echo "<br/>";
     echo "<br/>";
     echo "<br/>";
echo "MVC_PATH: ".MVC_PATH;
   echo "<br/>";
     echo "<br/>";
     echo "<br/>";
     echo "<br/>";


# класс защиты пользовательских данных
$defence = new defence;
$registry->set('defence', $defence);
$GLOBALS['registry'] = $registry;

# выводим переменные для сайта
# телефон (задается в шаблоне в админке, меняется на всем сайте)
$GLOBALS["phone"] = $tpl->getTemplate('phone.html');
$GLOBALS["phone_for_link"] = preg_replace('/[^0-9]/i', '', $GLOBALS["phone"]);
$GLOBALS["phone_for_link"] = preg_replace('/^8/i', '+7', $GLOBALS["phone_for_link"]);

# подключаем общие функции
include(DOCUMENT_ROOT.'/app/library/functions.php');

### настройка динамичных путей

# 15 июля 2016 заменено на URL selector
# Статичные страницы
# Сортировки по мощности
# $router->addRoute(array('path' => '/^generatora\/(10-kvt)|(15-kvt)|(16-kvt)|(20-kvt)|(30-kvt)|(40-kvt)|(50-kvt)|(60-kvt)|(64-kvt)|(70-kvt)|(80-kvt)|(100-kvt)|(120-kvt)|(150-kvt)|(160-kvt)|(200-kvt)|(220-kvt)|(240-kvt)|(250-kvt)|(300-kvt)|(320-kvt)|(400-kvt)|(500-kvt)|(600-kvt)|(650-kvt)|(800-kvt)|(1000-kvt)$/', 'controller' => 'site_section_default', 'action' => 'index'));

# 15 июля 2016 заменено на URL selector
# Электростанции. Подробно
# $router->addRoute(array('path' => '/^generatora\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'catalog', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# URL selector: либо это статичный раздел, либо это электростанция подробно
$router->addRoute(array('path' => '/^generatora\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'site_section_default', 'action' => 'urlSelectorGeneratora', 'vars' => array(1 => 'itemURL')));

# Новости. Постраничный вывод
$router->addRoute(array('path' => '/^novosti\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# Новости. Подробно
$router->addRoute(array('path' => '/^novosti\/([-0-9]{10})$/', 'controller' => 'news', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# Отзывы. Постраничный вывод
$router->addRoute(array('path' => '/^otzyvy\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# Отзывы. Подробно
$router->addRoute(array('path' => '/^otzyvy\/([\/0-9]*)$/', 'controller' => 'feedback', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# Вопросы-ответы. Постраничный вывод
$router->addRoute(array('path' => '/^vopros\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# Вопросы-ответы. Подробно
$router->addRoute(array('path' => '/^vopros\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'faq', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# Статьи. Постраничный вывод
$router->addRoute(array('path' => '/^sovet\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# Статьи. Подробно
$router->addRoute(array('path' => '/^sovet\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'articles', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# Карта сайта
$router->addRoute(array('path' => '/^karta-sajta/', 'controller' => 'map', 'action' => 'index'));

### /настройка динамичных путей

# Инициализация классов: подключаем контроллеры в loader
# нестандартный функционал
/*
include(MVC_PATH.'cars_controller.php');
$cars_controller = new cars_controller($registry);
# получаем список мин. цен для столбца слева
$_ = $cars_controller->model->getCarsMinCostForLeftColumn(); # echo '<pre>'.(print_r($_, true)).'</pre>';
$GLOBALS['tpl_cars_min_cost'] = $_;
*/

# ФУНКЦИОНАЛ

# подключаем контроллеры
# include(MVC_PATH.'articles_controller.php');
# include(MVC_PATH.'news_controller.php');
# include(MVC_PATH.'faq_controller.php');
# include(MVC_PATH.'feedback_controller.php');

# $articles_controller = new articles_controller($registry);
# $news_controller = new news_controller($registry);
# $faq_controller = new faq_controller($registry);
# $feedback_controller = new feedback_controller($registry);

# $articles_controller = $articles_controller->load('articles');
# $news_controller = $news_controller->load('news');
# $faq_controller = $faq_controller->load('faq');
# $feedback_controller = $feedback_controller->load('feedback');
# /подключаем контроллеры

# /ФУНКЦИОНАЛ

# загрузка классов "на лету"
function __autoload($class_name)
{
	$filename = strtolower($class_name); # echo "filename: ".$filename."<hr />";
	# для не-MVC классов
	$file = DOCUMENT_ROOT.'/app/library/'.$filename.'.php';
	# для MVC-классов
    $file2 = DOCUMENT_ROOT.'/app/library/mvc_'.$filename.'.php';
    # для контроллеров и моделей
    $file3 = DOCUMENT_ROOT.'/app/mvc/'.$filename.'.php';
	/* # отладка
	echo "file: {$file} (exists: ".file_exists($file).")<br />";
	echo "file2: {$file2} (exists: ".file_exists($file2).")<br />";
	echo "file3: {$file3} (exists: ".file_exists($file3).")<hr />";
	# */
   
	if (file_exists($file)) include($file);
	elseif (file_exists($file2)) include($file2);
	elseif (file_exists($file3)) include($file3);
	else return;
} # /загрузка классов "на лету"