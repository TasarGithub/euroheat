<?php # общие функции для index.php и ajax.php

define('HTTP_HOST', str_replace('www.', '', $_SERVER['HTTP_HOST']));

# ПОЛУЧАЕМ СТАТИСТИКУ
function getCallsStatistics($array = null)
{
    global $dbh;

    # Всего звонков было
    # Всего уникальных звонков было

    # Всего принятых звонков было
    # Всего принятых уникальных звонков было

    # Всего пропущенных звонков было
    # Всего пропущенных уникальных звонков было

    # модификация sql-запроса
    unset($sqlCondition);
    # даты
    if (!empty($array['date_from']) && empty($array['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($array['date_from']) && !empty($array['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($array['date_from']) && !empty($array['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    }
    # /даты
    # сортировка
    switch ($array['type_of_sorting']) {
        case 'accepted': $sqlCondition .= ' and is_missed_call is null'; break;
        case 'missed': $sqlCondition .= ' and is_missed_call = 1'; break;
        case 'chaser': $sqlCondition .= ' and caller = "74996775485"'; break;
        # default: break;
    }
    # /сортировка
    # /модификация sql-запроса

    $sql = '
    select 1,

    (select count(1) from '.DB_PREFIX.'mango_calls where 1'.$sqlCondition.') as all_calls_count,
    (select count(distinct(caller)) from '.DB_PREFIX.'mango_calls where 1'.$sqlCondition.') as all_unique_calls_count,

    (select count(1) from '.DB_PREFIX.'mango_calls where is_missed_call is null'.$sqlCondition.') as all_accepted_call_counts,
    (select count(distinct(caller)) from '.DB_PREFIX.'mango_calls where is_missed_call is null'.$sqlCondition.') as all_unique_accepted_calls_count,

    (select count(1) from '.DB_PREFIX.'mango_calls where is_missed_call = 1'.$sqlCondition.') as all_missed_calls_count,
    (select count(distinct(caller)) from '.DB_PREFIX.'mango_calls where is_missed_call = 1'.$sqlCondition.') as all_unique_missed_calls_count
    '; # echo '<pre>'.$sql."</pre><hr />";

    $sth = $dbh->prepare($sql);

    if (!empty($array['date_from']) && empty($array['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($array['date_from']));
        $sth->bindValue(':date_from', $dateFrom);
    }
    elseif (empty($array['date_from']) && !empty($array['date_to'])) {
        $dateTo = date("Y-m-d", strtotime($array['date_to']));
        $sth->bindValue(':date_to', $dateTo);
    }
    elseif (!empty($array['date_from']) && !empty($array['date_to'])) {
        $dateFrom = date("Y-m-d", strtotime($array['date_from']));
        $dateTo = date("Y-m-d", strtotime($array['date_to']));
        $sth->bindValue(':date_from', $dateFrom);
        $sth->bindValue(':date_to', $dateTo);
    }

    $sth->execute();
    $_ = $sth->fetch();

    if (!empty($_) && is_array($_)) {
        $statistics = '<b>Статистика</b>: всего звонков: '.$_['all_calls_count'].', всего уникальных: '.$_['all_unique_calls_count'].', всего принятых: '.$_['all_accepted_call_counts'].', всего принятых уникальных: '.$_['all_unique_accepted_calls_count'].',<br />всего пропущенных: '.$_['all_missed_calls_count'].', всего пропущенных уникальных: '.$_['all_unique_missed_calls_count'];

        return $statistics;
    }
    else return null;

} # /ПОЛУЧАЕМ СТАТИСТИКУ