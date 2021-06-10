<?php

class site_section_default_model extends model_base
{
	# �������� ���������� �� ������� �� ������� URL
	function getSiteSectionInfo($fullURL)
	{
		# �������� ����������
        if (empty($fullURL)) { echo '�� ������� fullURL � ����� getSiteSectionInfo'; return; }
        
        $dbh = $this->dbh;
		
        $sql = '
        select *
        from '.DB_PREFIX.'site_sections
        where full_url = :full_url
        '; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':full_url', $fullURL);
		try { if ($sth->execute()) {
            $_ = $sth->fetch(); # print_r($_);
            return $_;
        }}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /�������� ���������� �� ������� �� ������� URL

	# �������� full URL �� ������� �� ������� URL
	function getSiteSectionURLById($id)
	{
		# �������� ����������
		if (empty($id)) { echo '�� ������� fullURL � ����� getSiteSectionInfo'; return; }

		$dbh = $this->dbh;

		$sql = '
        select full_url
        from '.DB_PREFIX.'site_sections
        where id = :id
        '; # echo '<pre>'.$sql."</pre><hr />";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', $id);
		try { if ($sth->execute()) {
			$_ = $sth->fetchColumn(); # print_r($_);
			return $_;
		}}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /�������� full URL �� ������� �� ������� URL
	
	# �������� ������� ��������
    /*
	function getSectionURLAndName($fullURL)
	{
		# �������� ����������
		if (empty($fullURL)) return;
	
		$sql = "select full_url, name
				from site_sections
				where full_url = ?"; # echo $sql."<hr />";
		$sth = $dbh->prepare($sql); # var_dump($sth);
		$sth->bindParam(1, $fullURL);
		try
		{
			if ($sth->execute())
			{
				$_ = $sth->fetch(); # print_r($_);
				return $_;
			}
		}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	}
    */
	# /�������� ������� ��������
	
	# �������� ������ ��������� �� ������� URL'�
	function getNavigationByFullURL($fullURL)
	{
		# �������� ����������
		if (empty($fullURL)) return;
        
        $dbh = $this->dbh;
		
		$sql = '
        select navigation
        from '.DB_PREFIX.'site_sections
        where full_url = :full_url
        '; # echo $sql."<hr />";
		$sth = $dbh->prepare($sql); # var_dump($sth);
		$sth->bindParam(':full_url', $fullURL);
		try { if ($sth->execute()) {
            $_ = $sth->fetchColumn(); # print_r($_);
            if (!empty($_)) return $_;
        }}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); }}
	}
	# �������� ������ ��������� �� ������� URL'�

    # URL SELECTOR: ���� ��� ��������� ������, ���� ��� �������������� ��������
    # @param $params['url'] - URL �������� ������� | ������������ ��������
    function urlSelectorGeneratora($params)
    {
        # �������� ����������
        if (empty($params['url'])) {
            echo 'Variable "params[url]" is not defined in "urlSelectorGeneratora" method in "sites_section_defauld" model.';
            return false;
        }

        $dbh = $this->dbh;

        $sql = '
        select 1,
        (select 1 from '.DB_PREFIX.'site_sections where parent_id = 180 and url = :url) as is_site_section,
        (select 1 from '.DB_PREFIX.'catalog where url = :url) as catalog_detailed
        '; # echo $sql."<hr />";
        $sth = $dbh->prepare($sql); # var_dump($sth);
        $sth->bindParam(':url', $params['url']);
        try { if ($sth->execute()) {
            $_ = $sth->fetch(); # echo '<pre>'.(print_r($_, true)).'</pre>';
            if (!empty($_)) return $_;
        }}
        catch (PDOException $e) { if (DB_SHOW_ERRORS) {	echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); }}
    }
    # /URL SELECTOR: ���� ��� ��������� ������, ���� ��� �������������� ��������
}