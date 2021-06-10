<?php 
# Модуль админки для работы с шоу (представления) (таблица shows)
# romanov.egor@gmail.com; 2015.10.14

# подключаем файл конфига
include('../loader.control.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'Шоу (Лужники)';
$GLOBALS['imagesPath'] = '/public/images/shows/';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > добавляем шоу';
    $GLOBALS['tpl_h1'] = 'Добавляем шоу';
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > добавляем шоу';
    $GLOBALS['tpl_h1'] = 'Добавляем шоу';
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > редактируем шоу';
    $GLOBALS['tpl_h1'] = 'Редактируем шоу';
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем шоу';
    $GLOBALS['tpl_h1'] = 'Удаляем шоу';
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > все шоу';
    $GLOBALS['tpl_h1'] = 'Все шоу в Лужниках ('.$dbh->query('select count(1) from `'.DB_PREFIX.'shows`')->fetchColumn().')';
    $GLOBALS['tpl_content'] = showItems(); 
}
# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ФОРМИРУЕМ СПИСОК ВСЕХ ШОУ
function showItems($count = null)
{
    global $dbh;
    
    # получаем список шоу
    $sql = '
    select id,
           name,
           url,
           image_for_slider_on_main_page,
           element_order_listing,
           is_showable
    from `'.DB_PREFIX.'shows`
    order by isnull(element_order_listing),
             element_order_listing,
             name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sql_for_count = '
    select count(id)
    from `'.DB_PREFIX.'shows`
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # текущая страница
					   25, # записей на страницу
					   $dbh, # объект базы данных
                       '', # routeVars
					   $sql, # sql-запрос
					   $sql_for_count, # sql-запрос для подсчета количества записей
					   '/control/shows/', # ссыка на 1ю страницу
					   '/control/shows/?page=%page%', # ссыка на остальные страницы
						1500 # максимальное количество записей на страницу
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">Страницы: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # ссылка
        $link = '<a href="/shou/'.$_[$i]['url'].'/" target="_blank">смотреть</a>';
        
        # is_showable
        if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
        else unset($trClass);

        # image_for_slider_on_main_page
        if (!empty($_[$i]['image_for_slider_on_main_page'])) $is_image_for_slider_on_main_page = 'да';
        else $is_image_for_slider_on_main_page = '&nbsp;';
        
        $rows[] = '
		<tr'.$trClass.'>
            <td class="center vertical_middle">
                <a class="block" href="/control/shows/?action=editItem&itemID='.$_[$i]['id'].'">
                    <i class="fa fa-edit size_18"></i>
                </a>
            </td>
			<td class="center vertical_middle">'.$link.'</td>
			<td>'.$_[$i]['name'].'</td>
			<td class="center">'.(!empty($_[$i]['element_order_listing']) ? $_[$i]['element_order_listing'] : '&nbsp;').'</td>
			<td class="center">'.$is_image_for_slider_on_main_page.'</td>
			<td class="center vertical_middle">
                <a class="block" title="Удалить шоу" href="/control/shows/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Шоу будет удалено безвозвратно. Удалить шоу?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    }
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);
    
    $result = '
	<script type="text/javascript" src="/control/shows/index.js"></script>
	
    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="/shou/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/shou/</a>
    </div>
    <div style="width:50%;float:right;text-align:right;padding-right:15px">
        Поиск по названию: &nbsp;
        <input id="search" class="form-control form_required" type="text" value="" style="display:inline-block;width:150px" />
    </div>
    <br style="clear:both" />
    
    <div class="center" style="margin-bottom:15px">
        <a href="/control/shows/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    Добавить шоу
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= 'В системе не задано ни одно шоу.';
    else
    {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">Правка</th>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">Ссылка</th>
                <th class="center vertical_middle">Название</th>
                <th class="center vertical_middle" style="width:175px">Порядок вывода в листинге</th>
                <th class="center vertical_middle" style="width:175px">Картинка для слайдера на главной</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">Удаление</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # / ФОРМИРУЕМ СПИСОК ВСЕХ ШОУ

# ФОРМА РЕДАКТИРОВАНИЯ ШОУ
function showEditForm()
{
    global $dbh;
    
    $showEditForm = 1;

    # выводим сообщение
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = 'Шоу успешно добавлено.';
    
    # сохраняем изменения в бд
    if ($_GET['subaction'] == 'submit' && !empty($_POST))
    {
        $sql = '
        update `'.DB_PREFIX.'shows`
        set name = :name,
            url = :url,
            title = :title,
            navigation = :navigation,
            full_navigation = :full_navigation,
            h1 = :h1,
            footeranchor = :footeranchor,
            text_for_slider_on_main_page = :text_for_slider_on_main_page,
            element_order_listing = :element_order_listing,
            is_showable = :is_showable
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':name', $_POST['show_form_name']);
        $sth->bindParam(':url', $_POST['show_form_url']);
        $sth->bindParam(':title', $_POST['show_form_title']);
        $sth->bindParam(':navigation', $_POST['show_form_navigation']);
        # full_navigation
        if (empty($_POST['show_form_full_navigation'])) $_POST['show_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['show_form_full_navigation']);
        $sth->bindParam(':h1', $_POST['show_form_h1']);
        # text_for_slider_on_main_page
        $sth->bindValue(':text_for_slider_on_main_page', !empty($_POST['show_form_text_for_slider_on_main_page']) ? $_POST['show_form_text_for_slider_on_main_page'] : null);
        # is_showable
        $isShowable = !empty($_POST['show_form_is_showable']) ? 1 : NULL;
        $sth->bindParam(':is_showable', $isShowable, PDO::PARAM_INT);
        # footeranchor
        if ($_POST['show_form_footeranchor'] == '') $_POST['show_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['show_form_footeranchor']);
        # element_order_listing
        $sth->bindValue(':element_order_listing', !empty($_POST['show_form_element_order_listing']) ? $_POST['show_form_element_order_listing'] : null);
        # id
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute())
        {
            $GLOBALS['tpl_success'] = 'Информация сохранена.';

            # копируем картинку # print_r($_FILES);
            if (!empty($_FILES['show_form_image_for_slider_on_main_page']['tmp_name']))
            {
                copyImage(array(
                    'itemID' => $_GET['itemID'],
                    'imageFormName' => 'show_form_image_for_slider_on_main_page',
                    'imageDbColumnName' => 'image_for_slider_on_main_page',
                    'imagePrefix' => ''
                ));
            } # /копируем картинку
            
			# сохраняем текст в файл
			saveContentToFile($_GET['itemID'],
							  $_POST['show_form_text']);
        }
        else
        {
            $GLOBALS['tpl_failure'] = 'К сожалению, информация не сохранена. Пжл, обратитесь к разработчикам сайта.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
        }
    } # /сохраняем изменения в бд

    # удаляем указанную картинку
    if ($_GET['subaction'] == 'remove_photo') {
        # проверка переменных
        $allowedCoumns = array('image_for_slider_on_main_page');
        if (empty($_GET['itemID'])) $GLOBALS['tpl_failure'] = 'Не передан ID записи.';
        elseif (empty($_GET['db_column_name'])) $GLOBALS['tpl_failure'] = 'Неверно передано название столбца картинки.';
        elseif (!in_array($_GET['db_column_name'], $allowedCoumns)) $GLOBALS['tpl_failure'] = 'Неверно передано название столбца картинки.';
        else
        {
            # получаем инофрмацию о картинке
            $sql = '
            select '.$_GET['db_column_name'].'
            from '.DB_PREFIX.'shows
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
            update '.DB_PREFIX.'shows
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

    # выводим форму редактирования
    if ($showEditForm)
	{
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # защита
        if (!$itemInfo['id']) exit('
		Не существует записи с ID='.$_GET['itemID'].'
		<br /><a href="/control/shows/">Перейти к списку шоу</a>
		');
        
		# читаем текст шоу из файла
        if (!empty($itemInfo['file_name']))
        {
            $fullPathToFile = $_SERVER['DOCUMENT_ROOT'].'/app/site_sections_shows/'.basename($itemInfo['file_name']); # echo $fullPathToFile.'<hr />';
            if (file_exists($fullPathToFile))
            {
                $content = file_get_contents($fullPathToFile); # echo $content.'<hr />';
                # prepare for showing
                $content = htmlspecialchars($content, ENT_QUOTES);
                $content = str_replace("\t", "", $content);
            }
        }

        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);

        # получаем и выводим инфу по фото
        $imageInfo = showPhotoInfo(array('imageName' => $itemInfo['image_for_slider_on_main_page'], 'imageDbColumnName' => 'image_for_slider_on_main_page'));
        
        return "
		<script type='text/javascript' src='/control/shows/index.js'></script>
		<form id='show_form' action='/control/shows/?action=editItem&itemID=".$itemInfo['id']."&subaction=submit' name='show_form' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='btn btn-primary submit_button' type='submit'>Сохранить информацию</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/shows/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            Перейти к списку
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/shows/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            Добавить шоу
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/shows/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"Шоу будет удалено безвозвратно. Удалить шоу?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> Удалить шоу</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/shou/".$itemInfo['url']."/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/shou/".$itemInfo['url']."/</a>
            
            <br><br>
            <div class='form-group' style='width:60%'>
                <label>Директория (по-английски): <span style='color:red'>*</span></label>
                <input type='text' name='show_form_url' id='show_form_url' class='form-control form_required' data-required-label='Пжл, укажите директорию (например: legenda)' value='".$itemInfo['url']."' />
            </div>

            <div class='form-group' style='width:60%'>
                <label>Название: <span style='color:red'>*</span></label>
                <input type='text' name='show_form_name' id='show_form_name' class='form-control form_required' data-required-label='Пжл, укажите название по-русски' value='".$itemInfo['name']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>Заголовок страницы:</label>
                <input type='text' name='show_form_title' id='show_form_title' class='form-control' data-required-label='Пжл, укажите заголовок страницы' value='".$itemInfo['title']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>Строка навигации:</label>
                <input type='text' name='show_form_navigation' id='show_form_navigation' class='form-control' value='".$itemInfo['navigation']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>Строка навигации в ручном режиме:
                       <br />
                       <span style='font-weight:normal'>* если указана, на сайте выводится строка навигации из этого поля:</span>
                </label>
                <textarea name='show_form_full_navigation' id='show_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>

            <div class='form-group' style='width:90%'>
                <label>Заголовок h1:</label>
                <input type='text' name='show_form_h1' id='show_form_h1' class='form-control' value='".$itemInfo['h1']."' />
            </div>

            <div class='form-group'>
                <label>Текст страницы:</label>
                <textarea name='show_form_text' id='show_form_text' class='form-control lined' style='width:90%;height:270px'>".$content."</textarea>
            </div>

            <div class='well' style='width:85%'>

                <div class='form-group' style='width:90%'>
                    <label>Текст-подсказка для слайдера на главной (<a href='/control/public/images/help_screenshot_1.png' target='_blank'>скриншот</a>):</label>
                    <input type='text' name='show_form_text_for_slider_on_main_page' id='show_form_text_for_slider_on_main_page' class='form-control' value='".$itemInfo['text_for_slider_on_main_page']."' />
                </div>

                <div class='form-group'>
                    <label>Картинка для слайдера на главной<br /><span style='font-weight:normal'>* если не указана, в слайдере на главной не выводится</span>:</label>
                    <br /> <input id='show_form_image_for_slider_on_main_page' name='show_form_image_for_slider_on_main_page' type='file' style='display:inline-block' />
                </div>

                ".$imageInfo."

            </div>

            <div class='form-group' style='width:60%'>
                <label>Порядок вывода в листинге:</label> &nbsp;
                <input type='text' name='show_form_element_order_listing' id='show_form_element_order_listing' class='form-control' data-required-label='' value='".$itemInfo['element_order_listing']."' style='display:inline-block;width:100px' />
            </div>

            <div class='form-group' style='width:95%'>
                <label>Анкор для перелинковки в подвале:</label> &nbsp; 
                <textarea name='show_form_footeranchor' id='show_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$itemInfo['footeranchor']."</textarea>
            </div>

            <div class='form-group' style='margin-bottom:0'>
                <label>
                    <input type='checkbox' name='show_form_is_showable' id='show_form_is_showable' class='form_checkbox' ".(!empty($itemInfo['is_showable']) ? 'checked="checekd"' : '')." />&nbsp; Отображать шоу на сайте
                </label>
            </div>
            
            <br />
			<button class='btn btn-primary submit_button' type='submit' style='margin-top:5px'>Сохранить информацию</button>
            
		</form>
		";
    }
} # /ФОРМА РЕДАКТИРОВАНИЯ ШОУ

# ФОРМА ДОБАВЛЕНИЯ ШОУ
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/shows/index.js'></script>
	<form id='show_form' action='/control/shows/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>Добавить шоу</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/shows/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        Перейти к списку
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/shou/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/shou/</a>

        <br /><br />
        <div class='form-group' style='width:60%'>
            <label>Директория (по-английски): <span style='color:red'>*</span></label>
            <input type='text' name='show_form_url' id='show_form_url' class='form-control form_required' data-required-label='Пжл, укажите директорию (например: legenda)' value='".$_POST['url']."' />
        </div>

        <div class='form-group' style='width:60%'>
            <label>Название: <span style='color:red'>*</span></label>
            <input type='text' name='show_form_name' id='show_form_name' class='form-control form_required' data-required-label='Пжл, укажите название по-русски' value='".$_POST['name']."' />
        </div>

        <div id='show_form_name_alert_div' class='alert alert-info hidden width_95'></div>

        <div class='form-group' style='width:90%'>
            <label>Заголовок страницы:</label>
            <input type='text' name='show_form_title' id='show_form_title' class='form-control' data-required-label='Пжл, укажите заголовок страницы' value='".$_POST['title']."' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>Строка навигации:</label>
            <input type='text' name='show_form_navigation' id='show_form_navigation' class='form-control' value='".$_POST['navigation']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>Строка навигации в ручном режиме:
                   <br />
                   <span style='font-weight:normal'>* если указана, на сайте выводится строка навигации из этого поля:</span>
            </label>
            <textarea name='show_form_full_navigation' id='show_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['show_form_full_navigation']."</textarea>
        </div>

        <div class='form-group' style='width:90%'>
            <label>Заголовок h1:</label>
            <input type='text' name='show_form_h1' id='show_form_h1' class='form-control' value='".$_POST['h1']."' />
        </div>

        <div class='form-group'>
            <label>Текст страницы:</label>
            <textarea name='show_form_text' id='show_form_text' class='form-control lined' style='width:90%;height:270px'></textarea>
        </div>

        <div class='well' style='width:85%'>

            <div class='form-group' style='width:90%'>
                <label>Текст-подсказка для слайдера на главной (<a href='/control/public/images/help_screenshot_1.png' target='_blank'>скриншот</a>):</label>
                <input type='text' name='show_form_text_for_slider_on_main_page' id='show_form_text_for_slider_on_main_page' class='form-control' value='".$_POST['show_form_text_for_slider_on_main_page']."' />
            </div>

            <div class='form-group' style='margin-bottom:5px'>
                <label>Картинка для слайдера на главной<br /><span style='font-weight:normal'>* если не указана, в слайдере на главной не выводится</span>:</label>
                <br /> <input id='show_form_image_for_slider_on_main_page' name='show_form_image_for_slider_on_main_page' type='file' style='display:inline-block' />
            </div>

        </div>

        <div class='form-group' style='width:60%'>
            <label>Порядок вывода в листинге:</label> &nbsp;
            <input type='text' name='show_form_element_order_listing' id='show_form_element_order_listing' class='form-control' data-required-label='' value='".$_POST['show_form_element_order_listing']."' style='display:inline-block;width:100px' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>Анкор для перелинковки в подвале:</label> &nbsp;
            <textarea name='show_form_footeranchor' id='show_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$_POST['show_form_footeranchor']."</textarea>
        </div>

        <div class='form-group' style='margin-bottom:0'>
            <label>
                <input type='checkbox' name='show_form_is_showable' id='show_form_is_showable' class='form_checkbox' checked='checekd' />&nbsp; Отображать шоу на сайте
            </label>
        </div>
        
        <br />
        
        <button class='btn btn-primary submit_button' type='submit'>Добавить шоу</button>
	</form>
	";
} # /ФОРМА ДОБАВЛЕНИЯ НОВОСТИ

# ДОБАВЛЯЕМ ШОУ В БД
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# защита от прямого запроса URL'а: http://kupi-krovat.ru/control/shows/?action=addItemSubmit
	if (!empty($_POST))
	{
        # проверка + нужная кодировка POST-переменных
        preparePOSTVariables(); # print_r($_POST); exit;

		# добавляем шоу в БД
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# если шоу успешно добавлено
		if (!empty($lastInsertID)) {
            # копируем картинку # print_r($_FILES);
            if (!empty($_FILES['show_form_image_for_slider_on_main_page']['tmp_name']))
            {
                copyImage(array(
                    'itemID' => $lastInsertID,
                    'imageFormName' => 'show_form_image_for_slider_on_main_page',
                    'imageDbColumnName' => 'image_for_slider_on_main_page',
                    'imagePrefix' => ''
                ));
            } # /копируем картинку

			# сохраняем текст в файл
			saveContentToFile($lastInsertID,
							  $_POST['show_form_text']);
            
			# делаем перенаправление на форму редактирования
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/shows/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# если возникла ошибка и шоу не добавлено
		else {
            $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и шоу не добавлено. Пожалуйста, обратитесь к разработчикам сайта.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# если набран: /control/shows/addItemSubmit/ и при этом $_POST пустой
	else
	{
		# выводим список шоу
        $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и шоу не добавлено. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /ДОБАВЛЯЕМ ШОУ В БД

# УДАЛЯЕМ ШОУ
function deleteItem(){
	
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID'])) {
		# выводим ошибку
		$GLOBALS['tpl_failure'] = 'Шоу не удалено. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# выводим список шоу
        showItems();
	}
	else {
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# удаляем шоу из БД
        $sql = '
        delete from `'.DB_PREFIX.'shows`
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = 'Шоу успешно удалено.';

            # удаляем картинку
            if (!empty($itemInfo['image_for_slider_on_main_page'])) {
                $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$itemInfo['image_for_slider_on_main_page'];
                if (file_exists($fullPathToImage) && is_file($fullPathToImage)) unlink($fullPathToImage);
            }

            # удаляем файл шоу
            if (!empty($itemInfo['file_name'])) {
                $fullPathToFile = $_SERVER['DOCUMENT_ROOT'].'/app/site_sections_shows/'.basename($itemInfo['file_name']);
                if (is_file($fullPathToFile)) unlink($fullPathToFile);
            }

            # уадялем backup'ы
            $sql = '
            delete from '.DB_PREFIX.'backups
            where table_name = "faq"
                  and entry_id = :entry_id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':entry_id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            
			# выводим список шоу
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = 'К сожалению, шоу не удалено. Пожалуйста, обратитесь к разработчикам сайта.';
			# выводим список шоу
			return showItems();
		}
	}
} # /УДАЛЯЕМ ШОУ

# ДОБАВЛЯЕМ ШОУ В БД
function addItemToDB()
{
	global $dbh;
	
	if (!empty($_POST['show_form_name']))
	{ 
        $sql = '
        insert into `'.DB_PREFIX.'shows`
        (name, 
         url,
         title,
         full_navigation,
         navigation,
         h1,
         text_for_slider_on_main_page,
         footeranchor,
         element_order_listing,
         is_showable)
        values
        (:name,
         :url,
         :title,
         :full_navigation,
         :navigation,
         :h1,
         :text_for_slider_on_main_page,
         :footeranchor,
         :element_order_listing,
         :is_showable)
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':name', $_POST['show_form_name']);
        $sth->bindParam(':url', $_POST['show_form_url']);
        $sth->bindParam(':title', $_POST['show_form_title']);
        $sth->bindParam(':navigation', $_POST['show_form_navigation']);
        # full_navigation
        if (empty($_POST['show_form_full_navigation'])) $_POST['show_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['show_form_full_navigation']);
        $sth->bindParam(':h1', $_POST['show_form_h1']);
        # text_for_slider_on_main_page
        $sth->bindValue(':text_for_slider_on_main_page', !empty($_POST['show_form_text_for_slider_on_main_page']) ? $_POST['show_form_text_for_slider_on_main_page'] : null);
        # footeranchor
        if ($_POST['show_form_footeranchor'] == '') $_POST['show_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['show_form_footeranchor']);
        # element_order_listing
        $sth->bindValue(':element_order_listing', !empty($_POST['show_form_element_order_listing']) ? $_POST['show_form_element_order_listing'] : null);
        # is_showable
        $isShowable = !empty($_POST['show_form_is_showable']) ? 1 : NULL;
        $sth->bindParam(':is_showable', $isShowable, PDO::PARAM_INT);
		try { if ($sth->execute()) {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			if (!empty($last_insert_id)) {
                # фиксируем имя файла шоу
                $sql = '
                update `'.DB_PREFIX.'shows`
                set file_name = :file_name
                where id = :id
                '; # echo '<pre>'.$sql."</pre><hr />";
                $sth = $dbh->prepare($sql);
                # file_name
                $file_name = $last_insert_id.'.php';
                $sth->bindParam(':file_name', $file_name);
                $sth->bindParam(':id', $last_insert_id, PDO::PARAM_INT);
                $sth->execute();
                # /фиксируем имя файла шоу
                
                return $last_insert_id;
            }
			else return;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; }}
	}
    else echo 'В метод addItemToDB не передано show_form_name.';
} # /ДОБАВЛЯЕМ ШОУ В БД

# ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ
function getItemInfo()
{
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID'])) return;
	
	$sql = '
	select *
	from `'.DB_PREFIX.'shows`
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $itemInfo = $sth->fetch();
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ

# СОХРАНЯЕМ КОНТЕНТ В ФАЙЛ
function saveContentToFile($itemID,
						   $text)
{
    /*
    echo 'itemID: '.$itemID.'<br />';
    echo 'text: '.$text.'<br />';
    */

	# проверка переменных
	if (empty($itemID)) return;
	if (empty($text)) return;
	
	$fullPathToFile = $_SERVER['DOCUMENT_ROOT'].'/app/site_sections_shows/'.basename($itemID.'.php'); # echo 'fullPathToNewFile: '.$fullPathToNewFile.'<br />';
    
    file_put_contents($fullPathToFile, $text, LOCK_EX);
    if (is_file($fullPathToFile)) chmod($fullPathToFile, 0755);
} # /СОХРАНЯЕМ КОНТЕНТ В ФАЙЛ

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
		from '.DB_PREFIX.'shows
		where id = :id
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $array['itemID'], PDO::PARAM_INT);
        $sth->execute();
        $_ = $sth->fetchColumn();
        if (!empty($_)) {
            $oldImage = $_;
            # удаляем из БД
            $sql = '
			update '.DB_PREFIX.'shows
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
        if (move_uploaded_file($_FILES[$array['imageFormName']]['tmp_name'], $fullPathToUpload)) {
            # пишем инфу в БД
            $sql = '
			update '.DB_PREFIX.'shows
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

    if (file_exists($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath']).$array['imageName']) {
        $imageInfo = @getimagesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']); # echo '<pre>'.(print_r($imageInfo, true)).'</pre>';
        $imageSize = @filesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']);
        $imageSize = @round($imageSize / 1024, 1);

        return '
		Путь: <a href="'.$GLOBALS['imagesPath'].$array['imageName'].'" target="_blank">'.$_SERVER['HTTP_HOST'].$GLOBALS['imagesPath'].$array['imageName'].'</a>
		<br />Вес: '.$imageSize.' кб.
		<br />Размер: '.$imageInfo[0].'px x '.$imageInfo[1].'px
		<br /><br />
		<a href="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" target="_blank"><img src="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" border="0" /></a>
        <br /><a href="/control/shows/?action=editItem&itemID='.$_GET['itemID'].'&subaction=remove_photo&db_column_name='.$array['imageDbColumnName'].'" onclick="return confirm(\'Удалить картинку?\');">Удалить картинку</a>
		';
        # <hr style="border:none;background-color:#ccc;color:#ccc;height:1px" />
    }
} # /ВЫВОДИМ ИНФУ ПО КАРТИНКЕ

# /ФУНКЦИОНАЛ