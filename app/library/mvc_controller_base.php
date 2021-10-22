<?php
/*
Этот абстрактный класс является родительским классом для всех контроллеров. 
При помощи абстрактного метода index() заставлять все дочерние контроллеры реализовывать этот метод.
*/

abstract class controller_base
{
	protected $registry;
	protected $tpl;
	public $route; # полный URL запроса
	public $routeVars; # если на этапе 1 найден маршрут, записываем в этот массив переменных vars динамичного URL'а
	public $siteSectionInfo; # задается в методе $this->getSiteSectionInfo
	public $model; # экземпляр класса модели для данного контроллера
	public $fullURL; # полный URL для любой страницы
	
	function __construct($registry)
	{
		$this->registry = $registry;
		$this->tpl = $this->registry->get('tpl'); # var_dump($this->tpl); echo "<hr />";
		$this->defence = $this->registry->get('defence'); # var_dump($this->defence); echo "<hr />";
		$this->route = 	$this->registry->get('router')->getRoute();
		$this->routeVars = 	$this->registry->get('router')->getRouteVars();
		# $this->siteSectionInfo = $this->registry->get('router')->getSiteSectionInfo(); # print_r($this->siteSectionInfo);
		# подгружаем класс модели # echo get_class($this).'<hr />';
		$modelName = str_replace('_controller', '', get_class($this)).'_model'; # echo $modelName.'<hr />';
		$this->model = new $modelName($this->registry);
		# полный URL для любой страницы
		$this->fullURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; # echo $this->fullURL; # print_r($_SERVER);
	}
	
	/* загружаем контроллер в другом контроллере */
	function load($class_name)
	{
		if (empty($class_name)) return;
		# провреяем, не объявлен ли класс ранее
		
		$controller_name = $class_name.'_controller';
		$controller_file_name = $class_name.'_controller.php';
		
		# echo MVC_PATH;
		/* провреяем, не объявлен ли класс ранее */
		if (!class_exists($controller_name))
		{
			include(MVC_PATH.$controller_file_name);
		}
		
		/*
		$included_files = get_included_files();
		foreach ($included_files as $filename) {
			echo "$filename\n";
		}
		*/

		$result = new $controller_name($this->registry); # echo echo $controller_name.'<hr />';
		
		if (is_object($result)) return $result;
		else return;
	}
	/* /загружаем контроллер в другом контроллере */
	
	# abstract function index();
}