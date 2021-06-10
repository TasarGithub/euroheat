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
<title><?php echo $lang['titles']['custom']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style type="text/css">
<!--
@import url(<?php echo WP_WEB_DIRECTORY; ?>dialoge_theme.css);
body {
        font-family:verdana, arial, helvetica, sans-serif;
        font-size: 11px;
}
button {
        width:300px;
        height:100px;
        background-color:#ffffff;
        padding: 5px;
        border: 2px solid threedshadow;
}
-->
</style>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogEditorShared.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogShared.js"></script>
<script type="text/javascript" language="JavaScript">
<!--//
function insert(srcElement) {
        code=srcElement.innerHTML;
        parentWindow.wp_insert_code(obj,code);
        window.close();
}
function initiate() {
        document.getElementById('cancel').blur();
        kids = document.getElementsByTagName('BUTTON');
        for (var i=0; i < kids.length; i++) {
                if (kids[i].type == "button") {
                        kids[i].onmouseover = m_over;
                        kids[i].onmouseout = m_out;
                }
        }
}
function m_over() {
        this.style.border= "3px solid highlight"
}
function m_out() {
        this.style.border= "2px solid threedshadow"
}
// -->
</script>
</head>
<body onLoad="initiate();hideLoadMessage();">
<?php include('includes/load_message.php'); ?>
<div align="center">
        <input class="button" type="button" id="cancel" value="<?php echo $lang['cancel']; ?>" onClick="window.close();">
        <p><?php echo $lang['click_on_an_object']; ?></p>
        <!-- begin custom inserts -->
        <hr>
        <!-- custom inserts as sent from the parent window -->
        <script language="JavaScript" type="text/javascript">
<!--//
        document.write(obj.custom_inserts);
//-->
</script>
        <!-- end custom inserts -->
</div>
</body>
</html>