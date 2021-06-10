<?php
class map_model extends model_base
{
	# онксвюел яохянй бяеу пюгдекнб хг рюакхжш ".DB_PREFIX."site_sections дкъ transpark.ru
	function getSectionsList()
	{
		$sql = "
		select id,
			   parent_id,
			   name,
			   full_url,
			   is_showable
		from ".DB_PREFIX."site_sections
		where url != 'map'
			  and is_showable = 1
		order by parent_id,
				 name
		"; # echo '<pre>'.$sql."</pre><hr />";
		$result = $this->dbh->prepare($sql); # var_dump($result);
		try	{
			if ($result->execute())	{
				$_ = $result->fetchAll(); # print_r($_);
				return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "нЬХАЙЮ Б SQL-ГЮОПНЯЕ:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /онксвюел яохянй бяеу пюгдекнб хг рюакхжш ".DB_PREFIX."site_sections дкъ transpark.ru
    
	# онксвюел яохянй бяеу пюгдекнб хг рюакхжш ".DB_PREFIX."site_sections дкъ korobki.transpark.ru
	function getSectionsListForKorobki()
	{
		$sql = "
		select id,
			   parent_id,
			   name,
			   full_url,
			   is_showable
		from ".DB_PREFIX."site_sections_korobki
		where url != 'map'
			  and is_showable = 1
		order by parent_id,
				 name
		"; # echo '<pre>'.$sql."</pre><hr />";
		$result = $this->dbh->prepare($sql); # var_dump($result);
		try	{
			if ($result->execute())	{
				$_ = $result->fetchAll(); # print_r($_);
				return $_;
			}
		}
		catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "нЬХАЙЮ Б SQL-ГЮОПНЯЕ:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
	} # /онксвюел яохянй бяеу пюгдекнб хг рюакхжш ".DB_PREFIX."site_sections дкъ korobki.transpark.ru
}