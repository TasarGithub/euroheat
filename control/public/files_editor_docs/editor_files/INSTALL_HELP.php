<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Install Help</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style type="text/css">
body { font-family: verdana; font-size: 10pt }
.highlight { background-color: #FFFFCC }
</style>
</head>

<body>
<h2>������ � ���������</h2>
<p>���� ���� ������ ��������� � ����� 'editor_files' ��� �� ����� ��������! �� ������� ��� ������� �����!</p>
<p>���� ���� ����� ��� ������ ��� � ���������  � ��������� ����� <i><b>config.php</b></i>.</p>
<p>This file might not work for everyone!</p>
<hr>
<p>���������� �������� <b>WP_FILE_DIRECTORY</b>: <span class="highlight"><nobr>'<?php echo str_replace('\\', '/', getcwd().'/'); ?>'</nobr></span></p>
<hr>
<p>���������� �������� <b>WP_WEB_DIRECTORY</b>: <span class="highlight"><nobr>'<?php echo preg_replace('/INSTALL_HELP\.php/smi', '', $_SERVER['SCRIPT_NAME']); ?>'</nobr></span></p>
<hr>
<p>� �� ���� ����� <b>IMAGE_FILE_DIRECTORY</b> �.�. � �� ���� ��� ��������� ����� ��� �������� �����������. ��� ������ ���� �������� �������� ���: <span class="highlight"><nobr>'<?php echo str_replace(array('\\', '/editor_files'), array('/', '/images'), getcwd().'/'); ?>'</nobr></span></p>
<hr>
<p>� �� ���� ����� <b>IMAGE_WEB_DIRECTORY</b> �.�. � �� ���� ��� ��������� ����� ��� �������� �����������. ��� ������ ���� �������� �������� ���: <span class="highlight"><nobr>'<?php echo preg_replace('/editor_files\/INSTALL_HELP\.php/smi', 'images/', $_SERVER['SCRIPT_NAME']); ?>'</nobr></span></p>
<hr>
<p>� �� ���� ����� <b>DOCUMENT_FILE_DIRECTORY</b> �.�. � �� ���� ��� ��������� ����� ��� �������� ����������� ����������. ��� ������ ���� �������� �������� ���: <span class="highlight"><nobr>'<?php echo str_replace(array('\\', '/editor_files'), array('/', '/downloads'), getcwd().'/'); ?>'</nobr></span></p>
<hr>
<p>� �� ���� ����� <b>DOCUMENT_WEB_DIRECTORY</b>  �.�. � �� ���� ��� ��������� ����� ��� �������� ����������� ������. ��� ������ ���� �������� �������� ���: <span class="highlight"><nobr>'<?php echo preg_replace('/editor_files\/INSTALL_HELP\.php/smi', 'downloads/', $_SERVER['SCRIPT_NAME']); ?>'</nobr></span></p>
<hr>
<p>�����������!</p>
</body>
</html>
