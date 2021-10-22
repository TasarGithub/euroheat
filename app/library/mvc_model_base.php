<?php # этот абстрактный класс является родительским классом для всех моделей.

abstract class model_base
{
	protected $registry;
	protected $db; # доступ к БД (PDO)
	public $route; # полный URL запроса
	public $routeVars; # массив переменных vars динамичного URL'а
	public $siteSectionInfo; # задается в методе $this->getSiteSectionInfo
	
	function __construct($registry)
	{
		global $db;
		$this->registry = $registry;
		$this->dbh = $this->registry->get('dbh'); # var_dump($this->db); echo "<hr />";
		$this->route = $this->registry->get('router')->getRoute();
		$this->routeVars = $this->registry->get('router')->getRouteVars();
		$this->siteSectionInfo = $this->registry->get('router')->getSiteSectionInfo(); # print_r($this->siteSectionInfo);
	}
}