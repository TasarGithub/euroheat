<?php
class news_model extends model_base
{
	# ПОЛУЧАЕМ СПИСОК НОВОСТЕЙ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ НОВОСТЕЙ
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
		$pages = new pages($this->routeVars['page'], # текущая страница
						   20, # записей на страницу
						   $this->dbh, # объект базы данных
						   $this->routeVars, # переменные динамичного маршрута
						   $sql, # sql-запрос
						   $sql_for_count, # sql-запрос для подсчета количества записей
						   "/novosti/", # ссыка на 1ю страницу
						   "/novosti/page%page%/", # ссыка на остальные страницы
							1500 # максимальное количество записей на страницу
							);
		$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>';
		
		if (!empty($_result)) return $_result;
	} # /ПОЛУЧАЕМ СПИСОК НОВОСТЕЙ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ НОВОСТЕЙ

    # ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО НОВОСТИ
    function getItemInfo($url)
    {
        # проверка переменных
        if (empty($url)) return;
        
        # echo $url; exit;
        $url = preg_replace('/[^-0-9]/i', '', $url);
        if (strlen($url) < 10) return;
        
        # получаем дату и id
        $urlDateDay = substr($url, 0, 2);
        $urlDateMonth = substr($url, 3, 2);
        $urlDateYear = substr($url, 6, 4);
        $urlDate = $urlDateYear.'-'.$urlDateMonth.'-'.$urlDateDay; # echo 'urlDate: '.$urlDate; exit;
        $urlId = substr($url, 8); # echo 'urlId: '.$urlId;

		$sql = '
        select *,
               date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО НОВОСТИ

    # ПОЛУЧАЕМ НОВОСТИ НА RANDOM
    # $idSelected - id новости, которую нужно исключить из вывода
    function getRandomItems($itemCount = 5, $idSelected = null)
    {
        # если указан id новости, которую не нужно выводить
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
        # если указан id новости, которую не нужно выводить
        if (!empty($idSelected)) $sth->bindValue(':id', $idSelected, PDO::PARAM_INT);
        try
        {
            if ($sth->execute())
            {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ НОВОСТИ НА RANDOM
    
    # ПОЛУЧАЕМ СПИСОК НОВОСТИ ДЛЯ БЛОКА "ДРУГИЕ НОВОСТИ"
    function getNewsForBlockAnotherNews($currentItemID)
    {
        # проверка переменных
        if (empty($currentItemID)) return;
        
		$sql = '
        select id,
               h1, 
               text, 
               image, 
               date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ СПИСОК НОВОСТИ ДЛЯ БЛОКА "ДРУГИЕ НОВОСТИ"

    # СЧИТАЕМ ОБЩЕЕ КОЛИЧЕСТВО СТАТЕЙ
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /СЧИТАЕМ ОБЩЕЕ КОЛИЧЕСТВО СТАТЕЙ

	# ПОЛУЧАЕМ СПИСОК НОВОСТЕЙ ДЛЯ КАРТЫ САЙТА
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /ПОЛУЧАЕМ СПИСОК НОВОСТЕЙ ДЛЯ КАРТЫ САЙТА
}