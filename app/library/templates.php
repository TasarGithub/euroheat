<?php
# назначение: шаблонизатор на базе php
# дата создания: 2014.1.3
# автор: romanov.egor@gmail.com

class templates
{
	# ПЕРЕМЕННЫЕ КЛАССА
	public $template; # главный шаблон
	
	# КОНСТРУКТОР
	function __construct()
	{
	} # /КОНСТРУКТОР
	
	# УКАЗАТЬ ГЛАВНЫЙ ШАБЛОН (путь прописывать от DOCUMENT_ROOT)
	function setMainTemplate($template) # путь к шаблону от DOCUMENT_ROOT
	{
		# проверка переменных
		if (empty($template))
		{
			echo "<!-- main template is not defined in setMainTemplate method. -->\n";
			return;
		}
		
		$fullPath = DOCUMENT_ROOT.'/app/templates/'.basename($template);
		
		if (file_exists($fullPath)) $this->template = $fullPath;
		else echo "<!-- main template not found in setMainTemplate method: ".$template." -->\n";
	} # /УКАЗАТЬ ГЛАВНЫЙ ШАБЛОН
	
	# ВЫВОДИМ ГЛАВНЫЙ ШАБЛОН
	function echoMainTemplate()
	{
		# проверка главного шаблона
		if (empty($this->template))
		{
			echo "<!-- main template variable not defined in echoMainTemplate method. -->\n";
			return;
		}
		if (!file_exists($this->template))
		{
			echo "<!-- main template file not found in echoMainTemplate method: ".$this->template." -->\n";
			return;
		}
		# /проверка главного шаблона

        # читаем и выводим файл из шаблона
        ob_start(); // start capturing output
        include($this->template); // execute the file
        $content = ob_get_contents(); // get the contents from the buffer
        ob_end_clean(); // stop buffering and discard contents

        # выводим телефоны в контенте
        $content = str_replace(
		array('{phone}',
			  '{phone_for_link}',
			  '{year}'
			 ),
		array($GLOBALS['phone'],
			  $GLOBALS["phone_for_link"],
			  date('Y')),
		$content);

        echo $content;
	} # /ВЫВОДИМ ГЛАВНЫЙ ШАБЛОН
	
	# ПОДКЛЮЧЯЕМ ВНЕШНИЙ ШАБЛОН: ВЫПОЛЯНЕМ В НЕМ PHP-КОД, РЕЗУЛЬТАТ ПРИСВАИВАЕМ ПЕРЕМЕННОЙ
	function getTemplate($template) # путь к шаблону от DOCUMENT_ROOT
	{
		# проверка переменных
		if (empty($template))
		{
			echo "<!-- template is not defined in setTemplate method. -->\n";
			return;
		}
		
		$fullPath = DOCUMENT_ROOT.'/app/templates/'.basename($template);
		
		if (file_exists($fullPath))
		{
			ob_start(); // start capturing output
			include($fullPath); // execute the file
			$content = ob_get_contents(); // get the contents from the buffer
			ob_end_clean(); // stop buffering and discard contents
			return $content;
		}
		else echo "<!-- full path not found in setTemplate method. -->\n";
	} # /ПОДКЛЮЧЯЕМ ВНЕШНИЙ ШАБЛОН
}