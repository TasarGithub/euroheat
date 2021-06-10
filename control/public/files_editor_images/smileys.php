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
<title><?php echo $lang['titles']['smileys']; ?></title>
<style type="text/css">
<!--
@import url(<?php echo WP_WEB_DIRECTORY; ?>dialoge_theme.css);
.text {
         cursor: pointer; cursor: hand;
}
-->
</style>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogEditorShared.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogShared.js"></script>
<script language="JavaScript" type="text/javascript">
<!-- //
var smiley = null;
function initiate() {
        document.getElementById('ok').blur();
        var kids = document.getElementsByTagName('TD');
        for (var i=0; i < kids.length; i++) {
                if (kids[i].className == "text") {
                        kids[i].onmouseover = m_over;
                        kids[i].onmouseout = m_out;
                        kids[i].onclick = AddSmileyIcon;
                }
        }
}
function m_over() {
        if (smiley == this) return
        this.style.backgroundColor = "highlight"
        this.style.color = "highlighttext"
}
function m_out() {
        if (smiley == this) return
        this.style.backgroundColor = "threedface"
        this.style.color = "black"
}
//Function to add smiley
function AddSmileyIcon(){
        this.style.backgroundColor = "highlight"
        this.style.color = "highlighttext"
        if (smiley) {
                        smiley.style.backgroundColor = "threedface";
                        smiley.style.color = "black"
        }
        smiley = this

}
function insert() {
        if (smiley != null) {
                var images = smiley.getElementsByTagName('IMG');
                imagePath = images[0].src
                parentWindow.wp_create_image_html(obj,imagePath, '17', '17', '', '', '', '');
        }
        window.close();
        return false;
}
// -->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>
<form name="foo" onsubmit="return insert();">
<body onLoad="initiate(); hideLoadMessage();">
<?php include('includes/load_message.php'); ?>
<fieldset>
<legend><?php echo $lang['emoticon_smilies']; ?></legend>
<table width="350" border="0" cellpadding="4" cellspacing="3" align="center">
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley1.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['smile']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley9.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['embarrassed']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley2.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['wink']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley10.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['star']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley3.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['shocked']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley11.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['dead']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley4.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['big_smile']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley12.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['sleepy']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley5.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['confused']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley13.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['disapprove']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley6.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['unhappy']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley14.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['approve']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley7.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['angry']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley15.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['evil_smile']; ?></td>
        </tr>
        <tr>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley8.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['clown']; ?></td>
                <td width="50%" class="text"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/smileys/smiley16.gif" width="17" height="17" border="0" align="absmiddle" alt="">
                        <?php echo $lang['cool']; ?></td>
        </tr>
</table>
</fieldset>
<br>
<div align="center">
        <button type="submit" id="ok"><?php echo $lang['ok']; ?></button>
        &nbsp;
        <button type="button" onClick="window.close();"><?php echo $lang['cancel']; ?></button>
</div></form>
</body>
</html>