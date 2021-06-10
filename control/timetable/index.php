<?php 
# Модуль админки для работы с расписанием (таблица timetable)
# romanov.egor@gmail.com; 2015.10.16

# подключаем файл конфига
include('../loader.control.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'Расписание';
$GLOBALS['public_url'] = '/raspisanie/';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА

$GLOBALS['tpl_title'] .= '';
$GLOBALS['tpl_h1'] = 'Расписание ('.$dbh->query('select count(1) from '.DB_PREFIX.'timetable')->fetchColumn().')';
$GLOBALS['tpl_content'] = showItems();

# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ВЫВОДИМ ФОРМУ ДЛЯ РАСПИСАНИЯ
function showItems($count = null)
{
    global $dbh;

    $lastShowID = getLastShowID();
    $optionsForShowsSelect = getShowsForSelect($lastShowID);
    $optionsForPlacesSelect = getPlacesForSelect($lastShowID);

    # считаем количество событий в Лужниках
    $timetable1Count = $dbh->query('select count(1) from '.DB_PREFIX.'timetable where place_id = 1')->fetchColumn();
    # считаем количество событий на Вернадского
    $timetable2Count = $dbh->query('select count(1) from '.DB_PREFIX.'timetable where place_id = 2')->fetchColumn();

    # ЕСЛИ УКАЗАНА СОРТИРОВКА ПО ПЛОЩАДКЕ (И/ИЛИ ПО ШОУ)
    # Лужники
    if ($_GET['place'] == 1) $active1 = ' class="active"';
    # Вернадского
    elseif ($_GET['place'] == 2) {
        $active2 = " class='active'";
        # формируем и выводим сортировку по шоу
        $sql = '
        select distinct t1.show_id as id,
               t2.name,
               (select count(1) from '.DB_PREFIX.'timetable where place_id = 2 and show_id = t1.show_id) as items_count
        from '.DB_PREFIX.'timetable as t1
        left outer join shows_circus as t2
        on t2.id = t1.show_id
        where t1.place_id = 2
        '; # echo '<pre>'.$sql."</pre><hr />";
        $tmp = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($tmp, true)).'</pre>'; # exit;
        if (!empty($tmp)) {
            $tmpResult = array();
            foreach ($tmp as $item) {
                # selected
                $active = $_GET['show'] == $item['id'] ? ' class="active"' : '';

                $tmpResult[] = '<a href="/control/timetable/?place=2&show='.$item['id'].'"'.$active.'>'.$item['name'].' ('.$item['items_count'].')</a>';
            }
            $sortingByShow = '
            <!-- Сортировка по шоу -->
            <div class="sorting"><b>Сортировка по шоу:</b> '.
            implode(' <span style="color:#ccc">|</span> ', $tmpResult).
            '</div>
            <!-- /Сортировка по шоу -->';
        }
    } # /ЕСЛИ УКАЗАНА СОРТИРОВКА ПО ПЛОЩАДКЕ (И/ИЛИ ПО ШОУ)

    $result = '
	<script type="text/javascript" src="/control/timetable/index.js?v=1.4"></script>

    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="'.$GLOBALS['public_url'].'" target="_blank">http://'.$_SERVER['SERVER_NAME'].$GLOBALS['public_url'].'</a>
    </div>

    <br style="clear:both" />

    <!-- Сортировка по площадке -->
    <div class="sorting"><b>Сортировка:</b>
    <a href="/control/timetable/?place=1"'.$active1.'>Лужники ('.$timetable1Count.')</a> <span style="color:#ccc">|</span>
    <a href="/control/timetable/?place=2"'.$active2.'>Вернадского ('.$timetable2Count.')</a>
    </div>
    '.$sortingByShow.'
    <!-- /Сортировка по площадке -->

    <div class="center" style="margin-top:10px;margin-bottom:15px">
        <a href="#" class="timetable_add_date_button"><button class="btn btn-success" type="button"><i class="fa fa-plus-square" style="margin-right:3px"></i> Добавить дату</button></a>

        &nbsp;&nbsp;&nbsp;

        <button class="btn btn-primary submit_button timetable_save_changes inline" type="submit">Сохранить изменения</button>
    </div>

    <div id="time_table_list"></div>

    <div id="template_add_date" style="display:none">
        <div class="well timetable">
            <div class="form-group timetable" data-item-id="'.$_[$i]['id'].'">
                <b>ДАТА</b><!-- (ДД.ММ.ГГГГ)-->:
                &nbsp; <input type="text" name="timetable_event_date" class="form-control timetable_event_date" value="'.$_[$i]['date_formatted'].'" />
                &nbsp;&nbsp;&nbsp;
                <b>ВРЕМЯ</b>:
                &nbsp;<input type="text" name="timetable_event_time" class="form-control timetable_event_time" value="'.$_[$i]['time'].'" maxlength="5" />
                &nbsp;&nbsp;&nbsp;
                <b>ПЛОЩАДКА</b>: &nbsp;
                <select name="timetable_event_place_id" class="timetable_place_id form-control">
                '.$optionsForPlacesSelect.'
                </select>
                <span>
                &nbsp;&nbsp;&nbsp;
                <b>ШОУ:</b> &nbsp;
                <select name="timetable_event_name" class="timetable_event_id form-control">
                '.$optionsForShowsSelect.'
                </select>
                &nbsp;&nbsp;&nbsp;
                <a title="Удалить дату" href="#" class="time_remove_item">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
                </span>
            </div>
        </div>
    </div>
    ';

    return $result;
} # /ВЫВОДИМ ФОРМУ ДЛЯ РАСПИСАНИЯ

# ПОЛУЧАЕМ СПИСОК ШОУ, ФОРМИРУЕМ OPTIONS ДЛЯ SELECT'А
function getShowsForSelect($showID)
{
    global $dbh;

    $sql = "
	select id, name
	from ".DB_PREFIX."shows_circus
	order by isnull(element_order_listing),
	         element_order_listing,
	         name
	"; # echo $sql."<hr />";
    $result = $dbh->prepare($sql);
    try {
        if ($result->execute()) {
            $_ = $result->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            $_c = count($_);
            if (!empty($_)) {
                $result = '';
                for ($i=0;$i<$_c;$i++) {
                    # selected
                    if ($_[$i]['id'] == $showID) $selected = ' selected="selected"';
                    else unset($selected);

                    $result .= '<option value="'.$_[$i]['id'].'"'.$selected.'>'.$_[$i]['name'].'</option>';
                }
            }

            return $result;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /ПОЛУЧАЕМ СПИСОК ШОУ, ФОРМИРУЕМ OPTIONS ДЛЯ SELECT'А

# ПОЛУЧАЕМ ID ПОСЛЕДНЕГО ШОУ
function getLastShowID()
{
    global $dbh;

    $sql = "
	select max(id)
	from ".DB_PREFIX."shows
	"; # echo $sql."<hr />";
    $result = $dbh->prepare($sql);
    try {
        if ($result->execute()) {
            $_ = $result->fetchColumn(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            if (!empty($_)) return $_;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /ПОЛУЧАЕМ ID ПОСЛЕДНЕГО ШОУ

# ПОЛУЧАЕМ СПИСОК МЕСТ ПРОВЕДЕНИЯ ПРЕДСТАВЛЕНИЙ, ФОРМИРУЕМ OPTIONS ДЛЯ SELECT'А
function getPlacesForSelect($showID)
{
    global $dbh;

    $sql = "
	select id, name
	from ".DB_PREFIX."places
	order by name
	"; # echo $sql."<hr />";
    $result = $dbh->prepare($sql);
    try {
        if ($result->execute()) {
            $_ = $result->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            $_c = count($_);
            if (!empty($_)) {
                $result = '';
                for ($i=0;$i<$_c;$i++) {
                    # selected
                    if ($_[$i]['id'] == $showID) $selected = ' selected="selected"';
                    else unset($selected);

                    $result .= '<option value="'.$_[$i]['id'].'"'.$selected.'>'.$_[$i]['name'].'</option>';
                }
            }

            return $result;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /ПОЛУЧАЕМ СПИСОК МЕСТ ПРОВЕДЕНИЯ ПРЕДСТАВЛЕНИЙ

# /ФУНКЦИОНАЛ