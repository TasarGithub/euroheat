<?php # class "Реестр" - замена GLOBALS

class registry
{
	protected $vars = array();
	
	# устанавливаем значение переменной
	public function set($var, $value)
	{
		if (empty($this->vars[$var])) $this->vars[$var] = $value;
		else throw new Exception('Unable to set var: '.$key.'. Already set.\n<hr />');
	}
	
	# получаем значение переменной
	public function get($var)
	{
		if (!empty($this->vars[$var])) return $this->vars[$var];
		# else throw new Exception('Unable to get var: '.$key.'. Undefined.\n<hr />');
		else echo '<!-- Unable to get var: '.$var.' -->';
	}
	
	# удаляем значение переменной
	public function remove()
	{
		unset($this->vars[$var]);
	}
}