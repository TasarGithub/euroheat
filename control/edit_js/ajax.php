<?php

### ОТЛАДКА
# print_r($_GET);
# print_r($_POST);

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

# проверка, защита + нужная кодировка GET-переменных
preparePOSTVariables(); # print_r($_POST); exit;

# защита
if (!empty($_POST['id'])) $_POST['id'] = (int) $_POST['id'];
if (!empty($_POST['template_id'])) $_POST['template_id'] = (int) $_POST['template_id'];
# if (!empty($_POST['file_name'])) $_POST['file_name'] = htmlentities($_POST['filename']);
# if (!empty($_POST['name'])) $_POST['name'] = htmlentities($_POST['name']);

# ЛОГИКА
# поиск по шаблонам
if ($_GET['action'] == 'searchTemplates') searchTemplates(); 
elseif ($_POST['action'] == 'getAllBackups') # для одного шаблона
{
	if (!empty($_POST['template_id'])) getAllBackups();
}
elseif ($_GET['action'] == 'getAllBackupsMulti') # для нескольких шаблонов сразу
{
	if (!empty($_GET['templates'])) getAllBackupsMulti($_GET['templates']);
}
elseif ($_POST['action'] == 'makeBackup')
{
	if (!empty($_POST['template_id'])
		and !empty($_POST['html_code'])
		)
	{
		# очищаем таблицу
		// clearTable();
		# добавляем запись
		makeBackup();
		# получаем список всех backup'ов для данного шаблона
		getAllBackups();
	}
}
elseif ($_POST['action'] == 'removeBackup')
{
	if (!empty($_POST['id']))
	{
		# удялем запись
		removeBackup();
		# получаем список всех backup'ов для данного шаблона
		# getAllBackups();
	}
}
// проверяем шаблон на ошибки
elseif ($_POST['action'] == 'checkTemplateForErrorsAddItem')
{
	if (!empty($_POST['file_name'])) checkTemplateForErrorsAddItem();
}
// проверяем шаблон на ошибки
elseif ($_POST['action'] == 'checkTemplateForErrorsEditItem')
{
	if (!empty($_POST['filename']) and !empty($_POST['pathToTemplates']))
	{
		checkTemplateForErrorsEditItem();
	}
}
elseif ($_POST['action'] == 'checkTemplatesDirForWriting')
{
	if (!empty($_POST['pathToTemplates']))
	{
		// проверяем, доступна ли директория с шаблонами для записи
		checkTemplatesDirForWriting();
	}
}
elseif ($_POST['action'] == 'edit_template_submit')
{
    # проверка переменных
    if (empty($_POST['name'])) exit;
    # if (empty($_POST['file_name'])) exit;
    if (empty($_POST['html_code'])) exit;
    if (empty($_POST['id'])) exit;
    
    edit_template_submit();
}
# провряем по названию, существует ли шаблон
elseif ($_POST['action'] == 'check_template_for_existence_by_name')
{
    # провряем по названию, существует ли шаблон по названию
    $sql = '
    select id
    from '.DB_PREFIX.'js
    where name = :name
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
elseif ($_POST['action'] == 'check_template_for_existence_by_file_name')
{
    # провряем по названию, существует ли шаблон по названию
    $sql = '
    select id
    from '.DB_PREFIX.'js
    where file_name = :file_name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':file_name', $_POST['file_name']);
    $sth->execute();
    if ($_ = $sth->fetchColumn())
    {
        $result = array('result' => 'exists', 'id' => $_);
        echo json_encode($result);
    }
}
# /ЛОГИКА

# ФУНКЦИИ

# сохраняем информацию по шаблону при редактировании
function edit_template_submit()
{
    # echo '<pre>'.(print_r($_POST, true)).'</pre>'; exit;
    
    global $dbh;
    
    if (!empty($_POST['id']))
    {
        $sql = '
        update '.DB_PREFIX.'js
        set name = :name
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':name', $_POST['name']);
        # $sth->bindParam(':file_name', $_POST['file_name']);
        $sth->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        try { if ($sth->execute()) {
                # получаем текущее имя файла из БД
                $oldFileName = getOldFileName($_POST['id']); # echo $oldFileName."<hr />";

                # сохраняем шаблон в файл
                if (saveContentToFile(PATH_TO_PUBLIC_JS,
                                      $oldFileName, # $_POST['file_name'], # new file name
                                      $oldFileName, # old file name
                                      $_POST['html_code'])) echo 'success';
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); exit; } }
    }
} # /сохраняем информацию по шаблону при редактировании

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
    if (file_put_contents($fullPathToNewFile, $htmlCode, LOCK_EX))
    {
        if (is_file($fullPathToNewFile)) chmod($fullPathToNewFile, 0755);
        return 1;
    }
} # /ЗАПИСЫВАЕМ ШАБЛОН В ФАЙЛ

# ПОЛУЧАЕМ ТЕКУЩЕЕ ИМЯ ФАЙЛА ИЗ БД
function getOldFileName($itemID)
{
	# проверка переменных
	if (empty($itemID)) return;

	global $dbh;
	
	$sql = '
	select file_name
	from '.DB_PREFIX.'js
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    try
    {
        $sth->bindParam(':id', $itemID, PDO::PARAM_INT);
        $sth->execute();
        $_ = $sth->fetchColumn(); # echo '<pre>'.(print_r($_, true)).'</pre>';
        if (!empty($_)) return $_;
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); exit; } }
    
} # /ПОЛУЧАЕМ ТЕКУЩЕЕ ИМЯ ФАЙЛА ИЗ БД

# поиск по шаблонам
function searchTemplates()
{
	if (!empty($_GET['q']))
	{
		global $dbh;
		
		# формируем список шаблонов
		$sql = "
		select id,
			   name,
			   file_name,
			   (select count(1) from ".DB_PREFIX."js_backups where template_id = i.id) as backups_count
		from ".DB_PREFIX."js as i
		where name like '%".$_GET['q']."%'
			  or file_name like '%".$_GET['q']."%'
		order by name
		"; # echo '<pre>'.$sql."</pre><hr />";
		$_ = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
		$_c = count($_);
		$rows = array();
		if (!empty($_))
		{
			for ($i=0;$i<$_c;$i++)
			{
				$rows[] = '
				<tr onmouseover="this.style.backgroundColor=\'#cccccc\'" onmouseout="this.style.backgroundColor=\'#ffffff\'">
					<td class="td_edit">
						<a href="/control/edit_js/?action=editItem&itemID='.$_[$i]['id'].'"><img src="/control/images/button_edit.png" style="border:0" /></a>
					</td>
					<td class="name">'.$_[$i]['name'].'</td>
					<td class="name">'.$_[$i]['file_name'].'</td>
					<td class="link">'.$_[$i]['backups_count'].'</td>
					<td class="td_remove">
						<a href="/control/edit_js/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Файл js-кода будет удален безвозвратно. Удалить файл?\')"><img src="/control/images/button_remove.png" style="border:0" /></a>
					</td>
				</tr>
				';
			}
			# /формируем список шаблонов
			
			if (!empty($rows) and is_array($rows))
			{
				$rows = implode("\n", $rows);
			}
			
			echo '
			<table border="1" cellpadding="2" class="items_list">
				<tr>
					<th style="width:30px">Правка</th>
					<th>Название</th>
					<th>Имя файла</th>
					<th>backup\'ов</th>
					<th style="width:30px">Удаление</th>
				</tr>
				'.$rows.'
			</table>
			';
		}
		else echo '<div id="ajax_search_result">По указанному запросу ничего не найдено.</div>';
	}
	else echo '<div id="ajax_search_result">По указанному запросу ничего не найдено.</div>';
}
# /поиск по шаблонам

# создать backup для шаблона
function makeBackup()
{
	global $dbh;
	
	# ПОДГОТАВЛИВАЕМ ДАННЫЕ ДЛЯ POST ЗАПРОСА
	# preparePOSTVariables();
	
	# $htmlCode = addslashes($_POST['html_code']);

	$sql = "
	insert into ".DB_PREFIX."js_backups 
	(template_id, date_add, html_code)
	values
	(:template_id, unix_timestamp(), :html_code)
	"; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':template_id', $_POST['template_id'], PDO::PARAM_INT);
    $sth->bindParam(':html_code', $_POST['html_code']);
    try
    {
        if ($sth->execute())
        {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
            
            if (!empty($last_insert_id)) return $last_insert_id;
            else return;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); exit; } }
}

function getAllBackups() # получаем список всех backup'ов для данного шаблона
{
	global $dbh;
	
	if (!empty($_POST['template_id']))
	{
		$condition = " and template_id = '".(int)$_POST['template_id']."'";
	}
	
	$sql = "
	select id,
		   template_id,
		   from_unixtime(date_add, '%e') as day,
		   ELT(MONTH(from_unixtime(date_add, '%Y-%m-%d')), 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря') as month,
		   from_unixtime(date_add, '%Y %H:%i:%s') as year_and_time,
		   html_code
	from ".DB_PREFIX."js_backups
	where 1
		  ".$condition."
	order by date_add desc
	"; # echo '<pre>'.$sql."</pre><hr />";
	$_ = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
	$_c = count($_); # echo 'count: '.$_c.'</hr>';
	if (!empty($_c))
	{
		$result = array();
		# вычисляем остаток
		if ($_c >= 5)
		{
			$remainder = $_c - 5;
		}
		for ($i=0;$i<$_c;$i++)
		{
			# если записей >= 5, скрываем остаток
			if ($_c >= 5 && $i == 5)
			{
				$result[] = "
				<div><a href='#' class='showAllBackups'>показать все backup'ы (".$remainder.")</a></div>
				<div style='display:none' id='allBackups'>
				";
			}
			
			$result[] = "
			<div id='".$_[$i]['id']."'>
				backup от <span id='date".$_[$i]['id']."'>".$_[$i]['day']." ".$_[$i]['month']." ".$_[$i]['year_and_time']."</span>
				&nbsp;&nbsp;&nbsp; <a href='#' class='showBackup' backupID='showBackup".$_[$i]['id']."'>js-код</a>
				&nbsp;&nbsp;&nbsp; <a href='#' class='removeBackup' backupID='removeBackup".$_[$i]['id']."'>удалить</a>
				<br />
				<div id='html_code".$_[$i]['id']."' style='display:none;margin:10px 0'><b>js-код:</b><br /><textarea class='form-control' style='width:95%;height:270px'>".htmlspecialchars($_[$i]['html_code'], ENT_QUOTES)."</textarea></div>
			</div>
			";
		}
		if ($_c >= 5)
		{
			$result[] = '</div>';
		}
		
		if (is_array($result))
		{
			$result = implode("\n", $result);
			echo $result;
		}
		else echo "backup'ов нет.";
	}
	else echo "backup'ов нет.";
} # /getAllBackups

function getAllBackupsMulti($templates) # получаем список всех backup'ов для нескольких шаблонов
{ # input: $templates = array(template1ID, template2ID, template3ID)
    # checking variables
    if (empty($templates) || !is_array($templates)) return;
    
    global $dbh;
	
    $condition = " and template_id in '".implode(', ', $templates)."'";
	
	$sql = "
	select id,
		   template_id,
		   from_unixtime(date_add, '%e') as day,
		   ELT(MONTH(from_unixtime(date_add, '%Y-%m-%d')), 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря') as month,
		   from_unixtime(date_add, '%Y %H:%i:%s') as year_and_time,
		   html_code
	from ".DB_PREFIX."js_backups
	where 1
		  ".$condition."
	order by date_add desc
	"; # echo '<pre>'.$sql."</pre><hr />";
	$_ = $dbh->query($sql); # echo '<pre>'.(print_r($_, true)).'</pre>';
	$_c = count($_); # echo 'count: '.$_c.'</hr>';
	if (!empty($_c))
	{
		$result = array();
		# вычисляем остаток
		if ($_c >= 5)
		{
			$remainder = $_c - 5;
		}
		for ($i=0;$i<$_c;$i++)
		{
			# если записей >= 5, скрываем остаток
			if ($_c >= 5 && $i == 5)
			{
				$result[] = "
				<div><a href='#' class='showAllBackups'>показать все backup'ы (".$remainder.")</a></div>
				<div style='display:none' id='allBackups'>
				";
			}
			
			$result[] = "
			<div id='".$_[$i]['id']."'>
				backup от <span id='date".$_[$i]['id']."'>".$_[$i]['day']." ".$_[$i]['month']." ".$_[$i]['year_and_time']."</span>
				&nbsp;&nbsp;&nbsp; <a href='#' class='showBackup' backupID='showBackup".$_[$i]['id']."'>js-код</a>
				&nbsp;&nbsp;&nbsp; <a href='#' class='removeBackup' backupID='removeBackup".$_[$i]['id']."'>удалить</a>
				<br />
				<div id='html_code".$_[$i]['id']."' style='display:none;margin:10px 0'><b>js-код:</b><br /><input class='backup-for-phones' value='".htmlspecialchars($_[$i]['html_code'], ENT_QUOTES)."' /></div>
			</div>
			";
		}
		if ($_c >= 5)
		{
			$result[] = '</div>';
		}
		
		if (is_array($result))
		{
			$result = implode("\n", $result);
			echo $result;
		}
		else echo "backup'ов нет.";
	}
	else echo "backup'ов нет.";
} # /getAllBackupsMulti

# удаляем выбранный backup
function removeBackup()
{
	global $dbh;
	
	# print_r($_POST);
	
	if (!empty($_POST['id']))
	{
		$sql = "
		delete from ".DB_PREFIX."js_backups 
		where id = '".$_POST['id']."'
		"; # echo '<pre>'.$sql."</pre><hr />";
        $result = $dbh->prepare($sql);
        $result->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        try { if ($result->execute()) return 1; }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); exit; } }
	}
	else return;
}

# очищаем таблицу
function clearTable()
{
	global $dbh;
	
	$sql = "
	delete from ".DB_PREFIX."js_backups
	"; # echo '<pre>'.$sql."</pre><hr />";
	$result = $dbh->query($sql);
	if (!empty($result)) return 1;
	else return;
}

// проверяем шаблон на ошибки
function checkTemplateForErrorsAddItem()
{
	# если файл существует, проверяем, можно ли его перезаписать
	$fileName = $_POST['file_name'];
	$fullPathToTemplate = PATH_TO_PUBLIC_JS.basename($fileName); # echo 'fullPathToTemplate: '.$fullPathToTemplate;
	# проверяем, существует ли файл
	if (file_exists($fullPathToTemplate))
	{
		# провряем, можно ли перезаписать файл
		if (is_writable($fullPathToTemplate))
		{
            $result = array('result' => 'exists', 'message' => iconv('windows-1251', 'UTF-8//TRANSLIT', '
            <b>Уведомление</b>: файл &quot;'.$fileName.'&quot; <b>уже существует</b>.
            <br />Если поле "js-код" оставить пустым, содержимое файла останется "как есть".
            <br />Если поле "js-код" заполнить, js-код будет записан в файл.
            '));
            echo json_encode($result);
		}
		else
		{
            $result = array('result' => 'exists', 'message' => iconv('windows-1251', 'UTF-8//TRANSLIT', '
            <b>Ошибка</b>: файл "'.$fileName.'" уже существует, но не доступен для записи.
            <br />Необходимо выставить права: 0646.
            '));
            echo json_encode($result);
		}
	}
}

function checkTemplateForErrorsEditItem()
{
	# если файл существует, проверяем, можно ли его перезаписать
	$fileName = $_POST['filename'];
	$pathToTemplates = $_POST['pathToTemplates'];
	$fullPathToTemplate = $pathToTemplates.$fileName; # echo $fullPathToTemplate;
	# если файл существует
	if (file_exists($fullPathToTemplate))
	{
		# если файл не доступен для записи
		if (!is_writable($fullPathToTemplate))
		{
			echo "
			<b style='color:red' class='error'>Ошибка</b>: файл &quot;{$fileName}&quot; не доступен для записи. Необходимо выставить права: 646.
			";
		}
	}
	# если файл не существует
	else
	{
		echo "<b style='color:#cccccc'>Уведомление</b>: файл &quot;{$fileName}&quot; не существует. При сохранении файла js-кода будет создан новый файл.";
	}
}

# проверяем, доступна ли директория с шаблонами для записи
function checkTemplatesDirForWriting()
{
	if (file_exists($_POST['pathToTemplates']))
	{
		if (!is_writable($_POST['pathToTemplates']))
		{
			echo "<b style='color:red' class='error'>Ошибка</b>: Директория с файлами js-кода не доступна для записи. Необходимо выставить права: 747.";
		}
	}
}

# /ФУНКЦИИ