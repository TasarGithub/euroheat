<?php # 2012.4.18, romanov.egor@gmail.com, version 2.5 (for PDO)

/*
������ �������������:
# �������� ������ ��������
$sql = '
select id,
       date_add,
       date_format(date_add, "%d.%m.%Y") as date_add_formatted,
       date_format(date_add, "%d%m%Y") as date_add_formatted_2,
       h1,
       is_showable
from '.DB_PREFIX.'news
order by date_add desc,
         id desc
'; # echo '<pre>'.$sql."</pre><hr />";
$sql_for_count = '
select count(id)
from '.DB_PREFIX.'news
'; # echo '<pre>'.$sql_for_count."</pre><hr />";
$pages = new pages($_GET["page"], # ������� ��������
                   2, # ������� �� ��������
                   $dbh, # ������ ���� ������
                   '', # routeVars
                   $sql, # sql-������
                   $sql_for_count, # sql-������ ��� �������� ���������� �������
                   '/control/news/', # ����� �� 1� ��������
                   '/control/news/?page=%page%', # ����� �� ��������� ��������
                    1500 # ������������ ���������� ������� �� ��������
                    );
$_result = $pages->getResult(); print_r($_result);
$_ = $_result['resultSet'];
if (!empty($_result['pagesSet'])) $pagesList = '<div class="pages_set">'.$_result['pagesSet'].'</div>';
$_c = count($_);
$rows = array();
for ($i=0;$i<$_c;$i++)
{...}

Array
(
    [pagesSet] =>  1, 2, 3    ��� (5)
    [pagesCount] => 3
    [resultSet] => Array
        (
            [0] => Array
                (
                    [id] => 5
                    [date_add] => 2015-06-17 05:00:00
                    [date_add_formatted] => 17.06.2015
                    [date_add_formatted_2] => 17062015
                    [h1] => ������� 5. ��������� h1
                    [is_showable] => 1
                )

            [1] => Array
                (
                    [id] => 4
                    [date_add] => 2015-05-26 05:00:00
                    [date_add_formatted] => 26.05.2015
                    [date_add_formatted_2] => 26052015
                    [h1] => ������� 4. ��������� h1
                    [is_showable] => 1
                )

        )

    [allRowsCount] => 5
)
*/

class pages
{
    var $current_page, # ������� ��������
        $rows_per_page, # ������� �� ��������
        $dbh, # ������ ���� ������
		$routeVars, # ���������� ����������� ��������
        $all_rows, # ����� �������
        $all_pages, # ����� �������
        $sql, # sql-������
        $sql_for_count, # sql-������ ��� �������� ������ ����� �������
        $bottom, # ������ ��������
        $top, # ������� ��������
        $first_page_link, # ������ �� ������ ��������
        $other_pages_link, # ������ �� �������������� ��������
        $max_rows_per_page; # ������������ ���������� ������� �� ��������

    function __construct ($current_page,
                    $rows_per_page,
                    $dbh_object,
					$routeVars,
                    $sql_query,
                    $sql_for_count,
                    $first_page_link,
                    $other_pages_link,
                    $max_rows_per_page)
	{
        
        if (!empty($current_page))
		{
			if ($current_page != "all") $current_page = (int) $current_page;
			$this->current_page = $current_page;
		}
        else $this->current_page = 1;
        
        $this->rows_per_page = $rows_per_page;
        $this->dbh = $dbh_object;
		if (!empty($routeVars)) $this->routeVars = $routeVars; # print_r($this->routeVars);
        $this->sql = $sql_query; # echo $this->sql.'<hr />';
        $this->sql_for_count = $sql_for_count; # echo $this->sql_for_count.'<hr />';

        $this->first_page_link = $first_page_link;
        $this->other_pages_link = $other_pages_link;
        
        $this->max_rows_per_page = $max_rows_per_page;
    }

    function getResult()
	{
        # ������� ������� ����� �������
        $this->all_rows = $this->dbh->query($this->sql_for_count)->fetchColumn(); # print_r($result);
        if ($this->all_rows > 0)
		{
            # ������� ������� ������ �������
            if ($this->all_rows >= $this->rows_per_page)
			{
                $this->all_pages = ceil($this->all_rows / $this->rows_per_page); 
                if ($this->current_page > $this->all_pages)
				{
                    header("HTTP/1.0 404 Not Found");
                    header('Location: http://'.$_SERVER["HTTP_HOST"]);
                    exit;
                }
            }
            else $this->all_pages = 1; # echo $this->all_pages;

            # ������� ������ ��������
            $this->bottom = ($this->current_page * $this->rows_per_page) - $this->rows_per_page + 1;
            # ������� ������� ��������
            # ���� ��� ��������� ��������
            if ($this->current_page >= $this->all_pages) $this->top = $this->all_rows;
            else $this->top = $this->current_page * $this->rows_per_page;
            # echo $this->bottom."<br>";
            # echo $this->top."<br>";
			
			# ���� ������� ������
			# if (!empty($GLOBALS['searchName'])) $result->bindParam(':searchName', $GLOBALS['searchName'], PDO::PARAM_STR);
			if (!empty($GLOBALS['categoryID'])) $substanceURL = "/{$this->routeVars['categoryURL']}";
			else unset($substanceURL);
			# ���� ������� ����������
			if (!empty($_GET['searchByName']))
			{
				unset($searchByName);
				$searchByName = '%'.$_GET['searchByName'].'%'; # echo $searchByName.'<hr />';
				$result->bindParam(':searchByName', $searchByName, PDO::PARAM_STR);
			}
			if (!empty($this->routeVars['sort'])) $sortURL = "/sort{$this->routeVars['sort']}";
			else unset($sortURL);
			
            # ��������� ��������
            for ($i = 0; $i < $this->all_pages; $i++)
			{
                $step = $i + 1;
                $bottom = ($step * $this->rows_per_page) - $this->rows_per_page + 1;

                if ($step == $this->all_pages) $top = $this->all_rows;
                else $top = $step * $this->rows_per_page;

                # $_pages[$step] = $bottom." .. ".$top;
                if ($this->current_page == $step) $_pages[$step] = "<b>".$step."</b>";
                # if ($this->current_page == $step) $_pages[$step] = "".$bottom." .. ".$top."";
                else{
                    if ($step == 1) $_pages[$step] = "<a href='".$this->first_page_link."'>".$step."</a>";
                    # if ($step == 1) $_pages[$step] = "<a href='".$substanceURL.$sortURL.$this->first_page_link."'>".$bottom." .. ".$top."</a> ";
                    else $_pages[$step] = "<a href='".str_replace("%page%", $step, $this->other_pages_link)."'>".$step."</a>";
                    # else $_pages[$step] = "<a href='".$substanceURL.$sortURL.str_replace("%page%", $step, $this->other_pages_link)."'>".$bottom." .. ".$top."</a> ";
                }
            }
            # print_r($_pages);

            #���������� ������ ������� �� �������� ���������
            $firstEntry =  $this->bottom - 1;
            $entriesCount =  $this->rows_per_page;

            $count = 0;
            
            if ($this->current_page == "all")
			{
                $sql = $this->sql;
                
                # ���� ������� �� �������� ������� �����, ������ �������� �� �������
                if ($this->all_rows > $this->max_rows_per_page)
				{
                    header("HTTP/1.0 404 Not Found");
                    header('Location: http://'.$_SERVER["HTTP_HOST"]);
                    exit;
                }
            }
            else $sql = $this->sql." limit ".$firstEntry.", ".$entriesCount;
            
            # $_ = $this->dbh->query($sql);
			# echo $sql."<hr />";
			$result = $this->dbh->prepare($sql); # var_dump($result);
			# ���� ������� ������
			# if (!empty($GLOBALS['searchName'])) $result->bindParam(':searchName', $GLOBALS['searchName'], PDO::PARAM_STR);
			if (!empty($GLOBALS['categoryID'])) $result->bindParam(':categoryID', $GLOBALS['categoryID'], PDO::PARAM_INT);
			if (!empty($_GET['searchByName']))
			{
				unset($searchByName);
				$searchByName = '%'.$_GET['searchByName'].'%'; # echo $searchByName.'<hr />';
				$result->bindParam(':searchByName', $searchByName, PDO::PARAM_STR);
			}
			try
			{
				if ($result->execute())
				{
					$_ = $result->fetchAll(); # print_r($_);
					$_c = count($_);
					if (!empty($_c))
					{
						for ($i = 1; $i <= $_c; $i++)
						{
							$_result[$count] = $_[$i-1];
							$count++;
						}
					}
					else $_result = NULL;

					# print_r($_result);

					# $_pages = implode(" | ", $_pages);
					$_pages = implode(", ", $_pages);
					if (strip_tags($_pages) == '1') $_pages = NULL;
					else
					{
						if ($this->current_page == "all") $allPagesLink = str_repeat("&nbsp;", 3)." ��� (".$this->all_rows.")";
						else $allPagesLink = str_repeat("&nbsp;", 3)." <a href='".$substanceURL.$sortURL.str_replace("%page%", "all", $this->other_pages_link)."'>���</a> (".$this->all_rows.")";
						
						# ���� ������� �� �������� ������� �����, ������� ������ "���"
						if ($this->all_rows > $this->max_rows_per_page) unset($allPagesLink);
						
						# ���� ����� 1 ��������, ������� ������ "���"
						if ($this->all_pages == 1) unset($allPagesLink);
                        
                        # ���� � ����������� ���� ������� ������ ������ "���"
                        if (!empty($GLOBALS['hideAllLinks'])) unset($allPagesLink);
						
						$pagesSet = " ".$_pages.$allPagesLink;
					}
					
					return array ("pagesSet" => $pagesSet,
								  "pagesCount" => $this->all_pages,
								  "resultSet" => $_result,
								  "allRowsCount" => $this->all_rows);
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
        }
        else return array ("pagesSet" => $this->all_rows);
    }

}