<?php

### �������
# print_r($_GET);
# print_r($_POST);

# sleep(5);

# ������ �� ������� c ������� �����
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# ��������� ���������, ������� ����� �������� javascript'� ajax-������
header('Content-type: text/html; charset=windows-1251');

# ���������� � �������������� ����� ��� ������ � �� ����� PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# ���������� ������
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# ���������� ������� ������ ���������� ��� ajax-��������
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# ��������, ������ + ������ ��������� GET-����������
preparePOSTVariables(); # print_r($_POST); exit;

# ������
if (!empty($_POST['id'])) $_POST['id'] = (int) $_POST['id'];
if (!empty($_POST['template_id'])) $_POST['template_id'] = (int) $_POST['template_id'];
# if (!empty($_POST['file_name'])) $_POST['file_name'] = htmlentities($_POST['filename']);
# if (!empty($_POST['name'])) $_POST['name'] = htmlentities($_POST['name']);

# ������
# ����� �� ��������
if ($_GET['action'] == 'searchTemplates') searchTemplates(); 
elseif ($_POST['action'] == 'searchTemplates') searchTemplates(); 
elseif ($_POST['action'] == 'getAllBackups') # ��� ������ �������
{
	if (!empty($_POST['template_id'])) getAllBackups();
}
elseif ($_GET['action'] == 'getAllBackupsMulti') # ��� ���������� �������� �����
{
	if (!empty($_GET['templates'])) getAllBackupsMulti($_GET['templates']);
}
elseif ($_POST['action'] == 'makeBackup')
{
	if (!empty($_POST['template_id'])
		and !empty($_POST['html_code'])
		)
	{
		# ������� �������
		// clearTable();
		# ��������� ������
		makeBackup();
		# �������� ������ ���� backup'�� ��� ������� �������
		getAllBackups();
	}
}
elseif ($_POST['action'] == 'removeBackup')
{
	if (!empty($_POST['id']))
	{
		# ������ ������
		removeBackup();
		# �������� ������ ���� backup'�� ��� ������� �������
		# getAllBackups();
	}
}
// ��������� ������ �� ������
elseif ($_POST['action'] == 'checkTemplateForErrorsAddItem')
{
	if (!empty($_POST['file_name'])) checkTemplateForErrorsAddItem();
}
// ��������� ������ �� ������
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
		// ���������, �������� �� ���������� � ��������� ��� ������
		checkTemplatesDirForWriting();
	}
}
elseif ($_POST['action'] == 'edit_template_submit')
{
    # �������� ����������
    if (empty($_POST['name'])) exit;
    # if (empty($_POST['file_name'])) exit;
    # if (empty($_POST['html_code'])) exit;
    if (empty($_POST['id'])) exit;
    
    edit_template_submit();
}
# �������� �� ��������, ���������� �� ������
elseif ($_POST['action'] == 'check_template_for_existence_by_name')
{
    # �������� �� ��������, ���������� �� ������ �� ��������
    $sql = '
    select id
    from '.DB_PREFIX.'templates
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
    # �������� �� ��������, ���������� �� ������ �� ��������
    $sql = '
    select id
    from '.DB_PREFIX.'templates
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
# /������

# �������

# ��������� ���������� �� ������� ��� ��������������
function edit_template_submit()
{
    # echo '<pre>'.(print_r($_POST, true)).'</pre>'; exit;
    
    global $dbh;
    
    if (!empty($_POST['id']))
    {
        $sql = '
        update '.DB_PREFIX.'templates
        set name = :name,
            url = :url
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':name', $_POST['name']);
        # $sth->bindParam(':file_name', $_POST['file_name']);
        $sth->bindParam(':url', $_POST['url']);
        $sth->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        try { if ($sth->execute()) {
                # �������� ������� ��� ����� �� ��
                $oldFileName = getOldFileName($_POST['id']); # echo $oldFileName."<hr />";

                # ��������� ������ � ����
                if (saveContentToFile(PATH_TO_PUBLIC_TEMPLATES,
                                      $oldFileName, # $_POST['file_name'], # new file name
                                      $oldFileName, # old file name
                                      !empty($_POST['html_code']) ? $_POST['html_code'] : '')) echo 'success';
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); exit; } }
    }
} # /��������� ���������� �� ������� ��� ��������������

# ���������� ������ � ����
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

	# �������� ����������
	if (empty($pathToTeplates)) return;
	if (empty($newFileName)) return;
	# if (empty($htmlCode)) return;
	
	# ���� ������� ������ ��� ����� � ��� �� ����� ������ �����,
	# ������� ������ ����
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
    if (file_put_contents($fullPathToNewFile, $htmlCode, LOCK_EX) !== false)
    {
        if (is_file($fullPathToNewFile)) chmod($fullPathToNewFile, 0755);
        return 1;
    }
} # /���������� ������ � ����

# �������� ������� ��� ����� �� ��
function getOldFileName($itemID)
{
	# �������� ����������
	if (empty($itemID)) return;

	global $dbh;
	
	$sql = '
	select file_name
	from '.DB_PREFIX.'templates
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
    
} # /�������� ������� ��� ����� �� ��

# ����� �� ��������
function searchTemplates()
{
	if (empty($_GET['q']) && !empty($_POST['q'])) $_GET['q'] = $_POST['q'];
    
	if (!empty($_GET['q']))
	{
		global $dbh;
		
		# ��������� ������ ��������
		$sql = "
		select id,
			   name,
			   file_name,
			   (select count(1) from ".DB_PREFIX."templates_backups where template_id = i.id) as backups_count
		from ".DB_PREFIX."templates as i
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
                <tr>
                    <td class="center">
                        <a class="block" href="/control/templates/?action=editItem&itemID='.$_[$i]['id'].'">
                            <i class="fa fa-edit size_18"></i>
                        </a>
                    </td>
                    <td class="center">'.$_[$i]['name'].'</td>
                    <td class="center">'.$_[$i]['file_name'].'</td>
                    <td class="center">'.$_[$i]['backups_count'].'</td>
                    <td class="center">
                        <a class="block" title="������� ������" href="/control/templates/?action=deleteItem&itemID='.$_[$i]['id'].'" onClick="return confirm(\'������ ����� ������ ������������. ������� ������?\')">
                            <i class="fa fa-trash-o size_18"></i>
                        </a>
                    </td>
                </tr>
				';
			}
			# /��������� ������ ��������
			
			if (!empty($rows) and is_array($rows))
			{
				$rows = implode("\n", $rows);
			}
			
			echo '
            <div id="resultSet">
            <table border="1" cellpadding="2" class="table table-striped table-bordered table-hover projects_list">
                <tr>
                    <th class="center" style="width:50px;white-space:nowrap">������</th>
                    <th class="center" style="width:35%">��������</th>
                    <th class="center">��� �����</th>
                    <th class="center" style="width:100px;white-space:nowrap">backup\'��</th>
                    <th class="center" style="width:100px;white-space:nowrap">��������</th>
                </tr>
                '.$rows.'
            </table>
            </div>
			';
		}
		else echo '<div id="ajax_search_result">�� ���������� ������� ������ �� �������.</div>';
	}
	else echo '<div id="ajax_search_result">�� ���������� ������� ������ �� �������.</div>';
}
# /����� �� ��������

# ������� backup ��� �������
function makeBackup()
{
	global $dbh;
	
	# �������������� ������ ��� POST �������
	# preparePOSTVariables();
	
	# $htmlCode = addslashes($_POST['html_code']);

	$sql = "
	insert into ".DB_PREFIX."templates_backups 
	(template_id, date_add, html_code, url)
	values
	(:template_id, unix_timestamp(), :html_code, :url)
	"; # echo '<pre>'.$sql."</pre><hr />";
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':template_id', $_POST['template_id'], PDO::PARAM_INT);
    $sth->bindParam(':html_code', $_POST['html_code']);
    $sth->bindParam(':url', $_POST['url']);
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

function getAllBackups() # �������� ������ ���� backup'�� ��� ������� �������
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
		   ELT(MONTH(from_unixtime(date_add, '%Y-%m-%d')), '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������') as month,
		   from_unixtime(date_add, '%Y %H:%i:%s') as year_and_time,
		   html_code
	from ".DB_PREFIX."templates_backups
	where 1
		  ".$condition."
	order by date_add desc
	"; # echo '<pre>'.$sql."</pre><hr />";
	$_ = $dbh->query($sql)->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
	$_c = count($_); # echo 'count: '.$_c.'</hr>';
	if (!empty($_c))
	{
		$result = array();
		# ��������� �������
		if ($_c >= 5)
		{
			$remainder = $_c - 5;
		}
		for ($i=0;$i<$_c;$i++)
		{
			# ���� ������� >= 5, �������� �������
			if ($_c >= 5 && $i == 5)
			{
				$result[] = "
				<div><a href='#' class='showAllBackups'>�������� ��� backup'� (".$remainder.")</a></div>
				<div style='display:none' id='allBackups'>
				";
			}
			
			$result[] = "
			<div id='".$_[$i]['id']."'>
				backup �� <span id='date".$_[$i]['id']."'>".$_[$i]['day']." ".$_[$i]['month']." ".$_[$i]['year_and_time']."</span>
				&nbsp;&nbsp;&nbsp; <a href='#' class='showBackup' backupID='showBackup".$_[$i]['id']."'>html-���</a>
				&nbsp;&nbsp;&nbsp; <a href='#' class='removeBackup' backupID='removeBackup".$_[$i]['id']."'>�������</a>
				<br />
				<div id='html_code".$_[$i]['id']."' style='display:none;margin:10px 0'><b>html-���:</b><br /><textarea class='form-control' style='width:95%;height:270px'>".htmlspecialchars($_[$i]['html_code'], ENT_QUOTES)."</textarea></div>
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
		else echo "backup'�� ���.";
	}
	else echo "backup'�� ���.";
} # /getAllBackups

function getAllBackupsMulti($templates) # �������� ������ ���� backup'�� ��� ���������� ��������
{ # input: $templates = array(template1ID, template2ID, template3ID)
    # checking variables
    if (empty($templates) || !is_array($templates)) return;
    
    global $dbh;
	
    $condition = " and template_id in '".implode(', ', $templates)."'";
	
	$sql = "
	select id,
		   template_id,
		   from_unixtime(date_add, '%e') as day,
		   ELT(MONTH(from_unixtime(date_add, '%Y-%m-%d')), '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������') as month,
		   from_unixtime(date_add, '%Y %H:%i:%s') as year_and_time,
		   html_code
	from ".DB_PREFIX."templates_backups
	where 1
		  ".$condition."
	order by date_add desc
	"; # echo '<pre>'.$sql."</pre><hr />";
	$_ = $dbh->query($sql); # echo '<pre>'.(print_r($_, true)).'</pre>';
	$_c = count($_); # echo 'count: '.$_c.'</hr>';
	if (!empty($_c))
	{
		$result = array();
		# ��������� �������
		if ($_c >= 5)
		{
			$remainder = $_c - 5;
		}
		for ($i=0;$i<$_c;$i++)
		{
			# ���� ������� >= 5, �������� �������
			if ($_c >= 5 && $i == 5)
			{
				$result[] = "
				<div><a href='#' class='showAllBackups'>�������� ��� backup'� (".$remainder.")</a></div>
				<div style='display:none' id='allBackups'>
				";
			}
			
			$result[] = "
			<div id='".$_[$i]['id']."'>
				backup �� <span id='date".$_[$i]['id']."'>".$_[$i]['day']." ".$_[$i]['month']." ".$_[$i]['year_and_time']."</span>
				&nbsp;&nbsp;&nbsp; <a href='#' class='showBackup' backupID='showBackup".$_[$i]['id']."'>html-���</a>
				&nbsp;&nbsp;&nbsp; <a href='#' class='removeBackup' backupID='removeBackup".$_[$i]['id']."'>�������</a>
				<br />
				<div id='html_code".$_[$i]['id']."' style='display:none;margin:10px 0'><b>html-���:</b><br /><input class='backup-for-phones' value='".htmlspecialchars($_[$i]['html_code'], ENT_QUOTES)."' /></div>
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
		else echo "backup'�� ���.";
	}
	else echo "backup'�� ���.";
} # /getAllBackupsMulti

# ������� ��������� backup
function removeBackup()
{
	global $dbh;
	
	# print_r($_POST);
	
	if (!empty($_POST['id']))
	{
		$sql = "
		delete from ".DB_PREFIX."templates_backups 
		where id = '".$_POST['id']."'
		"; # echo '<pre>'.$sql."</pre><hr />";
        $result = $dbh->prepare($sql);
        $result->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        try { if ($result->execute()) return 1; }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage(); exit; } }
	}
	else return;
}

# ������� �������
function clearTable()
{
	global $dbh;
	
	$sql = "
	delete from ".DB_PREFIX."templates_backups
	"; # echo '<pre>'.$sql."</pre><hr />";
	$result = $dbh->query($sql);
	if (!empty($result)) return 1;
	else return;
}

// ��������� ������ �� ������
function checkTemplateForErrorsAddItem()
{
	# ���� ���� ����������, ���������, ����� �� ��� ������������
	$fileName = $_POST['file_name'];
	$fullPathToTemplate = PATH_TO_PUBLIC_TEMPLATES.basename($fileName); # echo 'fullPathToTemplate: '.$fullPathToTemplate;
	# ���������, ���������� �� ����
	if (file_exists($fullPathToTemplate))
	{
		# ��������, ����� �� ������������ ����
		if (is_writable($fullPathToTemplate))
		{
            $result = array('result' => 'exists', 'message' => iconv('windows-1251', 'UTF-8//TRANSLIT', '
            <b>�����������</b>: ���� &quot;'.$fileName.'&quot; <b>��� ����������</b>.
            <br />���� ���� "html-���" �������� ������, ���������� ����� ��������� "��� ����".
            <br />���� ���� "html-���" ���������, html-��� ����� ������� � ����.
            '));
            echo json_encode($result);
		}
		else
		{
            $result = array('result' => 'exists', 'message' => iconv('windows-1251', 'UTF-8//TRANSLIT', '
            <b>������</b>: ���� "'.$fileName.'" ��� ����������, �� �� �������� ��� ������.
            <br />���������� ��������� �����: 0646.
            '));
            echo json_encode($result);
		}
	}
}

function checkTemplateForErrorsEditItem()
{
	# ���� ���� ����������, ���������, ����� �� ��� ������������
	$fileName = $_POST['filename'];
	$pathToTemplates = $_POST['pathToTemplates'];
	$fullPathToTemplate = $pathToTemplates.$fileName; # echo $fullPathToTemplate;
	# ���� ���� ����������
	if (file_exists($fullPathToTemplate))
	{
		# ���� ���� �� �������� ��� ������
		if (!is_writable($fullPathToTemplate))
		{
			echo "
			<b style='color:red' class='error'>������</b>: ���� &quot;{$fileName}&quot; �� �������� ��� ������. ���������� ��������� �����: 646.
			";
		}
	}
	# ���� ���� �� ����������
	else
	{
		echo "<b style='color:#cccccc'>�����������</b>: ���� &quot;{$fileName}&quot; �� ����������. ��� ���������� ������� ����� ������ ����� ����.";
	}
}

// ���������, �������� �� ���������� � ��������� ��� ������
function checkTemplatesDirForWriting()
{
	if (file_exists($_POST['pathToTemplates']))
	{
		if (!is_writable($_POST['pathToTemplates']))
		{
			echo "<b style='color:red' class='error'>������</b>: ���������� � ��������� �� �������� ��� ������. ���������� ��������� �����: 747.";
		}
	}
}

# /�������