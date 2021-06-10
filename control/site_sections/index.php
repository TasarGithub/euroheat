<?php 
# ������ ������� ��� ������ � ��������� ����� (������� site_sections)
# romanov.egor@gmail.com; 2015.4.22

# ���������� ���� �������
include('../loader.control.php');

# ���������� ������� ������ ���������� ��� ajax-��������
# include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# ���������� ����� ������� ��� index.php � ajax.php
include('common.functions.php');

# ���������
$GLOBALS['tpl_title'] = '������� �����';

# ������
if ($_GET['itemID']) $_GET['itemID'] = (int)$_GET['itemID'];

# ������
if ($_GET['action'] == "addItem")
{ 
    $GLOBALS['tpl_title'] .= ' > ������� ������ �����';
    $GLOBALS['tpl_h1'] = '������� ������ �����'; 
    $GLOBALS['tpl_content'] = showAddForm();
}
elseif ($_GET['action'] == "addItemSubmit") {
    $GLOBALS['tpl_title'] .= ' > ������� ������ �����';
    $GLOBALS['tpl_h1'] = '������� ������ �����'; 
    $GLOBALS['tpl_content'] = addItemSubmit(); 
}
elseif ($_GET['action'] == "editItem") {
    $GLOBALS['tpl_title'] .= ' > ����������� ������ �����';
    $GLOBALS['tpl_h1'] = '����������� ������ �����'; 
    $GLOBALS['tpl_content'] = showEditForm(); 
}
elseif ($_GET['action'] == "deleteItem") {
    $GLOBALS['tpl_title'] .= ' > ������� ������ �����';
    $GLOBALS['tpl_h1'] = '������� ������ �����'; 
    $GLOBALS['tpl_content'] = deleteItem(); 
}
else { 
    $GLOBALS['tpl_title'] .= ' > ��� ������� �����';
    $GLOBALS['tpl_h1'] = '��� ������� �����'; 
    $GLOBALS['tpl_content'] = showItems(); 
}
# /������

# ������� ������� ������
$tpl->setMainTemplate('template_for_all_pages.php');
$tpl->echoMainTemplate();

# ����������

# ��������� ������ �������� �����
function showItems($count = null)
{
    global $dbh;
	
	$GLOBALS['tpl_title'] = '������ ����� > ��� �������';
    
    # 2 �������
    $sql = '
    select id,
           parent_id, 
           name, 
           url, 
           full_url, 
           is_showable
    from '.DB_PREFIX.'site_sections 
    where parent_id = 1 
    order by name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $staticSectionsQuery = $dbh->query($sql)->fetchAll(); # print_r($staticSectionsQuery);
    $_c = count($staticSectionsQuery);
    
    # ������� ���������� �����������
    unset($subsectionsCount);
    $sql = '
    select count(1)
    from '.DB_PREFIX.'site_sections 
    where parent_id = 1
    '; # echo '<pre>'.$sql."</pre><hr />";
    $subsectionsCount = $dbh->query($sql)->fetchColumn();
    if ($subsectionsCount) $subsectionsCount = " (".$subsectionsCount.")";
    else unset($subsectionsCount);
    
    $staticSections[] = "
    <div class='center' style='margin-bottom:15px'>
    <a href='/control/site_sections/?action=addItem'>
    <button id='parse_all_projects' class='btn btn-success' type='button'>
    <i class='fa fa-plus-square' style='margin-right:3px'></i>
    ������� ������
    </button>
    </a>
    </div>
    
    <div><a href='/control/templates/?action=editItem&itemID=1'>�������</a> ".$subsectionsCount." <span style='color:#cccccc'>&nbsp;&nbsp;<a href='/' target='_blank' style='color:#cccccc'>https://".DOMAIN."</a></div>
    ";
    if ($staticSectionsQuery != 0)
    {
        if ($_c > 0) for ($i=0;$i<$_c;$i++)
        {
            $path = "<span style='color:#cccccc'>&nbsp;&nbsp;<a href='/".$staticSectionsQuery[$i]['full_url']."/' target='_blank' style='color:#cccccc'>https://".DOMAIN."/".$staticSectionsQuery[$i]['full_url']."/</a>";
            
            # ������� ���������� �����������
            unset($subsectionsCount);
            $sql = '
            select count(1) 
            from '.DB_PREFIX.'site_sections 
            where parent_id = "'.$staticSectionsQuery[$i]['id'].'"
            '; # echo '<pre>'.$sql."</pre><hr />";
            $subsectionsCount = $dbh->query($sql)->fetchColumn();
            if ($subsectionsCount) $subsectionsCount = " (".$subsectionsCount.")";
            else unset($subsectionsCount);
            
            # ���� ������ �����, ���������� ���
            if (!$staticSectionsQuery[$i]['is_showable']) $style = " style='color:#cccccc'";
            else unset($style);
            
            $staticSections[] = "<div class='sitemap_level_1'><a href='/control/site_sections/?action=editItem&itemID=".$staticSectionsQuery[$i]['id']."'".$style.">".$staticSectionsQuery[$i]['name']."</a> ".$subsectionsCount." ".$path."</div>";
            
            # echo $staticSectionsQuery[$i]['url']."<br />";
            
            # ��������
            if ($staticSectionsQuery[$i]['id'] == 36)
            {
                $staticSections[] = '<div class="sitemap_level_1_empty"> <i class="fa fa-share" style="color:#ccc"></i> <a href="/control/cars/">�������� ��� ����</a></div>';
            }
            # �������-������
            elseif ($staticSectionsQuery[$i]['id'] == 3)
            {
                $staticSections[] = '<div class="sitemap_level_1_empty"> <i class="fa fa-share" style="color:#ccc"></i> <a href="/control/faq/">�������� ��� �������-������</a></div>';
            }
            # �������
            elseif ($staticSectionsQuery[$i]['id'] == 38)
            {
                $staticSections[] = '<div class="sitemap_level_1_empty"> <i class="fa fa-share" style="color:#ccc"></i> <a href="/control/news/">�������� ��� �������</a></div>';
            }
            # ������
            elseif ($staticSectionsQuery[$i]['id'] == 51)
            {
                $staticSections[] = '<div class="sitemap_level_1_empty"> <i class="fa fa-share" style="color:#ccc"></i> <a href="/control/articles/">�������� ��� ������</a></div>';
            }
            # ������
            elseif ($staticSectionsQuery[$i]['id'] == 43)
            {
                $staticSections[] = '<div class="sitemap_level_1_empty"> <i class="fa fa-share" style="color:#ccc"></i> <a href="/control/feedbacks/">�������� ��� ������</a></div>';
            }
            
            # 3 �������
            $sql2 = '
            select id, 
                   parent_id, 
                   name, 
                   url, 
                   full_url, 
                   is_showable
            from '.DB_PREFIX.'site_sections 
            where parent_id = "'.$staticSectionsQuery[$i]['id'].'" 
            order by name
            '; # echo '<pre>'.$sql."</pre><hr />";
            $staticSectionsQuery2 = $dbh->query($sql2)->fetchAll();
            $_c2 = count($staticSectionsQuery2);
            if ($_c2 > 0) for ($j=0;$j<$_c2;$j++)
            {
                $path = "<span style='color:#cccccc'>&nbsp;&nbsp;<a href='/".$staticSectionsQuery2[$j]['full_url']."/' target='_blank' style='color:#cccccc'>https://".DOMAIN."/".$staticSectionsQuery2[$j]['full_url']."/</a>";
                
                # ������� ���������� �����������
                unset($subsectionsCount);
                $sql = '
                select count(1) 
                from '.DB_PREFIX.'site_sections 
                where parent_id = "'.$staticSectionsQuery2[$j]['id'].'"
                '; # echo '<pre>'.$sql."</pre><hr />";
                $subsectionsCount = $dbh->query($sql)->fetchColumn();
                if ($subsectionsCount) $subsectionsCount = " (".$subsectionsCount.")";
                else unset($subsectionsCount);
                
                # ���� ������ �����, ���������� ���
                if (!$staticSectionsQuery2[$j]['is_showable']) $style = " style='color:#cccccc'";
                else unset($style);
                
                $staticSections[] = "<div class='sitemap_level_2'><a href='/control/site_sections/?action=editItem&itemID=".$staticSectionsQuery2[$j]['id']."'".$style.">".$staticSectionsQuery2[$j]['name']."</a> ".$subsectionsCount." ".$path."</div>";
                
                # ��������
                if ($staticSectionsQuery2[$j]['id'] == 42)
                {
                    $staticSections[] = '<div class="sitemap_level_2_empty"> <i class="fa fa-share" style="color:#ccc"></i> <a href="/control/vacancies/">�������� ��� ��������</a></div>';
                }
                
                # 4 �������
                $sql3 = '
                select id, 
                       parent_id, 
                       name, 
                       url, 
                       full_url, 
                       is_showable 
                from '.DB_PREFIX.'site_sections 
                where parent_id = "'.$staticSectionsQuery2[$j]['id'].'"
                order by name
                '; # echo '<pre>'.$sql."</pre><hr />";
                $staticSectionsQuery3 = $dbh->query($sql3)->fetchAll();
                $_c3 = count($staticSectionsQuery3);
                if ($_c3 > 0) for ($x=0;$x<$_c3;$x++)
                {
                    $path = "<span style='color:#cccccc'>&nbsp;&nbsp;<a href='/".$staticSectionsQuery3[$x]['full_url']."/' target='_blank' style='color:#cccccc'>https://".DOMAIN."/".$staticSectionsQuery3[$x]['full_url']."/</a>";
                    
                    # ������� ���������� �����������
                    unset($subsectionsCount);
                    $sql = '
                    select count(1) 
                    from '.DB_PREFIX.'site_sections 
                    where parent_id = "'.$staticSectionsQuery3[$x]['id'].'"
                    '; # echo '<pre>'.$sql."</pre><hr />";
                    $subsectionsCount = $dbh->query($sql)->fetchColumn();
                    if ($subsectionsCount) $subsectionsCount = " (".$subsectionsCount.")";
                    else unset($subsectionsCount);
                    
                    # ���� ������ �����, ���������� ���
                    if (!$staticSectionsQuery3[$x]['is_showable']) $style = " style='color:#cccccc'";
                    else unset($style);
                    
                    $staticSections[] = "<div class='sitemap_level_3'><a href='/control/site_sections/?action=editItem&itemID=".$staticSectionsQuery3[$x]['id']."'".$style.">".$staticSectionsQuery3[$x]['name']."</a> ".$subsectionsCount." ".$path."</div>";
                    
                    # 5 �������
                    $sql4 = '
                    select id, 
                           parent_id, 
                           name, 
                           url, 
                           full_url, 
                           is_showable
                    from '.DB_PREFIX.'site_sections 
                    where parent_id = "'.$staticSectionsQuery3[$x]['id'].'"
                    order by name
                    '; # echo '<pre>'.$sql."</pre><hr />";
                    $staticSectionsQuery4 = $dbh->query($sql4)->fetchAll();
                    $_c4 = count($staticSectionsQuery4);
                    if ($_c4 > 0) for ($d=0;$d<$_c4;$d++)
                    {
                        $path = "<span style='color:#cccccc'>&nbsp;&nbsp;<a href='/".$staticSectionsQuery4[$d]['full_url']."/' target='_blank' style='color:#cccccc'>https://".DOMAIN."/".$staticSectionsQuery4[$d]['full_url']."/</a>";
                        
                        # ������� ���������� �����������
                        unset($subsectionsCount);
                        $sql = '
                        select count(1) 
                        from '.DB_PREFIX.'site_sections 
                        where parent_id = "'.$staticSectionsQuery4[$d]['id'].'"
                        ';
                        $subsectionsCount = $dbh->query($sql)->fetchColumn();
                        if ($subsectionsCount) $subsectionsCount = " (".$subsectionsCount.")";
                        else unset($subsectionsCount);
                        
                        # ���� ������ �����, ���������� ���
                        if (!$staticSectionsQuery4[$d]['is_showable']) $style = " style='color:#cccccc'";
                        else unset($style);
                        
                        $staticSections[] = "<div class='sitemap_level_4'><a href='/control/site_sections/?action=editItem&itemID=".$staticSectionsQuery4[$d]['id']."'".$style.">".$staticSectionsQuery4[$d]['name']."</a> ".$subsectionsCount." ".$path."</div>";
                        # 6 �������
                        $sql5 = '
                        select id, 
                               parent_id, 
                               name, 
                               url, 
                               full_url, 
                               is_showable
                        from '.DB_PREFIX.'site_sections 
                        where parent_id = "'.$staticSectionsQuery4[$d]['id'].'"
                        order by name
                        '; # echo '<pre>'.$sql."</pre><hr />";
                        $staticSectionsQuery5 = $dbh->query($sql5)->fetchAll();
                        $_c5 = count($staticSectionsQuery5);
                        if ($_c5 > 0) for ($q=0;$q<$_c5;$q++)
                        {
                            $path = "<span style='color:#cccccc'>&nbsp;&nbsp;<a href='/".$staticSectionsQuery5[$q]['full_url']."/' target='_blank' style='color:#cccccc'>https://".DOMAIN."/".$staticSectionsQuery5[$q]['full_url']."/</a>";
                            
                            # ������� ���������� �����������
                            unset($subsectionsCount);
                            $sql = '
                            select count(1) 
                            from '.DB_PREFIX.'site_sections 
                            where parent_id = "'.$staticSectionsQuery5[$q]['id'].'"
                            '; # echo '<pre>'.$sql."</pre><hr />";
                            $subsectionsCount = $dbh->query($sql)->fetchColumn();
                            if ($subsectionsCount) $subsectionsCount = " (".$subsectionsCount.")";
                            else unset($subsectionsCount);
                            
                            # ���� ������ �����, ���������� ���
                            if (!$staticSectionsQuery5[$q]['is_showable']) $style = " style='color:#cccccc'";
                            else unset($style);
                            
                            $staticSections[] = "<div class='sitemap_level_5'><a href='/control/site_sections/?action=editItem&itemID=".$staticSectionsQuery5[$q]['id']."'".$style.">".$staticSectionsQuery5[$q]['name']."</a> ".$subsectionsCount." ".$path."</div>";
                        }
                    }
                }
            }
        }
    }
    $result = "
    <script type='text/javascript' src='/control/site_sections/index.js'></script>
    <div id='showAllSection1'>".@implode("\n", $staticSections)."</div>
    ";
    
    return $result;
} # /��������� ������ �������� �����

# ����� �������������� ������� �����
function showEditForm(){
    global $dbh;
    
    $showEditForm = 1;

    # ������� ���������
    if ($_GET['success'] == 1) $GLOBALS['tpl_success'] = '������ ������� ��������.';

    if ($showEditForm) {
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';
        
        # ������
        if (!$itemInfo['id']) exit('
		�� ���������� ������ � ID='.$_GET['itemID'].'
		<br /><a href="/control/site_sections/">������� � ������ �������� �����</a>
		');

        # prepare all values for showing
        # foreach ($itemInfo as $k => $v) $itemInfo[$k] = htmlspecialchars($v, ENT_QUOTES);
		
		# �������� html-��� �������
        if (!empty($itemInfo['file_name_1'])) {
            $fullPathToFile = PATH_TO_PUBLIC_SITE_SECTIONS.basename($itemInfo['file_name_1']); # echo $fullPathToFile.'<hr />';
            if (file_exists($fullPathToFile)) {
                $content = file_get_contents($fullPathToFile); # echo $content.'<hr />';
                # prepare for showing
                $content = htmlspecialchars($content, ENT_QUOTES);
                $content = str_replace("\t", "", $content);
            }
        }
        
        return "
		<script type='text/javascript' src='/control/site_sections/index.js'></script>
		<form id='site_sections_edit_form' action='/control/site_sections/?action=editItem&itemID=".$itemInfo['id']."&subaction=editSubmit' name='form1' method='post' enctype='multipart/form-data' onSubmit=\"return SendForm('form1')\" id='editItemForm' style='font-size:14px;position:relative'>
            
            <button class='site_sections_edit_save_changes btn btn-primary submit_button' type='button'>��������� ����������</button>

            &nbsp;&nbsp;&nbsp; <a href='/control/site_sections/'><button class='btn btn-success' type='button'>
            <i class='fa fa-share-square' style='margin-right:3px'></i>
            ������� � ������
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/site_sections/?action=addItem'><button class='btn btn-success' type='button'>
            <i class='fa fa-plus-square' style='margin-right:3px'></i>
            ������� ������
            </button></a>
            
            &nbsp;&nbsp;&nbsp; <a href='/control/site_sections/?action=deleteItem&itemID=".$itemInfo['id']."' onClick='return confirm(\"������ ����� ������ ������������. ������� ������?\");'><button class='btn btn-danger' type='button'><i class='fa fa-trash-o' style='margin-right:3px'></i> ������� ������</button></a>

			<br><br><b>URL:</b>&nbsp; <a href='/".$itemInfo['full_url']."/' target='_blank'>https://".$_SERVER['SERVER_NAME']."/".$itemInfo['full_url']."/</a>

			<br><br>
            <div class='form-group' style='width:60%'>
                <label>��������������:</label> &nbsp; ".buildAllSiteSectionsSelect($itemInfo['parent_id'])."
            </div>
            
			<div class='form-group' style='width:60%'>
                <label>������� �������� ������� (��-������): <span style='color:red'>*</span></label>
                <input type='text' name='site_sections_form_name' id='site_sections_form_name' class='form-control form_required' data-required-label='���, ������� ������� �������� ������� ��-������' value='".$itemInfo['name']."' />
            </div>
            
            <!-- ajax-����������� ��� ���� '��������' -->
            <div id='site_sections_form_name_alert_div' class='alert alert-info hidden width_95'></div>
            
			<div class='form-group' style='width:60%'>
                <label>���������� (��-���������): <span style='color:red'>*</span></label>
                <input type='text' name='site_sections_form_url' id='site_sections_form_url' class='form-control form_required' data-required-label='���, ������� �������� ����������' value='".$itemInfo['url']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>��������� ��������:</label>
                <input type='text' name='site_sections_form_title' id='site_sections_form_title' class='form-control' value='".$itemInfo['title']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>Meta keywords:</label>
                <input type='text' name='site_sections_form_keywords' id='site_sections_form_keywords' class='form-control' value='".$itemInfo['keywords']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>Meta description:</label>
                <input type='text' name='site_sections_form_description' id='site_sections_form_description' class='form-control' value='".$itemInfo['description']."' />
            </div>

			<div class='form-group' style='width:95%'>
                <label>������ ���������:</label>
                <input type='text' name='site_sections_form_navigation' id='site_sections_form_navigation' class='form-control' value='".$itemInfo['navigation']."' />
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>������ ��������� � ������ ������:
                       <br />
                       <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
                </label>
                <textarea name='site_sections_form_full_navigation' id='site_sections_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$itemInfo['full_navigation']."</textarea>
            </div>
            
			<div class='form-group' style='width:95%'>
                <label>��������� h1:</label>
                <input type='text' name='site_sections_form_h1' id='site_sections_form_h1' class='form-control' value='".$itemInfo['h1']."' />
            </div>
			
			<div class='form-group'>
                <label>HTML-���:</label>
                <textarea name='site_sections_form_html_code_1' id='site_sections_form_html_code_1' class='form-control' style='width:95%;height:450px'>".$content."</textarea>
            </div>
            
            <div class='form-group' style='width:95%'>
                <label>����� ��� ������������ � �������:</label> &nbsp; 
                <input id='site_sections_form_footeranchor' name='site_sections_form_footeranchor' class='form-control' value='".$itemInfo['footeranchor']."' />
            </div>

            <!--
            <div class='form-group' style='width:95%'>
                <label>���� ������ &quot;������&quot;:</label> &nbsp; 
                <textarea id='site_sections_form_right_menu_services' name='site_sections_form_right_menu_services' class='form-control' style='width:95%;height:100px'>".$itemInfo['right_menu_services']."</textarea>
            </div>
            -->
            
			<button class='site_sections_edit_save_changes btn btn-primary submit_button' type='button' style='margin-top:5px'>��������� ����������</button>
            
            &nbsp; <div class='ajax_result bottom'></div>
            
            <br /><br />
            
            <div class='form-group' style='width:50%;position:relative'>
                <label>
                    <input type='checkbox' name='site_sections_form_is_showable' id='site_sections_form_is_showable' class='form_checkbox' ".(!empty($itemInfo['is_showable']) ? 'checked="checekd"' : '')." />&nbsp; ���������� ������ �� �����
                </label>
			</div>
            
            <div class='form-group'>
                <b>������ ���� � �������:</b> &nbsp; <span style='color:#aaaaaa;font-size:14px'>".PATH_TO_PUBLIC_SITE_SECTIONS.$itemInfo['file_name_1']."</span>
            </div>
            
			<input type='hidden' id='id' name='id' value='".$_GET['itemID']."' />
		</form>
		";
    }
} # /����� �������������� ������� �����

# ����� ���������� ������� �����
function showAddForm()
{
    global $dbh;
    
    return "
	<script type='text/javascript' src='/control/site_sections/index.js'></script>
	<form id='site_sections_add_form' action='/control/site_sections/?action=addItemSubmit' name='form1' method='post' enctype='multipart/form-data' id='addItemForm' style='font-size:14px;position:relative'>
        <button class='btn btn-primary submit_button' type='submit'>������� ������</button>
        
        &nbsp;&nbsp;&nbsp; <a href='/control/site_sections/'><button class='btn btn-success' type='button'>
        <i class='fa fa-share-square' style='margin-right:3px'></i>
        ������� � ������
        </button></a>
        
		<br /><br /><b>URL:</b>&nbsp; <a href='/' target='_blank'>https://".$_SERVER['SERVER_NAME']."</a>

        <br><br>
        <div class='form-group' style='width:60%'>
            <label>��������������:</label> &nbsp; ".buildAllSiteSectionsSelect()."
        </div>
        
        <div class='form-group' style='width:60%'>
            <label>������� �������� ������� (��-������): <span style='color:red'>*</span></label>
            <input type='text' name='site_sections_form_name' id='site_sections_form_name' class='form-control form_required' data-required-label='���, ������� ������� �������� ������� ��-������' value='".$_POST['site_sections_form_name']."' />
        </div>
        
        <!-- ajax-����������� ��� ���� '��������' -->
        <div id='site_sections_form_name_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group' style='width:60%'>
            <label>���������� (��-���������): <span style='color:red'>*</span></label>
            <input type='text' name='site_sections_form_url' id='site_sections_form_url' class='form-control form_required' data-required-label='���, ������� �������� ����������' value='".$_POST['site_sections_form_url']."' />
        </div>
        
        <!-- ajax-����������� ��� ���� '����������' -->
        <div id='site_sections_form_url_alert_div' class='alert alert-info hidden width_95'></div>
        
        <div class='form-group' style='width:95%'>
            <label>��������� ��������:</label>
            <input type='text' name='site_sections_form_title' id='site_sections_form_title' class='form-control' value='".$_POST['site_sections_form_title']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>Meta keywords:</label>
            <input type='text' name='site_sections_form_keywords' id='site_sections_form_keywords' class='form-control' value='".$_POST['site_sections_form_keywords']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>Meta description:</label>
            <input type='text' name='site_sections_form_description' id='site_sections_form_description' class='form-control' value='".$_POST['site_sections_form_description']."' />
        </div>

        <div class='form-group' style='width:95%'>
            <label>������ ���������:</label>
            <input type='text' name='site_sections_form_navigation' id='site_sections_form_navigation' class='form-control' value='".$_POST['site_sections_form_navigation']."' />
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>������ ��������� � ������ ������:
                   <br />
                   <span style='font-weight:normal'>* ���� �������, �� ����� ��������� ������ ��������� �� ����� ����:</span>
            </label>
            <textarea name='site_sections_form_full_navigation' id='site_sections_form_full_navigation' class='form-control' style='width:95%;height:100px'>".$_POST['site_sections_form_full_navigation']."</textarea>
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>��������� h1:</label>
            <input type='text' name='site_sections_form_h1' id='site_sections_form_h1' class='form-control' value='".$_POST['site_sections_form_h1']."' />
        </div>
        
        <div class='form-group'>
            <label>HTML-���:</label>
            <textarea name='site_sections_form_html_code_1' id='site_sections_form_html_code_1' class='form-control' style='width:95%;height:450px'>".$_POST['site_sections_form_html_code_1']."</textarea>
        </div>
        
        <div class='form-group' style='width:95%'>
            <label>����� ��� ������������ � �������:</label> &nbsp; 
            <input id='site_sections_form_footeranchor' name='site_sections_form_footeranchor' class='form-control' value='".$_POST['site_sections_form_footeranchor']."' />
        </div>

        <!--
        <div class='form-group' style='width:95%'>
            <label>���� ������ &quot;������&quot;:</label> &nbsp; 
            <textarea id='site_sections_form_right_menu_services' name='site_sections_form_right_menu_services' class='form-control' style='width:95%;height:100px'>".$_POST['site_sections_form_right_menu_services']."</textarea>
        </div>
        -->
        
        <div class='form-group' style='width:50%;position:relative'>
            <label>
                <input type='checkbox' name='site_sections_form_is_showable' id='site_sections_form_is_showable' class='form_checkbox' checked='checekd' />&nbsp; ���������� ������ �� �����
            </label>
        </div>
        
        <button class='btn btn-primary submit_button' type='submit'>������� ������</button>
	</form>
	";
} # /����� ���������� �������

# ������� ����� ������
function addItemSubmit()
{
	global $dbh, $html;
	
	# print_r($_POST);
	# ������ �� ������� ������� URL'�: https://www.kupi-krovat.ru/control/site_sections/?action=addItemSubmit
	if (!empty($_POST))
	{
        # �������� + ������ ��������� POST-����������
        preparePOSTVariables(); # print_r($_POST); exit;

		# ��������� ������ � ��
		$lastInsertID = addItemToDB(); # echo $lastInsertID.'<hr />';
		# ���� ������ ������� ��������
		if (!empty($lastInsertID))
		{
            $fullURL = getFullURL(array('parent_id' => $_POST['site_sections_form_parent_id'], 'url' => $_POST['site_sections_form_url']));
            $fileName = getFileName(array('id' => $lastInsertID, 'full_url' => $fullURL)); # echo 'new file name: '.$fileName;
            $fullPathToNewFileName = PATH_TO_PUBLIC_SITE_SECTIONS.$fileName; # echo 'fullPathToNewFileName: '.$fullPathToNewFileName;
            
			# ��������� ������ � ����
            saveContentToFile($fullPathToNewFileName, $_POST['site_sections_form_html_code_1']);
            
            # ��������� �������� ����� � ��
            $sql = '
            update '.DB_PREFIX.'site_sections
            set file_name_1 = :file_name_1
            where id = :id
            '; # echo '<pre>'.$sql."</pre><hr />";
            $sth = $dbh->prepare($sql);
            $sth->bindParam(':file_name_1', $fileName);
            $sth->bindParam(':id', $lastInsertID, PDO::PARAM_INT);
            $sth->execute();

			# ������� ����� ��������������
			$fullUrlForEdit = 'https://'.$_SERVER['SERVER_NAME']."/control/site_sections/?action=editItem&itemID=".$lastInsertID.'&success=1';  # echo $fullUrlForEdit.'<hr />';
			header('Location: '.$fullUrlForEdit);
		}
		# ���� �������� ������ � ������ �� ��������
		else
		{
            $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ������ �� ��������. ����������, ���������� � ������������.';
            if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr class="slim">'.$GLOBALS['error'];
            return showAddForm();
		}
	}
	# ���� ������: https://news.youroute.ru/control/news/addItemSubmit/ � ��� ���� $_POST ������
	else
	{
		# ������� ������ ��������
        $GLOBALS['tpl_failure'] = '� ���������, �������� ������ � ������ �� ��������. ����������, ���������� � ������������.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
        return showAddForm();
	}
} # /������� ����� ������

# ������� ������
function deleteItem(){
	
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID']))
	{
		# ������� ������
		$GLOBALS['tpl_failure'] = '������ �� ������. ����������, ���������� � ������������.';
        if (!empty($GLOBALS['error'])) $GLOBALS['tpl_failure'] .= '<hr>'.$GLOBALS['error'];
		# ������� ������ ��������
        showItems();
	}
	else
	{
		# �������� ������ �� �������
		$itemInfo = getItemInfo($_GET['itemID']); # echo '<pre>'.(print_r($itemInfo, true)).'</pre>';

		# �������� ��� ����� �������
		$fileName = $itemInfo['file_name_1']; # echo $fileName.'<hr />';

		# ������� ���� �������
		$fullPathToFileName = PATH_TO_PUBLIC_SITE_SECTIONS.$fileName; # echo 'fullPathToFileName: '.$fullPathToFileName.'<hr />';
		if (!empty($fullPathToFileName) && file_exists($fullPathToFileName)) unlink($fullPathToFileName);
		
		# ������� ������ �� ��
		$result = deleteItemFromDB(); # echo $result.'<hr />';
		if (!empty($result))
		{
			$GLOBALS['tpl_success'] = '������ ������� ������.';
			# ������� ������ ��������
			return showItems();
		}
		else
		{
            if (empty($GLOBALS['tpl_failure'])) $GLOBALS['tpl_failure'] = '� ���������, ������ �� ������. ����������, ���������� � ������������.';
			# ������� ������ ��������
			return showItems();
		}
	}
} # /������� ������

# �������� �������
# ���� ����� ��������� � ���� ����� �����, �������� ����������� �������� ���� ����������� 
function deleteItemFromDB()
{
	# �������� ����������
	if (empty($_GET['itemID'])) return;
	
	global $dbh;
    
    # ���������, ���������� �� ������
    $sql = '
    select 1
    from '.DB_PREFIX.'site_sections
    where id = :id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $_ = $sth->fetchColumn();
    if (empty($_))
    {
        $GLOBALS['tpl_failure'] = '� ���� �� ������ ������ � id='.$_GET['itemID'];
        return;
    }
    
    # ���� ���� �������, ������� ������
    $sql = '
    select 1
    from '.DB_PREFIX.'site_sections
    where parent_id = :parent_id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':parent_id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $_ = $sth->fetchColumn();
    if (!empty($_))
    {
        $GLOBALS['tpl_failure'] = '� ���������� ������� ���� �������.<br />���, ������� ���� �������� ���������� �������, ����� ����� ����� ������� ������� ������.';
        return;
    }

	# ������� ������ �� ������� backup'��
	$sql = '
	delete from '.DB_PREFIX.'backups
	where table_name = "site_sections"
          and entry_id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
	
	# ������� ������
	$sql = '
	delete from '.DB_PREFIX.'site_sections
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
	if ($sth->execute()) return 1;
} # /�������� �������

# ��������� ������ � ��
function addItemToDB()
{
	global $dbh;
	
	if (!empty($_POST['site_sections_form_name'])
		and !empty($_POST['site_sections_form_url']))
	{ 
		$sql = '
        insert into '.DB_PREFIX.'site_sections
        (parent_id,
         name,
         url, 
         full_url, 
         title,
         keywords,
         description,
         navigation, 
         full_navigation, 
         h1, 
         footeranchor,
         right_menu_services,
         is_showable)
        values
        (:parent_id,
         :name,
         :url,
         :full_url,
         :title,
         :keywords,
         :description,
         :navigation,
         :full_navigation,
         :h1,
         :footeranchor,
         :right_menu_services,
         :is_showable)
        '; # echo $sql.'<hr />';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':parent_id', $_POST['site_sections_form_parent_id']);
        $sth->bindParam(':name', $_POST['site_sections_form_name']);
        $_POST['site_sections_form_url'] = trim(str_replace('/', '', $_POST['site_sections_form_url']));
        $sth->bindParam(':url', $_POST['site_sections_form_url']);
        $fullURL = getFullURL(array('parent_id' => $_POST['site_sections_form_parent_id'], 'url' => $_POST['site_sections_form_url']));
        $sth->bindParam(':full_url', $fullURL);
        $sth->bindParam(':title', $_POST['site_sections_form_title']);
        $sth->bindParam(':keywords', $_POST['site_sections_form_keywords']);
        $sth->bindParam(':description', $_POST['site_sections_form_description']);
        $sth->bindParam(':navigation', $_POST['site_sections_form_navigation']);
        # full_navigation
        if (empty($_POST['site_sections_form_full_navigation'])) $_POST['site_sections_form_full_navigation'] = null;
        $sth->bindParam(':full_navigation', $_POST['site_sections_form_full_navigation']);
        # footeranchor
        if ($_POST['site_sections_form_footeranchor'] == '') $_POST['site_sections_form_footeranchor'] = null;
        $sth->bindParam(':footeranchor', $_POST['site_sections_form_footeranchor']);
        # right_menu_services
        if ($_POST['site_sections_form_right_menu_services'] == '') $_POST['site_sections_form_right_menu_services'] = null;
        $sth->bindParam(':right_menu_services', $_POST['site_sections_form_right_menu_services']);
        # h1
        $sth->bindParam(':h1', $_POST['site_sections_form_h1']);
        $isShowable = $_POST['site_sections_form_is_showable'] == 'on' ? 1 : null;
        $sth->bindParam(':is_showable', $isShowable);
		try { if ($sth->execute()) {
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			if (!empty($last_insert_id)) return $last_insert_id;
			else return;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { $GLOBALS['error'] = 'Error in SQL: '.$sql.' ('.$e->getMessage().')'; }}
	}
    else echo '�� �������� site_sections_form_name ��� site_sections_form_url';
} # /��������� ������ � ��

# ������ ������ ��������
function buildAllSiteSectionsSelect($parentID = NULL){
    global $dbh;

    # static sections for template
    $sql = '
    select id, 
           url, 
           name
    from '.DB_PREFIX.'site_sections 
    where url != "/"
          and parent_id = "1"
    order by name
    '; # echo '<pre>'.$sql."</pre><hr />";
    $staticSectionsQuery = $dbh->query($sql)->fetchAll(); # print_r($staticSectionsQuery);
    $_c = count($staticSectionsQuery);
    $staticSections[] = "<option value='1'>�������</option>";
    if ($_c != 0) for ($i=0;$i<$_c;$i++)
	{
		# selected
		if ($staticSectionsQuery[$i]['id'] == $parentID) $selected = ' selected="selected"';
		else unset($selected);
	
        $staticSections[] = "<option value='".$staticSectionsQuery[$i]['id']."'".$selected.">".str_repeat('-', 4)."&raquo; ".$staticSectionsQuery[$i]['name']."</option>";
        # subsections
        $sql2 = '
        select id, 
               url, 
               name 
        from '.DB_PREFIX.'site_sections 
        where parent_id = '.$staticSectionsQuery[$i]['id'].' 
        order by name
        '; # echo '<pre>'.$sql."</pre><hr />";
        $staticSectionsQuery2 = $dbh->query($sql2)->fetchAll();
        $_c2 = count($staticSectionsQuery2);
        if ($_c2 != 0) for ($j=0;$j<$_c2;$j++)
		{
			# selected
			if ($staticSectionsQuery2[$j]['id'] == $parentID) $selected = ' selected="selected"';
			else unset($selected);
			
            $staticSections[] = "<option value='".$staticSectionsQuery2[$j]['id']."'".$selected.">".str_repeat('-', 8)."&raquo; ".$staticSectionsQuery2[$j]['name']."</option>";
            # subsections
            $sql3 = '
            select id, 
                   url, 
                   name 
            from '.DB_PREFIX.'site_sections 
            where parent_id = '.$staticSectionsQuery2[$j]['id'].'
            order by name
            '; # echo '<pre>'.$sql."</pre><hr />";
            $staticSectionsQuery3 = $dbh->query($sql3)->fetchAll();
            $_c3 = count($staticSectionsQuery3);
            if ($_c3 != 0) for ($x=0;$x<$_c3;$x++)
			{
				# selected
				if ($staticSectionsQuery3[$x]['id'] == $parentID) $selected = ' selected="selected"';
				else unset($selected);
				
                $staticSections[] = "<option value='".$staticSectionsQuery3[$x]['id']."'".$selected.">".str_repeat('-', 12)."&raquo; ".$staticSectionsQuery3[$x]['name']."</option>";
                # subsections
                $sql4 = '
                select id,
                       url,
                       name
                from '.DB_PREFIX.'site_sections 
                where parent_id = '.$staticSectionsQuery3[$x]['id'].'
                order by name
                '; # echo '<pre>'.$sql."</pre><hr />";
                $staticSectionsQuery4 = $dbh->query($sql4)->fetchAll();
                $_c4 = count($staticSectionsQuery4);
                if ($_c4 != 0) for ($d=0;$d<$_c4;$d++)
				{
					# selected
					if ($staticSectionsQuery4[$d]['id'] == $parentID) $selected = ' selected="selected"';
					else unset($selected);
					
                    $staticSections[] = "<option value='".$staticSectionsQuery4[$d]['id']."'".$selected.">".str_repeat('-', 16)."&raquo; ".$staticSectionsQuery4[$d]['name']."</option>";
                }
            }
        }
    }

    # ��� ������ ���������� �������, �������� ������ option � select'�
    if ($_GET['action'] == 'addItemSubmit') $dataSelected = ' data-selected="'.$_POST['site_sections_form_parent_id'].'"';
    
    return '<select id="site_sections_form_parent_id" name="site_sections_form_parent_id" class="form-control"'.$dataSelected.'>'.implode("\n", $staticSections).'</select>';
}

# ������ SELECT � �������� ��� ������������ � �������
/*
function buildAllFooteranchors($footerAnchorID = NULL)
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
    $result = '<select id="site_sections_form_footeranchor_id" name="site_sections_form_footeranchor_id" class="form-control">'.PHP_EOL.'<option value="null">�� ������</option>'.PHP_EOL.$options.'</select>';
    if (!empty($result)) return $result;
} */ # /������ SELECT � �������� ��� ������������ � �������

# �������� ������ �� �������
function getItemInfo()
{
	global $dbh;
	
	# �������� ����������
	if (empty($_GET['itemID'])) return;
	
	$sql = '
	select *
	from '.DB_PREFIX.'site_sections
	where id = :id
	'; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['itemID'], PDO::PARAM_INT);
    $sth->execute();
    $itemInfo = $sth->fetch();
	if (!empty($itemInfo)) return $itemInfo;
	else return;
} # /�������� ������ �� �������

# /����������