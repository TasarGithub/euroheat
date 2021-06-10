<?php
# скрипт генерирует /sitemap.xml в корне сайта для поисковых систем
# Файлы sitemap: https://yandex.ru/support/webmaster/indexing-options/sitemap.xml#reqs_1
# Часто задаваемые вопросы: https://www.sitemaps.org/ru/faq.html#faq_xml_schema
# XML-формат файла Sitemap: https://www.sitemaps.org/ru/protocol.html
# 26.12.2018
# romanov.egor@gmail.com

# устанавливаем нужную кодировку, которую будет отдавать скрипт
header('Content-type: text/xml; charset=UTF-8');

# настройки
$_SERVER['DOCUMENT_ROOT'] = '/var/www/euroheater/data/www/euroheater.ru';
$GLOBALS['domain'] = 'https://euroheater.ru/';

# подключаем PDO для работы с mysql
include($_SERVER['DOCUMENT_ROOT'].'/app/db.connection.pdo.php');

# формируем карту сайта по таблице sections
buildSiteMap();

# ****************************************************************************************

# ФУНКЦИИ

# ФОРМИРУЕМ КАРТУ САЙТА ПО ТАБЛИЦЕ SECTIONS
function buildSiteMap()
{
    global $dbh;

    # результат
    $result = array();

    # фиксируем результат
    $result[] = array(
        'loc' => rtrim($GLOBALS['domain'], '/'),
        'priority' => '1.0',
        'changefreq' => 'daily');

    # ПОЛУЧАЕМ 2 УРОВЕНЬ
    $sql = "
    select id,
           name,
           full_url
    from ".DB_PREFIX."site_sections
    where parent_id = 1
          and is_showable = 1
    order by name
    "; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $level2 = $sth->fetchAll();
    $level2_c = count($level2);
    # echo '<pre>'.(print_r($level2, true)).'</pre>';

    # ****************************************************************************************

    if (!empty($level2)) {
        for ($i=0;$i<$level2_c;$i++) {
            # фиксируем результат
            $result[] = array(
                'loc' => $GLOBALS['domain'].$level2[$i]['full_url'].'/',
                'priority' => '0.9',
                'changefreq' => 'daily');

            # ****************************************************************************************

            # РУЧНАЯ ВСТАВКА ДЛЯ ПОТОМКОВ 2 УРОВНЯ

            if ($level2[$i]['full_url'] == 'generatora') {
                unset($items);
                $items = getCatalog();
                # echo '<pre>'.(print_r($items, true)).'</pre>';
                if (!empty($items)) $result = array_merge($items, $result);
            }

            # ****************************************************************************************

            if ($level2[$i]['full_url'] == 'novosti') {
                unset($items);
                $items = getNews();
                # echo '<pre>'.(print_r($items, true)).'</pre>';
                $items_c = count($items);
                for ($j=0;$j<$items_c;$j++) {
                    # фиксируем результат
                    $result[] = array(
                        'loc' => $GLOBALS['domain'].$level2[$i]['full_url'].'/'.$items[$j]['date_add_formatted_2'].'/',
                        'priority' => '0.8',
                        'changefreq' => 'daily');
                }
            }
            # ****************************************************************************************

            if ($level2[$i]['full_url'] == 'vopros') {
                unset($items);
                $items = getFaq();
                # echo '<pre>'.(print_r($items, true)).'</pre>';
                $items_c = count($items);
                for ($j=0;$j<$items_c;$j++) {
                    # фиксируем результат
                    $result[] = array(
                        'loc' => $GLOBALS['domain'].$level2[$i]['full_url'].'/'.$items[$j]['url'].'/',
                        'priority' => '0.8',
                        'changefreq' => 'daily');
                }
            }

            # ****************************************************************************************

            if ($level2[$i]['full_url'] == 'otzyvy') {
                unset($items);
                $items = getFeedback();
                # echo '<pre>'.(print_r($items, true)).'</pre>';
                $items_c = count($items);
                for ($j=0;$j<$items_c;$j++) {
                    # фиксируем результат
                    $result[] = array(
                        'loc' => $GLOBALS['domain'].$level2[$i]['full_url'].'/'.$items[$j]['id'].'/',
                        'priority' => '0.8',
                        'changefreq' => 'daily');
                }
            }

            # ****************************************************************************************

            if ($level2[$i]['full_url'] == 'sovet') {
                unset($items);
                $items = getArticles();
                # echo '<pre>'.(print_r($items, true)).'</pre>';
                $items_c = count($items);
                for ($j=0;$j<$items_c;$j++) {
                    # фиксируем результат
                    $result[] = array(
                        'loc' => $GLOBALS['domain'].$level2[$i]['full_url'].'/'.$items[$j]['url'].'/',
                        'priority' => '0.8',
                        'changefreq' => 'daily');
                }
            }

            # /РУЧНАЯ ВСТАВКА ДЛЯ ПОТОМКОВ 2 УРОВНЯ

            # ****************************************************************************************

            # ПОЛУЧАЕМ 3 УРОВЕНЬ
            $sql = "
            select id,
                   name,
                   full_url
            from ".DB_PREFIX."site_sections
            where parent_id = :parent_id
                  and is_showable = 1
            order by name
            "; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindValue(':parent_id', $level2[$i]['id']);
            $sth->execute();
            $level3 = $sth->fetchAll();
            $level3_c = count($level3);
            # echo '<pre>'.(print_r($level3, true)).'</pre>';

            if (!empty($level3)) {
                for ($x=0;$x<$level3_c;$x++) {
                    # фиксируем результат
                    $result[] = array(
                        'loc' => $GLOBALS['domain'].$level3[$x]['full_url'].'/',
                        'priority' => '0.8',
                        'changefreq' => 'daily');

                    # ПОЛУЧАЕМ 4 УРОВЕНЬ
                    $sql = "
                    select id,
                           name,
                           full_url
                    from ".DB_PREFIX."site_sections
                    where parent_id = :parent_id
                          and is_showable = 1
                    order by name
                    "; # echo '<pre>'.$sql."</pre><hr />";
                    $sth = $dbh->prepare($sql);
                    $sth->bindValue(':parent_id', $level3[$x]['id']);
                    $sth->execute();
                    $level4 = $sth->fetchAll();
                    $level4_c = count($level4);
                    # echo '<pre>'.(print_r($level4, true)).'</pre>';

                    if (!empty($level4)) {
                        for ($z=0;$z<$level4_c;$z++) {
                            # фиксируем результат
                            $result[] = array(
                                'loc' => $GLOBALS['domain'].$level4[$z]['full_url'].'/',
                                'priority' => '0.8',
                                'changefreq' => 'daily');

                            # ПОЛУЧАЕМ 5 УРОВЕНЬ
                            $sql = "
                            select id,
                                   name,
                                   full_url
                            from ".DB_PREFIX."site_sections
                            where parent_id = :parent_id
                                  and is_showable = 1
                            order by name
                            "; # echo '<pre>'.$sql."</pre><hr />";
                            $sth = $dbh->prepare($sql);
                            $sth->bindValue(':parent_id', $level4[$z]['id']);
                            $sth->execute();
                            $level5 = $sth->fetchAll();
                            $level5_c = count($level5);
                            # echo '<pre>'.(print_r($level5, true)).'</pre>';

                            if (!empty($level5)) {
                                for ($d=0;$d<$level4_c;$d++) {
                                    # фиксируем результат
                                    $result[] = array(
                                        'loc' => $GLOBALS['domain'].$level5[$d]['full_url'].'/',
                                        'priority' => '0.8',
                                        'changefreq' => 'daily');

                                    # ПОЛУЧАЕМ 6 УРОВЕНЬ
                                    $sql = "
                                    select id,
                                           name,
                                           full_url
                                    from ".DB_PREFIX."site_sections
                                    where parent_id = :parent_id
                                          and is_showable = 1
                                    order by name
                                    "; # echo '<pre>'.$sql."</pre><hr />";
                                    $sth = $dbh->prepare($sql);
                                    $sth->bindValue(':parent_id', $level5[$d]['id']);
                                    $sth->execute();
                                    $level6 = $sth->fetchAll();
                                    $level6_c = count($level6);
                                    # echo '<pre>'.(print_r($level6, true)).'</pre>';

                                    if (!empty($level6)) {
                                        for ($q=0;$q<$level6_c;$q++) {
                                            # фиксируем результат
                                            $result[] = array(
                                                'loc' => $GLOBALS['domain'].$level6[$q]['full_url'].'/',
                                                'priority' => '0.8',
                                                'changefreq' => 'daily');
                                        }
                                    }
                                    # /ПОЛУЧАЕМ 6 УРОВЕНЬ
                                }
                            }
                            # /ПОЛУЧАЕМ 5 УРОВЕНЬ
                        }
                    }
                    # /ПОЛУЧАЕМ 4 УРОВЕНЬ
                }
            }
            # /ПОЛУЧАЕМ 3 УРОВЕНЬ
        }
    }
    # /ПОЛУЧАЕМ 2 УРОВЕНЬ

    # выводим результат
    if (!empty($result)) {
        # $result = implode(PHP_EOL, $result);
        # echo '<pre>'.(print_r($result, true)).'</pre>';
        $resultXML = '';
        $result_c = count($result);
        for ($i=0;$i<$result_c;$i++) {
            $resultXML .=
            "\n\t\t".'<url>'.

                "\n\t\t\t".'<loc>'.escape($result[$i]['loc']).'</loc>'.

                "\n\t\t\t".'<changefreq>'.$result[$i]['changefreq'].'</changefreq>'.

                "\n\t\t\t".'<priority>'.$result[$i]['priority'].'</priority>'.

            "\n\t\t".'</url>'.PHP_EOL;
        }

        $resultXML =
        '<?xml version="1.0" encoding="UTF-8"?>'.
        
        "\n\t".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.
            $resultXML.
        "\n\t".'</urlset>';

        # echo $resultXML;

        # пишем результат в /sitemap.xml
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml', $resultXML);
    }

}
# /ФОРМИРУЕМ КАРТУ САЙТА ПО ТАБЛИЦЕ SECTIONS

# ****************************************************************************************

# ПОЛУЧАЕМ АВТО ПО ГРУППЕ ТЕХНИКИ
function getCatalogItemsForGroup($groupID)
{
    global $dbh;

    # проверка переменных
    if (empty($groupID)) return;

    $sql = "
    select id,
           url
    from ".DB_PREFIX."catalog
    where group_id = :group_id
    order by name
    "; # echo $sql."<hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindValue(':group_id', $groupID);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # print_r($_);
            return $_;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ПОЛУЧАЕМ АВТО ПО ГРУППЕ ТЕХНИКИ

# ****************************************************************************************

# ПОЛУЧАЕМ ВОПРОСЫ-ОТВЕТЫ
function getFaq()
{
    global $dbh;

    $sql = "
    select id,
           url
    from ".DB_PREFIX."faq
    where is_showable = 1
    order by name
    "; # echo $sql."<hr />";
    $sth = $dbh->prepare($sql);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # print_r($_);
            return $_;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ПОЛУЧАЕМ ВОПРОСЫ-ОТВЕТЫ

# ****************************************************************************************

# ПОЛУЧАЕМ НОВОСТИ
function getNews()
{
    global $dbh;

    $sql = "
    select id,
           date_format(date_add, '%d-%m-%Y') as date_add_formatted_2
    from ".DB_PREFIX."news
    where is_showable = 1
    order by date_add desc
    "; # echo $sql."<hr />";
    $sth = $dbh->prepare($sql);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # print_r($_);
            return $_;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ПОЛУЧАЕМ НОВОСТИ

# ****************************************************************************************

# ПОЛУЧАЕМ ОТЗЫВЫ
function getFeedback()
{
    global $dbh;

    $sql = "
    select id
    from ".DB_PREFIX."feedback
    where is_published = 1 
    order by date_add desc,
             name
    "; # echo $sql."<hr />";
    $sth = $dbh->prepare($sql);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # print_r($_);
            return $_;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ПОЛУЧАЕМ ОТЗЫВЫ

# ****************************************************************************************

# ПОЛУЧАЕМ СОВЕТЫ
function getArticles()
{
    global $dbh;

    $sql = "
    select id,
           url
    from ".DB_PREFIX."articles
    where is_showable = 1
    order by name
    "; # echo $sql."<hr />";
    $sth = $dbh->prepare($sql);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # print_r($_);
            return $_;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ПОЛУЧАЕМ СОВЕТЫ

# ****************************************************************************************
# ФОРМИРУЕМ КАТАЛОГ ТОВАРОВ
function getCatalog()
{
    global $dbh;

    # ФОРМИРУЕМ РАЗДЕЛЫ КАТАЛОГА
    $sql = '
    select t1.id,
           t1.url
    from '.DB_PREFIX.'catalog as t1
    where t1.is_showable = 1
    order by t1.url
    '; # echo '<pre>'.$sql.'</pre><hr />';
    # and t1.price > 0
    $sth = $dbh->prepare($sql);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
            $_c = count($_);
            # проходим по всем группам
            for ($i=0;$i<$_c;$i++) {
                $result[] = array(
                'loc' => $GLOBALS['domain'].'generatora/'.$_[$i]['url'].'/',
                'priority' => '0.9',
                'changefreq' => 'daily');
            }
            # если есть хотя бы один слуховой аппарат с ценой > 0
            if (!empty($result)) return $result;
            # если вся серия архивная (цена всех слуховых аппаратов в серии = 0)
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ФОРМИРУЕМ КАТАЛОГ ТОВАРОВ
# ****************************************************************************************

# ESCAPE SYMBOLS ACCORDING TO https://www.sitemaps.org/protocol.html#escaping
function escape($string)
{
    if (!empty($string)) {
        $string = str_replace("&", '&amp;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace("'", '&apos;', $string);

        return $string;
    }
}
# /ESCAPE SYMBOLS

# /ФУНКЦИИ

