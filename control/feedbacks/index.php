<?php 
# ������ ������� ��� ������ � �������� (������� "feedback")
# romanov.egor@gmail.com; 2015.5.25

# ���������� ���� �������
include('../loader.control.php');

# ���������� ������� ������ ����������
include($_SERVER['DOCUMENT_ROOT'].'/control/#library/functions.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '������';
# $GLOBALS['imagesPath'] = '/public/images/feedbacks/';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > ��������� �����';
    $GLOBALS['tpl_h1'] = '��������� �����'; 
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > ��������� �����';
    $GLOBALS['tpl_h1'] = '��������� �����'; 
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > ����������� �����';
    $GLOBALS['tpl_h1'] = '����������� �����'; 
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > ������� �����';
    $GLOBALS['tpl_h1'] = '������� �����'; 
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > ��� ������';
    $GLOBALS['tpl_h1'] = '��� ������ ('.$dbh->query('select count(1) from '.DB_PREFIX.'feedback')->fetchColumn().')'; 
    $GLOBALS['tpl_content'] = showItems(); 
}
# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ��������� ������ ���� �������
function showItems($count = null)
{
    global $dbh;

    $sql = '
    select id,
           name,
           feedback,
           votes_plus,
           votes_minus,
           date_format(date_add, "%e") as date_add_day,
           elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
           date_format(date_add,"%Y") as date_add_year,
           is_published
    from '.DB_PREFIX.'feedback
    where 1
    order by date_add desc
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sql_for_count = '
    select count(id)
    from '.DB_PREFIX.'feedback
    where 1
    '; # echo '<pre>'.$sql_for_count."</pre><hr />";
	$pages = new pages($_GET["page"], # ������� ��������
					   25, # ������� �� ��������
					   $dbh, # ������ ���� ������
                       '', # routeVars
					   $sql, # sql-������
					   $sql_for_count, # sql-������ ��� �������� ���������� �������
					   '/control/feedbacks/', # ����� �� 1� ��������
					   '/control/feedbacks/?page=%page%', # ����� �� ��������� ��������
						1500 # ������������ ���������� ������� �� ��������
						);
	$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>'; exit;
    $_ = $_result['resultSet'];
    if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">��������: '.$_result['pagesSet'].'</div>';
    $_c = count($_);
	$rows = array();
    for ($i=0;$i<$_c;$i++)
	{
        # ������
        if (!empty($_[$i]['id'])) $link = '<a href="/otzyvy/'.$_[$i]['id'].'/" target="_blank">��������</a>';
        else $link = '&nbsp;';
        
        # is_published
        if (empty($_[$i]['is_published'])) $trClass = ' class="item_hidden"';
        else unset($trClass);
        
        $rows[] = '
		<tr'.$trClass.'>
            <td class="center vertical_middle">
                <a class="block" href="/control/feedbacks/?action=editItem&itemID='.$_[$i]['id'].'">
                    <i class="fa fa-edit size_18"></i>
                </a>
            </td>
			<td class="center vertical_middle">'.$link.'</td>
			<td class="nowrap vertical_middle">'.$_[$i]['date_add_day'].' '.$_[$i]['date_add_month'].' '.$_[$i]['date_add_year'].'</td>
			<td class="vertical_middle">'.$_[$i]['name'].'</td>
			<td class="vertical_middle">'.cutText($_[$i]['feedback'], 113).'</td>
			<td class="center vertical_middle">
                <a class="block" title="������� �����" href="/control/feedbacks/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'����� ����� ������ ������������. ������� �����?\')">
                    <i class="fa fa-trash-o size_18"></i>
                </a>
			</td>
		</tr>
		';
    }
	
	if (!empty($rows) and is_array($rows)) $rows = implode("\n", $rows);
	else unset($rows);

    # ������� ���������� ������� �� ����� ���������� "�������"
    $feedbacksPlace1Count = $dbh->query('select count(1) from '.DB_PREFIX.'feedback where is_place_1 = 1')->fetchColumn();
    # ������� ���������� ������� �� ����� ���������� "�������� �����������"
    $feedbacksPlace2Count = $dbh->query('select count(1) from '.DB_PREFIX.'feedback where is_place_2 = 1')->fetchColumn();

    $result = '
	<script type="text/javascript" src="/control/feedbacks/index.js"></script>
	
    <div style="width:50%;float:left;min-height:34px;padding-top:8px">
        <b>URL:</b>&nbsp; <a href="/otzyvy/" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/otzyvy/</a>
    </div>
    <div style="width:50%;float:right;text-align:right;padding-right:15px">
        ����� �� �������: &nbsp;
        <input id="search_by_feedbacks" class="form-control form_required" type="text" value="" style="display:inline-block;width:150px" />
    </div>
    <br style="clear:both" />

    <div class="center" style="margin-bottom:15px">
        <a href="/control/feedbacks/?action=addItem">
            <button id="parse_all_projects" class="btn btn-success" type="button">
                <i class="fa fa-plus-square" style="margin-right:3px"></i>
                    �������� �����
            </button>
        </a>
    </div>
    ';
    
    if (empty($rows)) $result .= '� ������� �� ����� �� ���� �����.';
    else
    {
        $result .= '
        <div id="resultSet">
        <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
            <tr>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                <th class="center vertical_middle" style="width:50px;white-space:nowrap">������</th>
                <th class="center vertical_middle">����</th>
                <th class="center vertical_middle">���</th>
                <th class="center vertical_middle">�����</th>
                <th class="center vertical_middle" style="width:100px;white-space:nowrap">��������</th>
            </tr>
            '.$rows.'
        </table>
        '.$pagesList.'
        </div>';
    }
    
    return $result;
} # /��������� ������ ���� �������

# ����� �������������� ������
function showEditForm()
{
    global $dbh;
    
    $showEditForm = 1;

    # ������� ���������
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = '����� ������� ��������.';
    
    # ��������� ��������� � ��
    if ($_GET['subaction'] == 'submit' && !empty($_POST)) {
        # print_r($_POST);
        $sql = '
        update '.DB_PREFIX.'feedback
        set name = :name,
            feedback = :feedback,
            votes_plus = :votes_plus,
            votes_minus = :votes_minus,
            date_add = :date_add,
            full_navigation = :full_navigation,
            footeranchor = :footeranchor,
            is_place_1 = :is_place_1,
            is_place_2 = :is_place_2,
            is_published = :is_published
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $_POST['feedback_form_name'] = !empty($_POST['feedback_form_name']) ? $_POST['feedback_form_name'] : null;
        $sth->bindParam(':name', $_POST['feedback_form_name']);
        $_POST['feedback_form_feedback'] = !empty($_POST['feedback_form_feedback']) ? $_POST['feedback_form_feedback'] : null;
        $sth->bindParam(':feedback', $_POST['feedback_form_feedback']);
        $_POST['feedback_form_votes_plus'] = !empty($_POST['feedback_form_votes_plus']) ? $_POST['feedback_form_votes_plus'] : 0;
        $sth->bindParam(':votes_plus', $_POST['feedback_form_votes_plus']);
        $_POST['feedback_form_votes_minus'] = !empty($_POST['feedback_form_votes_minus']) ? $_POST['feedback_form_votes_minus'] : 0;
        $sth->bindParam(':votes_minus', $_POST['feedback_form_votes_minus']);
        $_POST['feedback_form_full_navigation'] = !empty($_POST['feedback_form_full_navigation']) ? $_POST['feedback_form_full_navigation'] : null;
        $sth->bindParam(':full_navigation', $_POST['feedback_form_full_navigation']);
        $_POST['feedback_form_footeranchor'] = !empty($_POST['feedback_form_footeranchor']) ? $_POST['feedback_form_footeranchor'] : null;
        $sth->bindParam(':footeranchor', $_POST['feedback_form_footeranchor']);
        # date_add
        # ������������ ���� �� ���� datepicker � mysql datetime:
        if (!empty($_POST['feedback_form_date_add'])) $date = date("Y-m-d H:i:s", strtotime($_POST['feedback_form_date_add'].' 05:00:00'));
        else $date = 'now()';
        $sth->bindParam(':date_add', $date);
        # is_published
        $isPublished = !empty($_POST['feedback_form_is_published']) ? 1 : NULL;
        $sth->bindParam(':is_published', $isPublished, PDO::PARAM_INT);
        # is_place_1
        $sth->bindValue(':is_place_1', !empty($_POST['feedback_form_is_place_1']) ? 1 : null);
        # is_place_2
        $sth->bindValue(':is_place_2', !empty($_POST['feedback_form_is_place_2']) ? 1 : null);
        # id
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute()) $GLOBALS['tpl_success'] = '���������� ���������.';
        else {
            $GLOBALS['tpl_failure'] = '� ���������, ���������� �� ���������. ���, ���������� � ������������� �����.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
        }
    } # /��������� ��������� � ��
    
    # ������� ����� ��������������
    if ($showEditForm) {
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # ������
        if (!$itemInfo['id']) exit('
		�� ���������� ������ � ID='.$_GET['itemID'].'
		<br /><a href="/control/feedbacks/">������� � ������ �������</a>
		');
        
        return "
		<script type='text/javascript' src='/control/feedbacks/index.js'></script>
		<form id='feedback_form' action='/control/feedbacks/?action=editItem&itemID=".$itemInfo['id']."&subaction=submit' name='feedback_form' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='btn btn-primary submit_button' type='submit'>��������� ����������</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/feedbacks/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            ������� � ������
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/feedbacks/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            �������� �����
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/feedbacks/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"����� ����� ������ ������������. ������� �����?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> ������� �����</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/otzyvy/".$itemInfo['id']."/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/otzyvy/".$itemInfo['id']."/</a>
            
            <br /><br />
            <div class='form-group' style='width:60%'>
                <label>���� (��.��.����): <span style='color:red'>*</span></label>
                <input type='text' name='feedback_form_date_add' id='feedback_form_date_add' class='form-control form_required' data-required-label='���, ������� ���� ���������� ������' value='".$itemInfo['date_add_formatted']."' style='width:100px;display:inline-block' />
            </div>

            <div class='form-group' style='width:90%'>
                <label>���: <span style='color:red'>*</span></label>
                <input type='text' name='feedback_form_name' id='feedback_form_name' class='form-control form_required' data-required-label='���, ������� ��� ������ ������' value='".$itemInfo['name']."' />
            </div>
            
            <div class='form-group'>
                <label>�����:</label>
                <textarea name='feedback_form_feedback' id='feedback_form_feedback' class='form-control lined form_required' data-required-label='���, ������� ����� ������' style='width:90%;height:230px'>".$itemInfo['feedback']."</textarea>
            </div>
            
            <div class='form-group' style='width:60%'>
                <label style='display:inline-block'>������� ��:</label> &nbsp;
                <input type='text' name='feedback_form_votes_plus' id='feedback_form_votes_plus' class='form-control' value='".$itemInfo['votes_plus']."' style='width:75px;display:inline-block;margin-left:37px' />
            </div>
            
            <div class='form-group' style='width:60%'>
                <label style='display:inline-block'>������� ������:</label> &nbsp;
                <input type='text' name='feedback_form_votes_minus' id='feedback_form_votes_minus' class='form-control' value='".$itemInfo['votes_minus']."' style='width:75px;display:inline-block' />
            </div>

            <div class='form-group' style='width:95%'>
                <label>������ ��������� � ������ ������:
                       <br />
                       <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
                </label>
                <textarea name='feedback_form_full_navigation' id='feedback_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>
            
            <div class='form-group' style='width:95%'>
                <label>����� ��� ������������ � �������:</label> &nbsp; 
                <textarea name='feedback_form_footeranchor' id='feedback_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$itemInfo['footeranchor']."</textarea>
            </div>
                        
            <div class='form-group' style='margin-bottom:0'>
                <label>
                    <input type='checkbox' name='feedback_form_is_published' id='feedback_form_is_published' class='form_checkbox' ".(!empty($itemInfo['is_published']) ? 'checked="checked"' : '')." />&nbsp; ���������� ����� �� �����
                </label>
            </div>
            
            <br />
			<button class='btn btn-primary submit_button' type='submit' style='margin-top:5px'>��������� ����������</button>
            
		</form>
		";
    }
} # /����� �������������� ������

# ����� ���������� ������
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/feedbacks/index.js'></script>
	<form id='feedback_form' action='/control/feedbacks/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>�������� �����</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/feedbacks/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        ������� � ������
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/otzyvy/' target='_blank'>http://".$_SERVER['SERVER_NAME']."/otzyvy/</a>

        <br /><br />
        <div class='form-group' style='width:60%'>
            <label>���� (��.��.����): <span style='color:red'>*</span></label>
            <input type='text' name='feedback_form_date_add' id='feedback_form_date_add' class='form-control form_required' data-required-label='���, ������� ���� ���������� ������' value='".date("d").".".date("m").".".date("Y")."' style='width:100px;display:inline-block' />
        </div>
        
        <div class='form-group' style='width:90%'>
            <label>���: <span style='color:red'>*</span></label>
            <input type='text' name='feedback_form_name' id='feedback_form_name' class='form-control form_required' data-required-label='���, ������� ��� ������ ������' value='".$_POST['feedback_form_name']."' />
        </div>
        
        <div class='form-group'>
            <label>�����:</label>
            <textarea name='feedback_form_feedback' id='feedback_form_feedback' class='form-control form_required lined' data-required-label='���, ������� ����� ������' style='width:90%;height:230px'>".$_POST['feedback_form_feedback']."</textarea>
        </div>
        
        <div class='form-group' style='width:60%'>
            <label style='display:inline-block'>������� ��:</label> &nbsp;
            <input type='text' name='feedback_form_votes_plus' id='feedback_form_votes_plus' class='form-control' value='".$_POST['feedback_form_votes_plus']."' style='width:75px;display:inline-block;margin-left:37px' />
        </div>
        
        <div class='form-group' style='width:60%'>
            <label style='display:inline-block'>������� ������:</label> &nbsp;
            <input type='text' name='feedback_form_votes_minus' id='feedback_form_votes_minus' class='form-control' value='".$_POST['feedback_form_votes_minus']."' style='width:75px;display:inline-block' />
        </div>

        <div class='form-group' style='width:95%'>
            <label>������ ��������� � ������ ������:
                   <br />
                   <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
            </label>
            <textarea name='feedback_form_full_navigation' id='feedback_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['feedback_form_full_navigation']."</textarea>
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>����� ��� ������������ � �������:</label> &nbsp; 
            <textarea name='feedback_form_footeranchor' id='feedback_form_footeranchor' class='form-control' style='width:95%;height:55px'>".$_POST['feedback_form_footeranchor']."</textarea>
        </div>
                    
        <div class='form-group' style='margin-bottom:0'>
            <label>
                <input type='checkbox' name='feedback_form_is_published' id='feedback_form_is_published' class='form_checkbox' checked='checked' />&nbsp; ���������� ����� �� �����
            </label>
        </div>
        
        <br />
        
        <button class='btn btn-primary submit_button' type='submit'>�������� �����</button>
	</form>
	";
} # /����� ���������� ������

# ��������� ����� � ��
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# ������ �� ������� ������� URL'�: http://kupi-krovat.ru/control/feedbacks/?action=addItemSubmit
	if (!empty($_POST))
	{
        # �������� + ������ ��������� POST-����������
        # echo '<pre>'.(print_r($_POST, true)).'</pre>';
        preparePOSTVariables();
        # echo '<pre>'.(print_r($_POST, true)).'</pre>'; exit;

		# ��������� ����� � ��
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# ���� ����� ������� ��������
		if (!empty($lastInsertID))
		{
            # �������� �������� # print_r($_FILES);
            if (!empty($_FILES['feedback_form_image']['tmp_name']))
            {
                copyImage(array(
                'itemID' => $lastInsertID,
                'imageFormName' => 'feedback_form_image',
                'imageDbColumnName' => 'image',
                'imagePrefix' => ''
                ));
            }
            # /�������� ��������
            
			# ������ ��������������� �� ����� ��������������
			$fullUrlForEdit = 'http://'.$_SERVER['SERVER_NAME']."/control/feedbacks/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# ���� �������� ������ � ����� �� ��������
		else
		{
            $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ����� �� ��������. ����������, ���������� � ������������� �����.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# ���� ������: /control/feedbacks/addItemSubmit/ � ��� ���� $_POST ������
	else
	{
		# ������� ������ �������
        $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ����� �� ��������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /��������� ����� � ��

# ������� �����
function deleteItem(){
	
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID']))
	{
		# ������� ������
		$GLOBALS['tpl_failure'] = '����� �� ������. ����������, ���������� � ������������� �����.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# ������� ������ �������
        showItems();
	}
	else
	{
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# ������� ��������
        if (!empty($itemInfo['image']))
        {
            $fullPathToImage = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$itemInfo['image'];
            if (file_exists($fullPathToImage) && is_file($fullPathToImage)) unlink($fullPathToImage);
        }

		# ������� ����� �� ��
        $sql = '
        delete from '.DB_PREFIX.'feedback
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
        if ($sth->execute())
		{
			$GLOBALS['tpl_success'] = '����� ������� ������.';
            
            # ������� backup'�
            $sql = '
            delete from '.DB_PREFIX.'backups
            where table_name = "feedbacks"
                  and entry_id = :entry_id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':entry_id', $_GET['itemID'], PDO::PARAM_INT);
            $sth->execute();
            
			# ������� ������ �������
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = '� ���������, ����� �� ������. ����������, ���������� � ������������� �����.';
			# ������� ������ �������
			return showItems();
		}
	}
} # /������� �����

# ��������� ����� � ��
function addItemToDB()
{
	global $dbh;
	
	if (!empty($_POST['feedback_form_name'])
		and !empty($_POST['feedback_form_feedback']))
	{ 
        $sql = '
        insert into '.DB_PREFIX.'feedback
        (name,
         feedback,
         votes_plus,
         votes_minus,
         date_add,
         full_navigation,
         footeranchor,
         is_place_1,
         is_place_2,
         is_published)
        values
        (:name,
         :feedback,
         :votes_plus,
         :votes_minus,
         :date_add,
         :full_navigation,
         :footeranchor,
         :is_place_1,
         :is_place_2,
         :is_published)
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $_POST['feedback_form_name'] = !empty($_POST['feedback_form_name']) ? $_POST['feedback_form_name'] : null;
        $sth->bindParam(':name', $_POST['feedback_form_name']);
        $_POST['feedback_form_feedback'] = !empty($_POST['feedback_form_feedback']) ? $_POST['feedback_form_feedback'] : null;
        $sth->bindParam(':feedback', $_POST['feedback_form_feedback']);
        $_POST['feedback_form_votes_plus'] = !empty($_POST['feedback_form_votes_plus']) ? $_POST['feedback_form_votes_plus'] : 0;
        $sth->bindParam(':votes_plus', $_POST['feedback_form_votes_plus']);
        $_POST['feedback_form_votes_minus'] = !empty($_POST['feedback_form_votes_minus']) ? $_POST['feedback_form_votes_minus'] : 0;
        $sth->bindParam(':votes_minus', $_POST['feedback_form_votes_minus']);
        $_POST['feedback_form_full_navigation'] = !empty($_POST['feedback_form_full_navigation']) ? $_POST['feedback_form_full_navigation'] : null;
        $sth->bindParam(':full_navigation', $_POST['feedback_form_full_navigation']);
        $_POST['feedback_form_footeranchor'] = !empty($_POST['feedback_form_footeranchor']) ? $_POST['feedback_form_footeranchor'] : null;
        $sth->bindParam(':footeranchor', $_POST['feedback_form_footeranchor']);
        # date_add
        # ������������ ���� �� ���� datepicker � mysql datetime:
        if (!empty($_POST['feedback_form_date_add'])) $date = date("Y-m-d H:i:s", strtotime($_POST['feedback_form_date_add'].' 05:00:00'));
        else $date = 'now()';
        $sth->bindParam(':date_add', $date);
        # is_place_1
        $sth->bindValue(':is_place_1', !empty($_POST['feedback_form_is_place_1']) ? 1 : null);
        # is_place_2
        $sth->bindValue(':is_place_2', !empty($_POST['feedback_form_is_place_2']) ? 1 : null);
        # is_published
        $isPublished = !empty($_POST['feedback_form_is_published']) ? 1 : NULL;
        $sth->bindParam(':is_published', $isPublished, PDO::PARAM_INT);
		try { if ($sth->execute()) {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			if (!empty($last_insert_id)) return $last_insert_id;
			else return;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; }}
	}
    else echo '� ����� &quot;addItemToDB&quot; �� �������� &quot;feedback_form_name&quot; ��� &quot;feedback_form_feedback&quot;.';
} # /��������� ����� � ��

# �������� ������ �� �������
function getItemInfo()
{
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID'])) return;
	
	$sql = '
	select *,
           date_format(date_add, "%d.%m.%Y") as date_add_formatted
           from '.DB_PREFIX.'feedback
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $itemInfo = $sth->fetch();
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /�������� ������ �� �������

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
		from '.DB_PREFIX.'feedback
		where id = :id
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $array['itemID'], PDO::PARAM_INT);
        $sth->execute();
        $_ = $sth->fetchColumn();
		if (!empty($_))	{
			$oldImage = $_;
			# ������� �� ��
			$sql = '
			update '.DB_PREFIX.'feedback
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
			update '.DB_PREFIX.'feedback
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
	
	if (file_exists($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath']).$array['imageName'])
	{
		$imageInfo = @getimagesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']); # echo '<pre>'.(print_r($imageInfo, true)).'</pre>';
		$imageSize = @filesize($_SERVER['DOCUMENT_ROOT'].$GLOBALS['imagesPath'].$array['imageName']);
		$imageSize = @round($imageSize / 1024, 1);
		
		return '
		����: <a href="'.$GLOBALS['imagesPath'].$array['imageName'].'" target="_blank">'.$_SERVER['HTTP_HOST'].$GLOBALS['imagesPath'].$array['imageName'].'</a>
		<br />���: '.$imageSize.' ��.
		<br />������: '.$imageInfo[0].'px x '.$imageInfo[1].'px
		<br /><br />
		<a href="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" target="_blank"><img src="'.$GLOBALS['imagesPath'].$array['imageName'].'?rand='.rand(1, 99999999).'" border="0" /></a>
        <br /><a href="/control/feedbacks/?action=editItem&itemID='.$_GET['itemID'].'&subaction=remove_photo&db_column_name='.$array['imageDbColumnName'].'" onclick="return confirm(\'������� ��������?\');">������� ��������</a>
		<hr style="border:none;background-color:#ccc;color:#ccc;height:1px" />
		';
	}
} # /������� ���� �� ��������

# ������ SELECT � �������� ��� ������������ � �������
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
    $result = '<select id="feedback_form_footeranchor_id" name="feedback_form_footeranchor_id" class="form-control">'.PHP_EOL.'<option value="null">�� ������</option>'.PHP_EOL.$options.'</select>';
    if (!empty($result)) return $result;
} */ # /������ SELECT � �������� ��� ������������ � �������

# /����������