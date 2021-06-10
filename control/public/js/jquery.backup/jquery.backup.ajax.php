<?php
# purpose: hadle ajax requests from jquery.backup.js
# date of creation: 2015.1.20
# author: romanov.egor@gmail.com

# ������������
# sleep(1.5); # exit();
# echo 'result'; exit;

# print_r($_POST);

header('Content-type: text/html; charset=windows-1251');

# ���������� � �������������� ����� ��� ������ � �� ����� PDO
include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');

# ���������� ������
include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');

# ���������� ������� ������ ���������� ��� ajax-��������
include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');

# ������ �� ������� c ������� �����
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# ��������� ���������, ������� ����� �������� javascript'� ajax-������
header('Content-type: text/html; charset=windows-1251');

# �������� + ������ ��������� POST-����������
preparePOSTVariables(); # print_r($_POST); exit;

# echo '<pre>'.(print_r($_POST, true)).'</pre>';

# �������

# ������ BACKUP �� ���������� ����
if ($_POST['action'] == 'makeBackup')
{
    # print_r($_POST);

	# $htmlCode = addslashes($_POST['html_code']);
    
    $sql = "
    insert into ".DB_PREFIX."backups
    (
     table_name,
     entry_id,
     field_name,
     date_add,
     html_code
    )
    values
    (
     :table_name,
     :entry_id,
     :field_name,
     now(),
     :html_code
    )
    "; # echo '<pre>'.$sql."</pre><hr />";
    $result = $dbh->prepare($sql);
    
    $result->bindParam(':table_name', $_POST['table_name'], PDO::PARAM_STR);
    $result->bindParam(':entry_id', $_POST['entry_id'], PDO::PARAM_INT);
    $result->bindParam(':field_name', $_POST['field_name'], PDO::PARAM_STR);
    $result->bindParam(':html_code', $_POST['html_code'], PDO::PARAM_STR);
    try
    {
        if ($result->execute())
        {
            # echo 1;
            $last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
            if (!empty($last_insert_id)) echo $last_insert_id;
            # else return;
        }
    }
    catch (PDOException $e)
    {
        if (DB_SHOW_ERRORS)
        {
            echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
    
    
} # /������ BACKUP �� ���������� ����

# �������� ������ BACKUP'�� ��� �������� ����
elseif ($_POST['action'] == 'getAllBackupsForSpecificField')
{
    /*
    [action] => getAllBackups
    [table_name] => static_sections
    [entry_id] => 11
    [fields_name] => Array
        (
            [0] => name
            [1] => page_title
            [2] => navigation
            [3] => h1
            [4] => text
        )
    */
    
    $sql = "
    select id,
           field_name,
           date_format(date_add,'%e') as date_add_day,
           elt(month(date_add), '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������') as date_add_month,
           date_format(date_add,'%Y') as date_add_year,
           date_format(date_add,'%H:%i:%s') as date_add_time,
           html_code
    from ".DB_PREFIX."backups
    where table_name = :table_name
          and entry_id = :entry_id
          and field_name = :field_name
    order by date_add desc
    "; # echo '<pre>'.$sql."</pre><hr />";
    $result = $dbh->prepare($sql);
    
    $result->bindParam(':table_name', $_POST['table_name'], PDO::PARAM_STR);
    $result->bindParam(':entry_id', $_POST['entry_id'], PDO::PARAM_INT);
    $result->bindParam(':field_name', $_POST['field_name'], PDO::PARAM_STR);
    try
    {
        if ($result->execute())
        {
            $_ = $result->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
            $_c = count($_);
            if (!empty($_))
            {
                for ($i=0;$i<$_c;$i++)
                {
                    if (!empty($_[$i]['date_add_month'])) $_[$i]['date_add_month'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $_[$i]['date_add_month']);
                    if (!empty($_[$i]['html_code']))
                    {
                        $_[$i]['html_code'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $_[$i]['html_code']);
                        $_[$i]['html_code'] = htmlspecialchars($_[$i]['html_code'], ENT_QUOTES);
                    }
                }
                echo json_encode($_);
                # print_r($_);
            }
        }
    }
    catch (PDOException $e)
    {
        if (DB_SHOW_ERRORS)
        {
            echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /�������� ������ BACKUP'�� ��� �������� ����

# �������� ��� BACKUP'� ��� ���� �����
elseif ($_POST['action'] == 'getAllBackups')
{
    /*
    [action] => getAllBackups
    [table_name] => static_sections
    [entry_id] => 11
    [fields_name] => Array
        (
            [0] => name
            [1] => page_title
            [2] => navigation
            [3] => h1
            [4] => text
        )
    */
    
    # prepare where in statement
    $fields_name = $_POST['inputs_list']['fields_name']; # print_r($fields_name);
    $inQuery = implode(',', array_fill(0, count($fields_name), '?')); # echo $inQuery;
    # /prepare where in statement
    
    $sql = "
    select id,
           field_name,
           date_format(date_add,'%e') as date_add_day,
           elt(month(date_add), '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������') as date_add_month,
           date_format(date_add,'%Y') as date_add_year,
           date_format(date_add,'%H:%i:%s') as date_add_time,
           html_code
    from ".DB_PREFIX."backups
    where field_name in (".$inQuery.")
          and table_name = ?
          and entry_id = ?
    order by date_add desc
    "; # echo '<pre>'.$sql."</pre><hr />";
    $result = $dbh->prepare($sql);
    
    # where in statement
    # bindValue is 1-indexed, so $k+1
    foreach ($fields_name as $k => $name){ # $result->bindValue(($k+1), $name);
        $result->bindValue(($k+1), $name); # echo $k.':'.$name;
    }
    # /where in statement
    
    # echo $_POST['inputs_list']['table_name'];
    $result->bindValue((count($fields_name) + 1), $_POST['inputs_list']['table_name'], PDO::PARAM_STR);
    $result->bindValue((count($fields_name) + 2), $_POST['inputs_list']['entry_id'], PDO::PARAM_INT);
    try
    {
        if ($result->execute())
        {
            $_ = $result->fetchAll(); # echo '<pre>'.(print_r($_, true)).'</pre>';
            $_c = count($_);
            if (!empty($_))
            {
                for ($i=0;$i<$_c;$i++)
                {
                    if (!empty($_[$i]['date_add_month'])) $_[$i]['date_add_month'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $_[$i]['date_add_month']);
                    if (!empty($_[$i]['html_code']))
                    {
                        $_[$i]['html_code'] = iconv('windows-1251', 'UTF-8//TRANSLIT', $_[$i]['html_code']);
                        $_[$i]['html_code'] = htmlspecialchars($_[$i]['html_code'], ENT_QUOTES);
                    }
                }
                echo json_encode($_);
                # print_r($_);
            }
        }
    }
    catch (PDOException $e)
    {
        if (DB_SHOW_ERRORS)
        {
            echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
} # /�������� ��� BACKUP'� ��� ���� �����

# ������� BACKUP
elseif ($_POST['action'] == 'removeBackup')
{
    $sql = "
    delete from ".DB_PREFIX."backups
    where id = :id
    "; # echo '<pre>'.$sql."</pre><hr />";
    $result = $dbh->prepare($sql);
    $result->bindParam(':id', $_POST['backup_id'], PDO::PARAM_INT); # echo $itemID.'<hr />';
    try
    {
        if ($result->execute())
        {
            echo 1;
        }
    }
    catch (PDOException $e)
    {
        if (DB_SHOW_ERRORS)
        {
            echo "Error in SQL:<br /><br />".$sql."<br /><br />".$e->getMessage();
            exit;
        }
    }
    
} # /������� BACKUP

# /�������

# /�������