<?php 
# Модуль админки для работы с фотографиями (таблица photos)
# romanov.egor@gmail.com; 2015.10.22

# подключаем файл конфига
include('../loader.control.php');

# подключаем общие функции для index.php и ajax.php
include('common.functions.php');

# НАСТРОЙКИ
$GLOBALS['tpl_title'] = 'Фотографии';
$GLOBALS['imagesPath'] = '/public/images/photos/';

# ЗАЩИТА
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ЛОГИКА
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > добавляем фотографию';
    $GLOBALS['tpl_h1'] = 'Добавляем фотографию';
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > добавляем фотографию';
    $GLOBALS['tpl_h1'] = 'Добавляем фотографию';
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > редактируем фотографию';
    $GLOBALS['tpl_h1'] = 'Редактируем фотографию';
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > удаляем фотографию';
    $GLOBALS['tpl_h1'] = 'Удаляем фотографию';
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > все фотографии';
    $GLOBALS['tpl_h1'] = 'Все фотографии ('.$dbh->query('select count(1) from `'.DB_PREFIX.'photos`')->fetchColumn().')';
    $GLOBALS['tpl_content'] = showItems(); 
}
# /ЛОГИКА

# выводим главный шаблон
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ФУНКЦИОНАЛ

# ФОРМИРУЕМ СПИСОК ВСЕХ ФОТОГРАФИЙ
function showItems($count = null)
{
    global $dbh;

    # сортировка по фотоальбому
    if (!empty($_GET['photoalbum'])) $sqlModifier = ' and t1.photoalbum_id = '.(int)$_GET['photoalbum'].' ';
    else unset($sqlModifier);
    
    # получаем список фотографий
    $sql = '
    select t1.id,
           t1.name,
           t1.url,
           t1.image,
           t1.is_showable,
           t2.url as photoalbum_url,
           t2.name as photoalbum_name
    from '.DB_PREFIX.'photos as t1
    left outer join photoalbums as t2
        on t2.id = t1.photoalbum_id
    where 1 '.$sqlModifier.'
    order by t2.name,
             length(t1.name),
             t1.name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sql_for_count = '
    select count(id)
    from `'.DB_PREFIX.'photos` as t1
    where 1 '.$sqlModifier.'
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # текущая страница
					   25, # записей на страницу
					   $dbh, # объект базы данных
                       '', # routeVars
					   $sql, # sql-запрос
					   $sql_for_count, # sql-запрос для подсчета количества записей
					   '/control/photos/', # ссыка на 1ю страницу
					   '/control/photos/?page=%page%', # ссыка на остальные страницы
						1500 # максимальное количество записей на страницу
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">Страницы: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # ссылка
        $link = '<a href="/foto/'.$_[$i]['photoalbum_url'].'/'.$_[$i]['url'].'/" target="_blank">смотреть</a>';
        
        # is_showable
        if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
        else unset($trClass);

        # image
        if (!empty($_[$i]['image'])) $is_image = 'да';
        else $is_image = '&nbsp;';
        
        $rows[] = '
		<tr'.$trClass.'>
            <td class="center vertical_middle">
                <a class="block" href="/control/photos/?action=editItem&itemID='.$_[$i]['id'].'">
                    <i class="fa fa-edit size_18"></i>
                </a>
            </td>
			<td class="center vertical_middle">'.$link.'</td>
			<td>'.$_[$i]['name'].'</td>
			<td class="center">'.$_[$i]['photoalbum_name'].'</td>
			<td class="center">'.$is_image.'</td>
			<td class="center vertical_middle">
                <a class="block" title="Удалить фотографию" href="/control/photos/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'Фотография будет удалено безвозвратно. Удалить фотографию?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    }
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);

    # сортировка по фотоальбомам
    $tmp = getSortingByPhotoalbums(); # echo '<pre>'.(print_r($sortingByPhotoalbums, true)).'</pre>'; # exit;
    if (!empty($tmp)) {
        $sortingByPhotoalbums = array();
        foreach ($tmp as $item) {
            # selected
            if ($_GET['photoalbum'] == $item['photoalbum_id']) $selected = ' class="active"';
            else unset($selected);

            $sortingByPhotoalbums[] = '<a href="/control/photos/?photoalbum='.$item['photoalbum_id'].'"'.$selected.'>'.$item['name'].' ('.$item['items_count'].')</a>';
        }
        if (!empty($sortingByPhotoalbums) && is_array($sortingByPhotoalbums)) $sortingByPhotoalbums = implode(' <span style="color:#ccc">|</span> '.PHP_EOL, $sortingByPhotoalbums);
    }
    
    $result = '
	<script type="text/javascript" src="/control/photos/index.js"></script>
	
    <div style="width:50%;float:left">
        <b>URL:</b>&nbsp; <a href="/foto/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/foto/</a>
    </div>
    <div style="width:50%;float:right;text-align:right;padding-right:15px">
        Поиск по названию: &nbsp;
        <input id="search" class="form-control form_required" type="text" value="" style="display:inline-block;width:150px" />
    </div>
    <br style="clear:both" />

    <!-- Сортировка -->
    <div class="sorting"><b>Сортировка:</b>
    '.$sortingByPhotoalbums.'
    </div>
    <!-- /Сортировка -->
    
    <div class="center" style="margin-bottom:15px">
        <a href="/control/photos/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    Добавить фотографию
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= 'В системе не задана ни одна фотография.';
    else {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">Правка</th>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">Ссылка</th>
                <th class="center vertical_middle">Название</th>
                <th class="center vertical_middle" style="width:150px">Фотоальбом</th>
                <th class="center vertical_middle" style="width:175px">Картинка</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">Удаление</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # / ФОРМИРУЕМ СПИСОК ВСЕХ ФОТОГРАФИЙ

# ФОРМА РЕДАКТИРОВАНИЯ ФОТОГРАФИИ
function showEditForm()
{
    global $dbh;
    
    $showEditForm = 1;

    # выводим сообщение
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = 'Фотография успешно добавлена.';
    
    # сохраняем изменения в бд
    if ($_GET['subaction'] == 'submit' && !empty($_POST)) {
        $sql = '
        update `'.DB_PREFIX.'photos`
        set photoalbum_id = :photoalbum_id,
            name = :name,
            url = :url,
            title = :title,
            navigation = :navigation,
            full_navigation = :full_navigation,
            h1 = :h1,
            text = :text,
            anchor1 = :anchor1,
            anchor2 = :anchor2,
            footeranchor = :footeranchor,
            is_showable = :is_showable
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':photoalbum_id', $_POST['photo_form_photoalbum_id']);
        $sth->bindParam(':name', $_POST['photo_form_name']);
        $sth->bindParam(':url', $_POST['photo_form_url']);
        $sth->bindParam(':title', $_POST['photo_form_title']);
        $sth->bindParam(':navigation', $_POST['photo_form_navigation']);
        # full_navigation
        if (empty($_POST['photo_form_full_navigation'])) $_POST['photo_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['photo_form_full_navigation']);
        $sth->bindParam(':h1', $_POST['photo_form_h1']);
        $sth->bindParam(':text', $_POST['photo_form_text']);
        # anchor 1
        $sth->bindValue(':anchor1', !empty($_POST['photo_form_anchor1']) ? $_POST['photo_form_anchor1'] : null);
        # anchor 2
        $sth->bindValue(':anchor2', !empty($_POST['photo_form_anchor2']) ? $_POST['photo_form_anchor2'] : null);
        # is_showable
        $isShowable = !empty($_POST['photo_form_is_showable']) ? 1 : NULL;
        $sth->bindParam(':is_showable', $isShowable, PDO::PARAM_INT);
        # footeranchor
        if ($_POST['photo_form_footeranchor'] == '') $_POST['photo_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['photo_form_footeranchor']);
        # id
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
            $GLOBALS['tpl_success'] = 'Информация сохранена.';

            # копируем маленькую картинку # print_r($_FILES);
            if (!empty($_FILES['photo_form_image']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $_GET['itemID'],
                    'imageFormName' => 'photo_form_image',
                    'imageDbColumnName' => 'image',
                    'imagePrefix' => '_small'
                ));
            } # /копируем маленькую картинку

            # копируем большую картинку # print_r($_FILES);
            if (!empty($_FILES['photo_form_image_large']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $_GET['itemID'],
                    'imageFormName' => 'photo_form_image_large',
                    'imageDbColumnName' => 'image_large',
                    'imagePrefix' => '_large'
                ));
            } # /копируем большую картинку
        }
        else {
            $GLOBALS['tpl_failure'] = 'К сожалению, информация не сохранена. Пжл, обратитесь к разработчикам сайта.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
        }
    } # /сохраняем изменения в бд

    # удаляем указанную картинку
    if ($_GET['subaction'] == 'remove_photo') {
        # проверка переменных
        $allowedCoumns = array('image', 'image_large');
        if (empty($_GET['itemID'])) $GLOBALS['tpl_failure'] = 'Не передан ID записи.';
        elseif (empty($_GET['db_column_name'])) $GLOBALS['tpl_failure'] = 'Неверно передано название столбца картинки.';
        elseif (!in_array($_GET['db_column_name'], $allowedCoumns)) $GLOBALS['tpl_failure'] = 'Неверно передано название столбца картинки.';
        else {
            # получаем инофрмацию о картинке
            $sql = '
            select '.$_GET['db_column_name'].'
            from '.DB_PREFIX.'photos
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
            update '.DB_PREFIX.'photos
            set '.$_GET['db_column_name'].' = NULL
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
            if ($sth->execute()) {
                $GLOBALS['tpl_success'] = 'Картинка успешно удалена.';
                $_POST['tabs_state'] = 4;
            }
            else $GLOBALS['tpl_failure'] = 'К сожалению, картинка не удалена. Пжл, обратитесь к разработчикам сайта.';
        }
    } # /удаляем указанную картинку

    # выводим форму редактирования
    if ($showEditForm) {
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # защита
        if (!$itemInfo['id']) exit('
		Не существует записи с ID='.$_GET['itemID'].'
		<br /><a href="/control/photos/">Перейти к списку фотографию</a>
		');
        
        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);

        # получаем и выводим инфу по фото
        $imageInfo = showPhotoInfo(array('imageName' => $itemInfo['image'], 'imageDbColumnName' => 'image'));

        # получаем и выводим инфу по фото
        $imageLargeInfo = showPhotoInfo(array('imageName' => $itemInfo['image_large'], 'imageDbColumnName' => 'image_large'));

        return "
		<script type='text/javascript' src='/control/photos/index.js'></script>
		<form id='show_form' action='/control/photos/?action=editItem&itemID=".$itemInfo['id']."&subaction=submit' name='show_form' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='btn btn-primary submit_button' type='submit'>Сохранить информацию</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/photos/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            Перейти к списку
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/photos/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            Добавить фотографию
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/photos/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"Фотография будет удален безвозвратно. Удалить фотографию?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> Удалить фотографию</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/foto/".$itemInfo['photoalbum_url']."/".$itemInfo['url']."/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/foto/".$itemInfo['photoalbum_url']."/".$itemInfo['url']."/</a>
            
            <br><br>
            <div class='form-group' style='width:60%'>
                <label>Фотоальбом:</label> &nbsp; ".buildPhotoalbumsSelect($itemInfo['photoalbum_id'])."
            </div>

            <div class='form-group' style='width:60%'>
                <label>Директория (по-английски): <span style='color:red'>*</span></label>
                <input type='text' name='photo_form_url' id='photo_form_url' class='form-control form_required' data-required-label='Пжл, укажите директорию (например: legenda)' value='".$itemInfo['url']."' />
            </div>

            <div class='form-group' style='width:60%'>
                <label>Название: <span style='color:red'>*</span></label>
                <input type='text' name='photo_form_name' id='photo_form_name' class='form-control form_required' data-required-label='Пжл, укажите название по-русски' value='".$itemInfo['name']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>Заголовок страницы:</label>
                <input type='text' name='photo_form_title' id='photo_form_title' class='form-control' data-required-label='Пжл, укажите заголовок страницы' value='".$itemInfo['title']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>Строка навигации:</label>
                <input type='text' name='photo_form_navigation' id='photo_form_navigation' class='form-control' value='".$itemInfo['navigation']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>Строка навигации в ручном режиме:
                       <br />
                       <span style='font-weight:normal'>* если указана, на сайте выводится строка навигации из этого поля:</span>
                </label>
                <textarea name='photo_form_full_navigation' id='photo_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>

            <div class='form-group' style='width:90%'>
                <label>Заголовок h1:</label>
                <input type='text' name='photo_form_h1' id='photo_form_h1' class='form-control' value='".$itemInfo['h1']."' />
            </div>

            <div class='form-group'>
                <label>Текст страницы:</label>
                <textarea name='photo_form_text' id='photo_form_text' class='form-control lined' style='width:90%;height:270px'>".$itemInfo['text']."</textarea>
            </div>

            <div class='form-group'>
                <label>Картинка маленькая:</label>
                <br /> <input id='photo_form_image' name='photo_form_image' type='file' style='display:inline-block' />
            </div>

            ".$imageInfo."

            <div class='form-group'>
                <label>Картинка большая:</label>
                <br /> <input id='photo_form_image_large' name='photo_form_image_large' type='file' style='display:inline-block' />
            </div>

            ".$imageLargeInfo."

            <div class='form-group' style='width:90%'>
                <label>Анкор 1 в модальном окне (ссылка на главную, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>скриншот</a>):</label>
                <input type='text' name='photo_form_anchor1' id='photo_form_anchor1' class='form-control' value='".$itemInfo['anchor1']."' />
            </div>

            <div class='form-group' style='width:90%'>
                <label>Анкор 2 в модальном окне (ссылка на описание шоу, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>скриншот</a>):</label>
                <input type='text' name='photo_form_anchor2' id='photo_form_anchor2' class='form-control' value='".$itemInfo['anchor2']."' />
            </div>

            <div class='form-group' style='width:95%'>
                <label>Анкор для перелинковки в подвале:</label> &nbsp;
                <textarea name='photo_form_footeranchor' id='photo_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$itemInfo['footeranchor']."</textarea>
            </div>

            <div class='form-group' style='margin-bottom:0'>
                <label class='pointer'>
                    <input type='checkbox' name='photo_form_is_showable' id='photo_form_is_showable' class='form_checkbox pointer' ".(!empty($itemInfo['is_showable']) ? 'checked="checekd"' : '')." />&nbsp; Отображать фотографию на сайте
                </label>
            </div>
            
            <br />
			<button class='btn btn-primary submit_button' type='submit' style='margin-top:5px'>Сохранить информацию</button>
            
		</form>
		";
    }
} # /ФОРМА РЕДАКТИРОВАНИЯ ФОТОГРАФИИ

# ФОРМА ДОБАВЛЕНИЯ ФОТОГРАФИИ
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/photos/index.js'></script>
	<form id='show_form' action='/control/photos/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>Добавить фотографию</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/photos/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        Перейти к списку
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/foto/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/foto/</a>

        <br><br>
        <div class='form-group' style='width:60%'>
            <label>Фотоальбом:</label> &nbsp; ".buildPhotoalbumsSelect()."
        </div>

        <div class='form-group' style='width:60%'>
            <label>Директория (по-английски): <span style='color:red'>*</span></label>
            <input type='text' name='photo_form_url' id='photo_form_url' class='form-control form_required' data-required-label='Пжл, укажите директорию (например: legenda)' value='".$_POST['url']."' />
        </div>

        <div class='form-group' style='width:60%'>
            <label>Название: <span style='color:red'>*</span></label>
            <input type='text' name='photo_form_name' id='photo_form_name' class='form-control form_required' data-required-label='Пжл, укажите название по-русски' value='".$_POST['name']."' />
        </div>

        <div id='photo_form_name_alert_div' class='alert alert-info hidden width_95'></div>

        <div class='form-group' style='width:90%'>
            <label>Заголовок страницы:</label>
            <input type='text' name='photo_form_title' id='photo_form_title' class='form-control' data-required-label='Пжл, укажите заголовок страницы' value='".$_POST['title']."' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>Строка навигации:</label>
            <input type='text' name='photo_form_navigation' id='photo_form_navigation' class='form-control' value='".$_POST['navigation']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>Строка навигации в ручном режиме:
                   <br />
                   <span style='font-weight:normal'>* если указана, на сайте выводится строка навигации из этого поля:</span>
            </label>
            <textarea name='photo_form_full_navigation' id='photo_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['photo_form_full_navigation']."</textarea>
        </div>

        <div class='form-group' style='width:90%'>
            <label>Заголовок h1:</label>
            <input type='text' name='photo_form_h1' id='photo_form_h1' class='form-control' value='".$_POST['h1']."' />
        </div>

        <div class='form-group'>
            <label>Текст страницы:</label>
            <textarea name='photo_form_text' id='photo_form_text' class='form-control lined' style='width:90%;height:270px'></textarea>
        </div>

        <div class='form-group'>
            <label>Картинка:</label>
            <br /> <input id='photo_form_image' name='photo_form_image' type='file' style='display:inline-block' />
        </div>

        <div class='form-group'>
            <label>Картинка большая:</label>
            <br /> <input id='photo_form_image_large' name='photo_form_image_large' type='file' style='display:inline-block' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>Анкор 1 в модальном окне (ссылка на главную, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>скриншот</a>):</label>
            <input type='text' name='photo_form_anchor1' id='photo_form_anchor1' class='form-control' value='".$_POST['photo_form_anchor1']."' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>Анкор 2 в модальном окне (ссылка на описание шоу, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>скриншот</a>):</label>
            <input type='text' name='photo_form_anchor2' id='photo_form_anchor2' class='form-control' value='".$_POST['photo_form_anchor2']."' />
        </div>

        <div class='form-group' style='width:95%'>
            <label>Анкор для перелинковки в подвале:</label> &nbsp;
            <textarea name='photo_form_footeranchor' id='photo_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$_POST['photo_form_footeranchor']."</textarea>
        </div>

        <div class='form-group' style='margin-bottom:0'>
            <label class='pointer'>
                <input type='checkbox' name='photo_form_is_showable' id='photo_form_is_showable' class='form_checkbox pointer' checked='checekd' />&nbsp; Отображать фотографию на сайте
            </label>
        </div>
        
        <br />
        
        <button class='btn btn-primary submit_button' type='submit'>Добавить фотографию</button>
	</form>
	";
} # /ФОРМА ДОБАВЛЕНИЯ ФОТОГРАФИИ

# ДОБАВЛЯЕМ ФОТОГРАФИЮ В БД
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# защита от прямого запроса URL'а: http://kupi-krovat.ru/control/photos/?action=addItemSubmit
	if (!empty($_POST))	{
        # проверка + нужная кодировка POST-переменных
        preparePOSTVariables(); # print_r($_POST); exit;

		# добавляем фотографию в БД
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# если фотографию успешно добавлен
		if (!empty($lastInsertID)) {
            # копируем маленькую картинку # print_r($_FILES);
            if (!empty($_FILES['photo_form_image']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $lastInsertID,
                    'imageFormName' => 'photo_form_image',
                    'imageDbColumnName' => 'image',
                    'imagePrefix' => '_small'
                ));
            } # /копируем маленькую картинку

            # копируем большую картинку # print_r($_FILES);
            if (!empty($_FILES['photo_form_image_large']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $lastInsertID,
                    'imageFormName' => 'photo_form_image_large',
                    'imageDbColumnName' => 'image_large',
                    'imagePrefix' => '_large'
                ));
            } # /копируем большую картинку

			# делаем перенаправление на форму редактирования
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/photos/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# если возникла ошибка и фотографию не добавлен
		else {
            $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и фотографию не добавлен. Пожалуйста, обратитесь к разработчикам сайта.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# если набран: /control/photos/addItemSubmit/ и при этом $_POST пустой
	else {
		# выводим список фотографию
        $GLOBALS['tpl_failure'] = 'К сожалению, возникла ошибка и фотографию не добавлен. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /ДОБАВЛЯЕМ ФОТОГРАФИЮ В БД

# УДАЛЯЕМ ФОТОГРАФИЮ
function deleteItem(){
	
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID'])) {
		# выводим ошибку
		$GLOBALS['tpl_failure'] = 'Фотография не удалено. Пожалуйста, обратитесь к разработчикам сайта.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# выводим список фотографию
        showItems();
	}
	else {
		# получаем данные по позиции
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# удаляем фотографию из БД
        $sql = '
        delete from `'.DB_PREFIX.'photos`
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = 'Фотография успешно удалено.';

            # удаляем картинку
            if (!empty($itemInfo['image'])) {
                $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$itemInfo['image'];
                if (file_exists($fullPathToImage) && is_file($fullPathToImage)) unlink($fullPathToImage);
            }

            # уадялем backup'ы
            $sql = '
            delete from '.DB_PREFIX.'backups
            where table_name = "photos"
                  and entry_id = :entry_id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':entry_id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            
			# выводим список фотографию
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = 'К сожалению, фотографию не удалено. Пожалуйста, обратитесь к разработчикам сайта.';
			# выводим список фотографию
			return showItems();
		}
	}
} # /УДАЛЯЕМ ФОТОГРАФИЮ

# ДОБАВЛЯЕМ ФОТОГРАФИЮ В БД
function addItemToDB()
{
	global $dbh;
	
	if (!empty($_POST['photo_form_name'])) {
        $sql = '
        insert into `'.DB_PREFIX.'photos`
        (photoalbum_id,
         name,
         url,
         title,
         full_navigation,
         navigation,
         h1,
         text,
         anchor1,
         anchor2,
         footeranchor,
         is_showable)
        values
        (:photoalbum_id,
         :name,
         :url,
         :title,
         :full_navigation,
         :navigation,
         :h1,
         :text,
         :anchor1,
         :anchor2,
         :footeranchor,
         :is_showable)
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':photoalbum_id', $_POST['photo_form_photoalbum_id']);
        $sth->bindParam(':name', $_POST['photo_form_name']);
        $sth->bindParam(':url', $_POST['photo_form_url']);
        $sth->bindParam(':title', $_POST['photo_form_title']);
        $sth->bindParam(':navigation', $_POST['photo_form_navigation']);
        # full_navigation
        if (empty($_POST['photo_form_full_navigation'])) $_POST['photo_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['photo_form_full_navigation']);
        $sth->bindParam(':h1', $_POST['photo_form_h1']);
        $sth->bindParam(':text', $_POST['photo_form_text']);
        # anchor 1
        $sth->bindValue(':anchor1', !empty($_POST['photo_form_anchor1']) ? $_POST['photo_form_anchor1'] : null);
        # anchor 2
        $sth->bindValue(':anchor2', !empty($_POST['photo_form_anchor2']) ? $_POST['photo_form_anchor2'] : null);
        # footeranchor
        if ($_POST['photo_form_footeranchor'] == '') $_POST['photo_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['photo_form_footeranchor']);
        # is_showable
        $isShowable = !empty($_POST['photo_form_is_showable']) ? 1 : NULL;
        $sth->bindParam(':is_showable', $isShowable, PDO::PARAM_INT);
		try { if ($sth->execute()) {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			if (!empty($last_insert_id)) return $last_insert_id;
			else return;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; }}
	}
    else echo 'В метод addItemToDB не передано photo_form_name.';
} # /ДОБАВЛЯЕМ ФОТОГРАФИЮ В БД

# ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ
function getItemInfo()
{
	global $dbh;
	
	# проверка переменных
	if (empty($_GET['itemID'])) return;
	
	$sql = '
	select t1.*,
	       t2.url as photoalbum_url
	from '.DB_PREFIX.'photos as t1
	left outer join photoalbums as t2
	    on t1.photoalbum_id = t2.id
	where t1.id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $itemInfo = $sth->fetch(); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>'; # exit;
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /ПОЛУЧАЕМ ДАННЫЕ ПО ПОЗИЦИИ

# КОПИРУЕМ КАРТИНКУ
function copyImage($array)
{
    global $dbh;

    # echo '<pre>'.(print_r($_FILES, true)).'</pre>'; # exit;
    # print_r($array);

    # проверка переменных
    if (empty($array['itemID'])) return;
    if (empty($array['imageFormName'])) return;
    if (empty($array['imageDbColumnName'])) return;
    # if (empty($array['imagePrefix'])) return;

    # echo '<pre>'.(print_r($array, true)).'</pre>';
    # echo $_FILES[$array['imageFormName']]['tmp_name'];
    if (is_uploaded_file($_FILES[$array['imageFormName']]['tmp_name'])) {
        # УДАЛЯЕМ СТАРУЮ КАРТИНКУ, ЕСЛИ ОНА ЕСТЬ
        $sql = '
		select '.$array['imageDbColumnName'].'
		from '.DB_PREFIX.'photos
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
			update '.DB_PREFIX.'photos
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
			update '.DB_PREFIX.'photos
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
        <div style="margin-bottom:15px">
		Путь: <a href="'.$GLOBALS['imagesPath'].$array['imageName'].'" target="_blank">'.$_SERVER['HTTP_HOST'].$GLOBALS['imagesPath'].$array['imageName'].'</a>
		<br />Вес: '.$imageSize.' кб.
		<br />Размер: '.$imageInfo[0].'px x '.$imageInfo[1].'px
		<br /><br />
		<a href="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" target="_blank"><img src="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" border="0" /></a>
        <br /><a href="/control/photos/?action=editItem&itemID='.$_GET['itemID'].'&subaction=remove_photo&db_column_name='.$array['imageDbColumnName'].'" onclick="return confirm(\'Удалить картинку?\');">Удалить картинку</a>
        </div>
		';
        # <hr style="border:none;background-color:#ccc;color:#ccc;height:1px" />
    }
} # /ВЫВОДИМ ИНФУ ПО КАРТИНКЕ

# ВЫВОДИМ SELECT С ФОТОАЛЬБОМАМИ
function buildPhotoalbumsSelect($idSelected = null)
{
    global $dbh;

    if (empty($idSelected)) $idSelected = $dbh->query('select max(id) from '.DB_PREFIX.'photoalbums')->fetchColumn();

    # static sections for template
    $sql = '
    select id,
           name
    from '.DB_PREFIX.'photoalbums
    order by name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $_ = $dbh->query($sql)->fetchAll(); # print_r($_);
    $_c = count($_);
    $result = array();
    if (!empty($_)) {
        for ($i=0;$i<$_c;$i++) {
            # selected
            if ($_[$i]['id'] == $idSelected) $selected = ' selected="selected"';
            else unset($selected);

            $result[] = "<option value='".$_[$i]['id']."'".$selected.'> '.$_[$i]['name'].'</option>';
        }
    }

    if (!empty($result) && is_array($result)) $result = implode(PHP_EOL, $result);

    return '<select id="photo_form_photoalbum_id" name="photo_form_photoalbum_id" class="form-control">'.$result.'</select>';
} # /ВЫВОДИМ SELECT С ФОТОАЛЬБОМАМИ

# ВЫВОДИМ СОРТИРОВКУ ПО ФОТОАЛЬБОМАМ
function getSortingByPhotoalbums()
{
    global $dbh;

    $sql = '
    select subquery.*,
           t1.name,
           (select count(1) from '.DB_PREFIX.'photos where photoalbum_id = subquery.photoalbum_id) as items_count
    from
	(select distinct photoalbum_id
	from '.DB_PREFIX.'photos) as subquery
	left outer join '.DB_PREFIX.'photoalbums as t1
	on t1.id = subquery.photoalbum_id
	order by t1.name
	'; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    try {
        if ($sth->execute()) {
            $_ = $sth->fetchAll(); # print_r($_);
            if (!empty($_)) return $_;
        }
    }
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
} # /ВЫВОДИМ СОРТИРОВКУ ПО ФОТОАЛЬБОМАМ

# /ФУНКЦИОНАЛ