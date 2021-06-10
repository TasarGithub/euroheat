<?php 
# Модуль админки для работы со стилями (таблица css)
# romanov.egor@gmail.com; 2015.4.19

# подключаем файл конфига
include('../loader.control.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'css-стили';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > создаем файл css-стилей';
    $GLOBALS['tpl_h1'] = 'Создаем файл css-стилей'; 
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > создаем файл css-стилей';
    $GLOBALS['tpl_h1'] = 'Создаем файл css-стилей'; 
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > редактируем файл css-стилей';
    $GLOBALS['tpl_h1'] = 'Редактируем файл css-стилей'; 
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем файл css-стилей';
    $GLOBALS['tpl_h1'] = 'Удаляем файл css-стилей'; 
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > все файлы css-стилей';
    $GLOBALS['tpl_h1'] = 'Все файлы css-стилей'; 
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
	
	$GLOBALS['tpl_title'] = 'css-стили > все файлы';
    
	# формируем список шаблонов
    $sql = '
	select id,
		   name,
		   file_name,
		   (select count(1) from '.DB_PREFIX.'css_backups where template_id = i.id) as backups_count
	from '.DB_PREFIX.'css as i
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
                <a class="block" href="/control/edit_css/?action=editItem&itemID='.$_[$i]['id'].'">
                    <i class="fa fa-edit size_18"></i>
                </a>
            </td>
			<td class="center">'.$_[$i]['name'].'</td>
			<td class="center">'.$_[$i]['file_name'].'</td>
			<td class="center">'.$_[$i]['backups_count'].'</td>
			<td class="center">
                <a class="block" title="Удалить файл css-стилей" href="/control/edit_css/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Файл файл css-стилей будет удален безвозвратно. Удалить файл?\')">
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
	from '.DB_PREFIX.'css
	';
	$allItemsCount = $dbh->query($sql)->fetch();
	$allItemsCount = $allItemsCount[0]['items_count'];
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);
    
    $result = '
	<script type="text/javascript" src="/control/edit_css/index.js"></script>
	
    <div class="center" style="margin-bottom:15px">
        <a href="/control/edit_css/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    Создать файл css-стилей
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= 'В системе не задан ни один файл css-стилей.';
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
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = 'Файл css-стилей успешно создан.';

    if ($showEditForm)
	{
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # защита
        if (!$itemInfo['id']) exit('
		Не сущесвтует записи с ID='.$_GET['itemID'].'
		<br /><a href="/control/edit_css/">Перейти к списку файлов css-стилей</a>
		');

        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);
		
		# получаем html-код шаблона
		$fullPathToFile = PATH_TO_PUBLIC_CSS.basename($itemInfo['file_name']); # echo $fullPathToFile.'<hr />';
        
        # МОДИФИКАЦИЯ ДЛЯ СТАРОЙ ВЕРСИИ САЙТА
        # css-стили общего назначения для версии сайта 1.0 (старая версия)
        if ($itemInfo['id'] == 1)
        {
            $fullPathToFile = PATH_TO_PUBLIC_CSS_OLD.'2.0/'.basename($itemInfo['file_name']); # echo $fullPathToFile.'<hr />';
            $url = "<br><br>
            <b>URL:</b>&nbsp; <a href='".PATH_TO_PUBLIC_CSS_OLD_SHORT.'2.0/'.$itemInfo['file_name']."' target='_blank'>".PATH_TO_PUBLIC_CSS_OLD_SHORT.'2.0/'.$itemInfo['file_name']."</a>
            ";
        }
		elseif ($itemInfo['id'] == 2)
		{
			$fullPathToFile = PATH_TO_PUBLIC_CSS_OLD.basename($itemInfo['file_name']); # echo $fullPathToFile.'<hr />';
			$url = "<br><br>
            <b>URL:</b>&nbsp; <a href='".PATH_TO_PUBLIC_CSS_OLD_SHORT.$itemInfo['file_name']."' target='_blank'>".PATH_TO_PUBLIC_CSS_OLD_SHORT.$itemInfo['file_name']."</a>
            ";
		}
        # /МОДИФИКАЦИЯ ДЛЯ СТАРОЙ ВЕРСИИ САЙТА
        
        # НОВАЯ ВЕРСИЯ САЙТА
        else
        {
            $url = "<br><br>
            <b>URL:</b>&nbsp; <a href='".PATH_TO_PUBLIC_CSS_SHORT.$itemInfo['file_name']."' target='_blank'>".'http://www.'.$_SERVER['SERVER_NAME'].PATH_TO_PUBLIC_CSS_SHORT.$itemInfo['file_name']."</a>
            ";
        } # /НОВАЯ ВЕРСИЯ САЙТА
        
		if (file_exists($fullPathToFile))
		{
			$content = file_get_contents($fullPathToFile); # echo $content.'<hr />';
			# prepare for showing
			$content = htmlspecialchars($content, ENT_QUOTES);
			$content = str_replace("\t", "", $content);
		}
        
        return "
		<script type='text/javascript' src='/control/edit_css/index.js'></script>
		<form id='templates_edit_form' action='/control/edit_css/?action=editItem&itemID=".$itemInfo['id']."&subaction=editSubmit' name='form1' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
			
            <button id='templates_edit_save_changes' class='btn btn-primary submit_button' type='button'>Сохранить информацию</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/edit_css/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            Перейти к списку
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/edit_css/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            Создать css-файл
            </button></a>

			".$url."

            <br><br>
			<div class='form-group' style='width:60%'>
                <label>Название файла css-стилей: <span style='color:red'>*</span></label>
                <input type='text' name='templates_form_name' id='templates_form_name' class='form-control form_required' data-required-label='Пжл, укажите название файла css-стилей' value='".$itemInfo['name']."' />
            </div>
			
            <div class='form-group'>
                <label>css-код: <span style='color:red'>*</span></label>
                <textarea name='templates_form_html_code' id='templates_form_html_code' class='form-control form_required' style='width:95%;height:375px' data-required-label='Пжл, укажите css-код'>".$content."</textarea>
            </div>
            
			<button id='templates_edit_save_changes' class='btn btn-primary submit_button' type='button' style='margin-top:5px'>Сохранить информацию</button>
            
            &nbsp; <div class='ajax_result bottom'></div>
            
            <br /><br />
            
            <div class='form-group' style='border:1px dashed #ccc;padding:7px 10px'>
				<label>backup'ы (резервные копии)</label>
				<div><a href='#' id='makeBackup'>Сделать backup css-кода шаблона</a></div>
                <br />
				<div id='backupsResult' style='color:#aaaaaa'>backup'ов нет.</div>
			</div>
            
            <div class='form-group'>
                <b>Полный путь к файлу css-стилей:</b> &nbsp; <span style='color:#aaaaaa;font-size:14px'>".PATH_TO_PUBLIC_CSS.$itemInfo['file_name']."</span>
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
	<script type='text/javascript' src='/control/edit_css/index.js'></script>
	<form id='templates_add_form' action='/control/edit_css/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>Создать файл css-стилей</button>
        
		&nbsp;&nbsp;&nbsp;<b>URL:</b>&nbsp; <a href='/' target='_blank'>http://www.".$_SERVER['SERVER_NAME']."</a> 
		<span id='ajax_status' style='position:absolute;left:355px;top:5px'></span>

        <br><br>
        <div class='form-group' style='width:60%'>
            <label>Название файла css-стилей (по-русски, например: css-стили для главной): <span style='color:red'>*</span></label>
            <input type='text' name='templates_form_name' id='templates_form_name' class='form-control form_required' data-required-label='Пжл, укажите название файла css-стилей' value='".$_POST['templates_form_name']."' />
        </div>
        
        <!-- ajax-уведомления для поля 'название шаблона' -->
        <div id='templates_form_name_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group' style='width:60%'>
            <label>Имя файла (по-английски, например: styles_for_main_page.css): <span style='color:red'>*</span></label>
            <input type='text' name='templates_form_file_name' id='templates_form_file_name' data-required-label='Пжл, укажите имя файла css-стилей' class='form-control form_required' value='".$_POST['templates_form_file_name']."' />
        </div>
        
        <!-- ajax-уведомления для поля 'имя файла' -->
        <div id='templates_form_file_name_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group'>
            <label>css-код:</label> &nbsp; <div class='ajax_result'></div>
            <textarea name='templates_form_html_code' id='templates_form_html_code' class='form-control' style='width:95%;height:375px'>".$_POST['templates_form_html_code']."</textarea>
        </div>
        
        <button class='btn btn-primary submit_button' type='submit'>Создать файл css-стилей</button>
        
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
	# защита от прямого запроса URL'а: http://www.kupi-krovat.ru/control/edit_css/?action=addItemSubmit
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
			saveContentToFile(PATH_TO_PUBLIC_CSS,
							  $_POST['templates_form_file_name'], # new file name
							  NULL, # old file name
							  $_POST['templates_form_html_code']);

			# выводим форму редактирования
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/edit_css/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# если возникла ошибка и шаблон не добавлен
		else
		{
            $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и файл css-стилей не добавлен. Пожалуйста, обратитесь к разработчику.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# если набран: http://news.youroute.ru/control/news/addItemSubmit/ и при этом $_POST пустой
	else
	{
		# выводим список шаблонов
        $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и файл css-стилей не добавлен. Пожалуйста, обратитесь к разработчику.';
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
		$GLOBALS['tpl_failure'] = 'Файл css-стилей не удален. Пожалуйста, обратитесь к разработчику.';
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
		$fullPathToTemplate = PATH_TO_PUBLIC_CSS.$fileName; # echo $fullPathToTemplate.'<hr />';
		if (!empty($fullPathToTemplate) && file_exists($fullPathToTemplate)) unlink($fullPathToTemplate);
		
		# удаляем шаблон из БД
		$result = deleteItemFromDB(); # echo $result.'<hr />';
		if (!empty($result))
		{
			$GLOBALS['tpl_success'] = 'Файл css-стилей успешно удален.';
			# выводим список шаблонов
			return showItems();
		}
		else
		{
            $GLOBALS['tpl_failure'] = 'К сожалению, файл css-стилей не удален. Пожалуйста, обратитесь к разработчику.';
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
	delete from '.DB_PREFIX.'css_backups
	where template_id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
	
	# удаляем шаблон
	$sql = '
	delete from '.DB_PREFIX.'css
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
        insert into '.DB_PREFIX.'css
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
	from '.DB_PREFIX.'css
	where id = "'.$_GET['itemID'].'"
	'; # echo '<pre>'.$sql."</pre><hr />";
	$itemInfo = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
	$itemInfo = $itemInfo[0];
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ

# /ФУНКЦИОНАЛ