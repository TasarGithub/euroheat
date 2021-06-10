<?php 
# Модуль админки для работы с телефонными звонками
# * импорт звонков из mango-office.ru
# * таблица "mango_calls"
# romanov.egor@gmail.com; 2015.12.25

# подключаем файл конфига
include('../loader.control.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'Телефонные звонки';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА

if ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем звонок';
    $GLOBALS['tpl_h1'] = 'Удаляем звонок';
    $GLOBALS['tpl_content'] = deleteItem();
}
else {
    $GLOBALS['tpl_title'] = 'Все звонки';

    # $GLOBALS['tpl_h1'] = 'Все звонки ('.$dbh->query('select count(1) from '.DB_PREFIX.'mango_calls')->fetchColumn().')';
    $GLOBALS['tpl_h1'] = 'Все звонки';
    $GLOBALS['tpl_content'] = showItems();
}

# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ФОРМИРУЕМ СПИСОК ВСЕХ ЗВОНКОВ
function showItems($count = null)
{
    global $dbh;

    # получаем список звонков
    $sql = '
    select id,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "янв.", "фев.", "мар.", "апр.", "мая", "июня", "июля", "авг.", "сен.", "окт.", "ноя.", "дек.") as date_add_month,
           date_format(date_add, "%Y") as date_add_year,
           date_format(date_add, "%H:%i") as date_add_time,
           date_format(date_add, "%Y-%m-%d") as date_add_modified,
           date_format(date_add, "%Y.%m.%d_%H.%i") as date_add_for_file_name_for_download,
           caller,
           talk_duration,
           is_missed_call,
           reason_code,
           reason,
           call_record
    from '.DB_PREFIX.'mango_calls
    order by date_add desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    # date_format(date_add, "%c") as date_add_month,
    $sql_for_count = '
    select count(id)
    from '.DB_PREFIX.'mango_calls
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # текущая страница
					   25, # записей на страницу
					   $dbh, # объект базы данных
                       '', # routeVars
					   $sql, # sql-запрос
					   $sql_for_count, # sql-запрос для подсчета количества записей
					   '/control/phone_calls/'.str_replace('&', '?', $pageVariable), # ссыка на 1ю страницу
					   '/control/phone_calls/?page=%page%'.$pageVariable, # ссыка на остальные страницы
						1500 # максимальное количество записей на страницу
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set" style="margin-bottom:15px;text-align:left">Страницы: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # talk_duration: переводим секунды в менуты и секунды
        if ($_[$i]['talk_duration'] > 60) {
            $duration = floor($_[$i]['talk_duration'] / 60);
            $remainder = $_[$i]['talk_duration'] % 60;
            $talkDurationModified = $duration.' м. '.$remainder.' с.';
        }
        else $talkDurationModified = $_[$i]['talk_duration'] . ' с.';

        # talk_duration
        if (empty($_[$i]['is_missed_call'])) $talk_duration = $talkDurationModified;
        else {
            if (empty($_[$i]['reason'])) $_[$i]['reason'] = 'Причина не указана';

            $talk_duration = '<span class="red">'.$_[$i]['reason'].'</span> (' . $talkDurationModified . ')';
        }

        # date_add_day
        if ($_[$i]['date_add_modified'] == date('Y-m-d')) $dateAdd = '<span class="bold">сегодня в '.$_[$i]['date_add_time'].'</span>';
        else $dateAdd = $_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].', '.$_[$i]['date_add_time'];

        # проигрыватель записи разговора
        if (!empty($_[$i]['call_record'])) $listen = '
        <a class="audio {skin:\'blue\', showTime:true, downloadable:true }" href="/control/phone_calls/get_call_record.php?call_record='.$_[$i]['call_record'].'"></a>';
        else unset($listen);

        # имя файла для скачивания
        $callRecord = HTTP_HOST.'_'.$_[$i]['date_add_for_file_name_for_download'].'_s_nomera_'.$_[$i]['caller'].'.mp3';

        # caller
        unset($caller);
        # if ($_[$i]['caller'] == '74996775485') $caller = '74996775485<sup>чайзер</sup>';
        if ($_[$i]['caller'] == '74996775485') $caller = '<span class="chaser">74996775485</span><sup><img src="/control/public/images/chaser_logo_mini.png" /></sup>';
        else $caller = $_[$i]['caller'];

        $rows[] = '
		<tr>
		    <td class="vertical_middle nowrap for_player" data-call-record="'.$callRecord.'">'.$listen.'&nbsp;</td>
			<td class="center vertical_middle nowrap">'.$dateAdd.'</td>
            <td class="center vertical_middle nowrap">'.$caller.'</td>
            <td class="center vertical_middle nowrap">'.$talk_duration.'</td>
		</tr>
		';
    } # /формируем список звонков
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);

    $result = '
    <script src="/control/phone_calls/index.js?v=4.0"></script>

    <b>URL:</b>&nbsp; <a href="/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'</a>

    <!-- Сортировка -->
    <div class="sorting" style="display:inline;margin-left:35px"><b>Сортировка:</b> &nbsp;
    <a href="#" data-sorting-type="accepted">принятые</a> <span style="color:#ccc">|</span>
    <a href="#" data-sorting-type="missed">пропущенные</a> <span style="color:#ccc">|</span>
    <a href="#" data-sorting-type="chaser">чейзер</a> <span style="color:#ccc">|</span>
    <a href="#" data-sorting-type="all">все</a>
    </div>
    <!-- /Сортировка -->

    <!-- Поиск -->
    <div class="searching" style="margin-top:15px">
    <b>Поиск по дате с:</b> &nbsp;
    <input id="search_by_date_from" class="form-control form_required" type="text" value="'.date('d.m.Y', strtotime('-7 days', strtotime(date('d.m.Y')))).'" style="display:inline-block;width:110px;height:24px" placeholder="'.date('d.m.Y', strtotime('-7 days', strtotime(date('d.m.Y')))).'" />
    &nbsp;
    <b>по</b> &nbsp; <input id="search_by_date_to" class="form-control form_required" type="text" value="'.date('d.m.Y').'" style="display:inline-block;width:110px;height:24px" placeholder="'.date('d.m.Y').'" />
     &nbsp;
    <button id="search_by_date_button" class="btn btn-success" type="button" style="height:24px;padding-top:0;padding-bottom:0;margin-top:-3px;padding-left: 3px;padding-right:6px">
        <i class="fa" style="margin-right:3px"></i>Искать</button>
     &nbsp;
    <button id="export_to_excel" class="btn btn-success" type="button" style="height:24px;padding-top:0;padding-bottom:0;margin-top:-3px;padding-left: 3px;padding-right:6pxk">
        <i class="fa" style="margin-right:3px"></i>Выгрузить в Excel</button>
    </div>
    <!-- /Поиск -->

    <!-- Статистика -->
    <div id="statistics" style="margin-top:15px">
    '.getCallsStatistics().'
    </div>
    <!-- /Статистика -->

    <br />
    ';
    
    if (empty($rows)) $result .= 'В системе нет звонков.';
    else {
        $result .= '

        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list" style="width:1px;overflow:hidden">
            <tr>
                <th class="center vertical_middle nowrap" style="min-width:100px">Прослушать</th>
                <th class="center vertical_middle nowrap" style="min-width:100px">Дата и время</th>
                <th class="center vertical_middle nowrap" style="min-width:100px">Номер звонившего</th>
                <th class="center vertical_middle">Продолжительность</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
        # style="min-width:315px"
    }
    
    return $result;
} # /ФОРМИРУЕМ СПИСОК ВСЕХ ЗВОНКОВ

# УДАЛЯЕМ ЗВОНОК
function deleteItem(){
	
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID']))	{
		# выводим ошибку
		$GLOBALS['tpl_failure'] = 'Звонок не удален. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# выводим список звонков
        showItems();
	}
	else {
		# удаляем звонок из БД
        $sql = '
        delete from '.DB_PREFIX.'mango_calls
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = 'Звонок успешно удален.';
            
			# выводим список звонков
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = 'К сожалению, звонок не удален. Пожалуйста, обратитесь к разработчикам сайта.';
			# выводим список звонков
			return showItems();
		}
	}
} # /УДАЛЯЕМ ЗВОНОК

# /ФУНКЦИОНАЛ