<?php

//////////////////////////////////////////////
// function delete file and clear directory //
//////////////////////////////////////////////

// function for deleting files

// $file should be the file path to the document on the server eg: c://sites/mysite/myfile

// it is always safer to use the full file path to the file!!!!

// WARNING if $file is a directory this function will delete the directory and all contents including sub-directories!!!!!

// If $file is open in another program even Windows Explorer the delete routine might fail.
function wp_delete_file($file) {
	@chmod($file,CHMOD_MODE);
	if ((file_exists ($file)) && (!is_file($file))) { 
		if (wp_clr_dir($file)) {
			return true;
		} else {
			return false;
		}
	} elseif ((file_exists ($file)) && (is_file($file))) {
		if (@unlink($file)) {
			return true;
		} else {
			return false;
		}
	}
}
// Enables deletion of directory if not empty
function wp_clr_dir($dir) {
	if(@ ! $opendir = opendir($dir)) {
		return false;
	}
	while(false !== ($readdir = readdir($opendir))) {
		if($readdir !== '..' && $readdir !== '.') {
			$readdir = trim($readdir);
			if(is_file($dir.'/'.$readdir)) {
				if(@ ! unlink($dir.'/'.$readdir)) {
					return false;
				}
			} elseif(is_dir($dir.'/'.$readdir)) {
				// Calls itself to clear subdirectories
				if(! wp_clr_dir($dir.'/'.$readdir)) {
					return false;
				}
			}
		}
	}
	closedir($opendir);
	if(@ ! rmdir($dir)) {
		return false;
	}
	return true;
}

/****************************************************************************/
///////////////////////////////
// function create directory //
///////////////////////////////

// function for creating a directory

// $dir should be the file path for the new directory, including its name! eg: c://sites/mysite/myNewDirectory
function wp_create_dir($dir) {
	if (!file_exists ($dir)) {
		if (@mkdir ($dir)) {
            @chmod($dir, 0777);
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/****************************************************************************/
///////////////////////////////
// moving and copying files //
///////////////////////////////

// This feature has not been implemented!

// function for moving or copying a file/directory.

function wp_copy($oldname, $newname) {
	if (is_file($oldname)){
		$perms = fileperms($oldname);
		return copy($oldname, $newname) && chmod($newname, $perms);
	} else if (is_dir($oldname)){
		my_dir_copy($oldname, $newname);
	} else {
		die("Cannot copy file: $oldname (it's neither a file nor a directory)");
	} 
}

function wp_dir_copy($oldname, $newname) {
	if (!is_dir($newname)) {
		mkdir($newname);
		chmod("$newname", CHMOD_MODE);
	}
	$dir = opendir($oldname);
	while($file = readdir($dir)){
		if ($file == "." || $file == "..") {
			continue;
		}
		my_copy("$oldname/$file", "$newname/$file");
	}
	closedir($dir);
}

/****************************************************************************/
///////////////////////////
// function my filesize //
//////////////////////////

function wp_filesize($file) {
   // First check if the file exists.
  if(!is_file($file)) return '';
   // Setup some common file size measurements.
  $kb = 1024;         // Kilobyte
   $mb = 1024 * $kb;   // Megabyte
  $gb = 1024 * $mb;   // Gigabyte
   $tb = 1024 * $gb;   // Terabyte
   // Get the file size in bytes.
   $size = filesize($file);
   /* If it's less than a kb we just return the size, otherwise we keep going until
   the size is in the appropriate measurement range. */
   if($size < $kb) {
       return $size." B";
   }
   else if($size < $mb) {
      return round($size/$kb,2)." KB";
   }
   else if($size < $gb) {
       return round($size/$mb,2)." MB";
  }
   else if($size < $tb) {
       return round($size/$gb,2)." GB";
   }
   else {
      return round($size/$tb,2)." TB";
   }
}

function wp_convert_fsize($size) {
	   // Setup some common file size measurements.
  $kb = 1024;         // Kilobyte
   $mb = 1024 * $kb;   // Megabyte
  $gb = 1024 * $mb;   // Gigabyte
   $tb = 1024 * $gb;   // Terabyte
   /* If it's less than a kb we just return the size, otherwise we keep going until
   the size is in the appropriate measurement range. */
   if($size < $kb) {
       return $size." B";
   }
   else if($size < $mb) {
      return round($size/$kb,2)." KB";
   }
   else if($size < $gb) {
       return round($size/$mb,2)." MB";
  }
   else if($size < $tb) {
       return round($size/$gb,2)." GB";
   }
   else {
      return round($size/$tb,2)." TB";
   }

}

/****************************************************************************/
///////////////////////////////
// file_name_ok //
///////////////////////////////

// returns true if file name is OK
function wp_file_name_ok($name = '') {
	$values = array('/','\\','?','&','%','#','~',':',' ');
	$num = sizeof($values);
	$match = false;
	for ($i=0; $i<$num; $i++) { 
		if (stristr($name,$values[$i])) {
			$match = true;
			break;
		}
	}
	if ($match) {
		return false;
	} else {
		return true;
	}
}

// returns true if dir name is OK
function wp_dir_name_ok($name = '') {
	$values = array('./','.\\','?','&','%','#','~');
	$num = sizeof($values);
	$match = false;
	for ($i=0; $i<$num; $i++) { 
		if (stristr($name,$values[$i])) {
			$match = true;
			break;
		}
	}
	if ($match) {
		return false;
	} else {
		return true;
	}
}


/****************************************************************************/
///////////////////////////////
// extension_ok //
///////////////////////////////

function wp_extension_ok($extension, $accepted_types) {
	$accept_array = explode(',', str_replace(' ', '', strtolower($accepted_types)));
	if (in_array($extension, $accept_array)) {
		return true;
	} else {
		return false;
	}
}

/****************************************************************************/
///////////////////////////////
// get_fileinfo //
///////////////////////////////

function wp_get_fileinfo($extension) {
	global $lang;
	// to add more filetypes save an icon image to the images folder and add a description to your language file, then describe how the function should handle the file below:
	switch($extension) {
		case '.html':
		$info['icon'] = 'htm_icon';
		$info['description'] = $lang['files']['html'];
		$info['preview'] = 1;
		break;
	case '.htm':
		$info['icon'] = 'htm_icon';
		$info['description'] = $lang['files']['html'];
		$info['preview'] = 1;
		break;
	case '.pdf':
		$info['icon'] = 'pdf_icon';
		$info['description'] = $lang['files']['pdf'];
		$info['preview'] = 0;
		break;
	case '.rtf':
		$info['icon'] = 'rtf_icon';
		$info['description'] = $lang['files']['rtf'];
		$info['preview'] = 0;
		break;
	case '.txt':
		$info['icon'] = 'txt_icon';
		$info['description'] = $lang['files']['txt'];
		$info['preview'] = 1;
		break;
	// office files
	case '.doc':
		$info['icon'] = 'doc_icon';
		$info['description'] = $lang['files']['doc'];
		$info['preview'] = 0;
		break;
	case '.xl':
		$info['icon'] = 'xl_icon';
		$info['description'] = $lang['files']['xl'];
		$info['preview'] = 0;
		break;
	case '.xls':
		$info['icon'] = 'xl_icon';
		$info['description'] = $lang['files']['xl'];
		$info['preview'] = 0;
		break;
	case '.ppt':
		$info['icon'] = 'ppt_icon';
		$info['description'] = $lang['files']['ppt'];
		$info['preview'] = 0;
		break;
	case '.pps':
		$info['icon'] = 'pps_icon';
		$info['description'] = $lang['files']['pps'];
		$info['preview'] = 0;
		break;
	// compression file types
	case '.zip':
		$info['icon'] = 'zip_icon';
		$info['description'] = $lang['files']['zip'];
		$info['preview'] = 0;
		break;
	case '.tar':
		$info['icon'] = 'zip_icon';
		$info['description'] = $lang['files']['tar'];
		$info['preview'] = 0;
		break;
	// media files
	case '.swf':
		$info['icon'] = 'swf_icon';
		$info['description'] = $lang['files']['swf'];
		$info['preview'] = 0;
		break;
	case '.wmv':
		$info['icon'] = 'wmv_icon';
		$info['description'] = $lang['files']['wmv'];
		$info['preview'] = 0;
		break;
	case '.rm':
		$info['icon'] = 'rm_icon';
		$info['description'] = $lang['files']['rm'];
		$info['preview'] = 0;
		break;
	case '.mov':
		$info['icon'] = 'mov_icon';
		$info['description'] = $lang['files']['mov'];
		$info['preview'] = 0;
		break;
	// image file types
	case '.jpg':
		$info['icon'] = 'jpg_icon';
		$info['description'] = $lang['files']['jpg'];
		$info['preview'] = 1;
		break;
	case '.jpeg':
		$info['icon'] = 'jpg_icon';
		$info['description'] = $lang['files']['jpg'];
		$info['preview'] = 1;
		break;
	case '.gif':
		$info['icon'] = 'gif_icon';
		$info['description'] = $lang['files']['gif'];
		$info['preview'] = 1;
		break;
	case '.png':
		$info['icon'] = 'rtf_icon';
		$info['description'] = $lang['files']['rtf'];
		$info['preview'] = 1;
		break;
	// executable file types
	case '.exe':
		$info['icon'] = 'exe_icon';
		$info['description'] = $lang['files']['exe'];
		$info['preview'] = 0;
		break;
	// default;	
	default: 
		$info['icon'] = 'unknown_icon';
		$info['description'] = strtoupper(str_replace('.', '', $extension)).' '.$lang['files']['file'];
		$info['preview'] = 0;
		break;	
	}
	return $info;
}
/****************************************************************************/
/////////////////
// var_replace //
/////////////////

function wp_var_replace($code='', $array) {
	$search = array();
	$replace = array();
	foreach($array as $k => $v) {
		array_push($search, '##'.$k.'##');
		array_push($replace, $v);
	}
	return str_replace($search, $replace, $code);
}


/****************************************************************************/
////////////////////////
// function longwordbreak //
////////////////////////
// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
// this breaks up words that are too long and might damage the page layout such as excessive use of tabs
// it does not cut through html tags
// call it before saving your code like this: $myCode = longwordbreak($myCode);

// $str = required, your html code
// $cols = optional, words over this length will be cut (the default is 40, how many real words can you think of over this length?)
// $cut = optional, how would you like your excessively long words cut sir? (the default is a space, other options would be a hyphen or carriage return)

function longwordbreak($str, $cols=70, $cut=' ') {
   $len = strlen($str);
   $tag = 0;
   for ($i = 0; $i < $len; $i++) {
       $chr = $str[$i];
       if ($chr == '<') {
          $tag++;
       } elseif ($chr == '>') {
          $tag--;
       } elseif ((!$tag) && (wp_is_whitespace($chr))) {
          $wordlen = 0;
       } elseif (!$tag) {
          $wordlen++;
       }
       if ((!$tag) && ($wordlen) && (!($wordlen % $cols))) {
           $chr .= $cut;
      }
       $result .= $chr;
   }
   return $result;
}
function wp_is_whitespace($chr) {
	if ($chr == " ") return true;
	if ($chr == "\r") return true;
	if ($chr == "\n") return true;
	if ($chr == "\t") return true;
	return false;
}

/****************************************************************************/
//////////////////////////
// function remove_tags //
//////////////////////////
// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
// allows you to remove unwanted tags from the code

// $code the html code to be processed
// $tags an array of tags to remove

function remove_tags($code, $tags) {
	if (!empty($code)) {
		if (!is_array($tags)) {
			die('<p><b>WYSIWYGPRO Paramater Error:</b> Your list of tags is not an array!</p>');
		} else {
			foreach($tags as $k => $v) {
				if (!empty($k)) {
					if ($v) {
						// remove tags and all code contained within the tags
						$code = preg_replace("/<".quotemeta($k)."[^>]*?>.*?<\/".quotemeta($k).">/smi",  "", $code);
						$code = preg_replace("/<".quotemeta($k)."[^>]*?>/smi",  "", $code);
					} else {
						// remove tags but leave code within the tags
						$code = preg_replace("/<".quotemeta($k)."[^>]*?>(.*?)<\/".quotemeta($k).">/smi",  "\$1", $code);
						$code = preg_replace("/<".quotemeta($k)."[^>]*?>/smi",  "", $code);
					}
				}
			}
		}
	}
	return $code;
}
/****************************************************************************/
//////////////////////////
// function comm2php //
//////////////////////////
// Converts comments back to PHP

function comm2php($code) {
	if (!empty($code)) {
		$code = preg_replace("/<\!--p(.*?)-->/smi",  "<?php\$1 ?>", $code);
		$code = preg_replace("/<\?xml version=\"1.0\" encoding=\"(.*?)\"\?>/smi",  '<?php echo "<?xml version=\"1.0\" encoding=\"$1\"?".">"; ?>', $code);
	}
	return $code;
}
?>