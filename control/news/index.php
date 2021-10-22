<?php 
# Модуль админки для работы с новостями (таблица news)
# romanov.egor@gmail.com; 2015.5.25

# подключаем файл конфига
include('../loader.control.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'Новости';
$GLOBALS['imagesPath'] = '/public/images/news/';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > добавляем новость';
    $GLOBALS['tpl_h1'] = 'Добавляем новость'; 
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > добавляем новость';
    $GLOBALS['tpl_h1'] = 'Добавляем новость'; 
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > редактируем новость';
    $GLOBALS['tpl_h1'] = 'Редактируем новость'; 
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем новость';
    $GLOBALS['tpl_h1'] = 'Удаляем новость'; 
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > все новости';
    $GLOBALS['tpl_h1'] = 'Все новости ('.$dbh->query('select count(1) from '.DB_PREFIX.'news')->fetchColumn().')'; 
    $GLOBALS['tpl_content'] = showItems(); 
}
# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ФОРМИРУЕМ СПИСОК ВСЕХ НОВОСТЕЙ
function showItems($count = null)
{
    global $dbh;
    
    # получаем список новостей
    $sql = '
    select id,
           date_add,
           date_format(date_add, "%d.%m.%Y") as date_add_formatted,
           date_format(date_add, "%d-%m-%Y") as date_add_formatted_2,
           h1,
           is_showable
    from '.DB_PREFIX.'news
    order by date_add desc,
             id desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sql_for_count = '
    select count(id)
    from '.DB_PREFIX.'news
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # текущая страница
					   25, # записей на страницу
					   $dbh, # объект базы данных
                       '', # routeVars
					   $sql, # sql-запрос
					   $sql_for_count, # sql-запрос для подсчета количества записей
					   '/control/news/', # ссыка на 1ю страницу
					   '/control/news/?page=%page%', # ссыка на остальные страницы
						1500 # максимальное количество записей на страницу
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">Страницы: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # ссылка
        if (!empty($_[$i]['date_add_formatted_2'])) $link = '<a href="/novosti/'.$_[$i]['date_add_formatted_2'].'/" target="_blank">смотреть</a>';
        else $link = '&nbsp;';
        
        # is_showable
        if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
        else unset($trClass);
        
        $rows[] = '
		<tr'.$trClass.'>
            <td class="center vertical_middle">
                <a class="block" href="/control/news/?action=editItem&itemID='.$_[$i]['id'].'">
                    <i class="fa fa-edit size_18"></i>
                </a>
            </td>
			<td class="center vertical_middle">'.$link.'</td>
			<td><span style="color:#ababab;padding-right:10px">'.$_[$i]['date_add_formatted'].'</span>'.$_[$i]['h1'].'</td>
			<td class="center vertical_middle">
                <a class="block" title="Удалить новость" href="/control/news/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Новость будет удалена безвозвратно. Удалить новость?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    } # /формируем список новостей
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);
    
    $result = '
	<script type="text/javascript" src="/control/news/index.js"></script>
	
    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="/novosti/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/novosti/</a>
    </div>
    <div style="width:50%;float:right;text-align:right;padding-right:15px">
        Поиск по заголовку h1 / дате: &nbsp;
        <input id="search_by_news" class="form-control form_required" type="text" value="" style="display:inline-block;width:150px" />
    </div>
    <br style="clear:both" />
    
    <div class="center" style="margin-bottom:15px">
        <a href="/control/news/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    Добавить новость
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= 'В системе не задана ни одна новость.';
    else {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">Правка</th>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">Ссылка</th>
                <th class="center vertical_middle">Заголовок h1</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">Удаление</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # /ФОРМИРУЕМ СПИСОК ВСЕХ НОВОСТЕЙ

# ФОРМА РЕДАКТИРОВАНИЯ НОВОСТИ
function showEditForm()
{
    global $dbh;
    
    $showEditForm = 1;

    # выводим сообщение
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = 'Новость успешно добавлена.';
    
    # сохраняем изменения в бд
    if ($_GET['subaction'] == 'submit' && !empty($_POST)) {
        $sql = '
        update '.DB_PREFIX.'news
        set page_title = :page_title,
            full_navigation = :full_navigation,
            h1 = :h1,
            text = :text,
            date_add = :date_add,
            footeranchor = :footeranchor,
            is_showable = :is_showable
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':page_title', $_POST['news_form_page_title']);
        # full_navigation
        if (empty($_POST['news_form_full_navigation'])) $_POST['news_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['news_form_full_navigation']);
        $sth->bindParam(':h1', $_POST['news_form_h1']);
        # text
        $text = !empty($_POST['news_form_text']) ? $_POST['news_form_text'] : NULL;
        $sth->bindParam(':text', $text);
        # date_add
        # конвертируем дату из поля datepicker в mysql datetime:
        if (!empty($_POST['news_form_date_add'])) $date = date("Y-m-d H:i:s", strtotime($_POST['news_form_date_add'].' 05:00:00'));
        else $date = 'now()';
        $sth->bindParam(':date_add', $date);
        # footeranchor
        if ($_POST['news_form_footeranchor'] == '') $_POST['news_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['news_form_footeranchor']);
        # is_showable
        $isShowable = !empty($_POST['news_form_is_showable']) ? 1 : NULL;
        $sth->bindParam(':is_showable', $isShowable, PDO::PARAM_INT);
        # id
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute())
        {
            $GLOBALS['tpl_success'] = 'Информация сохранена.';
            
            # копируем картинку # print_r($_FILES);
            if (!empty($_FILES['news_form_image']['tmp_name']))
            {
                copyImage(array(
                'itemID' => $_GET['itemID'],
                'imageFormName' => 'news_form_image',
                'imageDbColumnName' => 'image',
                'imagePrefix' => ''
                ));
            }
            # /копируем картинку
        }
        else
        {
            $GLOBALS['tpl_failure'] = 'К сожалению, информация не сохранена. Пжл, обратитесь к разработчикам сайта.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
        }
    } # /сохраняем изменения в бд
    # удаляем указанную картинку
    if ($_GET['subaction'] == 'remove_photo')
    {
        # проверка переменных
        $allowedCoumns = array('image');
        if (empty($_GET['itemID'])) $GLOBALS['tpl_failure'] = 'Не передан ID записи.';
        elseif (empty($_GET['db_column_name'])) $GLOBALS['tpl_failure'] = 'Неверно передано название столбца картинки.';
        elseif (!in_array($_GET['db_column_name'], $allowedCoumns)) $GLOBALS['tpl_failure'] = 'Неверно передано название столбца картинки.';
        else
        {
            # получаем инофрмацию о картинке
            $sql = '
            select '.$_GET['db_column_name'].'
            from '.DB_PREFIX.'news
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            $columnName = $sth->fetchColumn(); # echo 'columnName: '.$columnName;
            # удаляем картинку
            $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$columnName; # echo 'fullPathToImage: '.$fullPathToImage;
            if (!empty($fullPathToImage) && file_exists($fullPathToImage)) unlink($fullPathToImage);
            # вносим изменения в бд
            $sql = '
            update '.DB_PREFIX.'news
            set '.$_GET['db_column_name'].' = NULL
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
            if ($sth->execute())
            {
                $GLOBALS['tpl_success'] = 'Картинка успешно удалена.';
                $_POST['tabs_state'] = 4;
            }
            else $GLOBALS['tpl_failure'] = 'К сожалению, картинка не удалена. Пжл, обратитесь к разработчикам сайта.';
        }
    } # /удаляем указанную картинку
    
    # /проверка переменных

    # выводим форму редактирования
    if ($showEditForm)
	{
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # защита
        if (!$itemInfo['id']) exit('
		Не существует записи с ID='.$_GET['itemID'].'
		<br /><a href="/control/news/">Перейти к списку новостей</a>
		');

        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);
        
        # получаем и выводим инфу по фото
        $imageInfo = showPhotoInfo(array('imageName' => $itemInfo['image'], 'imageDbColumnName' => 'image'));
        
        return "
		<script type='text/javascript' src='/control/news/index.js'></script>
		<form id='news_form' action='/control/news/?action=editItem&itemID=".$itemInfo['id']."&subaction=submit' name='news_form' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='btn btn-primary submit_button' type='submit'>Сохранить информацию</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/news/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            Перейти к списку
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/news/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            Добавить новость
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/news/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"Новость будет удалена безвозвратно. Удалить новость?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> Удалить новость</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/novosti/".$itemInfo['date_add_formatted_2']."/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/novosti/".$itemInfo['date_add_formatted_2']."/</a>
            
            <br /><br />
            <div class='form-group' style='width:60%'>
                <label>Дата (ДД.ММ.ГГГГ): <span style='color:red'>*</span></label>
                <input type='text' name='news_form_date_add' id='news_form_date_add' class='form-control form_required' data-required-label='Пжл, укажите дату добавления новости' value='".$itemInfo['date_add_formatted']."' style='width:100px;display:inline-block' />
            </div>

            <div class='form-group' style='width:90%'>
                <label>Заголовок страницы: <span style='color:red'>*</span></label>
                <input type='text' name='news_form_page_title' id='news_form_page_title' class='form-control form_required' data-required-label='Пжл, укажите заголовок страницы' value='".$itemInfo['page_title']."' />
            </div>

            <div class='form-group' style='width:90%'>
                <label>Заголовок h1: <span style='color:red'>*</span></label>
                <input type='text' name='news_form_h1' id='news_form_h1' class='form-control form_required' data-required-label='Пжл, укажите заголовок h1' value='".$itemInfo['h1']."' />
            </div>

            <div id='news_form_h1_alert_div' class='alert alert-info hidden width_95'></div>

            <div class='form-group'>
                <label>Текст новости:</label>
                <textarea name='news_form_text' id='news_form_text' class='form-control lined' style='width:90%;height:270px'>".$itemInfo['text']."</textarea>
            </div>

            <div class='form-group'>
                <label>Картинка (не обязательно):</label>
                &nbsp; <input id='news_form_image' name='news_form_image' type='file' style='display:inline-block' />
            </div>
            
            ".$imageInfo."
            
			<div class='form-group' style='width:95%'>
                <label>Строка навигации в ручном режиме:
                       <br />
                       <span style='font-weight:normal'>* если указана, на сайте выводится строка навигации из этого поля:</span>
                </label>
                <textarea name='news_form_full_navigation' id='news_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>
            
            <div class='form-group' style='width:95%'>
                <label>Анкор для перелинковки в подвале:</label> &nbsp; 
                <textarea name='news_form_footeranchor' id='news_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$itemInfo['footeranchor']."</textarea>
            </div>
                        
            <div class='form-group' style='margin-bottom:0'>
                <label>
                    <input type='checkbox' name='news_form_is_showable' id='news_form_is_showable' class='form_checkbox' ".(!empty($itemInfo['is_showable']) ? 'checked="checked"' : '')." />&nbsp; Отображать новость на сайте
                </label>
            </div>
            
            <br />
			<button class='btn btn-primary submit_button' type='submit' style='margin-top:5px'>Сохранить информацию</button>
            
		</form>
		";
    }
} # /ФОРМА РЕДАКТИРОВАНИЯ НОВОСТИ

# ФОРМА ДОБАВЛЕНИЯ НОВОСТИ
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/news/index.js'></script>
	<form id='news_form' action='/control/news/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>Добавить новость</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/news/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        Перейти к списку
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/novosti/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/novosti/</a>

        <br /><br />
        <div class='form-group' style='width:60%'>
            <label>Дата (ДД.ММ.ГГГГ): <span style='color:red'>*</span></label>
            <input type='text' name='news_form_date_add' id='news_form_date_add' class='form-control form_required' data-required-label='Пжл, укажите дату добавления новости' value='".date("d").".".date("m").".".date("Y")."' style='width:100px;display:inline-block' />
        </div>

        <div id='news_form_date_add_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group' style='width:90%'>
            <label>Заголовок страницы: <span style='color:red'>*</span></label>
            <input type='text' name='news_form_page_title' id='news_form_page_title' class='form-control form_required' data-required-label='Пжл, укажите заголовок страницы' value='".$_POST['news_form_page_title']."' />
        </div>
        
        <div class='form-group' style='width:90%'>
            <label>Заголовок h1: <span style='color:red'>*</span></label>
            <input type='text' name='news_form_h1' id='news_form_h1' class='form-control form_required' data-required-label='Пжл, укажите заголовок h1' value='".$_POST['news_form_h1']."' />
        </div>
        
        <div id='news_form_h1_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group'>
            <label>Текст новости:</label>
            <textarea name='news_form_text' id='news_form_text' class='form-control lined' style='width:90%;height:270px'>".$_POST['news_form_text']."</textarea>
        </div>
        
       <div class='form-group'>
            <label>Картинка (не обязательно):</label>
            &nbsp; <input id='news_form_image' name='news_form_image' type='file' style='display:inline-block' />
       </div>
        
        <div class='form-group' style='width:95%'>
            <label>Строка навигации в ручном режиме:
                   <br />
                   <span style='font-weight:normal'>* если указана, на сайте выводится строка навигации из этого поля:</span>
            </label>
            <textarea name='news_form_full_navigation' id='news_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['news_form_full_navigation']."</textarea>
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>Анкор для перелинковки в подвале:</label> &nbsp;
            <textarea name='news_form_footeranchor' id='news_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$_POST['news_form_footeranchor']."</textarea>
        </div>
                    
        <div class='form-group' style='margin-bottom:0'>
            <label>
                <input type='checkbox' name='news_form_is_showable' id='news_form_is_showable' class='form_checkbox' checked='checked' />&nbsp; Отображать новость на сайте
            </label>
        </div>
        
        <br />
        
        <button class='btn btn-primary submit_button' type='submit'>Добавить новость</button>
	</form>
	";
} # /ФОРМА ДОБАВЛЕНИЯ НОВОСТИ

# ДОБАВЛЯЕМ НОВОСТЬ В БД
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# защита от прямого запроса URL'а: http://kupi-krovat.ru/control/news/?action=addItemSubmit
	if (!empty($_POST))
	{
        # проверка + нужная кодировка POST-переменных
        preparePOSTVariables(); # print_r($_POST); exit;

		# добавляем новость в БД
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# если новость успешно добавлена
		if (!empty($lastInsertID))
		{
            # копируем картинку # print_r($_FILES);
            if (!empty($_FILES['news_form_image']['tmp_name']))
            {
                copyImage(array(
                'itemID' => $lastInsertID,
                'imageFormName' => 'news_form_image',
                'imageDbColumnName' => 'image',
                'imagePrefix' => ''
                ));
            } # /копируем картинку
            
			# делаем перенаправление на форму редактирования
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/news/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# если возникла ошибка и новость не добавлено
		else
		{
            $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и новость не добавлена. Пожалуйста, обратитесь к разработчикам сайта.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# если набран: http://news.youroute.ru/control/news/addItemSubmit/ и при этом $_POST пустой
	else
	{
		# выводим список новостей
        $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и новость не добавлена. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /ДОБАВЛЯЕМ НОВОСТЬ В БД

# УДАЛЯЕМ НОВОСТЬ
function deleteItem(){
	
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID']))
	{
		# выводим ошибку
		$GLOBALS['tpl_failure'] = 'Новость не удалена. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# выводим список новостей
        showItems();
	}
	else
	{
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# удаляем картинку
        if (!empty($itemInfo['image']))
        {
            $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$itemInfo['image'];
            if (file_exists($fullPathToImage) && is_file($fullPathToImage)) unlink($fullPathToImage);
        }

		# удаляем новость из БД
        $sql = '
        delete from '.DB_PREFIX.'news
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute())
		{
			$GLOBALS['tpl_success'] = 'Новость успешно удалена.';
            
            # уадялем backup'ы
            $sql = '
            delete from '.DB_PREFIX.'backups
            where table_name = "news"
                  and entry_id = :entry_id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':entry_id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            
			# выводим список новостей
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = 'К сожалению, новость не удалена. Пожалуйста, обратитесь к разработчикам сайта.';
			# выводим список новостей
			return showItems();
		}
	}
} # /УДАЛЯЕМ НОВОСТЬ

# ДОБАВЛЯЕМ НОВОСТЬ В БД
function addItemToDB()
{
	global $dbh;
	
	if (!empty($_POST['news_form_page_title'])
		and !empty($_POST['news_form_h1']))
	{ 
        $sql = '
        insert into '.DB_PREFIX.'news
        (page_title,
         full_navigation,
         h1,
         text,
         date_add,
         footeranchor,
         is_showable)
        values
        (:page_title,
         :full_navigation,
         :h1,
         :text,
         :date_add,
         :footeranchor,
         :is_showable)    
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':page_title', $_POST['news_form_page_title']);
        # full_navigation
        if (empty($_POST['news_form_full_navigation'])) $_POST['news_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['news_form_full_navigation']);
        $sth->bindParam(':h1', $_POST['news_form_h1']);
        # text
        $text = !empty($_POST['news_form_text']) ? $_POST['news_form_text'] : NULL;
        $sth->bindParam(':text', $text);
        # date_add
        # конвертируем дату из поля datepicker в mysql datetime:
        if (!empty($_POST['news_form_date_add'])) $date = date("Y-m-d H:i:s", strtotime($_POST['news_form_date_add'].' 05:00:00'));
        else $date = 'now()';
        $sth->bindParam(':date_add', $date);
        # footeranchor
        if ($_POST['news_form_footeranchor'] == '') $_POST['news_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['news_form_footeranchor']);
        # is_showable
        $isShowable = !empty($_POST['news_form_is_showable']) ? 1 : NULL;
        $sth->bindParam(':is_showable', $isShowable, PDO::PARAM_INT);
		try { if ($sth->execute()) {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			if (!empty($last_insert_id)) return $last_insert_id;
			else return;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; }}
	}
    else echo 'В метод addItemToDB не переданы news_form_page_title или news_form_h1.';
} # /ДОБАВЛЯЕМ НОВОСТЬ В БД

# ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ
function getItemInfo()
{
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID'])) return;
	
	$sql = '
	select *,
           date_format(date_add, "%d.%m.%Y") as date_add_formatted,
           date_format(date_add, "%d-%m-%Y") as date_add_formatted_2
	from '.DB_PREFIX.'news
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $itemInfo = $sth->fetch();
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ

# КОПИРУЕМ КАРТИНКУ
function copyImage($array)
{
	global $dbh;
	
    # print_r($_FILES);
    # print_r($array);
    
	# проверка переменных
	if (empty($array['itemID'])) return;
	if (empty($array['imageFormName'])) return;
	if (empty($array['imageDbColumnName'])) return;
	# if (empty($array['imagePrefix'])) return;

	# echo '<pre>'.(print_r($array, true)).'</pre>';
	# echo $_FILES[$array['imageFormName']]['tmp_name'];
	if (is_uploaded_file($_FILES[$array['imageFormName']]['tmp_name']))
	{
		# УДАЛЯЕМ СТАРУЮ КАРТИНКУ, ЕСЛИ ОНА ЕСТЬ
		$sql = '
		select '.$array['imageDbColumnName'].'
		from '.DB_PREFIX.'news
		where id = :id
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $array['itemID'], PDO::PARAM_INT);
        $sth->execute();
        $_ = $sth->fetchColumn();
		if (!empty($_))
		{
			$oldImage = $_;
			# удаляем из БД
			$sql = '
			update '.DB_PREFIX.'news
			set '.$array['imageDbColumnName'].' = NULL
			where id = :id
			'; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $array['itemID'], PDO::PARAM_INT);
            $sth->execute();
			# удаляем файл
			$result = @unlink($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$oldImage); 
			# echo $result.'<hr />';
			# echo $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$oldImage;
		}
		# /УДАЛЯЕМ СТАРУЮ КАРТИНКУ, ЕСЛИ ОНА ЕСТЬ
	
		# КОПИРУЕМ НОВУЮ КАРТИНКУ
		$ext = getImageExt($_FILES[$array['imageFormName']]['tmp_name']); # echo $ext.'<hr />';
		$newImageName = $array['itemID']."".$array['imagePrefix'].".".$ext; # echo $newImageName.'<hr />';
		$fullPathToUpload = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$newImageName; # echo $fullPathToUpload.'<hr />';
		# копируем на основную зону
		if (move_uploaded_file($_FILES[$array['imageFormName']]['tmp_name'], $fullPathToUpload))
		{
			# пишем инфу в БД
			$sql = '
			update '.DB_PREFIX.'news
			set '.$array['imageDbColumnName'].' = :new_image_name
			where id = :id
			'; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':new_image_name', $newImageName);
            $sth->bindParam(':id', $array['itemID']);
            $sth->execute();
			
			return array($newImageName, $newImageLargeName);
		}
		else return;
		# /КОПИРУЕМ НОВУЮ КАРТИНКУ
	}
} # /КОПИРУЕМ КАРТИНКУ

# ПОЛУЧАЕМ РАСШИРЕНИЕ КАРТИНКИ
# $imageName - full path to image
function getImageExt($fullPathToImage)
{
	# print_r($fullPathToImage);
	
	if (empty($fullPathToImage)) return;

	$info = getimagesize($fullPathToImage); # print_r($info);
	$ext = str_replace("image/", "", $info['mime']); # echo $ext.'<hr />';
	
	if (!empty($ext)) return $ext;
	else return;
} # /ПОЛУЧАЕМ РАСШИРЕНИЕ КАРТИНКИ

# ВЫВОДИМ ИНФУ ПО КАРТИНКЕ
function showPhotoInfo($array)
{
	# проверка переменных
	if (empty($array['imageName'])) return;
	if (empty($array['imageDbColumnName'])) return;
	
	if (file_exists($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath']).$array['imageName'])
	{
		$imageInfo = @getimagesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']); # echo '<pre>'.(print_r($imageInfo, true)).'</pre>';
		$imageSize = @filesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']);
		$imageSize = @round($imageSize / 1024, 1);
		
		return '
		Путь: <a href="'.$GLOBALS['imagesPath'].$array['imageName'].'" target="_blank">'.$_SERVER['HTTP_HOST'].$GLOBALS['imagesPath'].$array['imageName'].'</a>
		<br />Вес: '.$imageSize.' кб.
		<br />Размер: '.$imageInfo[0].'px x '.$imageInfo[1].'px
		<br /><br />
		<a href="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" target="_blank"><img src="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" border="0" /></a>
        <br /><a href="/control/news/?action=editItem&itemID='.$_GET['itemID'].'&subaction=remove_photo&db_column_name='.$array['imageDbColumnName'].'" onclick="return confirm(\'Удалить картинку?\');">Удалить картинку</a>
		<hr style="border:none;background-color:#ccc;color:#ccc;height:1px" />
		';
	}
} # /ВЫВОДИМ ИНФУ ПО КАРТИНКЕ

# СТРОИМ SELECT С АНКОРАМИ ДЛЯ ПЕРЕЛИНКОВКИ В ПОДВАЛЕ
/* function buildAllFooteranchors($footerAnchorID = NULL)
{
    global $dbh;
    
    $sql = '
    select id,
           anchor
    from '.DB_PREFIX.'footeranchors
    order by anchor
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $_ = $sth->fetchAll();
    $options = array();
    foreach ($_ as $item)
    {
        # selected
        if (!empty($footerAnchorID) && $footerAnchorID == $item['id']) $selected = ' selected="selected"';
        else unset($selected);
        
        $options[] = '<option value="'.$item['id'].'"'.$selected.'>'.$item['anchor'].'</option>';
    }
    if (!empty($options) and is_array($options)) $options = implode(PHP_EOL, $options);
    $result = '<select id="news_form_footeranchor_id" name="news_form_footeranchor_id" class="form-control">'.PHP_EOL.'<option value="null">не выбран</option>'.PHP_EOL.$options.'</select>';
    if (!empty($result)) return $result;
} */ # /СТРОИМ SELECT С АНКОРАМИ ДЛЯ ПЕРЕЛИНКОВКИ В ПОДВАЛЕ

# /ФУНКЦИОНАЛ