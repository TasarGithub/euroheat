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
# провряем, существует ли шоу по названию
if ($_POST['action'] == 'check_item_for_existence_by_name')
{
    $sql = '
    select id
    from `'.DB_PREFIX.'shows`
    where h1 = :name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':name', $_POST['name']);
    $sth->execute();
    if ($_ = $sth->fetchColumn())
    {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
elseif ($_POST['action'] == 'search')
{
    $sql = '
    select id,
           name,
           url,
           is_showable
    from `'.DB_PREFIX.'shows`
    where name like :q
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
	$q = '%'.$_POST['q'].'%';
	$sth->bindParam(':q', $q);
    $sth->execute();
    if ($_ = $sth->fetchAll())
    {
        $_c = count($_);
        $rows = array();
        for ($i=0;$i<$_c;$i++)
        {
            # ссылка
            $link = '<a href="/shou/'.$_[$i]['url'].'/" target="_blank">смотреть</a>';
            
            # is_showable
            if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
            else unset($trClass);
            
            $rows[] = '
            <tr'.$trClass.'>
                <td class="center vertical_middle">
                    <a class="block" href="/control/shows/?action=editItem&itemID='.$_[$i]['id'].'">
                        <i class="fa fa-edit size_18"></i>
                    </a>
                </td>
                <td class="center vertical_middle">'.$link.'</td>
                <td>'.$_[$i]['name'].'</td>
                <td class="center vertical_middle">
                    <a class="block" title="Удалить шоу" href="/control/shows/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Шоу будет удалено безвозвратно. Удалить шоу?\')">
                        <i class="fa fa-trash-o size_18"></i>
                    </a>
                </td>
            </tr>
            ';
        }
        if (empty($rows)) $result .= 'В системе не задано ни одно шоу.';
        else
        {
            if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
            else unset($rows);
            
            $result .= '
            <div id="resultSet">
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
                <tr>
                    <th class="center vertical_middle" style="width:50px;white-space:nowrap">Правка</th>
                    <th class="center vertical_middle" style="width:50px;white-space:nowrap">Ссылка</th>
                    <th class="center vertical_middle">Название</th>
                    <th class="center vertical_middle" style="width:100px;white-space:nowrap">Удаление</th>
                </tr>
                '.$rows.'
            </table>
            </div>
            ';
        }
        echo $result;
    }
    else echo 'По запросу &quot;'.$_POST['q'].'&quot; ничего не найдено.';
}

# /ЛОГИКА

# ФУНКЦИИ

# /ФУНКЦИИ