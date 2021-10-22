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
<title><?php echo $lang['titles']['pastewin']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link rel="stylesheet" href="<?php echo WP_WEB_DIRECTORY; ?>dialoge_theme.css" type="text/css">
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogEditorShared.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogShared.js"></script>
<script type="text/javascript" language="JavaScript">
<!--//
function start() {
        if (wp_is_ie) {
                document.frames('pasteFrame').document.designMode = "on";
                document.frames('pasteFrame').focus()
        } else {
                document.getElementById('pasteFrame').contentWindow.document.designMode = "on";
                document.getElementById('pasteFrame').contentWindow.focus()
        }
}
function html_tidy(code) {
        code = code.replace(/<([\w]+) class=([^ |>]+)([^>]+)/gi, "<$1$3")
        code = code.replace(/<font[^>]+>/gi, "")
        code = code.replace(/<\/font>/gi, "")
        var del = new RegExp("<del[^>]+>(.+)<\/del>","gi");
        code = code.replace(del, "")
        code = code.replace(/<ins[^>]+>/gi, "")
        code = code.replace(/<\/ins>/gi, "")
        // if remove styles
        if (document.getElementById('remove_style').checked == true) {
                if (wp_is_ie) {
                        code = code.replace(/<([\w]+) style="([^"]+)"/gi, "<$1 ")
                } else {
                        code = code.replace(/ style="([^"]+)"/gi, "")
                }
        }
        code = code.replace(/<span[^>]+>/gi, "")
        code = code.replace(/<\/span>/gi, "")
        code = code.replace(/<([\w]+) lang=([^ |>]+)([^>]+)/gi, "<$1$3")
        code = code.replace(/<xml[^>]+>/gi, "")
        code = code.replace(/<\xml[^>]+>/gi, "")
        <?php echo "code = code.replace(/<?xml[^>]+>/gi, \"\")\n"; ?>
        code = code.replace(/<\?[^>]+>/gi, "")
        code = code.replace(/<\/?\w+:[^>]+>/gi, "")
        code = code.replace(/<p[^>]+><\/p>/gi,"")
        code = code.replace(/<div[^>]+><\/div>/gi,"")

        <?php

        $browser_string = strtolower($_SERVER["HTTP_USER_AGENT"]);

        if (strstr($browser_string, 'gecko')) {
                echo "code = code.replace(/<\!--(.*?)-->/gmi,'')";
        }

        ?>

        code = code.replace(/\/secure\.htm#/gi,"WP_BOOKMARK#")
        code = code.replace(/<a name="([^"]+)[^>]+><\/a>/gi, "<img name=\"$1\" src=\"" + parentWindow.wp_directory + "/images/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">")
        code = code.replace(/<a name=([^>]+)><\/a>/gi, "<img name=\"$1\" src=\"" + parentWindow.wp_directory + "/images/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">")
        code = code.replace(/href="#/gi, "href=\"WP_BOOKMARK#")

        return code
}
function insert() {
        if (wp_is_ie) {
                parentWindow.wp_insert_code(obj,html_tidy(document.frames('pasteFrame').document.body.innerHTML));
        } else {
                parentWindow.wp_insert_code(obj,html_tidy(document.getElementById('pasteFrame').contentWindow.document.body.innerHTML));
        }
        window.close();
        return false;
}
//-->
</script>
</head>
<body onLoad="start(); hideLoadMessage();">
<form name="foo" onsubmit="return insert()">
<?php include('includes/load_message.php'); ?>
<div class="dialog_content">
        <p><?php echo $lang['paste_word_contents_below']; ?></p>
        <div align="center">
                <iframe src="<?php echo WP_WEB_DIRECTORY.'secure.htm' ?>" id="pasteFrame" class="inset" style="background-color: #ffffff; height:145px; width:98%;" frameborder="0"></iframe>
        </div>
        <div>
                <input id="remove_style" name="remove_style" type="checkbox" value="" checked="checked">
                <?php echo $lang['remove_styles']; ?></div>
        <div align="center">
                <input class="button" type="submit" value="<?php echo $lang['insert']; ?>" name="Insert">
                <input class="button" type="button" value="<?php echo $lang['cancel']; ?>" name="Cancel" onClick="window.close();">
        </div>
</div></form>
</body>
</html>