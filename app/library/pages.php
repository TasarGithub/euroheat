<?php # 2012.4.18, romanov.egor@gmail.com, version 2.5 (for PDO)





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

                    $db_object,

					$routeVars,

                    $sql_query,

                    $sql_for_count,

                    $first_page_link,

                    $other_pages_link,

                    $max_rows_per_page)

	{

        

        # ���� ������� ��������

        if (!empty($current_page)) {

			if ($current_page != "all") $current_page = (int) $current_page;

			$this->current_page = $current_page;

		}

        else $this->current_page = 1;



        # 301 �������� � /url/page1/ > /url/

        if (stristr($_SERVER['REQUEST_URI'], $first_page_link.'page1/')) {

            header("HTTP/1.0 301 Moved Permanently");

            header("Location: http://".DOMAIN.$first_page_link);

            exit;

        }



        $this->rows_per_page = $rows_per_page;

        $this->dbh = $db_object;

		if (!empty($routeVars)) $this->routeVars = $routeVars; # print_r($routeVars);

        $this->sql = $sql_query; # echo $this->sql.'<hr />';

        $this->sql_for_count = $sql_for_count; # echo $this->sql_for_count.'<hr />';



        $this->first_page_link = $first_page_link;

        $this->other_pages_link = $other_pages_link;

        

        $this->max_rows_per_page = $max_rows_per_page;

    }



    function getResult()

	{

        # ������� ������� ����� �������

        # $this->all_rows = $this->dbh->query($this->sql_for_count); # print_r($this->all_rows);

        # $this->all_rows = $this->all_rows[0]['count(1)']; # echo $this->all_rows."<hr />";

		# echo $this->sql_for_count."<hr />";

		$result = $this->dbh->prepare($this->sql_for_count); # var_dump($result);

		# ���� ������� ������

		# echo $GLOBALS['categoryID']."<hr />";

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

				$this->all_rows = $result->fetch(); # print_r($this->all_rows);

				$this->all_rows = $this->all_rows['count(1)']; # echo $this->all_rows."<hr />";

			}

		}

		catch (PDOException $e)

		{

			if (DB_SHOW_ERRORS)

			{

				echo "������ � SQL-�������:<br /><br />".$this->sql."<br /><br />".$e->getMessage();

				exit;

			}

		}

		

        if ($this->all_rows > 0)

		{

            # ������� ������� ������ �������

            if ($this->all_rows <= $this->rows_per_page) $this->all_pages = 1; # echo $this->all_pages;



            $this->all_pages = ceil($this->all_rows / $this->rows_per_page);

            if ($this->current_page > $this->all_pages) {

                header("HTTP/1.0 404 Not Found");

                header('Location: http://'.$_SERVER["HTTP_HOST"]);

                exit;

            }



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

                if ($this->current_page == $step) $_pages[$step] = "<a href='./' class='active'>".$step."</a>";

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

					$_pages = implode(' ', $_pages);

					if (strip_tags($_pages) == '1') $_pages = NULL;

					else

					{

						# if ($this->current_page == "all") $allPagesLink = str_repeat("&nbsp;", 3)." ��� (".$this->all_rows.")";

						# else $allPagesLink = str_repeat("&nbsp;", 3)." <a href='".$substanceURL.$sortURL.str_replace("%page%", "all", $this->other_pages_link)."'>���</a> (".$this->all_rows.")";

						

						# ���� ������� �� �������� ������� �����, ������� ������ "���"

						# if ($this->all_rows > $this->max_rows_per_page) unset($allPagesLink);

						

						# ���� ����� 1 ��������, ������� ������ "���"

						# if ($this->all_pages == 1) unset($allPagesLink);

                        

                        # ���� � ����������� ���� ������� ������ ������ "���"

                        # if (!empty($GLOBALS['hideAllLinks'])) unset($allPagesLink);

						

						# $pagesSet = " ".$_pages.$allPagesLink;

						$pagesSet = " ".$_pages;

					}

					

					return array ("pagesSet" => $pagesSet,

								  "pagesCount" => $this->all_pages,

								  "resultSet" => $_result,

								  "allRowsCount" => $this->all_rows);

				}

			}

			catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }

        }

        else return array ("pagesSet" => $this->all_rows);

    }



}