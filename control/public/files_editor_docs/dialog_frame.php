<?php
// An iframe container for web-page dialogs so that links etc will work in Internet Explorer.
include_once ('config.php');
if (isset ($_GET['lang']) ? $_GET['lang'] : '') {
        $lang_include = $_GET['lang'];
} else {
        $lang_include = DEFAULT_LANG;
}
include_once ('lang/'.$lang_include);
$arr = explode('/',$_GET['window']);
$length = count($arr);
$title = str_replace('.php', '', $arr[$length - 1]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang['titles'][$title]; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link rel="stylesheet" href="<?php echo WP_WEB_DIRECTORY; ?>dialoge_theme.css" type="text/css">
<style type="text/css">
body {
        padding: 0px 0px;
        margin: 0px 0px;
}
</style>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogShared.js"></script>
</head>
<body>
<iframe src="<?php echo stripslashes($_GET['window'] . '?' . $_SERVER["QUERY_STRING"]); ?>" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
</body>
</html>