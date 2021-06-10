<?php # ����� ������� ��� index.php � ajax.php

define('HTTP_HOST', str_replace('www.', '', $_SERVER['HTTP_HOST']));

# �������� ����������
function getCallsStatistics($array = null)
{
    global $dbh;

    # ����� ������� ����
    # ����� ���������� ������� ����

    # ����� �������� ������� ����
    # ����� �������� ���������� ������� ����

    # ����� ����������� ������� ����
    # ����� ����������� ���������� ������� ����

    # ����������� sql-�������
    unset($sqlCondition);
    # ����
    if (!empty($array['date_from']) && empty($array['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from';
    }
    elseif (empty($array['date_from']) && !empty($array['date_to'])) {
        $sqlCondition = ' and date(date_add) <= :date_to';
    }
    elseif (!empty($array['date_from']) && !empty($array['date_to'])) {
        $sqlCondition = ' and date(date_add) >= :date_from and date(date_add) <= :date_to';
    }
    # /����
    # ����������
    switch ($array['type_of_sorting']) {
        case 'accepted': $sqlCondition .= ' and is_missed_call is null'; break;
        case 'missed': $sqlCondition .= ' and is_missed_call = 1'; break;
        case 'chaser': $sqlCondition .= ' and caller = "74996775485"'; break;
        # default: break;
    }
    # /����������
    # /����������� sql-�������

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
        $statistics = '<b>����������</b>: ����� �������: '.$_['all_calls_count'].', ����� ����������: '.$_['all_unique_calls_count'].', ����� ��������: '.$_['all_accepted_call_counts'].', ����� �������� ����������: '.$_['all_unique_accepted_calls_count'].',<br />����� �����������: '.$_['all_missed_calls_count'].', ����� ����������� ����������: '.$_['all_unique_missed_calls_count'];

        return $statistics;
    }
    else return null;

} # /�������� ����������