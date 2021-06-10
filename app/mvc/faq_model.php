<?php
class faq_model extends model_base
{
	# �������� ������ ��������-������� ��� ������� �������� ��������-�������
	function getItemsForIndex()
	{
		$sql = '
        select id,
               url,
               h1,
               file_name
        from '.DB_PREFIX.'faq
        where is_showable = 1
        order by h1
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sql_for_count = "
		select count(1)
		from ".DB_PREFIX."faq
        where is_showable = 1
		"; # echo '<pre>'.$sql."</pre><hr />";
		$pages = new pages($this->routeVars['page'], # ������� ��������
						   10, # ������� �� ��������
						   $this->dbh, # ������ ���� ������
						   $this->routeVars, # ���������� ����������� ��������
						   $sql, # sql-������
						   $sql_for_count, # sql-������ ��� �������� ���������� �������
						   "/vopros/", # ����� �� 1� ��������
						   "/vopros/page%page%/", # ����� �� ��������� ��������
							1500 # ������������ ���������� ������� �� ��������
							);
		$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>';
		
		if (!empty($_result)) return $_result;
	} # /�������� ������ ��������-������� ��� ������� �������� ��������-�������

    # �������� �������-������ �� RANDOM
    # $idSelected - id �������-������, ������� ����� ��������� �� ������
    function getRandomItems($itemCount = 5, $idSelected = null)
    {
        # ���� ������ id �������-������, ������� �� ����� ��������
        unset($sqlCondition);
        if (!empty($idSelected)) $sqlCondition = ' and id != :id ';

        $sql = '
        select id,
               url,
               h1,
               file_name
        from '.DB_PREFIX.'faq
        where is_showable = 1
              '.$sqlCondition.'
        order by rand()
        limit :limit
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        $sth->bindValue(':limit', $itemCount, PDO::PARAM_INT);
        # ���� ������ id �������-������, ������� �� ����� ��������
        if (!empty($idSelected)) $sth->bindValue(':id', $idSelected, PDO::PARAM_INT);
        try
        {
            if ($sth->execute())
            {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� �������-������ �� RANDOM
    
    # �������� ���������� �� �������-������
    function getItemInfo($url)
    {
        # �������� ����������
        if (empty($url)) return;
        
		$sql = '
        select *
        from '.DB_PREFIX.'faq
        where url = :url
              and is_showable = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
        $sth->bindParam(':url', $url);
		try
		{
			if ($sth->execute())
			{
				$_ = $sth->fetch(); # print_r($_);
				if (!empty($_)) return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ���������� �� �������-������
    
    # �������� �������-������ ��� ����� "������ �������-������"
    function getFaqForBlockAnotherFaq($currentItemID)
    {
        # �������� ����������
        if (empty($currentItemID)) return;
        
		$sql = '
        select id,
               url,
               h1
        from '.DB_PREFIX.'faq
        where id != :id
              and is_showable = 1
        order by rand()
        limit 3
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
        $sth->bindParam(':id', $currentItemID, PDO::PARAM_INT);
		try
		{
			if ($sth->execute())
			{
				$_ = $sth->fetchAll(); # print_r($_);
				if (!empty($_)) return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� �������-������ ��� ����� "������ �������-������"
    
	# �������� ������ ������� ��� ����� �����
	function getItemsForMap()
	{
		$sql = "
		select id,
			   name,
			   url
		from ".DB_PREFIX."faq
        where is_showable = 1
		order by name
        "; # echo '<pre>'.$sql."</pre><hr />";
		$result = $this->dbh->prepare($sql);
		try
		{
			if ($result->execute())
			{
				$_ = $result->fetchAll(); # print_r($_);
				return $_;
			}
		}
		catch (PDOException $e)
		{
			if (DB_SHOW_ERRORS)
			{
				echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
				exit;
			}
		}
	} # /�������� ������ ������� ��� ����� �����
    
    # ������� ���������� ��������-�������
    function getItemsCount()
    {
		$sql = '
        select count(1)
        from '.DB_PREFIX.'faq
        where is_showable = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
		try
		{
			if ($sth->execute())
			{
				$_ = $sth->fetchColumn(); # print_r($_);
				if (!empty($_)) return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /������� ���������� ��������-�������
}