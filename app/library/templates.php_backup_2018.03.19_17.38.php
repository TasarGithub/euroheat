<?php
# ����������: ������������ �� ���� php
# ���� ��������: 2014.1.3
# �����: romanov.egor@gmail.com

class templates
{
	# ���������� ������
	public $template; # ������� ������
	
	# �����������
	function __construct()
	{
	} # /�����������
	
	# ������� ������� ������ (���� ����������� �� DOCUMENT_ROOT)
	function setMainTemplate($template) # ���� � ������� �� DOCUMENT_ROOT
	{
		# �������� ����������
		if (empty($template))
		{
			echo "<!-- main template is not defined in setMainTemplate method. -->\n";
			return;
		}
		
		$fullPath = DOCUMENT_ROOT.'/app/templates/'.basename($template);
		
		if (file_exists($fullPath)) $this->template = $fullPath;
		else echo "<!-- main template not found in setMainTemplate method: ".$template." -->\n";
	} # /������� ������� ������
	
	# ������� ������� ������
	function echoMainTemplate()
	{
		# �������� �������� �������
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
		# /�������� �������� �������
		
		# ������ � ������� ���� �� �������
		include($this->template);
	} # /������� ������� ������
	
	# ���������� ������� ������: ��������� � ��� PHP-���, ��������� ����������� ����������
	function getTemplate($template) # ���� � ������� �� DOCUMENT_ROOT
	{
		# �������� ����������
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
	} # /���������� ������� ������
}