<?php
class catalog_model extends model_base
{
	# ПОЛУЧАЕМ СПИСОК СТАТЕЙ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ СПИСКА ЭЛЕКТРОСТАНЦИЙ
	function getItemsForIndex()
	{
		$sql = '
        select id,
               name,
               url,
               cost,
               image_small,
               spec_main_power,
               spec_spare_power,
               spec_engine,
               spec_fuel_rate
        from '.DB_PREFIX.'catalog
        where is_showable = 1
        order by isnull(order_listing),
                 order_listing
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
	} # /ПОЛУЧАЕМ СПИСОК СТАТЕЙ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ СПИСКА ЭЛЕКТРОСТАНЦИЙ

    # ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО ЭЛЕКТРОСТАНЦИИ
    function getItemInfo($url)
    {
        # проверка переменных
        if (empty($url)) return;

        $sql = '
        select *
        from '.DB_PREFIX.'catalog
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
    } # /ПОЛУЧАЕМ ИНФОРМАЦИЮ ПО ЭЛЕКТРОСТАНЦИИ

    # ПОЛУЧАЕМ ИНФОРМАЦИЮ ДЛЯ БЛОКА "ДРУГИЕ МОДЕЛИ ЭЛЕКТРОСТАНЦИЙ"
    function getAnotherItems($currentItemID)
    {
        # проверка переменных
        if (empty($currentItemID)) return;

        $sql = '
        select id,
               name,
               url,
               cost,
               image_small,
               spec_main_power,
               spec_spare_power,
               spec_engine,
               spec_fuel_rate
        from '.DB_PREFIX.'catalog
        where id != :id
              and is_showable = 1
        order by rand()
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
    } # /ПОЛУЧАЕМ ИНФОРМАЦИЮ ДЛЯ БЛОКА "ДРУГИЕ МОДЕЛИ ЭЛЕКТРОСТАНЦИЙ"

    # СЧИТАЕМ КОЛИЧЕСТВО ЭЛЕКТРОСТАНЦИЙ
    function getItemsCount()
    {
        $sql = '
        select count(1)
        from '.DB_PREFIX.'catalog
        where is_showable = 1
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try
        {
            if ($sth->execute()) {
                $_ = $sth->fetchColumn(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /СЧИТАЕМ КОЛИЧЕСТВО ЭЛЕКТРОСТАНЦИЙ

    # ПОЛУЧАЕМ СПИСОК ПОЗИЦИЙ ДЛЯ КАРТЫ САЙТА
    function getItemsForMap()
    {
        $sql = "
		select id,
			   name,
			   url,
			   cost
		from ".DB_PREFIX."catalog
        order by isnull(order_listing),
                 order_listing
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
    } # /ПОЛУЧАЕМ СПИСОК ПОЗИЦИЙ ДЛЯ КАРТЫ САЙТА
}