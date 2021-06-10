<?php # ����� "�������������"

class router{
	protected $mvcPath;
	protected $args = array();
	protected $registry;
	protected $routes = array(); # ���������� ��������
	# protected $route = array(); # ���� �� ����� 1 ������ �������, ���������� ���� ������ � ��� �������
	protected $route; # ������ URL �������
	protected $routeVars = array(); # ���� �� ����� 1 ������ �������, ���������� ���� ����������
	protected $siteSectionInfo = array(); # ���� �� ����� 2 ������ ������ � site_sections, ���������� ���� ������ � ����������� � ���
    public $showRouterInfo = 0; # �������� � html-���� ��������� ������ router'�: ����� ���������� � ����� ����� ������������ ������� URL
	# protected $denial301; # ������ ��������� �� ����� �� URL, �� �� ������

	function __construct($registry)
	{
		# 301 �������� ���� URL ������������� �� �� "/", ������ 301 �������� �� URL �� "/"
		/*
		if ($_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI']) - 1] != "/")
		{
			# ������ �� 301 �������� ��� �������: http://www.circuses.su/bronirovanie/?event=20
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
	
	# ������������� ���� � ����� "mvc"
	function setPath($mvcPath)
	{
		# $mvcPath = trim($mvcPath, '/\\');
		if (is_dir($mvcPath)) $this->mvcPath = $mvcPath;
		else throw new Exception ('Invalid mvc path: '.$mvcPath);
		# echo 'mvc mvcPath: '.$this->mvcPath.'<hr />';
	}
	
	# ����������� ������ � ���������� ��� �����������
	function delegate()
	{
		# ����������� ���� � �������� controller/action/args
		$this->getController($controllerFullPath,
							 $controller,
							 $action,
							 $args
							 );
        # ��������� ����������� �����
		if (is_readable($controllerFullPath) == false)
		{
			# exit('404 Not Found. File is not readable: '.$controllerFullPath);
			# 404
			header("HTTP/1.0 404 Not Found");
			header('Location: http://'.DOMAIN);
			# echo '<!-- controller is not found: '.$controllerFullPath.' -->';
			# exit('404');
		}
		# ���������� ���� �������� ����������� � ������
		# include(SITE_PATH."#kernel/classes/controller_base.php"); # echo SITE_PATH."#kernel/classes/controller_base.php"."<hr />";
		# include(SITE_PATH."#kernel/classes/model_base.php"); # echo SITE_PATH."#kernel/classes/controller_base.php"."<hr />";
		
		# ������ ��������� �����������
		$class = $controller.'_controller'; # echo $class.'<hr />';
		
		/* ���������, �� �������� �� ����� ����� */
		if (!class_exists($class))
		{
			# ���������� ���� ����������� ��� ������� ��������
			include($controllerFullPath); # echo $controllerFullPath."<hr />";
		}
		$controllerInstance = new $class($this->registry);
		/* /���������, �� �������� �� ����� ����� */
		# �������� ��������?
		if (is_callable(array($controllerInstance, $action)) == false)
        {
            # exit("Controller: `{$controller}`, action `{$action}` is not accessible.");
            header("HTTP/1.0 404 Not Found");
            header("Location: http://".$_SERVER['SERVER_NAME']);
            exit;
        }
        # ��������� ��������
        $controllerInstance->$action();
	}
	
	# ����������� ���� � �������� controller/action/args
	# ���� ��� ����������� ���� �������� ����� controller/action/args � vars
	# ������ URL'�: DOMAIN/controller/action/args
	private function getController(&$controllerFullPath,
								   &$controller,
								   &$action,
								   &$args)
	{

		$route = (empty($_GET['route'])) ? '' : $_GET['route']; # echo $route."<hr />";
		# ������� '/' � �����, ���� �� ����
		if ($route[strlen($route)-1] == '/') $route = substr($route, 0, strlen($route)-1); # echo $route."<hr />";
		$this->route = $route; # echo $this->route.'<hr />';
		
		# �������������� ������ ��� URL'� (��������, ����� ��)
		$defence = new defence();
		$route = $defence->clearUserData($route); # echo $route."<hr />";
		
		### 1 ���� ������ �������: ��������� ���������� ������� (��������� � ./loader.php)
		# �������� �� ���������� URL'� � ���������� �����
		$routesMatches = NULL;
		# ��������� ���������� URL's
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
								# ������� /
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

		### 2 ���� ������ �������: ���� ������ � ������� site_sections �� ���� full_url �� ���������� $_GET['route']
		if (empty($routesMatches))
		{
			$sectionInfo = $this->setSiteSectionInfo($route); # print_r($sectionInfo);

			if (!empty($sectionInfo))
			{
				$isSiteSectionsFound = 1;
				
				# ���� ������ ���������, �������� ���������� ���, ����� �������� ���������� ����������� �� ���������
				if (!empty($sectionInfo['controller'])) $controller = $sectionInfo['controller'];
				else $controller = 'site_section_default';
				# echo $controller.'<hr />';
				
				$action = 'index';
				$this->siteSectionInfo = $sectionInfo; # print_r($this->siteSectionInfo);
			}
		}
		
		### 3 ���� ������ �������: ���� ���������� ������ �� ������
		# � � ������� site_sections ��� ��������������� �������, ���� ���������� �� URL'�: controller/action/args
		if (empty($routesMatches) # �� 1 ����� ������ �� �������
			and empty($isSiteSectionsFound) # �� 2 ����� ������ �� �������
			)
		{ # echo '!';
			if (empty($route)) $route = 'index';

			# ��������� URL �� �����
			# echo "route: ".$route."<hr />";
			$route = trim($route, '/\\');
			$routeParts = explode('/', $route); # echo "route parts: "; print_r($routeParts)."<hr />";
					
			# ���������� ���������� (controller)
			if (!empty($routeParts[0]))
			{
				$controller = $routeParts[0];
				# ������� ������ ������� �������
				array_shift($routeParts);
			}
			else $controller = 'index_controller.php';
			
			# if (is_file($fullControllerPath)) include($fullControllerPath);
			# else exit('Unable to load controller: '.$fullControllerPath);
			
			# ���������� �������� (action)
			if (!empty($routeParts[0]))
			{
				$action = $routeParts[0];
				# ������� ������ ������� �������
				array_shift($routeParts); # print_r($routeParts);
			}
			else $action = 'index';

			# ���������� ��������� (args)
			if (!empty($routeParts[0]))
			{
				$args = $routeParts;
			}
			else unset($args);
			# print_r($args);
		}
		
		$controllerFullPath = $this->mvcPath.$controller.'_controller.php'; # echo "$ontrollerFullPath: ".$controllerFullPath."<hr />";
		
		### �������
		# ��� ����������� ����
        if (!empty($this->showRouterInfo))
        {
            echo "<!--\n";
            # ��� ����������� ����
            if (!empty($routesMatches))
            {
                echo "route info: "; print_r($this->route); echo "\n";
                echo "controller: ".$controller."\n";
                echo "controller path: ".$controllerFullPath."\n";
                echo "action: ".$action."\n";
                echo "vars: "; print_r($this->routeVars); echo "\n";
            }
            # ��� ���������� ����
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
	
	# ������������� ���������� ����
	# ������ ������ ������: $router->addRoute(array('path' => '^page([/0-9]*)$', 'controller' => 'catalog', 'action' => 'showItems', 'vars' => array(1 => 'page')));
	public function addRoute($array)
	{
		if (is_array($array))
		{
			# ���������, ������ �� ��� ��������� ������� ���������
			if (empty($array['path']))
			{
				echo "�� ����� 'path' ��� ����������� ��������:";
				print_r($array);
				exit;
			}
			elseif (empty($array['controller']))
			{
				echo "�� ����� 'controller' ��� ����������� ��������:";
				print_r($array);
				exit;
			}
			elseif (empty($array['action']))
			{
				echo "�� ����� 'action' ��� ����������� ��������:";
				print_r($array);
				exit;
			}
			else
			# ��������� �������
			{
				$this->routes[] = array('path' => $array['path'], 'controller' => $array['controller'], 'action' => $array['action'], 'vars' => $array['vars']);
			}
		}
		else
		{
			echo "���������� ������� ����� �������:";
			print_r($array);
			exit;
		}
	}
	
	# ���� ������ � ������� site_sections ��
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
					echo "������ � SQL-�������:<br /><br />".$sql."<br /><br />".$e->getMessage();
					exit;
				}
			}
		}
		else return NULL;
	}
	
	# ������ URL �������
	function getRoute()
	{
		# print_r($this->route);
		if (!empty($this->route)) return $this->route;
	}
	
	# ������ ���������� vars ����������� URL'�
	function getRouteVars()
	{
		if (!empty($this->routeVars)) return $this->routeVars;
	}
	
	# ���������� � ������� �� ������� site_sections
	function getSiteSectionInfo()
	{
		# print_r($this->siteSectionInfo);
		if (!empty($this->siteSectionInfo)) return $this->siteSectionInfo;
	}

	/*
	# ������ �� 301 �������� �� ������ � �����
	function set301Denial()
	{
		$this->denial301 = 1;
	}
	*/
}
