<?php

# ������ ������
session_start();

# echo '<pre>'.(print_r($_SESSION, true)).'</pre>';

# echo '<!-- '.$_SERVER['REQUEST_URI'].' -->';

# unset($_SESSION['auth']);

### �������
# print_r($_GET);
# print_r($_POST);
# print_r($_FILES);
# echo '<pre>'.(print_r($_COOKIE, true)).'</pre>';

# ������
include('config.php');

# ����� "������": � ��� �������� ���������� ������ �������
$registry = new registry;

# ��������� mysql � ������������� ����������: http://ru2.php.net/manual/en/book.pdo.php
include('db.connection.pdo.php');
$registry->set('dbh', $dbh);

# ����� ������������ �� ���� php
include(DOCUMENT_ROOT.'/app/library/templates.php');
$tpl = new templates();
$registry->set('tpl', $tpl);

# ����� "�������������"
$router = new router($registry);
$router->setPath(MVC_PATH);
$registry->set('router', $router);
# $router->showRouterInfo = 1; # ������� ���������� ����������
$router->showRouterInfo = 0; # ������� ���������� ����������

# ����� ������ ���������������� ������
$defence = new defence;
$registry->set('defence', $defence);
$GLOBALS['registry'] = $registry;

# ������� ���������� ��� �����
# ������� (�������� � ������� � �������, �������� �� ���� �����)
$GLOBALS["phone"] = $tpl->getTemplate('phone.html');
$GLOBALS["phone_for_link"] = preg_replace('/[^0-9]/i', '', $GLOBALS["phone"]);
$GLOBALS["phone_for_link"] = preg_replace('/^8/i', '+7', $GLOBALS["phone_for_link"]);

# ���������� ����� �������
include(DOCUMENT_ROOT.'/app/library/functions.php');

### ��������� ���������� �����

# 15 ���� 2016 �������� �� URL selector
# ��������� ��������
# ���������� �� ��������
# $router->addRoute(array('path' => '/^generatora\/(10-kvt)|(15-kvt)|(16-kvt)|(20-kvt)|(30-kvt)|(40-kvt)|(50-kvt)|(60-kvt)|(64-kvt)|(70-kvt)|(80-kvt)|(100-kvt)|(120-kvt)|(150-kvt)|(160-kvt)|(200-kvt)|(220-kvt)|(240-kvt)|(250-kvt)|(300-kvt)|(320-kvt)|(400-kvt)|(500-kvt)|(600-kvt)|(650-kvt)|(800-kvt)|(1000-kvt)$/', 'controller' => 'site_section_default', 'action' => 'index'));

# 15 ���� 2016 �������� �� URL selector
# ��������������. ��������
# $router->addRoute(array('path' => '/^generatora\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'catalog', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# URL selector: ���� ��� ��������� ������, ���� ��� �������������� ��������
$router->addRoute(array('path' => '/^generatora\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'site_section_default', 'action' => 'urlSelectorGeneratora', 'vars' => array(1 => 'itemURL')));

# �������. ������������ �����
$router->addRoute(array('path' => '/^novosti\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# �������. ��������
$router->addRoute(array('path' => '/^novosti\/([-0-9]{10})$/', 'controller' => 'news', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# ������. ������������ �����
$router->addRoute(array('path' => '/^otzyvy\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# ������. ��������
$router->addRoute(array('path' => '/^otzyvy\/([\/0-9]*)$/', 'controller' => 'feedback', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# �������-������. ������������ �����
$router->addRoute(array('path' => '/^vopros\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# �������-������. ��������
$router->addRoute(array('path' => '/^vopros\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'faq', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# ������. ������������ �����
$router->addRoute(array('path' => '/^sovet\/page([0-9]*)$/', 'controller' => 'site_section_default', 'action' => 'index', 'vars' => array(1 => 'page')));
# ������. ��������
$router->addRoute(array('path' => '/^sovet\/([-\/_0-9.a-zA-Z]*)$/', 'controller' => 'articles', 'action' => 'showItem', 'vars' => array(1 => 'itemURL')));

# ����� �����
$router->addRoute(array('path' => '/^karta-sajta/', 'controller' => 'map', 'action' => 'index'));

### /��������� ���������� �����

# ������������� �������: ���������� ����������� � loader
# ������������� ����������
/*
include(MVC_PATH.'cars_controller.php');
$cars_controller = new cars_controller($registry);
# �������� ������ ���. ��� ��� ������� �����
$_ = $cars_controller->model->getCarsMinCostForLeftColumn(); # echo '<pre>'.(print_r($_, true)).'</pre>';
$GLOBALS['tpl_cars_min_cost'] = $_;
*/

# ����������

# ���������� �����������
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
# /���������� �����������

# /����������

# �������� ������� "�� ����"
function __autoload($class_name)
{
	$filename = strtolower($class_name); # echo "filename: ".$filename."<hr />";
	# ��� ��-MVC �������
	$file = DOCUMENT_ROOT.'/app/library/'.$filename.'.php';
	# ��� MVC-�������
    $file2 = DOCUMENT_ROOT.'/app/library/mvc_'.$filename.'.php';
    # ��� ������������ � �������
    $file3 = DOCUMENT_ROOT.'/app/mvc/'.$filename.'.php';
	/* # �������
	echo "file: {$file} (exists: ".file_exists($file).")<br />";
	echo "file2: {$file2} (exists: ".file_exists($file2).")<br />";
	echo "file3: {$file3} (exists: ".file_exists($file3).")<hr />";
	# */
   
	if (file_exists($file)) include($file);
	elseif (file_exists($file2)) include($file2);
	elseif (file_exists($file3)) include($file3);
	else return;
} # /�������� ������� "�� ����"