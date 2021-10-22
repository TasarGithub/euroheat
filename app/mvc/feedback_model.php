<?php
class feedback_model extends model_base
{
	# ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ОТЗЫВОВ
	function getItemsForIndex()
	{
		$sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
		$pages = new pages($this->routeVars['page'], # текущая страница
						   20, # записей на страницу
						   $this->dbh, # объект базы данных
						   $this->routeVars, # переменные динамичного маршрута
						   $sql, # sql-запрос
						   $sql_for_count, # sql-запрос для подсчета количества записей
						   "/otzyvy/", # ссыка на 1ю страницу
						   "/otzyvy/page%page%/", # ссыка на остальные страницы
							1500 # максимальное количество записей на страницу
							);
		$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>';
		
		if (!empty($_result)) return $_result;
	} # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ОТЗЫВОВ

    # ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ
    function getItemsForMainPage()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ

    # ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ВНУТРЕННИХ СТРАНИЦ
    # $idSelected - id отзыва, который нужно исключить из вывода
    function getItemsForInsidePages($idSelected = null)
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ВНУТРЕННИХ СТРАНИЦ

    # ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ РАЗДЕЛА "ФОТОГРАФИИ"
    function getItemsForPhotos()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ РАЗДЕЛА "ФОТОГРАФИИ"

    # ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ЛУЖНИКОВ
    function getItemsForPlace1()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ЛУЖНИКОВ

    # ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ПРОСТЕКТА ВЕРНАДСКОГО
    function getItemsForPlace2()
    {
        $sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ ПРОСТЕКТА ВЕРНАДСКОГО

    # ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО ВОПРОСУ-ОТВЕТУ
    function getItemInfo($id)
    {
        # проверка переменных
        if (empty($id)) return;
        
		$sql = '
		select *,
               date_format(date_add,"%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО ВОПРОСУ-ОТВЕТУ
    
    # СЧИТАЕМ ОБЩЕЕ КОЛИЧЕСТВО ВОПРОСОВ-ОТВЕТОВ ДЛЯ МЕНЮ СПРАВА
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /СЧИТАЕМ ОБЩЕЕ КОЛИЧЕСТВО ВОПРОСОВ-ОТВЕТОВ ДЛЯ МЕНЮ СПРАВА
    
    # ПОЛУЧАЕМ ОТЗЫВЫ НА РАНДОМ ДЛЯ БЛОКА "ДРУГИЕ ОТЗЫВЫ"
    function getFeedbackForBlockAnotherFeedback($currentItemID)
    {
        # проверка переменных
        if (empty($currentItemID)) return;
        
		$sql = '
		select id,
			   name,
			   feedback,
			   votes_plus,
			   votes_minus,
			   date_format(date_add, "%e") as date_add_day,
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ ОТЗЫВЫ НА РАНДОМ ДЛЯ БЛОКА "ДРУГИЕ ОТЗЫВЫ"
    
    # СЧИТАЕМ ОБЩЕЕ КОЛИЧЕСТВО ОТЗЫВОВ ДЛЯ МЕНЮ СПРАВА
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /СЧИТАЕМ ОБЩЕЕ КОЛИЧЕСТВО ОТЗЫВОВ ДЛЯ МЕНЮ СПРАВА
    
	# ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ МЕНЮ СЛЕВА ДЛЯ ВНУТРЕННИХ
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
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ МЕНЮ СЛЕВА ДЛЯ ВНУТРЕННИХ
    
	# ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ МЕНЮ СПРАВА ДЛЯ ГЛАВНОЙ
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
               elt(month(date_add), "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря") as date_add_month,
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
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /ПОЛУЧАЕМ СПИСОК ОТЗЫВОВ ДЛЯ МЕНЮ СПРАВА ДЛЯ ГЛАВНОЙ
    
    # СЧИТАЕМ КОЛИЧЕСТВО ОТЗЫВОВ
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /СЧИТАЕМ КОЛИЧЕСТВО ОТЗЫВОВ
    
	# ПОЛУЧАЕМ ОТЗЫВЫ ДЛЯ КАРТЫ САЙТА
	function getItemsForMap()
	{
		$sql = "
		select id,
			   name,
			   date_format(date_add,'%e') as date_add_day,
               elt(month(date_add), 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря') as date_add_month,
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /ПОЛУЧАЕМ ОТЗЫВЫ ДЛЯ КАРТЫ САЙТА
}