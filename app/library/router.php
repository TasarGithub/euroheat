<?php # Класс "Маршрутизатор"



class router{

	protected $mvcPath;

	protected $args = array();

	protected $registry;

	protected $routes = array(); # динамичные маршруты

	# protected $route = array(); # если на этапе 1 найден маршрут, записываем сюда массив с его данными

	protected $route; # полный URL запроса

	protected $routeVars = array(); # если на этапе 1 найден маршрут, записываем сюда переменные

	protected $siteSectionInfo = array(); # если на этапе 2 найден раздел в site_sections, записываем сюда массив с информацией о нем

    public $showRouterInfo = 0; # показать в html-коде результат работы router'а: какой контроллер и какой метод обрабатывает текущий URL

	# protected $denial301; # запрет редиректа на такой же URL, но со слешем



	function __construct($registry)

	{

		# 301 редирект если URL заканчиваетсЯ не на "/", делаем 301 редирект на URL со "/"

		/*

		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/")

		{

			# запрет на 301 редирект длЯ раздела: http://www.circuses.su/bronirovanie/?event=20

			if (!stristr($_SERVER['REQUEST_URI'], 'bronirovanie'))

			{

				header("HTTP/1.0 301 Moved Permanently");

				header("Location: http://".DOMAIN.$_SERVER['REQUEST_URI']."/");

				exit;

			}

		}

		*/

		

		$this->registry = $registry;

		# var_dump($this->registry);

	}

	

	# устанавливаем путь к папке "mvc"

	function setPath($mvcPath)

	{

		# $mvcPath = trim($mvcPath, '/\\');

		if (is_dir($mvcPath)) $this->mvcPath = $mvcPath;

		else throw new Exception ('Invalid mvc path: '.$mvcPath);

		# echo 'mvc mvcPath: '.$this->mvcPath.'<hr />';

	}

	

	# анализируем запрос и делегируем его контроллеру

	function delegate()

	{

		# анализируем путь и получаем controller/action/args

		$this->getController($controllerFullPath,

							 $controller,

							 $action,

							 $args

							 );

        # проверЯем доступность файла

		if (is_readable($controllerFullPath) == false)

		{

			# exit('404 Not Found. File is not readable: '.$controllerFullPath);

			# 404

			header("HTTP/1.0 404 Not Found");

			header('Location: http://'.DOMAIN);

			# echo '<!-- controller is not found: '.$controllerFullPath.' -->';

			# exit('404');

		}

		# подключаем файл базового контроллера и модели

		# include(SITE_PATH."#kernel/classes/controller_base.php"); # echo SITE_PATH."#kernel/classes/controller_base.php"."<hr />";

		# include(SITE_PATH."#kernel/classes/model_base.php"); # echo SITE_PATH."#kernel/classes/controller_base.php"."<hr />";

		

		# создаём экземплЯр контроллера

		$class = $controller.'_controller'; # echo $class.'<hr />';

		

		/* провреЯем, не объЯвлен ли класс ранее */

		if (!class_exists($class))

		{

			# подключаем файл контроллера длЯ текущей страницы

			include($controllerFullPath); # echo $controllerFullPath."<hr />";

		}

		$controllerInstance = new $class($this->registry);

		/* /провреЯем, не объЯвлен ли класс ранее */

		# действие доступно?

		if (is_callable(array($controllerInstance, $action)) == false)

        {

            # exit("Controller: `{$controller}`, action `{$action}` is not accessible.");

            header("HTTP/1.0 404 Not Found");

            header("Location: http://".$_SERVER['SERVER_NAME']);

            exit;

        }

        # выполнЯем действие

        $controllerInstance->$action();

	}

	

	# анализируем путь и получаем controller/action/args

	# либо длЯ динамичного урла получаем также controller/action/args и vars

	# формат URL'а: DOMAIN/controller/action/args

	private function getController(&$controllerFullPath,

								   &$controller,

								   &$action,

								   &$args)

	{



		$route = (empty($_GET['route'])) ? '' : $_GET['route']; # echo $route."<hr />";

		# убираем '/' в конце, если он есть

		if ($route[strlen($route)-1] == '/') $route = substr($route, 0, strlen($route)-1); # echo $route."<hr />";

		$this->route = $route; # echo $this->route.'<hr />';

		

		# дополнительнаЯ защита длЯ URL'а (подумать, нужна ли)

		$defence = new defence();

		$route = $defence->clearUserData($route); # echo $route."<hr />";

		

		### 1 ЭТАП РАБОТЫ РОУТЕРА: ПРОВЕРЯЕМ ДИНАМИЧНЫЕ МАШРУТЫ (описанные в ./loader.php)

		# проверка на совпадение URL'а с динамичным путем

		$routesMatches = NULL;

		# проверЯем динамичные URL's

		if (is_array($this->routes))

		{

			# print_r ($this->routes); 

			$routes_c = count($this->routes);

			# echo "routes count: {$routes_c}<hr />";

			if (!empty($routes_c))

			{

				for ($i=0;$i<$routes_c;$i++)

				{

					# echo "path: {$this->routes[$i]['path']}, rounte: {$route}<hr />";

					if (preg_match($this->routes[$i]['path'], $route, $matches))

					{

						# print_r($this->routes[$i]);

						# print_r($matches);

						

						# $this->route = $this->routes[$i];

						

						$controller = $this->routes[$i]['controller'];

						$action = $this->routes[$i]['action'];

						

						# vars

						$vars_c = count($this->routes[$i]['vars']);

						if (!empty($vars_c)){

							for ($j=1;$j<=$vars_c;$j++)

							{

								# убираем /

								# $matches[$j] = str_replace("/", "", $matches[$j]);

								

								$this->routeVars[$this->routes[$i]['vars'][$j]] = $matches[$j];

							}

							# print_r($this->routeVars);

						}

						

						# $routeVars = "";

						# $args = "";

						

						$routesMatches = 1;

						break;

					}

				}

			}

		}



		### 2 ЭТАП РАБОТЫ РОУТЕРА: ИЩЕМ РАЗДЕЛ В ТАБЛИЦЕ site_sections по полю full_url по переменной $_GET['route']

		if (empty($routesMatches))

		{

			$sectionInfo = $this->setSiteSectionInfo($route); # print_r($sectionInfo);



			if (!empty($sectionInfo))

			{

				$isSiteSectionsFound = 1;

				

				# если указан конроллер, передаем управление ему, иначе передаем управлению контроллеру по умолчанию

				if (!empty($sectionInfo['controller'])) $controller = $sectionInfo['controller'];

				else $controller = 'site_section_default';

				# echo $controller.'<hr />';

				

				$action = 'index';

				$this->siteSectionInfo = $sectionInfo; # print_r($this->siteSectionInfo);

			}

		}

		

		### 3 ЭТАП РАБОТЫ РОУТЕРА: ЕСЛИ ДИНАМИЧНЫЙ МАШРУТ НЕ НАЙДЕН

		# И В ТАБЛИЦЕ site_sections НЕТ СООТВЕСТВУЮЩЕГО РАЗДЕЛА, ИЩЕМ КОНТРОЛЛЕР ПО URL'у: controller/action/args

		if (empty($routesMatches) # на 1 этапе ничего не найдено

			and empty($isSiteSectionsFound) # на 2 этапе ничего не найдено

			)

		{ # echo '!';

			if (empty($route)) $route = 'index';



			# разбиваем URL на части

			# echo "route: ".$route."<hr />";

			$route = trim($route, '/\\');

			$routeParts = explode('/', $route); # echo "route parts: "; print_r($routeParts)."<hr />";

					

			# определЯем контроллер (controller)

			if (!empty($routeParts[0]))

			{

				$controller = $routeParts[0];

				# удалЯем первый элемент массива

				array_shift($routeParts);

			}

			else $controller = 'index_controller.php';

			

			# if (is_file($fullControllerPath)) include($fullControllerPath);

			# else exit('Unable to load controller: '.$fullControllerPath);

			

			# определЯем действие (action)

			if (!empty($routeParts[0]))

			{

				$action = $routeParts[0];

				# удалЯем первый элемент массива

				array_shift($routeParts); # print_r($routeParts);

			}

			else $action = 'index';



			# определЯем аргументы (args)

			if (!empty($routeParts[0]))

			{

				$args = $routeParts;

			}

			else unset($args);

			# print_r($args);

		}

		

		$controllerFullPath = $this->mvcPath.$controller.'_controller.php'; # echo "$ontrollerFullPath: ".$controllerFullPath."<hr />";

		

		### отладка

		# длЯ динамичного пути

        if (!empty($this->showRouterInfo))

        {

            echo "<!--\n";

            # для динамичного пути

            if (!empty($routesMatches))

            {

                echo "route info: "; print_r($this->route); echo "\n";

                echo "controller: ".$controller."\n";

                echo "controller path: ".$controllerFullPath."\n";

                echo "action: ".$action."\n";

                echo "vars: "; print_r($this->routeVars); echo "\n";

            }

            # для статичного пути

            else

            {

                echo "controller: ".$controller."\n";

                echo "controller path: ".$controllerFullPath."\n";

                echo "action: ".$action."\n";

                echo "args: "; print_r($args); echo "\n";

            }

            echo "\n-->";

            # exit;

        }

	}

	

	# устанавливаем динамичный путь

	# пример вызова метода: $router->addRoute(array('path' => '^page([/0-9]*)$', 'controller' => 'catalog', 'action' => 'showItems', 'vars' => array(1 => 'page')));

	public function addRoute($array)

	{

		if (is_array($array))

		{

			# проверЯем, заданы ли все параметры массива корректно

			if (empty($array['path']))

			{

				echo "Не задан 'path' длЯ динамичного маршрута:";

				print_r($array);

				exit;

			}

			elseif (empty($array['controller']))

			{

				echo "Не задан 'controller' длЯ динамичного маршрута:";

				print_r($array);

				exit;

			}

			elseif (empty($array['action']))

			{

				echo "Не задан 'action' длЯ динамичного маршрута:";

				print_r($array);

				exit;

			}

			else

			# добавлЯем маршрут

			{

				$this->routes[] = array('path' => $array['path'], 'controller' => $array['controller'], 'action' => $array['action'], 'vars' => $array['vars']);

			}

		}

		else

		{

			echo "Динамичный маршрут задан неверно:";

			print_r($array);

			exit;

		}

	}

	

	# ищем раздел в таблице site_sections по

	function setSiteSectionInfo($route)

	{

		# echo $_GET['route'].'<hr />';

		

		if (!empty($route))

		{

			$sql = "select *

					from ".DB_PREFIX."site_sections

					where full_url = ?"; # echo $sql."<hr />";

			$result = $this->registry->get('dbh')->prepare($sql); # var_dump($result);

			$result->bindParam(1, $route);

			try

			{

				if ($result->execute())

				{

					$_ = $result->fetch(); # print_r($_);

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

		}

		else return NULL;

	}

	

	# полный URL запроса

	function getRoute()

	{

		# print_r($this->route);

		if (!empty($this->route)) return $this->route;

	}

	

	# массив переменных vars динамичного URL'а

	function getRouteVars()

	{

		if (!empty($this->routeVars)) return $this->routeVars;

	}

	

	# информациЯ о разделе из таблицы site_sections

	function getSiteSectionInfo()

	{

		# print_r($this->siteSectionInfo);

		if (!empty($this->siteSectionInfo)) return $this->siteSectionInfo;

	}



	/*

	# запрет на 301 редирект со слешем в конце

	function set301Denial()

	{

		$this->denial301 = 1;

	}

	*/

}

