<?php
class faq_model extends model_base
{
	# ПОЛУЧАЕМ СПИСОК ВОПРОСОВ-ОТВЕТОВ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ВОПРОСОВ-ОТВЕТОВ
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
		$pages = new pages($this->routeVars['page'], # текущая страница
						   10, # записей на страницу
						   $this->dbh, # объект базы данных
						   $this->routeVars, # переменные динамичного маршрута
						   $sql, # sql-запрос
						   $sql_for_count, # sql-запрос для подсчета количества записей
						   "/vopros/", # ссыка на 1ю страницу
						   "/vopros/page%page%/", # ссыка на остальные страницы
							1500 # максимальное количество записей на страницу
							);
		$_result = $pages->getResult(); # echo '<pre>'.(print_r($_result, true)).'</pre>';
		
		if (!empty($_result)) return $_result;
	} # /ПОЛУЧАЕМ СПИСОК ВОПРОСОВ-ОТВЕТОВ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ВОПРОСОВ-ОТВЕТОВ

    # ПОЛУЧАЕМ ВОПРОСЫ-ОТВЕТЫ НА RANDOM
    # $idSelected - id вопроса-ответа, который нужно исключить из вывода
    function getRandomItems($itemCount = 5, $idSelected = null)
    {
        # если указан id вопроса-ответа, который не нужно выводить
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
        # если указан id вопроса-ответа, который не нужно выводить
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
    } # /ПОЛУЧАЕМ ВОПРОСЫ-ОТВЕТЫ НА RANDOM
    
    # ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО ВОПРОСУ-ОТВЕТУ
    function getItemInfo($url)
    {
        # проверка переменных
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО ВОПРОСУ-ОТВЕТУ
    
    # получаем вопросы-ответы для блока "Другие вопросы-ответы"
    function getFaqForBlockAnotherFaq($currentItemID)
    {
        # проверка переменных
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /получаем вопросы-ответы для блока "Другие вопросы-ответы"
    
	# ПОЛУЧАЕМ СПИСОК ПОЗИЦИЙ ДЛЯ КАРТЫ САЙТА
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
				echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage();
				exit;
			}
		}
	} # /ПОЛУЧАЕМ СПИСОК ПОЗИЦИЙ ДЛЯ КАРТЫ САЙТА
    
    # СЧИТАЕМ КОЛИЧЕСТВО ВОПРОСОВ-ОТВЕТОВ
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
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /СЧИТАЕМ КОЛИЧЕСТВО ВОПРОСОВ-ОТВЕТОВ
}