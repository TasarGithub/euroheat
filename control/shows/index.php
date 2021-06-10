<?php 
# ������ ������� ��� ������ � ��� (�������������) (������� shows)
# romanov.egor@gmail.com; 2015.10.14

# ���������� ���� �������
include('../loader.control.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '��� (�������)';
$GLOBALS['imagesPath'] = '/public/images/shows/';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > ��������� ���';
    $GLOBALS['tpl_h1'] = '��������� ���';
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > ��������� ���';
    $GLOBALS['tpl_h1'] = '��������� ���';
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > ����������� ���';
    $GLOBALS['tpl_h1'] = '����������� ���';
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > ������� ���';
    $GLOBALS['tpl_h1'] = '������� ���';
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > ��� ���';
    $GLOBALS['tpl_h1'] = '��� ��� � �������� ('.$dbh->query('select count(1) from `'.DB_PREFIX.'shows`')->fetchColumn().')';
    $GLOBALS['tpl_content'] = showItems(); 
}
# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ��������� ������ ���� ���
function showItems($count = null)
{
    global $dbh;
    
    # �������� ������ ���
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
	$pages = new pages($_GET["page"], # ������� ��������
					   25, # ������� �� ��������
					   $dbh, # ������ ���� ������
                       '', # routeVars
					   $sql, # sql-������
					   $sql_for_count, # sql-������ ��� �������� ���������� �������
					   '/control/shows/', # ����� �� 1� ��������
					   '/control/shows/?page=%page%', # ����� �� ��������� ��������
						1500 # ������������ ���������� ������� �� ��������
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">��������: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++) {
        # ������
        $link = '<a href="/shou/'.$_[$i]['url'].'/" target="_blank">��������</a>';
        
        # is_showable
        if (empty($_[$i]['is_showable'])) $trClass = ' class="item_hidden"';
        else unset($trClass);

        # image_for_slider_on_main_page
        if (!empty($_[$i]['image_for_slider_on_main_page'])) $is_image_for_slider_on_main_page = '��';
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
                <a class="block" title="������� ���" href="/control/shows/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'��� ����� ������� ������������. ������� ���?\')">
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
        ����� �� ��������: &nbsp;
        <input id="search" class="form-control form_required" type="text" value="" style="display:inline-block;width:150px" />
    </div>
    <br style="clear:both" />
    
    <div class="center" style="margin-bottom:15px">
        <a href="/control/shows/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    �������� ���
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= '� ������� �� ������ �� ���� ���.';
    else
    {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                <th class="center vertical_middle">��������</th>
                <th class="center vertical_middle" style="width:175px">������� ������ � ��������</th>
                <th class="center vertical_middle" style="width:175px">�������� ��� �������� �� �������</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">��������</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # / ��������� ������ ���� ���

# ����� �������������� ���
function showEditForm()
{
    global $dbh;
    
    $showEditForm = 1;

    # ������� ���������
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = '��� ������� ���������.';
    
    # ��������� ��������� � ��
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
            $GLOBALS['tpl_success'] = '���������� ���������.';

            # �������� �������� # print_r($_FILES);
            if (!empty($_FILES['show_form_image_for_slider_on_main_page']['tmp_name']))
            {
                copyImage(array(
                    'itemID' => $_GET['itemID'],
                    'imageFormName' => 'show_form_image_for_slider_on_main_page',
                    'imageDbColumnName' => 'image_for_slider_on_main_page',
                    'imagePrefix' => ''
                ));
            } # /�������� ��������
            
			# ��������� ����� � ����
			saveContentToFile($_GET['itemID'],
							  $_POST['show_form_text']);
        }
        else
        {
            $GLOBALS['tpl_failure'] = '� ���������, ���������� �� ���������. ���, ���������� � ������������� �����.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
        }
    } # /��������� ��������� � ��

    # ������� ��������� ��������
    if ($_GET['subaction'] == 'remove_photo') {
        # �������� ����������
        $allowedCoumns = array('image_for_slider_on_main_page');
        if (empty($_GET['itemID'])) $GLOBALS['tpl_failure'] = '�� ������� ID ������.';
        elseif (empty($_GET['db_column_name'])) $GLOBALS['tpl_failure'] = '������� �������� �������� ������� ��������.';
        elseif (!in_array($_GET['db_column_name'], $allowedCoumns)) $GLOBALS['tpl_failure'] = '������� �������� �������� ������� ��������.';
        else
        {
            # �������� ���������� � ��������
            $sql = '
            select '.$_GET['db_column_name'].'
            from '.DB_PREFIX.'shows
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
            update '.DB_PREFIX.'shows
            set '.$_GET['db_column_name'].' = NULL
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
            if ($sth->execute())
            {
                $GLOBALS['tpl_success'] = '�������� ������� �������.';
                $_POST['tabs_state'] = 4;
            }
            else $GLOBALS['tpl_failure'] = '� ���������, �������� �� �������. ���, ���������� � ������������� �����.';
        }
    } # /������� ��������� ��������

    # ������� ����� ��������������
    if ($showEditForm)
	{
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # ������
        if (!$itemInfo['id']) exit('
		�� ���������� ������ � ID='.$_GET['itemID'].'
		<br /><a href="/control/shows/">������� � ������ ���</a>
		');
        
		# ������ ����� ��� �� �����
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

        # �������� � ������� ���� �� ����
        $imageInfo = showPhotoInfo(array('imageName' => $itemInfo['image_for_slider_on_main_page'], 'imageDbColumnName' => 'image_for_slider_on_main_page'));
        
        return "
		<script type='text/javascript' src='/control/shows/index.js'></script>
		<form id='show_form' action='/control/shows/?action=editItem&itemID=".$itemInfo['id']."&subaction=submit' name='show_form' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='btn btn-primary submit_button' type='submit'>��������� ����������</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/shows/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            ������� � ������
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/shows/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            �������� ���
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/shows/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"��� ����� ������� ������������. ������� ���?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> ������� ���</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/shou/".$itemInfo['url']."/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/shou/".$itemInfo['url']."/</a>
            
            <br><br>
            <div class='form-group' style='width:60%'>
                <label>���������� (��-���������): <span style='color:red'>*</span></label>
                <input type='text' name='show_form_url' id='show_form_url' class='form-control form_required' data-required-label='���, ������� ���������� (��������: legenda)' value='".$itemInfo['url']."' />
            </div>

            <div class='form-group' style='width:60%'>
                <label>��������: <span style='color:red'>*</span></label>
                <input type='text' name='show_form_name' id='show_form_name' class='form-control form_required' data-required-label='���, ������� �������� ��-������' value='".$itemInfo['name']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>��������� ��������:</label>
                <input type='text' name='show_form_title' id='show_form_title' class='form-control' data-required-label='���, ������� ��������� ��������' value='".$itemInfo['title']."' />
            </div>
            
            <div class='form-group' style='width:90%'>
                <label>������ ���������:</label>
                <input type='text' name='show_form_navigation' id='show_form_navigation' class='form-control' value='".$itemInfo['navigation']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>������ ��������� � ������ ������:
                       <br />
                       <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
                </label>
                <textarea name='show_form_full_navigation' id='show_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>

            <div class='form-group' style='width:90%'>
                <label>��������� h1:</label>
                <input type='text' name='show_form_h1' id='show_form_h1' class='form-control' value='".$itemInfo['h1']."' />
            </div>

            <div class='form-group'>
                <label>����� ��������:</label>
                <textarea name='show_form_text' id='show_form_text' class='form-control lined' style='width:90%;height:270px'>".$content."</textarea>
            </div>

            <div class='well' style='width:85%'>

                <div class='form-group' style='width:90%'>
                    <label>�����-��������� ��� �������� �� ������� (<a href='/control/public/images/help_screenshot_1.png' target='_blank'>��������</a>):</label>
                    <input type='text' name='show_form_text_for_slider_on_main_page' id='show_form_text_for_slider_on_main_page' class='form-control' value='".$itemInfo['text_for_slider_on_main_page']."' />
                </div>

                <div class='form-group'>
                    <label>�������� ��� �������� �� �������<br /><span style='font-weight:normal'>* ���� �� �������, � �������� �� ������� �� ���������</span>:</label>
                    <br /> <input id='show_form_image_for_slider_on_main_page' name='show_form_image_for_slider_on_main_page' type='file' style='display:inline-block' />
                </div>

                ".$imageInfo."

            </div>

            <div class='form-group' style='width:60%'>
                <label>������� ������ � ��������:</label> &nbsp;
                <input type='text' name='show_form_element_order_listing' id='show_form_element_order_listing' class='form-control' data-required-label='' value='".$itemInfo['element_order_listing']."' style='display:inline-block;width:100px' />
            </div>

            <div class='form-group' style='width:95%'>
                <label>����� ��� ������������ � �������:</label> &nbsp; 
                <textarea name='show_form_footeranchor' id='show_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$itemInfo['footeranchor']."</textarea>
            </div>

            <div class='form-group' style='margin-bottom:0'>
                <label>
                    <input type='checkbox' name='show_form_is_showable' id='show_form_is_showable' class='form_checkbox' ".(!empty($itemInfo['is_showable']) ? 'checked="checekd"' : '')." />&nbsp; ���������� ��� �� �����
                </label>
            </div>
            
            <br />
			<button class='btn btn-primary submit_button' type='submit' style='margin-top:5px'>��������� ����������</button>
            
		</form>
		";
    }
} # /����� �������������� ���

# ����� ���������� ���
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/shows/index.js'></script>
	<form id='show_form' action='/control/shows/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>�������� ���</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/shows/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        ������� � ������
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/shou/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/shou/</a>

        <br /><br />
        <div class='form-group' style='width:60%'>
            <label>���������� (��-���������): <span style='color:red'>*</span></label>
            <input type='text' name='show_form_url' id='show_form_url' class='form-control form_required' data-required-label='���, ������� ���������� (��������: legenda)' value='".$_POST['url']."' />
        </div>

        <div class='form-group' style='width:60%'>
            <label>��������: <span style='color:red'>*</span></label>
            <input type='text' name='show_form_name' id='show_form_name' class='form-control form_required' data-required-label='���, ������� �������� ��-������' value='".$_POST['name']."' />
        </div>

        <div id='show_form_name_alert_div' class='alert alert-info hidden width_95'></div>

        <div class='form-group' style='width:90%'>
            <label>��������� ��������:</label>
            <input type='text' name='show_form_title' id='show_form_title' class='form-control' data-required-label='���, ������� ��������� ��������' value='".$_POST['title']."' />
        </div>

        <div class='form-group' style='width:90%'>
            <label>������ ���������:</label>
            <input type='text' name='show_form_navigation' id='show_form_navigation' class='form-control' value='".$_POST['navigation']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>������ ��������� � ������ ������:
                   <br />
                   <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
            </label>
            <textarea name='show_form_full_navigation' id='show_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['show_form_full_navigation']."</textarea>
        </div>

        <div class='form-group' style='width:90%'>
            <label>��������� h1:</label>
            <input type='text' name='show_form_h1' id='show_form_h1' class='form-control' value='".$_POST['h1']."' />
        </div>

        <div class='form-group'>
            <label>����� ��������:</label>
            <textarea name='show_form_text' id='show_form_text' class='form-control lined' style='width:90%;height:270px'></textarea>
        </div>

        <div class='well' style='width:85%'>

            <div class='form-group' style='width:90%'>
                <label>�����-��������� ��� �������� �� ������� (<a href='/control/public/images/help_screenshot_1.png' target='_blank'>��������</a>):</label>
                <input type='text' name='show_form_text_for_slider_on_main_page' id='show_form_text_for_slider_on_main_page' class='form-control' value='".$_POST['show_form_text_for_slider_on_main_page']."' />
            </div>

            <div class='form-group' style='margin-bottom:5px'>
                <label>�������� ��� �������� �� �������<br /><span style='font-weight:normal'>* ���� �� �������, � �������� �� ������� �� ���������</span>:</label>
                <br /> <input id='show_form_image_for_slider_on_main_page' name='show_form_image_for_slider_on_main_page' type='file' style='display:inline-block' />
            </div>

        </div>

        <div class='form-group' style='width:60%'>
            <label>������� ������ � ��������:</label> &nbsp;
            <input type='text' name='show_form_element_order_listing' id='show_form_element_order_listing' class='form-control' data-required-label='' value='".$_POST['show_form_element_order_listing']."' style='display:inline-block;width:100px' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>����� ��� ������������ � �������:</label> &nbsp;
            <textarea name='show_form_footeranchor' id='show_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$_POST['show_form_footeranchor']."</textarea>
        </div>

        <div class='form-group' style='margin-bottom:0'>
            <label>
                <input type='checkbox' name='show_form_is_showable' id='show_form_is_showable' class='form_checkbox' checked='checekd' />&nbsp; ���������� ��� �� �����
            </label>
        </div>
        
        <br />
        
        <button class='btn btn-primary submit_button' type='submit'>�������� ���</button>
	</form>
	";
} # /����� ���������� �������

# ��������� ��� � ��
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# ������ �� ������� ������� URL'�: http://kupi-krovat.ru/control/shows/?action=addItemSubmit
	if (!empty($_POST))
	{
        # �������� + ������ ��������� POST-����������
        preparePOSTVariables(); # print_r($_POST); exit;

		# ��������� ��� � ��
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# ���� ��� ������� ���������
		if (!empty($lastInsertID)) {
            # �������� �������� # print_r($_FILES);
            if (!empty($_FILES['show_form_image_for_slider_on_main_page']['tmp_name']))
            {
                copyImage(array(
                    'itemID' => $lastInsertID,
                    'imageFormName' => 'show_form_image_for_slider_on_main_page',
                    'imageDbColumnName' => 'image_for_slider_on_main_page',
                    'imagePrefix' => ''
                ));
            } # /�������� ��������

			# ��������� ����� � ����
			saveContentToFile($lastInsertID,
							  $_POST['show_form_text']);
            
			# ������ ��������������� �� ����� ��������������
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/shows/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# ���� �������� ������ � ��� �� ���������
		else {
            $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ��� �� ���������. ����������, ���������� � ������������� �����.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# ���� ������: /control/shows/addItemSubmit/ � ��� ���� $_POST ������
	else
	{
		# ������� ������ ���
        $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ��� �� ���������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /��������� ��� � ��

# ������� ���
function deleteItem(){
	
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID'])) {
		# ������� ������
		$GLOBALS['tpl_failure'] = '��� �� �������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# ������� ������ ���
        showItems();
	}
	else {
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# ������� ��� �� ��
        $sql = '
        delete from `'.DB_PREFIX.'shows`
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) {
			$GLOBALS['tpl_success'] = '��� ������� �������.';

            # ������� ��������
            if (!empty($itemInfo['image_for_slider_on_main_page'])) {
                $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$itemInfo['image_for_slider_on_main_page'];
                if (file_exists($fullPathToImage) && is_file($fullPathToImage)) unlink($fullPathToImage);
            }

            # ������� ���� ���
            if (!empty($itemInfo['file_name'])) {
                $fullPathToFile = $_SERVER['DOCUMENT_ROOT'].'/app/site_sections_shows/'.basename($itemInfo['file_name']);
                if (is_file($fullPathToFile)) unlink($fullPathToFile);
            }

            # ������� backup'�
            $sql = '
            delete from '.DB_PREFIX.'backups
            where table_name = "faq"
                  and entry_id = :entry_id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':entry_id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            
			# ������� ������ ���
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = '� ���������, ��� �� �������. ����������, ���������� � ������������� �����.';
			# ������� ������ ���
			return showItems();
		}
	}
} # /������� ���

# ��������� ��� � ��
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
                # ��������� ��� ����� ���
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
                # /��������� ��� ����� ���
                
                return $last_insert_id;
            }
			else return;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; }}
	}
    else echo '� ����� addItemToDB �� �������� show_form_name.';
} # /��������� ��� � ��

# �������� ������ �� �������
function getItemInfo()
{
	global $dbh;
	
	# �������� ����������
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
} # /�������� ������ �� �������

# ��������� ������� � ����
function saveContentToFile($itemID,
						   $text)
{
    /*
    echo 'itemID: '.$itemID.'<br />';
    echo 'text: '.$text.'<br />';
    */

	# �������� ����������
	if (empty($itemID)) return;
	if (empty($text)) return;
	
	$fullPathToFile = $_SERVER['DOCUMENT_ROOT'].'/app/site_sections_shows/'.basename($itemID.'.php'); # echo 'fullPathToNewFile: '.$fullPathToNewFile.'<br />';
    
    file_put_contents($fullPathToFile, $text, LOCK_EX);
    if (is_file($fullPathToFile)) chmod($fullPathToFile, 0755);
} # /��������� ������� � ����

# �������� ��������
function copyImage($array)
{
    global $dbh;

    # print_r($_FILES);
    # print_r($array);

    # �������� ����������
    if (empty($array['itemID'])) return;
    if (empty($array['imageFormName'])) return;
    if (empty($array['imageDbColumnName'])) return;
    # if (empty($array['imagePrefix'])) return;

    # echo '<pre>'.(print_r($array, true)).'</pre>';
    # echo $_FILES[$array['imageFormName']]['tmp_name'];
    if (is_uploaded_file($_FILES[$array['imageFormName']]['tmp_name']))
    {
        # ������� ������ ��������, ���� ��� ����
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
            # ������� �� ��
            $sql = '
			update '.DB_PREFIX.'shows
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
		����: <a href="'.$GLOBALS['imagesPath'].$array['imageName'].'" target="_blank">'.$_SERVER['HTTP_HOST'].$GLOBALS['imagesPath'].$array['imageName'].'</a>
		<br />���: '.$imageSize.' ��.
		<br />������: '.$imageInfo[0].'px x '.$imageInfo[1].'px
		<br /><br />
		<a href="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" target="_blank"><img src="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" border="0" /></a>
        <br /><a href="/control/shows/?action=editItem&itemID='.$_GET['itemID'].'&subaction=remove_photo&db_column_name='.$array['imageDbColumnName'].'" onclick="return confirm(\'������� ��������?\');">������� ��������</a>
		';
        # <hr style="border:none;background-color:#ccc;color:#ccc;height:1px" />
    }
} # /������� ���� �� ��������

# /����������