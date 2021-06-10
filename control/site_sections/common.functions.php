<?php # ����� ������� ��� index.php � ajax.php

function getFileName($_params)
{
    # �������� ����������
    if (empty($_params['id'])) exit('� ������� getFileName �� ������� id');
    if (empty($_params['full_url'])) exit('� ������� getFileName �� ������� full_url');
    
    # return str_replace('/', '.', $_params['full_url']).'__file_name_1.'.$_params['id'].'.php';
    return $_params['id'].'.file_name_1.php';
}

# ��������� ������ URL �� id �������
function getFullURL($_params)
{
    global $dbh;
    
    # �������� ����������
    if (empty($_params['parent_id'])) return;
    if (empty($_params['url'])) return;
    
    $sql = '
    select full_url 
    from '.DB_PREFIX.'site_sections
    where id = :parent_id
    '; # echo '<pre>'.$sql."</pre><hr />";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':parent_id', $_params['parent_id'], PDO::PARAM_INT);
    $sth->execute();
    $parentFullURL = $sth->fetchColumn(); # echo 'parentFullURL: '.$parentFullURL."\n";
    if ($parentFullURL == '/') unset($parentFullURL); # echo 'parentFullURL: '.$parentFullURL."\n";
    
    $fullURL = $parentFullURL.'/'.$_params['url'];
    if ($fullURL[0] == '/') $fullURL = substr($fullURL, 1); # echo 'fullURL: '.$fullURL."\n";
    return $fullURL;
} # /��������� ������ URL �� id �������

# ���������� ������� � ����
function saveContentToFile($fullPathToFile, $content)
{
    /*
    echo 'fullPathToFile: '.$fullPathToFile.'<br />';
    echo 'content: '.$content.'<br />';
    */

	# �������� ����������
	if (empty($fullPathToFile)) return;
	# if (empty($content)) return;
	
    if (file_put_contents($fullPathToFile, $content, LOCK_EX) !== false)
    {
        if (is_file($fullPathToFile)) chmod($fullPathToFile, 0755);
        return 1;
    }
    else echo '�� ���������� �������� ���������� � ����: '.$fullPathToFile;
} # /���������� ������� � ����

# �������� ������� ��� ����� �� ��
function getOldFileName1($itemID)
{
	# �������� ����������
	if (empty($itemID)) return;

	global $dbh;
	
	$sql = '
	select file_name_1
	from '.DB_PREFIX.'site_sections
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