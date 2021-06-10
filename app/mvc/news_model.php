<?php
class news_model extends model_base
{
	# �������� ������ �������� ��� ������� �������� ��������
	function getItemsForIndex()
	{
		$sql = '
        select id,
               h1,
               date_format(date_add, "%e") as date_add_day,
               date_format(date_add, "%m") as date_add_month,
               date_format(date_add, "%Y") as date_add_year,
               date_format(date_add, "%d-%m-%Y") as date_add_formatted_2
        from '.DB_PREFIX.'news
        where is_showable = 1
        order by date_add desc,
                 h1
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sql_for_count = "
		select count(1)
		from ".DB_PREFIX."news
        where is_showable = 1
		"; # echo '<pre>'.$sql."</pre><hr />";
		$pages = new pages($this->routeVars['page'], # ������� ��������
						   20, # ������� �� ��������
						   $this->dbh, # ������ ���� ������
						   $this->routeVars, # ���������� ����������� ��������
						   $sql, # sql-������
						   $sql_for_count, # sql-������ ��� �������� ���������� �������
						   "/novosti/", # ����� �� 1� ��������
						   "/novosti/page%page%/", # ����� �� ��������� ��������
							1500 # ������������ ���������� ������� �� ��������
							);
		$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>';
		
		if (!empty($_result)) return $_result;
	} # /�������� ������ �������� ��� ������� �������� ��������

    # �������� ���������� �� �������
    function getItemInfo($url)
    {
        # �������� ����������
        if (empty($url)) return;
        
        # echo $url; exit;
        $url = preg_replace('/[^-0-9]/i', '', $url);
        if (strlen($url) < 10) return;
        
        # �������� ���� � id
        $urlDateDay = substr($url, 0, 2);
        $urlDateMonth = substr($url, 3, 2);
        $urlDateYear = substr($url, 6, 4);
        $urlDate = $urlDateYear.'-'.$urlDateMonth.'-'.$urlDateDay; # echo 'urlDate: '.$urlDate; exit;
        $urlId = substr($url, 8); # echo 'urlId: '.$urlId;

		$sql = '
        select *,
               date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
               date_format(date_add, "%Y") as date_add_year,
               date_format(date_add, "%d-%m-%Y") as date_add_formatted_2
        from '.DB_PREFIX.'news
        where date(date_add) = :date
              and is_showable = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
        # $sth->bindParam(':id', $urlId, PDO::PARAM_INT);
        $sth->bindParam(':date', $urlDate);
		try {
			if ($sth->execute()) {
				$_ = $sth->fetch(); # print_r($_);
				if (!empty($_)) return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ���������� �� �������

    # �������� ������� �� RANDOM
    # $idSelected - id �������, ������� ����� ��������� �� ������
    function getRandomItems($itemCount = 5, $idSelected = null)
    {
        # ���� ������ id �������, ������� �� ����� ��������
        unset($sqlCondition);
        if (!empty($idSelected)) $sqlCondition = ' and id != :id ';

        $sql = '
        select id,
               h1,
               date_format(date_add, "%e") as date_add_day,
               date_format(date_add, "%m") as date_add_month,
               date_format(date_add, "%Y") as date_add_year,
               date_format(date_add, "%d-%m-%Y") as date_add_formatted_2
        from '.DB_PREFIX.'news
        where is_showable = 1
              '.$sqlCondition.'
        order by date_add desc,
                 h1
        limit :limit
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        $sth->bindValue(':limit', $itemCount, PDO::PARAM_INT);
        # ���� ������ id �������, ������� �� ����� ��������
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
    } # /�������� ������� �� RANDOM
    
    # �������� ������ ������� ��� ����� "������ �������"
    function getNewsForBlockAnotherNews($currentItemID)
    {
        # �������� ����������
        if (empty($currentItemID)) return;
        
		$sql = '
        select id,
               h1, 
               text, 
               image, 
               date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
               date_format(date_add, "%Y") as date_add_year,
               date_format(date_add, "%d-%m-%Y") as date_add_formatted_2
        from '.DB_PREFIX.'news
        where id != :id
              and is_showable = 1
        order by date_add desc,
                 h1
        limit 3
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
        $sth->bindParam(':id', $currentItemID, PDO::PARAM_INT);
		try {
			if ($sth->execute()) {
				$_ = $sth->fetchAll(); # print_r($_);
				if (!empty($_)) return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ������ ������� ��� ����� "������ �������"

    # ������� ����� ���������� ������
    function getItemsCount()
    {
		$sql = '
        select count(1)
        from '.DB_PREFIX.'news
        where is_showable = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
		try	{
			if ($sth->execute()) {
				$_ = $sth->fetchColumn(); # print_r($_);
				if (!empty($_)) return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /������� ����� ���������� ������

	# �������� ������ �������� ��� ����� �����
	function getItemsForMap()
	{
		$sql = "
		select id,
			   h1,
			   date_format(date_add, '%e.%m.%Y') as date_add_formatted,
			   date_format(date_add, '%d-%m-%Y') as date_add_formatted_2
		from ".DB_PREFIX."news 
		order by date_add desc, 
				 id desc
		"; # echo $sql."<hr />";
		$result = $this->dbh->prepare($sql);
		try
		{
			if ($result->execute())
			{
				$_ = $result->fetchAll(); # print_r($_);
				return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /�������� ������ �������� ��� ����� �����
}