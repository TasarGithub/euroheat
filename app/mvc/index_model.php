<?php
class index_model extends model_base
{
    # ПОЛУЧАЕМ ШОУ ДЛЯ СДАЙДЕРА "ПОСЛЕДНИЕ ШОУ"
    function getLastShowsForSlider()
    {
        $sql = '
        select id,
               name,
               url,
               image_for_slider_on_main_page,
               text_for_slider_on_main_page
        from '.DB_PREFIX.'shows
        where image_for_slider_on_main_page is not null
              and is_showable = 1
        order by rand()
		'; # echo '<pre>'.$sql."</pre><hr />";
        $sth = $this->dbh->prepare($sql);
        try {
            if ($sth->execute()) {
                $_ = $sth->fetchAll(); # print_r($_);
                if (!empty($_)) return $_;
            }
        }
        catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
    } # /ПОЛУЧАЕМ ШОУ ДЛЯ СДАЙДЕРА "ПОСЛЕДНИЕ ШОУ"
}