<?php 
# ������ ������� ��� ������ � ������������ (������� photos)
# romanov.egor@gmail.com; 2015.10.22

# ���������� ���� �������
include('../loader.control.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '����������';
$GLOBALS['imagesPath'] = '/public/images/photos/';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > ��������� ����������';
    $GLOBALS['tpl_h1'] = '��������� ����������';
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > ��������� ����������';
    $GLOBALS['tpl_h1'] = '��������� ����������';
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > ����������� ����������';
    $GLOBALS['tpl_h1'] = '����������� ����������';
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > ������� ����������';
    $GLOBALS['tpl_h1'] = '������� ����������';
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > ��� ����������';
    $GLOBALS['tpl_h1'] = '��� ���������� ('.$dbh->query('select count(1) from `'.DB_PREFIX.'photos`')->fetchColumn().')';
    $GLOBALS['tpl_content'] = showItems(); 
}
# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ��������� ������ ���� ����������
function showItems($count = null)
{
    global $dbh;

    # ���������� �� �����������
    if (!empty($_GET['photoalbum'])) $sqlModifier = ' and t1.photoalbum_id = '.(int)$_GET['photoalbum'].' ';
    else unset($sqlModifier);
    
    # �������� ������ ����������
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
	$pages = new pages($_GET["page"], # ������� ��������
					   25, # ������� �� ��������
					   $dbh, # ������ ���� ������
                       '', # routeVars
					   $sql, # sql-������
					   $sql_for_count, # sql-������ ��� �������� ���������� �������
					   '/control/photos/', # ����� �� 1� ��������
					   '/control/photos/?page=%page%', # ����� �� ��������� ��������
						1500 # ������������ ���������� ������� �� ��������
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">��������: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # ������
        $link = '<a href="/foto/'.$_[$i]['photoalbum_url'].'/'.$_[$i]['url'].'/" target="_blank">��������</a>';
        
        # is_showable
        if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
        else unset($trClass);

        # image
        if (!empty($_[$i]['image'])) $is_image = '��';
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
                <a class="block" title="������� ����������" href="/control/photos/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'���������� ����� ������� ������������. ������� ����������?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    }
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);

    # ���������� �� ������������
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
        ����� �� ��������: &nbsp;
        <input id="search" class="form-control form_required" type="text" value="" style="display:inline-block;width:150px" />
    </div>
    <br style="clear:both" />

    <!-- ���������� -->
    <div class="sorting"><b>����������:</b>
    '.$sortingByPhotoalbums.'
    </div>
    <!-- /���������� -->
    
    <div class="center" style="margin-bottom:15px">
        <a href="/control/photos/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    �������� ����������
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= '� ������� �� ������ �� ���� ����������.';
    else {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                <th class="center vertical_middle">��������</th>
                <th class="center vertical_middle" style="width:150px">����������</th>
                <th class="center vertical_middle" style="width:175px">��������</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">��������</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # / ��������� ������ ���� ����������

# ����� �������������� ����������
function showEditForm()
{
    global $dbh;
    
    $showEditForm = 1;

    # ������� ���������
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = '���������� ������� ���������.';
    
    # ��������� ��������� � ��
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
            $GLOBALS['tpl_success'] = '���������� ���������.';

            # �������� ��������� �������� # print_r($_FILES);
            if (!empty($_FILES['photo_form_image']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $_GET['itemID'],
                    'imageFormName' => 'photo_form_image',
                    'imageDbColumnName' => 'image',
                    'imagePrefix' => '_small'
                ));
            } # /�������� ��������� ��������

            # �������� ������� �������� # print_r($_FILES);
            if (!empty($_FILES['photo_form_image_large']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $_GET['itemID'],
                    'imageFormName' => 'photo_form_image_large',
                    'imageDbColumnName' => 'image_large',
                    'imagePrefix' => '_large'
                ));
            } # /�������� ������� ��������
        }
        else {
            $GLOBALS['tpl_failure'] = '� ���������, ���������� �� ���������. ���, ���������� � ������������� �����.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
        }
    } # /��������� ��������� � ��

    # ������� ��������� ��������
    if ($_GET['subaction'] == 'remove_photo') {
        # �������� ����������
        $allowedCoumns = array('image', 'image_large');
        if (empty($_GET['itemID'])) $GLOBALS['tpl_failure'] = '�� ������� ID ������.';
        elseif (empty($_GET['db_column_name'])) $GLOBALS['tpl_failure'] = '������� �������� �������� ������� ��������.';
        elseif (!in_array($_GET['db_column_name'], $allowedCoumns)) $GLOBALS['tpl_failure'] = '������� �������� �������� ������� ��������.';
        else {
            # �������� ���������� � ��������
            $sql = '
            select '.$_GET['db_column_name'].'
            from '.DB_PREFIX.'photos
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            $columnName = $sth->fetchColumn(); # echo 'columnName: '.$columnName;
            # ������� ��������
            $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$columnName; # echo 'fullPathToImage: '.$fullPathToImage;
            if (!empty($fullPathToImage) && file_exists($fullPathToImage)) unlink($fullPathToImage);
            # ������ ��������� � ��
            $sql = '
            update '.DB_PREFIX.'photos
            set '.$_GET['db_column_name'].' = NULL
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
            if ($sth->execute()) {
                $GLOBALS['tpl_success'] = '�������� ������� �������.';
                $_POST['tabs_state'] = 4;
            }
            else $GLOBALS['tpl_failure'] = '� ���������, �������� �� �������. ���, ���������� � ������������� �����.';
        }
    } # /������� ��������� ��������

    # ������� ����� ��������������
    if ($showEditForm) {
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # ������
        if (!$itemInfo['id']) exit('
		�� ���������� ������ � ID='.$_GET['itemID'].'
		<br /><a href="/control/photos/">������� � ������ ����������</a>
		');
        
        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);

        # �������� � ������� ���� �� ����
        $imageInfo = showPhotoInfo(array('imageName' => $itemInfo['image'], 'imageDbColumnName' => 'image'));

        # �������� � ������� ���� �� ����
        $imageLargeInfo = showPhotoInfo(array('imageName' => $itemInfo['image_large'], 'imageDbColumnName' => 'image_large'));

        return "
		<script type='text/javascript' src='/control/photos/index.js'></script>
		<form id='show_form' action='/control/photos/?action=editItem&itemID=".$itemInfo['id']."&subaction=submit' name='show_form' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='btn btn-primary submit_button' type='submit'>��������� ����������</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/photos/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            ������� � ������
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/photos/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            �������� ����������
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/photos/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"���������� ����� ������ ������������. ������� ����������?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> ������� ����������</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/foto/".$itemInfo['photoalbum_url']."/".$itemInfo['url']."/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/foto/".$itemInfo['photoalbum_url']."/".$itemInfo['url']."/</a>
            
            <br><br>
            <div class='form-group' style='width:60%'>
                <label>����������:</label> &nbsp; ".buildPhotoalbumsSelect($itemInfo['photoalbum_id'])."
            </div>

            <div class='form-group' style='width:60%'>
                <label>���������� (��-���������): <span style='color:red'>*</span></label>
                <input type='text' name='photo_form_url' id='photo_form_url' class='form-control form_required' data-required-label='���, ������� ���������� (��������: legenda)' value='".$itemInfo['url']."' />
            </div>

            <div class='form-group' style='width:60%'>
                <label>��������: <span style='color:red'>*</span></label>
                <input type='text' name='photo_form_name' id='photo_form_name' class='form-control form_required' data-required-label='���, ������� �������� ��-������' value='".$itemInfo['name']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>��������� ��������:</label>
                <input type='text' name='photo_form_title' id='photo_form_title' class='form-control' data-required-label='���, ������� ��������� ��������' value='".$itemInfo['title']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>������ ���������:</label>
                <input type='text' name='photo_form_navigation' id='photo_form_navigation' class='form-control' value='".$itemInfo['navigation']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>������ ��������� � ������ ������:
                       <br />
                       <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
                </label>
                <textarea name='photo_form_full_navigation' id='photo_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>

            <div class='form-group' style='width:90%'>
                <label>��������� h1:</label>
                <input type='text' name='photo_form_h1' id='photo_form_h1' class='form-control' value='".$itemInfo['h1']."' />
            </div>

            <div class='form-group'>
                <label>����� ��������:</label>
                <textarea name='photo_form_text' id='photo_form_text' class='form-control lined' style='width:90%;height:270px'>".$itemInfo['text']."</textarea>
            </div>

            <div class='form-group'>
                <label>�������� ���������:</label>
                <br /> <input id='photo_form_image' name='photo_form_image' type='file' style='display:inline-block' />
            </div>

            ".$imageInfo."

            <div class='form-group'>
                <label>�������� �������:</label>
                <br /> <input id='photo_form_image_large' name='photo_form_image_large' type='file' style='display:inline-block' />
            </div>

            ".$imageLargeInfo."

            <div class='form-group' style='width:90%'>
                <label>����� 1 � ��������� ���� (������ �� �������, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>��������</a>):</label>
                <input type='text' name='photo_form_anchor1' id='photo_form_anchor1' class='form-control' value='".$itemInfo['anchor1']."' />
            </div>

            <div class='form-group' style='width:90%'>
                <label>����� 2 � ��������� ���� (������ �� �������� ���, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>��������</a>):</label>
                <input type='text' name='photo_form_anchor2' id='photo_form_anchor2' class='form-control' value='".$itemInfo['anchor2']."' />
            </div>

            <div class='form-group' style='width:95%'>
                <label>����� ��� ������������ � �������:</label> &nbsp;
                <textarea name='photo_form_footeranchor' id='photo_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$itemInfo['footeranchor']."</textarea>
            </div>

            <div class='form-group' style='margin-bottom:0'>
                <label class='pointer'>
                    <input type='checkbox' name='photo_form_is_showable' id='photo_form_is_showable' class='form_checkbox pointer' ".(!empty($itemInfo['is_showable']) ? 'checked="checekd"' : '')." />&nbsp; ���������� ���������� �� �����
                </label>
            </div>
            
            <br />
			<button class='btn btn-primary submit_button' type='submit' style='margin-top:5px'>��������� ����������</button>
            
		</form>
		";
    }
} # /����� �������������� ����������

# ����� ���������� ����������
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/photos/index.js'></script>
	<form id='show_form' action='/control/photos/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>�������� ����������</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/photos/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        ������� � ������
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/foto/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/foto/</a>

        <br><br>
        <div class='form-group' style='width:60%'>
            <label>����������:</label> &nbsp; ".buildPhotoalbumsSelect()."
        </div>

        <div class='form-group' style='width:60%'>
            <label>���������� (��-���������): <span style='color:red'>*</span></label>
            <input type='text' name='photo_form_url' id='photo_form_url' class='form-control form_required' data-required-label='���, ������� ���������� (��������: legenda)' value='".$_POST['url']."' />
        </div>

        <div class='form-group' style='width:60%'>
            <label>��������: <span style='color:red'>*</span></label>
            <input type='text' name='photo_form_name' id='photo_form_name' class='form-control form_required' data-required-label='���, ������� �������� ��-������' value='".$_POST['name']."' />
        </div>

        <div id='photo_form_name_alert_div' class='alert alert-info hidden width_95'></div>

        <div class='form-group' style='width:90%'>
            <label>��������� ��������:</label>
            <input type='text' name='photo_form_title' id='photo_form_title' class='form-control' data-required-label='���, ������� ��������� ��������' value='".$_POST['title']."' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>������ ���������:</label>
            <input type='text' name='photo_form_navigation' id='photo_form_navigation' class='form-control' value='".$_POST['navigation']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>������ ��������� � ������ ������:
                   <br />
                   <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
            </label>
            <textarea name='photo_form_full_navigation' id='photo_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['photo_form_full_navigation']."</textarea>
        </div>

        <div class='form-group' style='width:90%'>
            <label>��������� h1:</label>
            <input type='text' name='photo_form_h1' id='photo_form_h1' class='form-control' value='".$_POST['h1']."' />
        </div>

        <div class='form-group'>
            <label>����� ��������:</label>
            <textarea name='photo_form_text' id='photo_form_text' class='form-control lined' style='width:90%;height:270px'></textarea>
        </div>

        <div class='form-group'>
            <label>��������:</label>
            <br /> <input id='photo_form_image' name='photo_form_image' type='file' style='display:inline-block' />
        </div>

        <div class='form-group'>
            <label>�������� �������:</label>
            <br /> <input id='photo_form_image_large' name='photo_form_image_large' type='file' style='display:inline-block' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>����� 1 � ��������� ���� (������ �� �������, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>��������</a>):</label>
            <input type='text' name='photo_form_anchor1' id='photo_form_anchor1' class='form-control' value='".$_POST['photo_form_anchor1']."' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>����� 2 � ��������� ���� (������ �� �������� ���, <a href='/control/public/images/help_screenshot_2.png' target='_blank'>��������</a>):</label>
            <input type='text' name='photo_form_anchor2' id='photo_form_anchor2' class='form-control' value='".$_POST['photo_form_anchor2']."' />
        </div>

        <div class='form-group' style='width:95%'>
            <label>����� ��� ������������ � �������:</label> &nbsp;
            <textarea name='photo_form_footeranchor' id='photo_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$_POST['photo_form_footeranchor']."</textarea>
        </div>

        <div class='form-group' style='margin-bottom:0'>
            <label class='pointer'>
                <input type='checkbox' name='photo_form_is_showable' id='photo_form_is_showable' class='form_checkbox pointer' checked='checekd' />&nbsp; ���������� ���������� �� �����
            </label>
        </div>
        
        <br />
        
        <button class='btn btn-primary submit_button' type='submit'>�������� ����������</button>
	</form>
	";
} # /����� ���������� ����������

# ��������� ���������� � ��
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# ������ �� ������� ������� URL'�: http://kupi-krovat.ru/control/photos/?action=addItemSubmit
	if (!empty($_POST))	{
        # �������� + ������ ��������� POST-����������
        preparePOSTVariables(); # print_r($_POST); exit;

		# ��������� ���������� � ��
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# ���� ���������� ������� ��������
		if (!empty($lastInsertID)) {
            # �������� ��������� �������� # print_r($_FILES);
            if (!empty($_FILES['photo_form_image']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $lastInsertID,
                    'imageFormName' => 'photo_form_image',
                    'imageDbColumnName' => 'image',
                    'imagePrefix' => '_small'
                ));
            } # /�������� ��������� ��������

            # �������� ������� �������� # print_r($_FILES);
            if (!empty($_FILES['photo_form_image_large']['tmp_name'])) {
                copyImage(array(
                    'itemID' => $lastInsertID,
                    'imageFormName' => 'photo_form_image_large',
                    'imageDbColumnName' => 'image_large',
                    'imagePrefix' => '_large'
                ));
            } # /�������� ������� ��������

			# ������ ��������������� �� ����� ��������������
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/photos/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# ���� �������� ������ � ���������� �� ��������
		else {
            $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ���������� �� ��������. ����������, ���������� � ������������� �����.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# ���� ������: /control/photos/addItemSubmit/ � ��� ���� $_POST ������
	else {
		# ������� ������ ����������
        $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ���������� �� ��������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /��������� ���������� � ��

# ������� ����������
function deleteItem(){
	
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID'])) {
		# ������� ������
		$GLOBALS['tpl_failure'] = '���������� �� �������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# ������� ������ ����������
        showItems();
	}
	else {
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# ������� ���������� �� ��
        $sql = '
        delete from `'.DB_PREFIX.'photos`
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = '���������� ������� �������.';

            # ������� ��������
            if (!empty($itemInfo['image'])) {
                $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$itemInfo['image'];
                if (file_exists($fullPathToImage) && is_file($fullPathToImage)) unlink($fullPathToImage);
            }

            # ������� backup'�
            $sql = '
            delete from '.DB_PREFIX.'backups
            where table_name = "photos"
                  and entry_id = :entry_id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':entry_id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            
			# ������� ������ ����������
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = '� ���������, ���������� �� �������. ����������, ���������� � ������������� �����.';
			# ������� ������ ����������
			return showItems();
		}
	}
} # /������� ����������

# ��������� ���������� � ��
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
    else echo '� ����� addItemToDB �� �������� photo_form_name.';
} # /��������� ���������� � ��

# �������� ������ �� �������
function getItemInfo()
{
	global $dbh;
	
	# �������� ����������
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
} # /�������� ������ �� �������

# �������� ��������
function copyImage($array)
{
    global $dbh;

    # echo '<pre>'.(print_r($_FILES, true)).'</pre>'; # exit;
    # print_r($array);

    # �������� ����������
    if (empty($array['itemID'])) return;
    if (empty($array['imageFormName'])) return;
    if (empty($array['imageDbColumnName'])) return;
    # if (empty($array['imagePrefix'])) return;

    # echo '<pre>'.(print_r($array, true)).'</pre>';
    # echo $_FILES[$array['imageFormName']]['tmp_name'];
    if (is_uploaded_file($_FILES[$array['imageFormName']]['tmp_name'])) {
        # ������� ������ ��������, ���� ��� ����
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
            # ������� �� ��
            $sql = '
			update '.DB_PREFIX.'photos
			set '.$array['imageDbColumnName'].' = NULL
			where id = :id
			'; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $array['itemID'], PDO::PARAM_INT);
            $sth->execute();
            # ������� ����
            $result = @unlink($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$oldImage);
            # echo $result.'<hr />';
            # echo $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$oldImage;
        }
        # /������� ������ ��������, ���� ��� ����

        # �������� ����� ��������
        $ext = getImageExt($_FILES[$array['imageFormName']]['tmp_name']); # echo $ext.'<hr />';
        $newImageName = $array['itemID']."".$array['imagePrefix'].".".$ext; # echo $newImageName.'<hr />';
        $fullPathToUpload = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$newImageName; # echo $fullPathToUpload.'<hr />';
        # �������� �� �������� ����
        if (move_uploaded_file($_FILES[$array['imageFormName']]['tmp_name'], $fullPathToUpload)) {
            # ����� ���� � ��
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
        # /�������� ����� ��������
    }
} # /�������� ��������

# �������� ���������� ��������
# $imageName - full path to image
function getImageExt($fullPathToImage)
{
    # print_r($fullPathToImage);

    if (empty($fullPathToImage)) return;

    $info = getimagesize($fullPathToImage); # print_r($info);
    $ext = str_replace("image/", "", $info['mime']); # echo $ext.'<hr />';

    if (!empty($ext)) return $ext;
    else return;
} # /�������� ���������� ��������

# ������� ���� �� ��������
function showPhotoInfo($array)
{
    # �������� ����������
    if (empty($array['imageName'])) return;
    if (empty($array['imageDbColumnName'])) return;

    if (file_exists($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath']).$array['imageName']) {
        $imageInfo = @getimagesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']); # echo '<pre>'.(print_r($imageInfo, true)).'</pre>';
        $imageSize = @filesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']);
        $imageSize = @round($imageSize / 1024, 1);

        return '
        <div style="margin-bottom:15px">
		����: <a href="'.$GLOBALS['imagesPath'].$array['imageName'].'" target="_blank">'.$_SERVER['HTTP_HOST'].$GLOBALS['imagesPath'].$array['imageName'].'</a>
		<br />���: '.$imageSize.' ��.
		<br />������: '.$imageInfo[0].'px x '.$imageInfo[1].'px
		<br /><br />
		<a href="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" target="_blank"><img src="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" border="0" /></a>
        <br /><a href="/control/photos/?action=editItem&itemID='.$_GET['itemID'].'&subaction=remove_photo&db_column_name='.$array['imageDbColumnName'].'" onclick="return confirm(\'������� ��������?\');">������� ��������</a>
        </div>
		';
        # <hr style="border:none;background-color:#ccc;color:#ccc;height:1px" />
    }
} # /������� ���� �� ��������

# ������� SELECT � �������������
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
} # /������� SELECT � �������������

# ������� ���������� �� ������������
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
    catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
} # /������� ���������� �� ������������

# /����������