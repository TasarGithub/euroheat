<?php 
# Модуль админки для работы с заявками (таблица online_requests)
# romanov.egor@gmail.com; 2015.10.19

# подключаем файл конфига
include('../loader.control.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'Онлайн-заявки';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА

if ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем заявку';
    $GLOBALS['tpl_h1'] = 'Удаляем заявку';
    $GLOBALS['tpl_content'] = deleteItem();
}
else {
    $GLOBALS['tpl_title'] = 'Все онлайн-заявки';
    $GLOBALS['tpl_h1'] = 'Все онлайн-заявки ('.$dbh->query('select count(1) from '.DB_PREFIX.'online_requests')->fetchColumn().')';
    $GLOBALS['tpl_content'] = showItems();
}

# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ФОРМИРУЕМ СПИСОК ВСЕХ ЗАЯВОК
function showItems($count = null)
{
    global $dbh;
    
    # получаем список заявок
    $sql = '
    select t1.id,
           t1.date_add,
           date_format(t1.date_add, "%e") as date_add_day,
           elt(month(t1.date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
           date_format(t1.date_add, "%Y") as date_add_year,
           date_format(t1.date_add, "%H:%i") as date_add_time,
           t1.order_content,
           t2.name as type_name
    from '.DB_PREFIX.'online_requests as t1
    left outer join online_requests_types as t2
        on t2.id = t1.request_type_id
    order by t1.id desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sql_for_count = '
    select count(id)
    from '.DB_PREFIX.'online_requests
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # текущая страница
					   25, # записей на страницу
					   $dbh, # объект базы данных
                       '', # routeVars
					   $sql, # sql-запрос
					   $sql_for_count, # sql-запрос для подсчета количества записей
					   '/control/online_requests/', # ссыка на 1ю страницу
					   '/control/online_requests/?page=%page%', # ссыка на остальные страницы
						1500 # максимальное количество записей на страницу
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">Страницы: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # type_name
        if (!empty($_[$i]['type_name'])) $typeName = '<b>'.$_[$i]['type_name'].'</b>';
        else unset($typeName);

        $rows[] = '
		<tr>
			<td>'.$typeName.'<pre class="no_border">'.$_[$i]['order_content'].'</pre></td>
			<td><pre class="no_border">'.$_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].' '.$_[$i]['date_add_time'].'</pre></td>
			<td class="center vertical_middle">
                <a class="block" title="Удалить заявку" href="/control/online_requests/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Заявка будет удалена безвозвратно. Удалить заявку?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    } # /формируем список заявок
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);
    
    $result = '
    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'</a>
    </div>

    <br style="clear:both" />
    <br />
    ';
    
    if (empty($rows)) $result .= 'В системе нет заявок.';
    else {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle">Заявка</th>
                <th class="center vertical_middle" style="width:200px">Дата</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">Удаление</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # /ФОРМИРУЕМ СПИСОК ВСЕХ ЗАЯВОК

# УДАЛЯЕМ ЗАЯВКУ
function deleteItem(){
	
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID']))	{
		# выводим ошибку
		$GLOBALS['tpl_failure'] = 'Заявка не удалена. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# выводим список заявок
        showItems();
	}
	else {
		# удаляем заявку из БД
        $sql = '
        delete from '.DB_PREFIX.'online_requests
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = 'Заявка успешно удалена.';
            
			# выводим список заявок
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = 'К сожалению, заявка не удалена. Пожалуйста, обратитесь к разработчикам сайта.';
			# выводим список заявок
			return showItems();
		}
	}
} # /УДАЛЯЕМ ЗАЯВКУ

# /ФУНКЦИОНАЛ