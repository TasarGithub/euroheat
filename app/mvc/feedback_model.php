<?php
class feedback_model extends model_base
{
	# �������� ������ ������� ��� ������� �������� �������
	function getItemsForIndex()
	{
		$sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
		order by date_add desc
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sql_for_count = "
		select count(1)
		from ".DB_PREFIX."feedback
        where is_published = 1
		"; # echo '<pre>'.$sql."</pre><hr />";
		$pages = new pages($this->routeVars['page'], # ������� ��������
						   20, # ������� �� ��������
						   $this->dbh, # ������ ���� ������
						   $this->routeVars, # ���������� ����������� ��������
						   $sql, # sql-������
						   $sql_for_count, # sql-������ ��� �������� ���������� �������
						   "/otzyvy/", # ����� �� 1� ��������
						   "/otzyvy/page%page%/", # ����� �� ��������� ��������
							1500 # ������������ ���������� ������� �� ��������
							);
		$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>';
		
		if (!empty($_result)) return $_result;
	} # /�������� ������ ������� ��� ������� �������� �������

    # �������� ������ ������� ��� ������� ��������
    function getItemsForMainPage()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
        order by rand()
        limit 3
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try {
            if ($sth->execute()) {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ������ ������� ��� ������� ��������

    # �������� ������ ������� ��� ���������� �������
    # $idSelected - id ������, ������� ����� ��������� �� ������
    function getItemsForInsidePages($idSelected = null)
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
        order by rand()
        limit 3
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try {
            if ($sth->execute()) {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ������ ������� ��� ���������� �������

    # �������� ������ ������� ��� ������� "����������"
    function getItemsForPhotos()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
        order by rand()
        limit 9
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try {
            if ($sth->execute()) {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ������ ������� ��� ������� "����������"

    # �������� ������ ������� ��� ��������
    function getItemsForPlace1()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
		      and is_place_1 = 1
        order by rand()
        limit 9
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try {
            if ($sth->execute()) {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ������ ������� ��� ��������

    # �������� ������ ������� ��� ��������� �����������
    function getItemsForPlace2()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
		      and is_place_2 = 1
        order by rand()
        limit 9
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try {
            if ($sth->execute()) {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /�������� ������ ������� ��� ��������� �����������

    # �������� ���������� �� �������-������
    function getItemInfo($id)
    {
        # �������� ����������
        if (empty($id)) return;
        
		$sql = '
		select *,
               date_format(date_add,"%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
			  and id = :id
		'; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $this->dbh->prepare($sql);
        $sth->bindParam(':id', $id);
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
    
    # ������� ����� ���������� ��������-������� ��� ���� ������
    function getFaqItemsCountForRightMenu()
    {
        $dbh = $this->dbh;
        
		$sql = '
		select count(1)
		from '.DB_PREFIX.'faq
		where is_showable = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
        try {
            $_ = $dbh->query($sql)->fetchColumn();
            if (!empty($_)) return $_;
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /������� ����� ���������� ��������-������� ��� ���� ������
    
    # �������� ������ �� ������ ��� ����� "������ ������"
    function getFeedbackForBlockAnotherFeedback($currentItemID)
    {
        # �������� ����������
        if (empty($currentItemID)) return;
        
		$sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where id != :id
              and is_published = 1
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
    } # /�������� ������ �� ������ ��� ����� "������ ������"
    
    # ������� ����� ���������� ������� ��� ���� ������
    function getItemsCountForRightMenu()
    {
        $dbh = $this->dbh;
        
		$sql = '
		select count(1)
		from '.DB_PREFIX.'feedback
		where is_published = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
        try {
            $_ = $dbh->query($sql)->fetchColumn();
            if (!empty($_)) return $_;
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /������� ����� ���������� ������� ��� ���� ������
    
	# �������� ������ ������� ��� ���� ����� ��� ����������
	function getItemsForMenu()
	{
        $dbh = $this->dbh;
        
		$sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
		order by rand()
		limit 1
		'; # echo '<pre>'.$sql."</pre><hr />";
        try {
            $_ = $dbh->query($sql)->fetch();
            if (!empty($_)) return $_;
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /�������� ������ ������� ��� ���� ����� ��� ����������
    
	# �������� ������ ������� ��� ���� ������ ��� �������
	function getItemsForRightMenuForMainPage()
	{
        $dbh = $this->dbh;
        
		$sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "������", "�������", "�����", "������", "���", "����", "����", "�������", "��������", "�������", "������", "�������") as date_add_month,
			   date_format(date_add,"%Y") as date_add_year
		from '.DB_PREFIX.'feedback
		where is_published = 1
		order by rand()
		limit 6
		'; # echo '<pre>'.$sql."</pre><hr />";
        try {
            $_ = $dbh->query($sql)->fetchAll();
            if (!empty($_)) return $_;
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /�������� ������ ������� ��� ���� ������ ��� �������
    
    # ������� ���������� �������
    function getItemsCount()
    {
		$sql = '
        select count(1)
        from '.DB_PREFIX.'feedback
        where is_published = 1
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
    } # /������� ���������� �������
    
	# �������� ������ ��� ����� �����
	function getItemsForMap()
	{
		$sql = "
		select id,
			   name,
			   date_format(date_add,'%e') as date_add_day,
               elt(month(date_add), '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������') as date_add_month,
			   date_format(date_add,'%Y') as date_add_year
		from ".DB_PREFIX."feedback
        where is_published = 1 
		order by date_add desc,
                 name
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /�������� ������ ��� ����� �����
}