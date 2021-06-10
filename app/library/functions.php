<?php

function convertRuLettersToEn($text) # convert ru letters to en
{
    // variables checking
    if (empty($text)) return;
    
    $search  = array('а', 'А', 'б', 'Б', 'в', 'В', 'г', 'Г', 'д', 'Д', 'е', 'Е', 'ё',  'Ё',  'ж', 'Ж',  'з', 'З', 'и', 'И', 'й', 'Й', 'к', 'К', 'л', 'Л', 'м', 'М', 'н', 'Н', 'о', 'О', 'п', 'П', 'р', 'Р', 'с', 'С', 'т', 'Т', 'у', 'У', 'ф', 'Ф', 'х', 'Х', 'ц', 'Ц', 'ч',  'Ч',  'ш',   'Ш', 'щ',  'Щ',  'ъ', 'Ъ', 'ы', 'Ы', 'ь', 'Ь', 'э', 'Э', 'ю',  'Ю',  'я',  'Я');
    $replace = array('a', 'A', 'b', 'B', 'v', 'V', 'g', 'G', 'd', 'D', 'e', 'E', 'jo', 'Jo', 'zh', 'Zh', 'z', 'Z', 'i', 'I', 'j', 'J', 'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'r', 'R', 's', 'S', 't', 'T', 'u', 'U', 'f', 'F', 'h', 'H', 'c', 'C', 'ch', 'Ch', 'sh', 'Sh', 'sh', 'Sh', '',  '',  'y', 'Y', '',  '', 'e', 'E', 'ju', 'Ju', 'ya', 'Ya');
           
    $text = str_replace($search, $replace, $text);
	
	return $text;
} # /convert ru letters to en

# ВОВЗРАЩАЕМ НАЗВАНИЕ МЕСЯЦА НА РУССКОМ В РОДИТЕЛЬНОМ ПАДЕЖЕ
function getRusMonthName($monthNum)
{
	if (!$monthNum) return;
	
	if ($monthNum == '1' || $monthNum == '01') return 'января';
	if ($monthNum == '2' || $monthNum == '02') return 'февраля';
	if ($monthNum == '3' || $monthNum == '03') return 'марта';
	if ($monthNum == '4' || $monthNum == '04') return 'апреля';
	if ($monthNum == '5' || $monthNum == '05') return 'мая';
	if ($monthNum == '6' || $monthNum == '06') return 'июня';
	if ($monthNum == '7' || $monthNum == '07') return 'июля';
	if ($monthNum == '8' || $monthNum == '08') return 'августа';
	if ($monthNum == '9' || $monthNum == '09') return 'сентября';
	if ($monthNum == '10') return 'октября';
	if ($monthNum == '11') return 'ноября';
	if ($monthNum == '12') return 'декабря';
} # /ВОВЗРАЩАЕМ НАЗВАНИЕ МЕСЯЦА НА РУССКОМ В РОДИТЕЛЬНОМ ПАДЕЖЕ

# ОБРЕЗАЕМ ТЕКCТ ДО ФИКСИРОВАННОГО КОЛИЧЕСТВА СИМВОЛОВ
function cutText($text, $length)
{
    # проверка переменных
    if (empty($text) || empty($length)) return;
    
    $text = strip_tags($text);
    
    if (strlen($text) > $length)
    {
        # $text = substr($text, 0, $length - 10);
        $text = substr($text, 0, $length);
        $text[strlen($text)-1] = preg_replace("/[^A-Za-z0-9 ]/", '', $text[strlen($text)-1]);
        $text = rtrim($text, ',.');
        $text = trim($text).'..';
    }
    return $text;
} # /ОБРЕЗАЕМ ТЕКCТ ДО ФИКСИРОВАННОГО КОЛИЧЕСТВА СИМВОЛОВ

# ЧИТАЕМ КОНТЕНТ ИЗ ФАЙЛА: ПУТЬ УКАЗЫВАЕМ ОТ DOCUMENT_ROOT
function getContent($pathToFile)
{
    # проверка переменных
    if (empty($pathToFile)) return;
    
    $fullPathToFile = $_SERVER['DOCUMENT_ROOT'].$pathToFile; # echo $fullPathToFile.'<hr />';
    if (file_exists($fullPathToFile))
    {
        ob_start(); // start capturing output
        include($fullPathToFile); // execute the file
        $content = ob_get_contents(); // get the contents from the buffer
        ob_end_clean(); // stop buffering and discard contents
        if (!empty($content)) return $content;
    }
} # /ЧИТАЕМ КОНТЕНТ ИЗ ФАЙЛА: ПУТЬ УКАЗЫВАЕМ ОТ DOCUMENT_ROOT

# БЛОК "СОВЕТЫ, НОВОСТИ, ВОПРОС-ОТВЕТ"
function showBlockInFooter()
{
    global $registry;

    # подключаем контроллеры
    if (!isIncluded('articles_controller.php')) include(MVC_PATH . 'articles_controller.php');
    if (!isIncluded('news_controller.php')) include(MVC_PATH.'news_controller.php');
    if (!isIncluded('faq_controller.php')) include(MVC_PATH.'faq_controller.php');
    if (!isIncluded('feedback_controller.php')) include(MVC_PATH.'feedback_controller.php');

    $articles_controller = new articles_controller($registry);
    $articles_controller = $articles_controller->load('articles');

    $news_controller = new news_controller($registry);
    $news_controller = $news_controller->load('news');

    $faq_controller = new faq_controller($registry);
    $faq_controller = $faq_controller->load('faq');

    $feedback_controller = new feedback_controller($registry);
    $feedback_controller = $feedback_controller->load('feedback');
    # /подключаем контроллеры

    # получаем список отзывов
    if (empty($GLOBALS['tpl_hide_feedback'])) { # если отзывы не скрыты
        $GLOBALS['tpl_feedback'] = $feedback_controller->model->getItemsForInsidePages(); # echo '<pre>'.(print_r($GLOBALS['tpl_feedback'], true)).'</pre>'; # exit;
        foreach ($GLOBALS['tpl_feedback'] as &$item) {
            $item['feedback'] = cutText($item['feedback'], 190);
            # $item['name'] = cutText($item['name'], 13);
        }
        unset($item);
    } # /если отзывы не скрыты

    # получаем количество статей
    $GLOBALS['tpl_articles_count'] = $articles_controller->model->getItemsCount();

    # получаем статьи на рандом
    $GLOBALS['tpl_articles'] = $articles_controller->model->getRandomItems(5, $GLOBALS['tpl_articles_id_selected']);

    # получаем количество вопросов-ответов
    $GLOBALS['tpl_faq_count'] = $faq_controller->model->getItemsCount();

    # получаем вопросы-ответы на рандом
    $GLOBALS['tpl_faq'] = $faq_controller->model->getRandomItems(5, $GLOBALS['tpl_faq_id_selected']);
    foreach ($GLOBALS['tpl_faq'] as &$item) {
        $item['h1'] = cutText($item['h1'], 55);
    } unset($item);

    # получаем новости на рандом
    $GLOBALS['tpl_news'] = $news_controller->model->getRandomItems(3, $GLOBALS['tpl_news_id_selected']);
} # /БЛОК "СОВЕТЫ, НОВОСТИ, ВОПРОС-ОТВЕТ"

# ПРОВЕРЯЕМ, ПОДКЛЮЧЕН ЛИ ФАЙЛ ИЛИ НЕТ
function isIncluded($fileName) {
    $included_files = get_included_files(); # echo '<pre>'.(print_r($included_files, true)).'</pre>';
    foreach ($included_files as $item) {
        if (basename($item) == $fileName) return 1;
    }
} # /ПРОВЕРЯЕМ, ПОДКЛЮЧЕН ЛИ ФАЙЛ ИЛИ НЕТТ

# ПОЛУЧАЕМ АНКОР ДЛЯ ПЕРЕЛИНКОВКИ В ПОДВАЛЕ
/*
function getFooteranchor($anchorId)
{
    # проверка переменных
    if (empty($anchorId)) return;
    
    global $dbh;
    
    $sql = '
    select anchor,
           target
    from '.DB_PREFIX.'footeranchors
    where id = :id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $anchorId, PDO::PARAM_INT);
    try
    {
        $sth->execute();
        $_ = $sth->fetch();
        if (!empty($_))
        {
            $GLOBALS['tpl_footeranchor']['anchor'] = $_['anchor'];
            $GLOBALS['tpl_footeranchor']['target'] = $_['target'];
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
} */ # /ПОЛУЧАЕМ АНКОР ДЛЯ ПЕРЕЛИНКОВКИ В ПОДВАЛЕ