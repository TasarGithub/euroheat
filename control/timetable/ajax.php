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

# ПОЛУЧАЕМ РАСПИСАНИЕ
if ($_POST['action'] == 'getTimetable') {
    # выводим расписание
    showTimetable();
}

# СОХРАНЯЕМ ИЗМЕНЕНИЯ
elseif ($_POST['action'] == 'saveChanges') {
    if (empty($_POST['eventsAndDates'])) exit('<div style="color:red;font-weight:bold;text-align:center;font-size:24px;margin-top:25px">Пожалуйста, укажите как минимум одно шоу, дату и время.</div>');

    # echo $_GET['eventsAndDates'];
    # if (saveChanges()) echo '<span style="color:blue;font-weight:bold">сохранено</span> &nbsp;';
    # else echo '<span style="color:red;font-weight:bold">не сохранено</span> &nbsp;';

    # сохраняем изменения
    saveChanges();

    # выводим расписание
    showTimetable();
} # /СОХРАНЯЕМ ИЗМЕНЕНИЯ

# УДАЛЯЕМ ДАТУ
elseif ($_POST['action'] == 'remove_date') {
    if (empty($_POST['date_id'])) exit('<div style="color:red;font-weight:bold;text-align:center;font-size:24px;margin-top:25px">Не передан id удаляемой даты.</div>');

    # удаляем дату
    removeDate();
}

# /ЛОГИКА

# ФУНКЦИИ

# ВЫВОДИМ РАСПИСАНИЕ
function showTimetable()
{
    global $dbh;

    $_ = getTimetable(); # echo '<pre>'.(print_r($_, true)).'</pre>';
    $_c = count($_);
    if (!empty($_)) {
        for ($i=0;$i<$_c;$i++) {
            $optionsForShowsSelect = getShowsForSelect($_[$i]['show_id']);
            $optionsForPlacesSelect = getPlacesForSelect($_[$i]['place_id']);

            # выводим select с шоу только для Вернадского
            # в Лужниках проходит только одно текущее шоу
            if ($_[$i]['place_id'] == 1) $class = ' class="hidden"';
            else unset($class);

            $result .= '
            <div class="well timetable">
                <div class="form-group timetable" data-item-id="'.$_[$i]['id'].'">
					<b>ДАТА</b><!-- (ДД.ММ.ГГГГ)-->:
					&nbsp; <input type="text" name="timetable_event_date" class="form-control timetable_event_date" value="'.$_[$i]['date_formatted'].'" />
					&nbsp;&nbsp;&nbsp;
					<b>ВРЕМЯ</b>:
					&nbsp; <input type="text" name="timetable_event_time" class="form-control timetable_event_time" value="'.$_[$i]['time'].'" maxlength="5" />
					&nbsp;&nbsp;&nbsp;
					<b>ПЛОЩАДКА</b>: &nbsp;
					<select name="timetable_event_place_id" class="timetable_place_id form-control">
					'.$optionsForPlacesSelect.'
					</select>

                    <span'.$class.'>
                    &nbsp;&nbsp;&nbsp;
                    <b>ШОУ:</b> &nbsp;
                    <select name="timetable_event_name" class="timetable_event_id form-control">
                    '.$optionsForShowsSelect.'
                    </select>
                    </span>

                    <a title="Удалить дату" href="#" class="time_remove_item">
                        <i class="fa fa-trash-o size_18"></i>
                    </a>
                </div>
            </div>
			';
        }
        echo $result;
    }
} # /ВЫВОДИМ РАСПИСАНИЕ

# ПОЛУЧАЕМ РАСПИСАНИЕ
function getTimetable() {
    global $dbh;

    unset($sqlModifier);

    # сортировка по месту проведения (площадке) "Лужники"
    if ($_POST['place'] == 1) $sqlModifier = ' and place_id = 1 ';
    # сортировка по месту проведения (площадке) "Проспект Вернадского"
    elseif ($_POST['place'] == 2) $sqlModifier = ' and place_id = 2 ';

    # сортировка по шоу
    if (!empty($_POST['show'])) $sqlModifier .= ' and show_id = :show_id ';

    $sql = "
	select id,
	       show_id,
	       place_id,
		   date_format(date,'%e.%m.%Y') as date_formatted,
		   date_format(date,'%H:%i') as time
	from ".DB_PREFIX."timetable
	where 1 ".$sqlModifier."
	order by date
	"; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    # сортировка по шоу
    if (!empty($_POST['show'])) $sth->bindValue(':show_id', $_POST['show'], PDO::PARAM_INT);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>'; # exit;
            if (!empty($_)) return $_;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /ПОЛУЧАЕМ РАСПИСАНИЕ

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

# СОХРАНЯЕМ ИЗМЕНЕНИЯ ПРИ ВЫБОРКЕ ДАТЫ И СПЕКТАКЛЯ
function saveChanges()
{
    # проверка переменных
    if (empty($_POST['eventsAndDates'])) return;

    global $dbh;

    # print_r($_POST); exit;

    # print_r($_POST['eventsAndDates']); exit;

    # проходим по массиву дат и спектаклей
    # получаем строки по символу ';'
    $eventsAndDates = rtrim($_POST['eventsAndDates'], ';'); # echo $eventsAndDates; exit;
    $eventsAndDates = explode(';', $eventsAndDates); # print_r($eventsAndDates); exit;
    if (!empty($eventsAndDates)
        and is_array($eventsAndDates)) {
        # очищаем таблицу с расписанием
        clearTimeTable();

        foreach($eventsAndDates as $eventAndDate) {
            if (!empty($eventAndDate)) {
                $item = explode('*', $eventAndDate); # print_r($item); echo '<hr />';
                # place + '*' + date + '*' + time + '*' + (!show ? '' : show) + ';';
                # example: 2*18.11.2015*19:00*2;1*19.12.2015*14:00*;1*19.12.2015*19:00*;
                if (!empty($item[0]) && !empty($item[1]) && !empty($item[2])) {
                    # echo 'event_id: '.$item[0].', date: '.$item[1].', time: '.$item[2].', stage: '.$item[3].'<br />';

                    $place = $item[0];

                    $date = date("Y-m-d", strtotime($item[1])); // echo $date.'<br />';
                    $date .= ' '.$item[2]; # echo $date.'<br />';

                    $show = $item[3];

                    # сохраняем расписание
                    saveNewData(array(
                        'show' => $show,
                        'place' => $place,
                        'date' => $date
                    ));
                }
            }
        }

        return 1;
    } # /проходим по массиву дат и спектаклей
} # /СОХРАНЯЕМ ИЗМЕНЕНИЯ ПРИ ВЫБОРКЕ ДАТЫ И СПЕКТАКЛЯ

# СОХРАНЯЕМ РАСПИСАНИЕ
function saveNewData($array)
{
    # print_r($array); echo '<hr>'; exit;

    # проверка переменных
    # if (empty($array['show'])) return;
    if (empty($array['place'])) return;
    if (empty($array['date'])) return;

    global $dbh;

    # для Лужников не сохраняем шоу
    if ($array['place'] == 1) $array['show'] = null;

    # сохраняем новые данные о спектаклях и датах
    $sql = "
    insert into ".DB_PREFIX."timetable
    (
     show_id,
     place_id,
     date
    )
    values
    (
     :show_id,
     :place_id,
     :date
    )
    "; # echo '<pre>'.$sql."</pre><hr />event: ".$event.', date: '.$date.'<hr />';

    $result = $dbh->prepare($sql);

    $result->bindValue(':show_id', !empty($array['show']) ? $array['show'] : null);
    $result->bindParam(':place_id', $array['place'], PDO::PARAM_INT);
    $result->bindParam(':date', $array['date'], PDO::PARAM_STR);

    try {
        if ($result->execute()) {
            return 1;
        }
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /СОХРАНЯЕМ РАСПИСАНИЕ

# ОЧИЩАЕМ ТАБЛИЦУ С РАСПИСАНИЕМ
function clearTimeTable()
{
    global $dbh;

    unset($sqlModifier);

    # сортировка по месту проведения (площадке) "Лужники"
    if ($_POST['place'] == 1) $sqlModifier = ' and place_id = 1 ';
    # сортировка по месту проведения (площадке) "Проспект Вернадского"
    elseif ($_POST['place'] == 2) $sqlModifier = ' and place_id = 2 ';

    # сортировка по шоу
    if (!empty($_POST['show'])) $sqlModifier .= ' and show_id = :show_id ';

    # удаляем старые записи, если есть
    $sql = "
	delete from ".DB_PREFIX."timetable
	where 1 ".$sqlModifier."
	"; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    # сортировка по шоу
    if (!empty($_POST['show'])) $sth->bindValue(':show_id', $_POST['show'], PDO::PARAM_INT);
    try {
        if ($sth->execute()) return 1;
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /ОЧИЩАЕМ ТАБЛИЦУ С РАСПИСАНИЕМ

# УДАЛЯЕМ ДАТУ
function removeDate()
{
    global $dbh;

    # удаляем старые записи, если есть
    $sql = "
	delete from ".DB_PREFIX."timetable
	where id = :id
	"; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindValue(':id', $_POST['date_id'], PDO::PARAM_INT);
    try {
        if ($sth->execute()) return 1;
    }
    catch (PDOException $e) {
        if (DB_SHOW_ERRORS) {
            echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /УДАЛЯЕМ ДАТУ

# /ФУНКЦИИ