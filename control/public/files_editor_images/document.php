<?php

// You may alter any of the code in this file to suit your requirements.

// If you need to locate this file outside of the editor_files folder you will need to alter the include paths below:

// if you intend to locate this file on a different physical server make sure that a copy of 'config.php' and 'editor_functions.php' is available to this script.

include_once ('config.php');
include_once ('editor_functions.php');
$instance_doc_dir = '';
// SET DIRECTORY LOCATIONS:
// This routine sets the location of the document directory, you can change this routine if you wish.
// If you want to change the document directory based on a user add your user authentication scripts to the top of this script.
// Then change the routine below so that it sets the directories based on the user rather than setting them the same as config.php.
if (isset ($trusted_directories[$_GET['instance_doc_dir']]) ? $trusted_directories[$_GET['instance_doc_dir']] : '') {
        $file_direcory = $trusted_directories[$_GET['instance_doc_dir']][0];
        $web_directory = $trusted_directories[$_GET['instance_doc_dir']][1];
        $instance_doc_dir = $_GET['instance_doc_dir'];
} else if (isset ($trusted_directories[$_POST['instance_doc_dir']]) ? $trusted_directories[$_POST['instance_doc_dir']] : '') {
        $file_direcory = $trusted_directories[$_POST['instance_doc_dir']][0];
        $web_directory = $trusted_directories[$_POST['instance_doc_dir']][1];
        $instance_doc_dir = $_POST['instance_doc_dir'];
} else {
        $file_direcory = DOCUMENT_FILE_DIRECTORY;
        $web_directory = DOCUMENT_WEB_DIRECTORY;
}

// load language file
if (isset ($_GET['lang']) ? $_GET['lang'] : '') {
        $lang_include = $_GET['lang'];
} else if (isset ($_POST['lang']) ? $_POST['lang'] : '') {
        $lang_include = $_POST['lang'];
} else {
        $lang_include = DEFAULT_LANG;
}
include_once ('lang/'.$lang_include);

if (isset ($_REQUEST['in_wp'])) {
        if ($_REQUEST['in_wp'] == 1) {
                $in_wp = true;
        } else {
                $in_wp = false;
        }
} else {
        $in_wp = true;
}


// variables

$message = '';
$name2 = '';

// functions
function document_exit($message) {
        echo '<html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
                <title>'.$lang['titles']['document'].'</title>
                <link rel="stylesheet" href="'.WP_WEB_DIRECTORY.'dialoge_theme.css" type="text/css">
                <script language="JavaScript" type="text/javascript" src="'.WP_WEB_DIRECTORY.'js/dialogShared.js"></script>
                </head>
                <body scroll="no" onload="hideLoadMessage();">'; ?><?php include('includes/load_message.php'); ?>
                <?php
                echo ' <div align="center">'.$message.'</div>
                </body>
                </html>';
                exit;
}
//get the folder for us to look inside, we'll also check that there are no ./ or ../ so that we are only ever looking at folders below the $web_directory, I'm sure there is a more secure way to do this?
if (isset ($_GET['folder']) ? $_GET['folder'] : '') {
        if (wp_dir_name_ok($_GET['folder'])) {
                $directory = $file_direcory.$_GET['folder'];
                $folderpath = $_GET['folder'];
        } else {
                $directory = $file_direcory;
                $folderpath = '';
        }
} elseif (isset ($_POST['folder']) ? $_POST['folder'] : '') {
        if (wp_dir_name_ok($_POST['folder'])) {
                $directory = $file_direcory.$_POST['folder'];
                $folderpath = $_POST['folder'];
        } else {
                $directory = $file_direcory;
                $folderpath = '';
        }
} else {
        $directory = $file_direcory;
        $folderpath = '';
}

// check that it exists
if (!file_exists ($directory)) {
        document_exit('<b>Warning: this directory does not exist: '.$directory.'. Check that you have set DOCUMENT_FILE_DIRECTORY correctly in config.php. If you are using the set_img_dir function check that the you have set the $trusted_directories array correctly.</b>');
}

if ($folderpath != '') {
        if (substr ($folderpath, strlen ($folderpath) - 1) != '/') {
                $folderpath.='/';
        }
}

if (substr ($directory, strlen ($directory) - 1) != '/') {
        $directory.='/';
}

////////////////
// actions... //
////////////////
if ((isset ($_GET['file']) ? $_GET['file'] : '') && (wp_file_name_ok($_GET['file']))) {
        if (isset ($_GET['action']) ? $_GET['action'] : '') {
                // delete file or directory
                if (($_GET['action']=='delete') && ($delete_files)) {
                        if (@wp_delete_file($directory.$_GET['file'])) {
                                $message='<div class="helpMessage"><p> '.wp_var_replace($lang['file_deleted'], array('file'=>$_GET['file'], 'folder' => $web_directory.$folderpath)).' </p></div>';
                        } else {
                                document_exit ('<p>&nbsp;</p>
                                <div class="helpMessage">
                                <form>
                                <p> '.wp_var_replace($lang['cannot_delete'], array('file'=>$_GET['file'])).' '.$lang['check_directory_permission'].'</p>
                                <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                </form>
                                </div>');
                        }
                }
                // rename
                if (($_GET['action']=='rename') && ($rename_files || $rename_directories) && (!isset($_GET['name']))) {

                        $filename = str_replace(strrchr($_GET['file'],'.'), '', $_GET['file']);

                        document_exit ('<p>&nbsp;</p>
        <div align="center" style="width:300px">
        <fieldset>
        <form action="'.WP_WEB_DIRECTORY.'document.php" method="get">
        <p>'.wp_var_replace($lang['enter_new_filename'],array('file'=>$_GET['file'])).'</p>
        <p><input type="text" name="name" value="'.$filename.'" size="36">'.strrchr($_GET['file'],'.').'</p>
        <input type="hidden" name="instance_doc_dir" value="'.$instance_doc_dir.'">
        <input type="hidden" name="folder" value="'.$folderpath.'">
        <input type="hidden" name="file" value="'.$_GET['file'].'">
        <input type="hidden" name="action" value="rename">
        <input type="hidden" name="in_wp" value="'.$in_wp.'">
        <input type="hidden" name="lang" value="'.$lang_include.'">
        <input class="button" type="submit" name="OK" value="'.$lang['ok'].'">
        &nbsp;
        <input class="button" type="button" name="Cancel" value="'.$lang['cancel'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
        </form><br>
        </fieldset>
        </div>');
                }
                if (($_GET['action']=='rename') && ($rename_files || $rename_directories) && (isset($_GET['name']) ? $_GET['name'] : '')) {
                        if (!wp_file_name_ok($_GET['name'])) {
                                document_exit ('<p>&nbsp;</p>
                                <div class="helpMessage">
                                <form>
                                <p> '.$lang['bad_file_name'].'</p>
                                <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;action=rename&amp;file='.$_GET['file'].'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                </form>
                                </div>');
                        } elseif (file_exists ($file_direcory.$folderpath.$_GET['name'].strrchr($_GET['file'],'.'))) {
                                document_exit ('<p>&nbsp;</p>
                                <div class="helpMessage">
                                <form>
                                <p> '.$lang['file_already_exists'].'</p>
                                <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;action=rename&amp;file='.$_GET['file'].'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                </form>
                                </div>');
                        } elseif (@rename($file_direcory.$folderpath.$_GET['file'], $file_direcory.$folderpath.$_GET['name'].strrchr($_GET['file'],'.'))) {
                                $message='<div class="helpMessage"><p> '.wp_var_replace($lang['file_renamed'],array('file'=>$_GET['file'],'name'=>$_GET['name'].strrchr($_GET['file'],'.'))).'</p></div>';
                        } else {
                                document_exit ('<p>&nbsp;</p>
                                <div class="helpMessage">
                                <form>
                                <p> '.wp_var_replace($lang['could_not_rename'],array('file'=>$_GET['file'],'name'=>$_GET['name'].strrchr($_GET['file'],'.'))).' '.$lang['check_directory_permission'].'</p>
                                <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                </form>
                                </div>');
                        }
                }
        }
}
if (isset ($_GET['action']) ? $_GET['action'] : '') {
        // create directory
        if (($_GET['action']=='create_dir') && ($create_directories) && (!isset($_GET['dir_name']))) {
                document_exit('<p>&nbsp;</p>
<div align="center" style="width:300px">
<fieldset>
<form action="'.WP_WEB_DIRECTORY.'document.php" method="get">
<p>'.$lang['enter_dirname_for_new_dir'].'</p>
<p><input type="text" name="dir_name" value="" size="36"></p>
<input type="hidden" name="instance_doc_dir" value="'.$instance_doc_dir.'">
<input type="hidden" name="folder" value="'.$folderpath.'">
<input type="hidden" name="action" value="create_dir">
<input type="hidden" name="in_wp" value="'.$in_wp.'">
<input type="hidden" name="lang" value="'.$lang_include.'">
<input class="button" type="submit" name="OK" value="'.$lang['ok'].'">
&nbsp;
<input class="button" type="button" name="Cancel" value="'.$lang['cancel'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
</form><br>
</fieldset>
</div>');

        }
        if (($_GET['action']=='create_dir') && ($create_directories) && (isset($_GET['dir_name']) ? $_GET['dir_name'] : '')) {
                if (!wp_file_name_ok($_GET['dir_name'])) {
                        document_exit ('<p>&nbsp;</p>
                        <div class="helpMessage">
                        <form>
                        <p> '.$lang['bad_file_name'].'</p>
                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;action=create_dir&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                        </form>
                        </div>');
                } else if (file_exists($file_direcory.$folderpath.$_GET['dir_name'])) {
                        document_exit ('<p>&nbsp;</p>
                        <div class="helpMessage">
                        <form>
                        <p> '.$lang['file_already_exists'].'</p>
                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;action=create_dir&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                        </form>
                        </div>');
                } elseif (@wp_create_dir($file_direcory.$folderpath.$_GET['dir_name'])) {
                        $message='<div class="helpMessage"><p> '.wp_var_replace($lang['file_created'],array('file'=>$_GET['dir_name'],'folder'=>$web_directory.$folderpath)).' </p></div>';
                } else {
                        document_exit ('<p>&nbsp;</p>
                        <div class="helpMessage">
                        <form>
                        <p> '.$lang['dir_not_created'].' '.$lang['check_directory_permission'].'</p>
                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                        </form>
                        </div>');
                }
        }
}
if ((isset($_POST['ok_to_overwrite']) ? $_POST['ok_to_overwrite'] : '') && ($overwrite)) {
        if (($_POST['ok_to_overwrite'] == $lang['yes']) && (isset($_POST['document_field']) ? $_POST['document_field'] : '') && (wp_file_name_ok($_POST['document_field']))) {
                if (is_file($directory.$_POST['document_field'])) {
                        wp_delete_file($directory.$_POST['document_field']);
                        if (rename($directory.$_POST['document_field'].'.TEMP', $directory.$_POST['document_field'])) {
                                // make sure we will be able to delete and re-name this file later
                                $message= '<div class="helpMessage"><p> '.$lang['file_uploaded1'].'</p></div>';
                        } else {
                                wp_delete_file($directory.$_POST['document_field'].'.TEMP');
                                document_exit ('<p>&nbsp;</p>
                                <div class="helpMessage">
                                <form>
                                <p> '.$lang['upload_failed'].' '.$lang['check_directory_permission'].' </p>
                                <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                </form>
                                </div>');
                        }
                } else {
                        wp_delete_file($directory.$_POST['document_field'].'.TEMP');
                        document_exit ('<p>&nbsp;</p>
                        <div class="helpMessage">
                        <form>
                        <p> '.$lang['dir_exists'].' </p>
                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                        </form>
                        </div>');
                }
        } elseif ($_POST['ok_to_overwrite'] == $lang['cancel']) {
                wp_delete_file($directory.$_POST['document_field'].'.TEMP');
        } else {
                $message.= '<div class="helpMessage"><p>'.$lang['copy_error'].'</p></div>';
        }
}
// upload files
if (isset($_FILES['document_field']) ? $_FILES['document_field'] : '') {
        if (is_uploaded_file($_FILES['document_field']['tmp_name'])) {
                $extension = strrchr(strtolower($_FILES['document_field']['name']),'.');
                //exit ($_FILES['document_field']['tmp_name']);
                // check filetype against accepted files
                if (!wp_extension_ok($extension, $document_types)) {
                        document_exit ('<p>&nbsp;</p>
                        <div class="helpMessage">
                        <form>
                        <p> '.wp_var_replace($lang['bad_filetype'],array('filetypes'=>$document_types)).' </p>
                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                        </form>
                        </div>');
                        break;
                }
                // check if there is an icon for this filetype and whether we can safely show a preview of it.
                $file_info = wp_get_fileinfo($extension);
                $icon = $file_info['icon'];
                $filetype = $file_info['description'];
                $preview = $file_info['preview'];

                if ($_FILES['document_field']['size'] >= $max_documentfile_size) {
                        document_exit ('<p>&nbsp;</p>
                        <div class="helpMessage">
                        <form>
                        <p> '.wp_var_replace($lang['file_too_large'],array('max_size'=>($max_documentfile_size/1000))).' </p>
                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                        </form>
                        </div>');
                } else {
                        $name=$_FILES['document_field']['name'];
                        // somepeople like to spit out an error if file have bad characters, I prefer to quetly rename their file.
                        $search_array = array(
                                "'",
                                ';',
                                '"',
                                '/',
                                '\\',
                                '@',
                                ":",
                                '?',
                                '&',
                                ' '
                        );
                        $replace_array = array(
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '_'
                        );
                        $name = str_replace($search_array, $replace_array, $name);

                        $just_file_name = $name;

                        //used later to populate the dialoge with the file they have just uploaded:
                        $name2 = $folderpath.$name;
                        $fsize = '';

                        if ($name != $_FILES['document_field']['name']) {
                                $extra_message = wp_var_replace($lang['but_was_renamed'],array('name'=>$name));
                        } else {
                                $extra_message = '';
                        }

                        $file_size = wp_convert_fsize($_FILES['document_field']['size']);

                        $name=$file_direcory.$folderpath.$name;
                        if (file_exists($name)) {
                                if ($overwrite) {
                                        @copy($_FILES['document_field']['tmp_name'], $name.'.TEMP');
                                        @chmod($name.'.TEMP',CHMOD_MODE);
                                        document_exit ('<p>&nbsp;</p>
                                        <div class="helpMessage">
                                        <form action="'.WP_WEB_DIRECTORY.'document.php" method="post">
                                        <input type="hidden" name="instance_doc_dir" value="'.$instance_doc_dir.'">
                                        <input name="document_field" type="hidden" value="'.$just_file_name.'">
                                        <input type="hidden" name="folder" value="'.$folderpath.'">
                                        <input type="hidden" name="lang" value="'.$lang_include.'">
                                        <input type="hidden" name="in_wp" value="'.$in_wp.'">
                                        <p> '.$lang['should_i_overwrite'].' </p>
                                        <input class="button" type="submit" name="ok_to_overwrite" value="'.$lang['yes'].'">
                                        <input class="button" type="submit" name="ok_to_overwrite" value="'.$lang['cancel'].'">
                                        </form>
                                        </div>');
                                } else {
                                        document_exit ('<p>&nbsp;</p>
                                        <div class="helpMessage">
                                        <form action="'.WP_WEB_DIRECTORY.'document.php" method="post">
                                                        <input name="document_field" type="hidden" value="'.$just_file_name.'">
                                                        <input type="hidden" name="instance_doc_dir" value="'.$instance_doc_dir.'">
                                                        <input type="hidden" name="folder" value="'.$folderpath.'">
                                                        <input type="hidden" name="lang" value="'.$lang_include.'">
                                                        <input type="hidden" name="in_wp" value="'.$in_wp.'">
                                        <p> '.$lang['no_overwrite_permission'].' </p>
                                        <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                        </form>
                                        </div>');
                                }
                        } elseif (@copy($_FILES['document_field']['tmp_name'], $name)) {
                                // make sure we will be able to delete and re-name this file later
                                @chmod($name,CHMOD_MODE);
                                $message.= '<div class="helpMessage"><p> '.wp_var_replace($lang['file_uploaded2'],array('file'=>$_FILES['document_field']['name'])).' '.$extra_message.'</p></div>';
                        } else {
                                document_exit ('<p>&nbsp;</p>
                                <div class="helpMessage">
                                <form>
                                <p> '.wp_var_replace($lang['upload_failed2'],array('file'=>$_FILES['document_field']['name'])).' '.$lang['check_directory_permission'].' </p>
                                <input class="button" type="button" name="Continue" value="'.$lang['ok'].'" onClick="document.location.replace(\''.WP_WEB_DIRECTORY.'document.php?in_wp='.$in_wp.'&amp;lang='.$lang_include.'&amp;folder='.$folderpath.'&amp;instance_doc_dir='.$instance_doc_dir.'\')">
                                </form>
                                </div>');
                        }
                }
        }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title><?php echo $lang['titles']['document']; ?></title>
<link rel="stylesheet" href="<?php echo WP_WEB_DIRECTORY; ?>dialoge_theme.css" type="text/css">
<style type="text/css">
p {
        margin:2px
}
</style>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogEditorShared.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WP_WEB_DIRECTORY; ?>js/dialogShared.js"></script>
<script type="text/javascript">
<!--//
var CURRENT_HIGHLIGHT
function highlight(srcElement) {
        if (CURRENT_HIGHLIGHT) {
                CURRENT_HIGHLIGHT.style.backgroundColor='';
                CURRENT_HIGHLIGHT.style.color='#003399';
        }
        srcElement.style.backgroundColor = 'highlight';
        srcElement.style.color = 'highlighttext';
        CURRENT_HIGHLIGHT = srcElement;
}
function insert_link() {
        parentWindow.wp_hyperlink(obj,document.document_form.oHerf.value, "_blank", "");
        top.window.close();
        return false;
}
function localLink(url,preview,fsize,type) {
        document.document_form.oHerf.value=url;
        document.getElementById('size').innerHTML = '<?php echo $lang['size']; ?> ' + fsize;
        document.getElementById('type').innerHTML = '<?php echo $lang['type']; ?> ' + type;
        if (preview == 0) {
                if (wp_is_ie) {
                        document.frames('preview').location.href = '<?php echo WP_WEB_DIRECTORY; ?>no_preview.php?lang=<?php echo $lang_include; ?>';
                } else {
                        document.getElementById('preview').contentWindow.document.open();
                        document.getElementById('preview').contentWindow.document.write('<div align="center" style="font-family:verdana"><br><br><?php echo $lang['no_preview']; ?></div>');
                        document.getElementById('preview').contentWindow.document.close();
                }
        } else if (wp_is_ie) {
                document.frames('preview').location.href = url;
        } else {
                document.getElementById('preview').contentWindow.location.assign(url);
        }
}
function preview(url) {
        if (wp_is_ie) {
                document.frames('preview').location.href = url;
        } else {
                document.getElementById('preview').contentWindow.location.assign(url);
        }
}
function initialize() {
        var current_href = parentWindow.wp_current_hyperlink;
        if (current_href!="") {
                document.document_form.oHerf.value=current_href;
                if (document.getElementById(current_href)) {
                        highlight(document.getElementById(current_href));
                        document.getElementById(current_href).focus();
                }
        } else {
                document.document_form.oHerf.value="http://";
        }
}
function doConfirm(url,msg) {
        if (confirm(msg)){
                document.location.assign(url)
        }
}
function upload_check() {
        if (document.getElementById('document_field').value == '') {
                alert("<?php echo $lang['click_browse']; ?>")
                document.getElementById('document_field').focus();
                return false;
        } else {
                return true;
        }
}
function showUploadMessage() {
        document.getElementById('uploadMessage').style.display = 'block'
}
function hideUploadMessage() {
        document.getElementById('uploadMessage').style.display = 'none'
}
function cancelUpload() {
        if (wp_is_ie) {
                try {
                        document.execCommand('stop');
                } catch (e) {
                        document.location.reload();
                }
        } else {
                window.stop();
        }
        hideUploadMessage()
}
//-->
</script>
<?php
// script to make any uploaded images currently selected
if (!empty($name2)) {
        echo '<script type="text/javascript">
        <!--// Begin
        function uploadedimage() {
                localLink("'.$web_directory.$name2.'", "'.$preview.'", "'.$file_size.'", "'.$filetype.'");
                highlight(document.getElementById("'.$web_directory.$name2.'"));
                document.getElementById("'.$web_directory.$name2.'").focus();
        }
        // End -->
</script>
';
} else {
        echo '<script type="text/javascript">
        <!--// Begin
        function uploadedimage() {
                return true;
        }
        // End -->
</script>
';

}
?>
</head>
<body scroll="no" onLoad="<?php if ($in_wp) {        ?>initialize(); <?php } ?>uploadedimage(); hideLoadMessage();">
<?php include('includes/load_message.php'); ?>
<div align="center" id="uploadMessage">
        <table width="100%" height="90%">
                <tr>
                        <td align="center" valign="middle"><div id="uploadMessageText"><?php echo $lang['upload_in_progress']; ?> <?php echo $lang['please_wait']; ?><br><br>
        <img src="images/load_bar.gif" height="12" width="251" alt="" class="inset"><br><br>
        <input class="button" type="button" value="<?php echo $lang['cancel']; ?>" onClick="cancelUpload()"></div></td>
                </tr>
        </table>
</div>
<div class="dialog_content">
        <div style="height:22px">
                <?php
if ($message) {echo $message;} else {echo '&nbsp;';}
?>
        </div>
        <table border="0" cellpadding="1" cellspacing="3">
                <tr>
                        <td rowspan="10" valign="top"> <fieldset>
                                <legend><?php echo $lang['select_document']; ?></legend>
                                <div align="left"><?php echo $lang['looking_in'] ; ?> <input class="disabledtextbox" type="text" name="imagename" value="<?php echo $web_directory.$folderpath; ?>" style="width:292px" readonly="readonly">
                                </div>
                                <table width="360" border="0" cellpadding="0" cellspacing="0" style="background-color:threedshadow;">
                                        <tr>
                                                <td> <p>
                                                                <?php
                if ($folderpath != '') {

                        $array = explode ('/',$folderpath);
                        $array[sizeof($array)-1] = NULL;
                        $array[sizeof($array)-2] = NULL;
                        $foo = implode ('/', $array);
                        while (substr ($foo, strlen ($foo) - 1) == '/') {
                                $foo = substr ($foo, 0, strlen ($foo) - 1);
                        }

                ?>
                                                                <a style="color:highlighttext" href="<?php echo WP_WEB_DIRECTORY; ?>document.php?in_wp=<?php echo $in_wp; ?>&amp;lang=<?php echo $lang_include; ?>&amp;folder=<?php echo $foo ?>&amp;instance_doc_dir=<?php echo $instance_doc_dir; ?>">
                                                                <img src="<?php echo WP_WEB_DIRECTORY; ?>images/up.gif" width="23" height="22" alt="<?php echo $lang['up_one_level']; ?>" title="<?php echo $lang['up_one_level']; ?>" border="0" align="absmiddle"><?php echo $lang['parent_directory']; ?></a>
                                                                <?php
                }  else {
                ?>
                                                                <img src="<?php echo WP_WEB_DIRECTORY; ?>images/spacer.gif" width="23" height="22" alt="" border="0" align="absmiddle">
                                                                <?php
                }
        if ($create_directories) { ?>
                                                        </p></td>
                                                <td align="right"> <p> <a style="color:highlighttext" href="<?php echo WP_WEB_DIRECTORY; ?>document.php?in_wp=<?php echo $in_wp; ?>&amp;lang=<?php echo $lang_include; ?>&amp;action=create_dir&amp;folder=<?php echo $folderpath ?>&amp;instance_doc_dir=<?php echo $instance_doc_dir; ?>"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/newfolder.gif" width="23" height="22" alt="<?php echo $lang['new_folder']; ?>" title="<?php echo $lang['new_folder']; ?>" border="0" align="absmiddle"><?php echo $lang['new_folder']; ?></a>
                                                                <?php } ?>
                                                                &nbsp;&nbsp;&nbsp;</p></td>
                                        </tr>
                                </table>
                                <table width="360px" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                                <td class="fileBar" width="190"><p><?php echo $lang['name']; ?></p></td>
                                                <td class="fileBar" width="100"><p><?php echo $lang['type']; ?></p></td>
                                                <td class="fileBar" width="70" style="border-right: 1px solid threedshadow"><p><?php echo $lang['action']; ?></p></td>
                                        </tr>
                                </table>
                                <div class="inset" style="height:315; width:100%; overflow:auto; background-color:#FFFFFF">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <?php
          // first build a list of folders at the top of the tree.
$count = 0;
$bhandle=opendir($directory);
while (false!==($folder = readdir($bhandle))) {
        if (file_exists ($directory.$folder) && (!is_file($directory.$folder))  && ($folder != "." && $folder != ".." && $folder != "_vti_cnf")) {
                $foldername  = $folder ;
                        $count += 1;
                        echo "
        <tr onmouseover=\"this.style.backgroundColor='#eeeeee'\" onmouseout=\"this.style.backgroundColor='#ffffff'\">
                <td width=\"190\">
                        <p><a href=\"".WP_WEB_DIRECTORY."document.php?in_wp=$in_wp&amp;lang=$lang_include&amp;folder=".$folderpath.$foldername."&amp;instance_doc_dir=$instance_doc_dir\"><img src=\"".WP_WEB_DIRECTORY."images/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">$foldername </a></p>
                </td>
                <td width=\"100\">
                <p>".$lang['files']['folder']."</p>
                </td>
                <td>
                        <p>";  if ($rename_directories) { echo ("<a href=\"".WP_WEB_DIRECTORY."document.php?in_wp=$in_wp&amp;lang=$lang_include&amp;folder=$folderpath&amp;action=rename&amp;file=$foldername&amp;instance_doc_dir=$instance_doc_dir\"><img src=\"".WP_WEB_DIRECTORY."images/rename.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\" title=\"".$lang['rename']."\" alt=\"".$lang['rename']."\"></a> ");} else { echo "&nbsp;"; } echo "</p>
                </td>
                <td>
                        <p>";  if ($delete_directories) { echo "<a class=\"delete\" href=\"javascript:doConfirm('".WP_WEB_DIRECTORY."document.php?in_wp=$in_wp&amp;lang=$lang_include&amp;folder=$folderpath&amp;action=delete&amp;file=$foldername&amp;instance_doc_dir=$instance_doc_dir','".$lang['folder_delete_warning']." ');\"><img src=\"".WP_WEB_DIRECTORY."images/delete.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\" title=\"".$lang['delete']."\" alt=\"".$lang['delete']."\"></a>";} else { echo "&nbsp;"; } echo "</p>
                </td>
        </tr>
        ";
        }
}
closedir($bhandle);
// now build a list of files inside the current folder
$handle=opendir($directory);
while (false!==($file = readdir($handle))) {
        if ((is_file($directory.$file)) && ($file != "." && $file != "..") && (!strstr($file, '.TEMP'))) {
                $filename = $file;
                $fsize = wp_filesize($directory.$filename);
                $extension = strrchr(strtolower($filename),'.');

                $file_info = wp_get_fileinfo($extension);
                $icon = $file_info['icon'];
                $filetype = $file_info['description'];
                $preview = $file_info['preview'];

                $count += 1;
                echo "
        <tr onmouseover=\"this.style.backgroundColor='#eeeeee'\" onmouseout=\"this.style.backgroundColor='#ffffff'\">
                <td width=\"190\">
                        <p style=\"width:180px;height:22px;overflow:hidden\"><a id=\"".$web_directory.$folderpath.$filename."\" class=\"filelink\" href=\"javascript:localLink('".$web_directory.$folderpath."$filename', $preview, '$fsize', '$filetype')\" onclick=\"highlight(this)\" title=\"".$lang['size']." $fsize\"><img src=\"".WP_WEB_DIRECTORY."images/$icon.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">$filename </a></p>
                </td>
                <td width=\"100\">
                <p>$filetype</p>
                </td>
                <td>
                        <p>"; if ($rename_files) { echo ("<a href=\"".WP_WEB_DIRECTORY."document.php?in_wp=$in_wp&amp;lang=$lang_include&amp;folder=$folderpath&amp;action=rename&amp;file=$filename&amp;instance_doc_dir=$instance_doc_dir\"><img src=\"".WP_WEB_DIRECTORY."images/rename.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\" title=\"".$lang['rename']."\" alt=\"".$lang['rename']."\"></a> ");} else { echo "&nbsp;"; } echo "</p>
                </td>
                <td>
                        <p>"; if ($delete_files) { echo "<a class=\"delete\" href=\"javascript:doConfirm('".WP_WEB_DIRECTORY."document.php?in_wp=$in_wp&amp;lang=$lang_include&amp;folder=$folderpath&amp;action=delete&amp;file=$filename&amp;instance_doc_dir=$instance_doc_dir','".$lang['file_delete_warning']." ');\"><img src=\"".WP_WEB_DIRECTORY."images/delete.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\" title=\"".$lang['delete']."\" alt=\"".$lang['delete']."\"></a>";} else { echo "&nbsp;"; } echo "</p>
                </td>
        </tr>
        ";
        }
}
if ($count ==0) {
 echo '<tr><td>'.$lang['no_files'].'</td></tr>';
}
closedir($handle);
?>
                                        </table>
                                </div>
                                </fieldset></td>
                        <td rowspan="3">&nbsp;</td>
                        <td colspan="3" valign="top">
                                <?php if ($upload_files) {?>
                                <fieldset>
                                <legend><?php echo $lang['upload_a_document']; ?></legend>
                                <form style="display:inline" enctype="multipart/form-data" action="<?php echo WP_WEB_DIRECTORY; ?>document.php?in_wp=<?php echo $in_wp; ?>&amp;lang=<?php echo $lang_include; ?>" method="post" onSubmit="showUploadMessage()">
                                        <input name="document_field" id="document_field" type="file" size="30" title="<?php echo $lang['click_browse']; ?>">
                                        <input type="submit" class="button" value="<?php echo $lang['upload_file']; ?>" onClick="return upload_check()">
                                        <input type="hidden" name="instance_doc_dir" id="instance_doc_dir" value="<?php echo $instance_doc_dir; ?>">
                                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang_include; ?>">
                                        <input type="hidden" name="folder" id="folder" value="<?php echo $folderpath ?>">
                                </form>
                                <p><?php echo wp_var_replace($lang['documents_must_be'],array('max_size'=>$max_documentfile_size/1000)); ?></p>
                                </fieldset>
                                <?php } else { echo '&nbsp;'; }?>
                        </td>
                </tr>
                <tr>
                        <td colspan="3" align="right" valign="top"> <div align="center">
                                        <iframe id="preview" width="99%" height="170" frameborder="0" class="previewWindow" src="<?php echo WP_WEB_DIRECTORY; ?>blank.php?lang=<?php echo $lang_include; ?>"></iframe>
                                </div></td>
                </tr>
                <tr>
                        <td colspan="3" valign="top"> <form name="document_form" id="document_form" style="display:inline" onsubmit="return insert_link()">
                                        <fieldset>
                                        <legend><?php echo $lang['document_information']; ?></legend>

          <table height="73" border="0" cellspacing="3" cellpadding="1">
            <tr>
                                                        <td><?php echo $lang['address']; ?></td>
                                                        <td width="100%"><input type="text" name="oHerf" id="oHerf" value="" style="width:200px" title="<?php echo $lang['type_document_address']; ?>"><button class="chooseImage" type="button" onclick="preview(document.getElementById('oHerf').value)" title="<?php echo $lang['preview']; ?>"><img src="<?php echo WP_WEB_DIRECTORY; ?>images/view.gif" width="16" height="16" align="absmiddle" title="<?php echo $lang['preview']; ?>" alt="<?php echo $lang['preview']; ?>"></button>
                                                        </td>
                                                </tr>
                                                <tr>

              <td height="21" colspan="2"><span id="type"> </span> &nbsp; <span id="size">
                </span> </td>
                                                </tr>
                                        </table>
                                        </fieldset>
                                        <?php
                                        if ($in_wp) {
                                        ?>
                                        <div align="center">
                                                <p>&nbsp;</p>
                                                <button type="submit"><?php echo $lang['ok']; ?></button>
                                                &nbsp;
                                                <button type="button" onClick="top.window.close();"><?php echo $lang['cancel']; ?></button>
                                        </div>
                                        <?php } ?>
                                </form></td>
                </tr>
        </table>
</div>
</body>
</html>