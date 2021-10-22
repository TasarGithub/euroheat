<?php 
# Модуль админки для работы с js-скриптами (таблица templates)
# romanov.egor@gmail.com; 2015.4.19

# подключаем файл конфига
include('../loader.control.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'js-код';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > создаем файл js-кода';
    $GLOBALS['tpl_h1'] = 'Создаем файл js-кода'; 
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > создаем файл js-кода';
    $GLOBALS['tpl_h1'] = 'Создаем файл js-кода'; 
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > редактируем файл js-кода';
    $GLOBALS['tpl_h1'] = 'Редактируем файл js-кода'; 
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем файл js-кода';
    $GLOBALS['tpl_h1'] = 'Удаляем файл js-кода'; 
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > все файлы js-кода';
    $GLOBALS['tpl_h1'] = 'Все файлы js-кода'; 
    $GLOBALS['tpl_content'] = showItems(); 
}
# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ФОРМИРУЕМ СПИСОК ШАБЛОНОВ
function showItems($count = null)
{
    global $dbh;
	
	$GLOBALS['tpl_title'] = 'js-код > все файлы';
    
	# формируем список шаблонов
    $sql = '
	select id,
		   name,
		   file_name,
		   (select count(1) from '.DB_PREFIX.'js_backups where template_id = i.id) as backups_count
	from '.DB_PREFIX.'js as i
	order by name
	'; # echo '<pre>'.$sql."</pre><hr />";
    $_ = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++)
	{
        $rows[] = '
		<tr>
            <td class="center">
                <a class="block" href="/control/edit_js/?action=editItem&itemID='.$_[$i]['id'].'">
                    <i class="fa fa-edit size_18"></i>
                </a>
            </td>
			<td class="center">'.$_[$i]['name'].'</td>
			<td class="center">'.$_[$i]['file_name'].'</td>
			<td class="center">'.$_[$i]['backups_count'].'</td>
			<td class="center">
                <a class="block" title="Удалить файл js-кода" href="/control/edit_js/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Файл js-кода будет удален безвозвратно. Удалить файл?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    }
	# /формируем список шаблонов
	
	# считаем общее количество шаблонов
	$sql = '
	select count(1) as items_count
	from '.DB_PREFIX.'js
	';
	$allItemsCount = $dbh->query($sql)->fetch();
	$allItemsCount = $allItemsCount[0]['items_count'];
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);
    
    $result = '
	<script type="text/javascript" src="/control/edit_js/index.js"></script>
	
    <div class="center" style="margin-bottom:15px">
        <a href="/control/edit_js/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    Создать файл js-кода
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= 'В системе не задан ни один файл js-кода.';
    else
    {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center" style="width:50px;white-space:nowrap">Правка</th>
                <th class="center" style="width:35%">Название</th>
                <th class="center">Имя файла</th>
                <th class="center" style="width:100px;white-space:nowrap">backup\'ов</th>
                <th class="center" style="width:100px;white-space:nowrap">Удаление</th>
            </tr>
            '.$rows.'
        </table>
        </div>
        ';
    }
    
    return $result;
} # /ФОРМИРУЕМ СПИСОК ШАБЛОНОВ

# ФОРМА РЕДАКТИРОВАНИЯ ШАБЛОНА
function showEditForm(){
    global $dbh;
    
    $showEditForm = 1;

    # выводим сообщение
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = 'Файл js-кода успешно добавлен.';

    if ($showEditForm)
	{
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # защита
        if (!$itemInfo['id']) exit('
		Не сущесвтует записи с ID='.$_GET['itemID'].'
		<br /><a href="/control/edit_js/">Перейти к списку файлов с js-кодом</a>
		');

        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);
		
		# получаем html-код шаблона
		$fullPathToFile = PATH_TO_PUBLIC_JS.basename($itemInfo['file_name']); # echo $fullPathToFile.'<hr />';
		if (file_exists($fullPathToFile))
		{
			$content = file_get_contents($fullPathToFile); # echo $content.'<hr />';
			# prepare for showing
			$content = htmlspecialchars($content, ENT_QUOTES);
			$content = str_replace("\t", "", $content);
		}
        
        return "
		<script type='text/javascript' src='/control/edit_js/index.js'></script>
		<form id='templates_edit_form' action='/control/edit_js/?action=editItem&itemID=".$itemInfo['id']."&subaction=editSubmit' name='form1' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
			
            <button id='templates_edit_save_changes' class='btn btn-primary submit_button' type='button'>Сохранить информацию</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/edit_js/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            Перейти к списку
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/edit_js/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            Создать js-файл
            </button></a>

			<br><br><b>URL:</b>&nbsp; <a href='".PATH_TO_PUBLIC_JS_SHORT.$itemInfo['file_name']."' target='_blank'>".'http://www.'.$_SERVER['SERVER_NAME'].PATH_TO_PUBLIC_JS_SHORT.$itemInfo['file_name']."</a> 

            <br><br>
			<div class='form-group' style='width:50%'>
                <label>Название файла js-кода: <span style='color:red'>*</span></label>
                <input type='text' name='templates_form_name' id='templates_form_name' class='form-control form_required' data-required-label='Пжл, укажите название файла js-кода' value='".$itemInfo['name']."' />
            </div>
            
			<div class='form-group'>
                <label>js-код: <span style='color:red'>*</span></label>
                <textarea name='templates_form_html_code' id='templates_form_html_code' class='form-control lined form_required' style='width:95%;height:375px' data-required-label='Пжл, укажите js-код'>".$content."</textarea>
            </div>
            
			<button id='templates_edit_save_changes' class='btn btn-primary submit_button' type='button' style='margin-top:5px'>Сохранить информацию</button>
            
            &nbsp; <div class='ajax_result bottom'></div>
            
            <br /><br />
            
            <div class='form-group' style='border:1px dashed #ccc;padding:7px 10px'>
				<label>backup'ы (резервные копии)</label>
				<div><a href='#' id='makeBackup'>Сделать backup js-кода</a></div>
                <br />
				<div id='backupsResult' style='color:#aaaaaa'>backup'ов нет.</div>
			</div>
            
            <div class='form-group'>
                <b>Полный путь к файлу js-кода:</b> &nbsp; <span style='color:#aaaaaa;font-size:14px'>".PATH_TO_PUBLIC_JS.$itemInfo['file_name']."</span>
            </div>
            
			<input type='hidden' id='template_id' value='".$_GET['itemID']."' />
		</form>
		";
    }
} # /ФОРМА РЕДАКТИРОВАНИЯ ШАБЛОНА

# ФОРМА ДОБАВЛЕНИЯ ШАБЛОНА
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/edit_js/index.js'></script>
	<form id='templates_add_form' action='/control/edit_js/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>Добавить файл js-кода</button>
        
		&nbsp;&nbsp;&nbsp;<b>URL:</b>&nbsp; <a href='/' target='_blank'>http://www.".$_SERVER['SERVER_NAME']."</a> 
		<span id='ajax_status' style='position:absolute;left:355px;top:5px'></span>

        <br><br>
        <div class='form-group' style='width:60%'>
            <label>Название файла js-кода (по-русски, например: js-скрипты для главной): <span style='color:red'>*</span></label>
            <input type='text' name='templates_form_name' id='templates_form_name' class='form-control form_required' data-required-label='Пжл, укажите название файла js-кода' value='".$_POST['templates_form_name']."' />
        </div>
        
        <!-- ajax-уведомления для поля 'название' -->
        <div id='templates_form_name_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group' style='width:60%'>
            <label>Имя файла (по-английски, например: jquery.main.page.js): <span style='color:red'>*</span></label>
            <input type='text' name='templates_form_file_name' id='templates_form_file_name' data-required-label='Пжл, укажите имя файла js-кода' class='form-control form_required' value='".$_POST['templates_form_file_name']."' />
        </div>
        
        <!-- ajax-уведомления для поля 'имя файла' -->
        <div id='templates_form_file_name_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group'>
            <label>js-код:</label> &nbsp; <div class='ajax_result'></div>
            <textarea name='templates_form_html_code' id='templates_form_html_code' class='form-control lined' style='width:95%;height:375px'>".$_POST['templates_form_html_code']."</textarea>
        </div>
        
        <button class='btn btn-primary submit_button' type='submit'>Добавить файл js-кода</button>
        
        <!-- скрытые поля -->
        <input type='hidden' id='form_submit_allowed' value='0' />
	</form>
	";
} # /ФОРМА ДОБАВЛЕНИЯ ШАБЛОНА

# СОЗДАЕМ НОВЫЙ ШАБЛОН
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# защита от прямого запроса URL'а: http://www.kupi-krovat.ru/control/edit_js/?action=addItemSubmit
	if (!empty($_POST))
	{
		# ПОДГОТАВЛИВАЕМ ДАННЫЕ ДЛЯ POST ЗАПРОСА
		preparePostValues();

		# добавляем шаблон в БД
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# если шаблон успешно добавлен
		if (!empty($lastInsertID))
		{
			# сохраняем шаблон в файл
			saveContentToFile(PATH_TO_PUBLIC_JS,
							  $_POST['templates_form_file_name'], # new file name
							  NULL, # old file name
							  $_POST['templates_form_html_code']);

			# выводим форму редактирования
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/edit_js/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# если возникла ошибка и шаблон не добавлен
		else
		{
            $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и файл js-кода не добавлен. Пожалуйста, обратитесь к разработчику.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# если набран: http://news.youroute.ru/control/news/addItemSubmit/ и при этом $_POST пустой
	else
	{
		# выводим список шаблонов
        $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и файл js-кода не добавлен. Пожалуйста, обратитесь к разработчику.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /СОЗДАЕМ НОВЫЙ ШАБЛОН

# УДАЛЯЕМ ШАБЛОН
function deleteItem(){
	
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID']))
	{
		# выводим ошибку
		$GLOBALS['tpl_failure'] = 'Файл js-кода не удален. Пожалуйста, обратитесь к разработчику.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# выводим список шаблонов
        showItems();
	}
	else
	{
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# получаем имя файла шаблона
		$fileName = $itemInfo['file_name']; # echo $fileName.'<hr />';

		# удаляем файл шаблона
		$fullPathToTemplate = PATH_TO_PUBLIC_JS.$fileName; # echo $fullPathToTemplate.'<hr />';
		if (!empty($fullPathToTemplate) && file_exists($fullPathToTemplate)) unlink($fullPathToTemplate);
		
		# удаляем шаблон из БД
		$result = deleteItemFromDB(); # echo $result.'<hr />';
		if (!empty($result))
		{
			$GLOBALS['tpl_success'] = 'Файл js-кода успешно удален.';
			# выводим список шаблонов
			return showItems();
		}
		else
		{
            $GLOBALS['tpl_failure'] = 'К сожалению, файл js-кода не удален. Пожалуйста, обратитесь к разработчику.';
			# выводим список шаблонов
			return showItems();
		}
	}
} # /УДАЛЯЕМ ШАБЛОН

# УДАЛЕНИЕ ПОЗИЦИИ
function deleteItemFromDB()
{
	# проверка переменных
	if (empty($_GET['itemID'])) return;
	
	global $dbh;

	# удаляем записи из таблицы backup'ов
	$sql = '
	delete from '.DB_PREFIX.'js_backups
	where template_id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
	
	# удаляем шаблон
	$sql = '
	delete from '.DB_PREFIX.'js
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
	if ($sth->execute()) return 1;
} # /УДАЛЕНИЕ ПОЗИЦИИ

# ДОБАВЛЯЕМ ШАБЛОН В БД
function addItemToDB()
{
	global $dbh;
	
	if (!empty($_POST['templates_form_name'])
		and !empty($_POST['templates_form_file_name']))
	{
		$sql = '
        insert into '.DB_PREFIX.'js
        (name, file_name)
        values
        (:name, :file_name)
        '; # echo $sql.'<hr />';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':name', $_POST['templates_form_name']);
        $sth->bindParam(':file_name', $_POST['templates_form_file_name']);
		try { if ($sth->execute()) {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			if (!empty($last_insert_id)) return $last_insert_id;
			else return;
        }}
        catch (PDOException $e) 
        { 
            if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; } 
        }
	}
} # /ДОБАВЛЯЕМ ШАБЛОН В БД

# ПОДГОТАВЛИВАЕМ ДАННЫЕ ДЛЯ POST ЗАПРОСА
function preparePostValues()
{
	# ПОДГОТАВЛИВАЕМ ДАННЫЕ ДЛЯ POST ЗАПРОСА
	# trim and stripslashes all elements
	foreach ($_POST as $key => &$val)
	{
		if (!empty($val))
		{
			if (!is_array($key) and !is_array($val))
			{
				$_POST[$key] = trim($val);
				# http://stackoverflow.com/questions/2128871/slashes-in-mysql-tables-but-using-pdo-and-parameterized-queries-whats-up
				# if (get_magic_quotes_gpc()) $_POST[$key] = stripslashes($val);
			}
		}
		else
		{
			# для PDO - чтобы он вставлял NULL для пустых значений
			if (empty($val)) $_POST[$key] = NULL;
		}
	} # print_r($_POST);
} # /ПОДГОТАВЛИВАЕМ ДАННЫЕ ДЛЯ POST ЗАПРОСА

# ЗАПИСЫВАЕМ ШАБЛОН В ФАЙЛ
function saveContentToFile($pathToTeplates,
						   $newFileName,
						   $oldFileName = NULL,
						   $htmlCode)
{
    /*
    echo 'pathToTeplates: '.$pathToTeplates.'<br />';
    echo 'newFileName: '.$newFileName.'<br />';
    echo 'oldFileName: '.$oldFileName.'<br />';
    echo 'htmlCode: '.$htmlCode.'<br />';
    */

	# проверка переменных
	if (empty($pathToTeplates)) return;
	if (empty($newFileName)) return;
	if (empty($htmlCode)) return;
	
	# если указано старое имя файла и оно не равно новому имени,
	# удаляем старый файл
	if (!empty($oldFileName) and $newFileName != $oldFileName)
	{
		$fullPathToOldFile = $pathToTeplates.basename($oldFileName);
		if (file_exists($fullPathToOldFile))
		{
			if (is_writable($fullPathToOldFile))
			{
				unlink($fullPathToOldFile);
			}
		}
	}
	
	$fullPathToNewFile = $pathToTeplates.basename($newFileName); # echo 'fullPathToNewFile: '.$fullPathToNewFile.'<br />';
    
    # если файл уже существует и поле "html-код" оставлено пустым, ничего не делаем
    # иначе создаем файл и пишем в него код из поля "html-код"
    if (file_exists($fullPathToNewFile) && empty($_POST['templates_form_html_code'])) {}
    else {
        file_put_contents($fullPathToNewFile, $htmlCode, LOCK_EX);
        if (is_file($fullPathToNewFile)) chmod($fullPathToNewFile, 0755);
    }
} # /ЗАПИСЫВАЕМ ШАБЛОН В ФАЙЛ

# ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ
function getItemInfo()
{
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID'])) return;
	
	$sql = '
	select *
	from '.DB_PREFIX.'js
	where id = "'.$_GET['itemID'].'"
	'; # echo '<pre>'.$sql."</pre><hr />";
	$itemInfo = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
	$itemInfo = $itemInfo[0];
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ

# /ФУНКЦИОНАЛ