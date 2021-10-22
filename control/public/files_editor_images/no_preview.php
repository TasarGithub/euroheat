<?php
// load language file
include_once ('config.php');
if (isset ($_GET['lang']) ? $_GET['lang'] : '') {
        $lang_include = $_GET['lang'];
} else {
        $lang_include = DEFAULT_LANG;
}
include_once ('lang/'.$lang_include);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>
<head>
<style type="text/css">
<!--
 body   {font-family:Verdana; font-size:12px; background-color: threedface;}
-->
</style>
</head>
<body>
<div align="center"> <br>
        <br>
        <?php echo $lang['no_preview']; ?></div>
</body>
</html>