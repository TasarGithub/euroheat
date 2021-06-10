<?php
/*CLASS WYSIWYGPRO

Purpose: configure and then display WYSIWYGPRO DHTML output

basically this file grabs one of 2 possible editors depending on the browser type and version. it then removes features from that file that are not requested or that have been requested for removal and then either returns or prints the code.

Requirements:
Requires config.php

*/

class wysiwygPro {

	// Begin

	// declare class variables

	var $classname = "wysiwygPro";

	// variables that are set by the user
	var $name='htmlCode';
	var $code='<p>&nbsp;</p>';
	var $baseURL='';
	var $stylesheet='';
	var $remove_array='';
	var $dontremoveserver = false;
	var $dsbleimgmngr = false;
	var $showbookmarkmngr = 'true';
	var $hyperlink_function = 'open_hyperlink_window(##name##,this)';
	var $hyperlink_function2 = 'open_hyperlink_window(parent.wp_current_obj,this)';
	var $font_menu =
		'<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\'Arial\')" title="Arial" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:\'Arial\'"><nobr>Arial</nobr></div>
		<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\'Times New Roman\')" title="Times New Roman" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:\'Times New Roman\'"><nobr>Times New Roman</nobr></div>
		<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\'Courier\')" title="Courier" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:\'Courier\'"><nobr>Courier</nobr></div>
		<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\'Georgia\')" title="Georgia" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:\'Georgia\'"><nobr>Georgia</nobr></div>
		<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\'Verdana\')" title="Verdana" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:\'Verdana\'"><nobr>Verdana</nobr></div>
		<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\'Geneva\')" title="Geneva" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:\'Geneva\'"><nobr>Geneva</nobr></div>';
	var $format_menu = '
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;div&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<div>##normal##</div>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;h1&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<h1>##heading_1##</h1>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;h2&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<h2>##heading_2##</h2>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;h3&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<h3>##heading_3##</h3>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;h4&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<h4>##heading_4##</h4>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;h5&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<h5>##heading_5##</h5>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;h6&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<h6>##heading_6##</h6>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;pre&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<pre>##pre_formatted##</pre>
			</nobr></div>
		<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;address&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<address>
			Address
			</address>
			<nobr></div>
	';
	var $size_menu = '
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'1\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="1">##1##</font></div>
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'2\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="2">##2##</font></div>
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'3\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="3">##3##</font></div>
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'4\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="4">##4##</font></div>
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'5\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="5">##5##</font></div>
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'6\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="6">##6##</font></div>
		<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\'7\')" onMouseOver="on(this)" onMouseOut="off(this)"><font size="7">##7##</font></div>
	';
	var $save_button = '';
	var $custom_objects = '';
	var $links = '';
	var $usexhtml = 'false';
	var $xhtml_lang = "en";
	var $encoding = "UTF-8";
	var $usep = 'false';
	var $subsequent = 'false';
	var $color_swatches = '';
	var $instance_img_dir = '';
	var $instance_doc_dir = '';
	// internal variables

	// browsers
	var $is_ie55 = false;
	var $is_ie50 = false;
	var $is_gecko = false;
	var $unsupported = false;

	// other stuff
	var $baseURL2 = '';
	var $domain = '';
	var $set_style = '';
	var $image_window = 'image.php';
	var $class_menu = '';
	var $save_name = '';
	var $has_expired = true;
	var $WYSIWYGPRO_code = '';
	var $instance_lang = DEFAULT_LANG;

	/***************************************************************************
	 wysiwygPro
	 Public: constructor
	 $save_name: string to save this configuration as. If WYSIWYGPRO is requested again with the same save_name the file will be generated from a cache file rather than generated from scratch.
	 If no paramater is set WYSIWYG PRO will perform no configuration saving.
	*/

	function wysiwygPro($save_name='') {

		if (!defined('WP_CONFIG')) {
			die('<p><b>WYSIWYGPRO - Setup Error:</b> You did not include \'config.php\' before declaring this class!</p>');
		}

		////////////////////
		// no-cache headers
		// send headers to prevent caching by proxy servers. This is extremely important because all browser detection is done by this script, and this script outputs different code depending on browser and platform.
		// Note that the script containing this class must either not output anything before calling this class or use ob_start (); and ob_end_flush();
		if (NOCACHE) {
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // expires in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
		}

		///////////////////////
		// browser detection
		///////////////////////

		$browser_string = strtolower($_SERVER["HTTP_USER_AGENT"]);

		$is_mac = strstr($browser_string, 'mac');

		$is_opera = strstr($browser_string, 'opera');

		$is_konq = strstr($browser_string, 'khtml');

		$ie_version = preg_replace('/.*msie/sm', '', $browser_string);

		$ie_version = substr ($ie_version, 0,4);

		$gecko_version = preg_replace('/.*rv:/sm', '', $browser_string);

		$gecko_version = substr ($gecko_version, 0,3);

		if ((($ie_version*10) >= 55) && !$is_opera && !$is_mac) {
			$this->is_ie55 = true;
			$add_to_cache_name = '_ie55';
		} elseif ((($ie_version*10) >= 50) && !$is_opera && !$is_mac) {
			$this->is_ie50 = true;
			$add_to_cache_name = '_ie50';
		} elseif (strstr($browser_string, 'gecko') && !$is_opera && !$is_konq && $gecko_version >= '1.3') {
			$this->is_gecko = true;
			$add_to_cache_name = '_gecko';
		} else {
			$this->unsupported = true;
			$add_to_cache_name = '_unsupported';
		}

		// Now that we have done browser detection it is safe to check for caching and do the caching
		if (!empty($save_name)) {
			$save_name = $save_name.$add_to_cache_name;
			// make sure the save name is safe to use as a filename (incase the user wants to use the current URL as the cache name)
			$save_name = preg_replace ("/[\/\\\?&%#~:]/", '', $save_name);
			$this->save_name = $save_name;
			// check if this file has expired
			$this->has_expired = $this->expired($this->save_name);
		} else {
			$this->has_expired = true;
		}

		if ($this->has_expired) {
			// If we reach here then the cache has expired or no save name has been set for this configuration

			/////////////////
			// check globals
			/////////////////

			// check we have been sent a valid directory and that the template files for the Internet Explorer editor and the alternative browser editor exist.
			if ((!file_exists (WP_FILE_DIRECTORY.'templates/IE5editor.tpl')) || (!file_exists (WP_FILE_DIRECTORY.'templates/MozEditor.tpl'))) {
				die("<p><b>WysiwygPro - Setup Error:</b> WysiwygPro cannot find its file library. Your WP_FILE_DIRECTORY setting is incorrect, or some files are missing. This paramater should be set to the full file path to the 'editor_files' folder. This value should be set in config.php.</p>");
			}

			if (!file_exists (WP_FILE_DIRECTORY.'lang/'.DEFAULT_LANG)) {
				die("<p><b>WysiwygPro - Setup Error:</b> your default language file does not exist.");
			}

			// check document directory. If NULL disable downloadable document manager
			if (DOCUMENT_FILE_DIRECTORY == NULL || DOCUMENT_WEB_DIRECTORY == NULL) {
				$this->remove('document');
			}

			// check image directory. If NULL disable image manager
			if (IMAGE_FILE_DIRECTORY == NULL || IMAGE_WEB_DIRECTORY == NULL) {
				$this->image_window = 'imageoptions.php';
			}

			// add features not supported by ie50 to the remove array
			if ($this->is_ie50 == true) {
				$this->remove('ie50_remove');
			}

			// read the contents of the editor code from the file into a variable.
			// choose a file based on the user agent
			if (($this->is_ie55) || ($this->is_ie50)) {
				$code_file = 'templates/IE5editor.tpl';
			} elseif ($this->is_gecko) {
				$code_file = 'templates/MozEditor.tpl';
			} else {
				$code_file = 'templates/textEditor.tpl';
			}
			// open file into a variable that we can work with
			$this->WYSIWYGPRO_code = implode("", @file(WP_FILE_DIRECTORY.$code_file));

		}
	}


	// Internal functions:

	/***************************************************************************
	 expired
	 Method: returns true if the cache has expired.

	*/

	function expired ($file) {
		if (! @file_exists (SAVE_DIRECTORY . $file)) {
			return true;
		}
		if ((filemtime (SAVE_DIRECTORY . $file)) < (time () - SAVE_LENGTH)) {
			return true;
		}
		return false;
	}


	/***************************************************************************
	 remove
	 Method: Adds items to the remove array. (note the so called 'remove array' is not yet an actual array!)
	 items: a comma separated list of items to add

	*/
	function remove($items) {
		if ($this->has_expired) {
			if (isset ($items) ? $items : '') {
				if (!empty($this->remove_array)) {
					$this->remove_array .= (','.$items);
				} else {
					$this->remove_array=$items;
				}
			}
		}
	}

	/***************************************************************************
	 wp_replace
	 Method: pastes variables into the editor
	 array: search and replace array

	*/
	function wp_replace($code, $array) {
		$search = array();
		$replace = array();
		foreach($array as $k => $v) {
			array_push($search, '##'.$k.'##');
			array_push($replace, $v);
		}
		return str_replace($search, $replace, $code);
	}



	// Public functions that set paramaters:

	/***************************************************************************
	 set_name
	 Public: sets the name of the code
	 $name: the name of the textarea that will hold the edited HTML code

	*/

	function set_name($name='htmlCode') {
		if ($this->has_expired) {
			if (!empty($name)) {
				$this->name = $name;
			}
		}
	}


	/***************************************************************************
	 set_code
	 Public: sets the code that should be loaded into the editor at startup
	 $code: the HTML code to load

	*/

	function set_code($code='<div>&nbsp;</div>', $convert_todiv = false) {
		// we will not check the cache because code is not cached so that you can still insert different code into a saved configuration.
		if (!empty($code)) {
			// convert <p> to <DIV> ready for use in the editor (only needed if page was not created in the editor)
			// this is depreciated and no-longer documented because the editor can now handle this in the browser
			if ($convert_todiv) {
					$code = preg_replace("/<p([^>]*?)>(.*?)<\/p>/smi",  "<div\$1>\$2</div>\n<div>&nbsp;</div>", $code);
			}
			// standardise carriage returns, strip slashes incase sent from a form post or database.
			$code = str_replace(array("\r\n", "\r"), array("\n", "\n"), stripslashes($code));

			// strip out the XML declaration because this can confuse Internet Explorer
			$code = preg_replace('/<\?php echo "<\?xml version=\"1\.0\" encoding=\"(.*?)\"\?"\.">"; \?>/smi',  "", $code);
			$code = preg_replace("/<\?xml version=\"1.0\" encoding=\"(.*?)\"\?>/smi",  "", $code);

			// convert anchors:
			$code = preg_replace("/<a name=\"(.*?)\".*?>(.*?)<\/a>/smi", "<img name=\"\$1\" src=\"".WP_WEB_DIRECTORY."/images/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\">\$2", $code);
			$code = preg_replace("/<a(.*?)href=\"#(.*?)\"(.*?)>/smi", "<a\$1href=\"WP_BOOKMARK#\$2\"\$3>", $code);

			if (is_gecko) {
				// Mozilla will completely ignore PHP tags so we need to convert them into comments here instead
				$code = preg_replace("/<\?php(.*?)\?>/smi",  "<!--p\$1-->", $code);
				// hack to stop bug in Mozilla where row modifications cause an endless loop if table html is not all on one line (Note for future development: is this a bug in Mozilla or a flaw in the table editing script?)
				// table
				$code = preg_replace("/<table([^>]*?)>\s+/smi",  "<table\$1>", $code);
				// tbody
				$code = preg_replace("/<tbody([^>]*?)>\s+/smi",  "<tbody\$1>", $code);
				// close tbody
				$code = preg_replace("/<\/tbody>\s+/smi",  "</tbody>", $code);
				// thead
				$code = preg_replace("/<thead([^>]*?)>\s+/smi",  "<thead\$1>", $code);
				// close thead
				$code = preg_replace("/<\/thead>\s+/smi",  "</thead>", $code);
				// tfoot
				$code = preg_replace("/<tfoot([^>]*?)>\s+/smi",  "<tfoot\$1>", $code);
				// close tfoot
				$code = preg_replace("/<\/tfoot>\s+/smi",  "</tfoot>", $code);
				// tr
				$code = preg_replace("/<tr([^>]*?)>\s+/smi",  "<tr\$1>", $code);
				// close tr
				$code = preg_replace("/<\/tr>\s+/smi",  "</tr>", $code);
				// close td
				$code = preg_replace("/<\/td>\s+/smi",  "</td>", $code);

			} else {

				$code = preg_replace("/<font>(.*?)</font>/smi",  "\$1", $code);

			}

			// convert to htmlentities to make it safe to paste in a textarea
			$this->code = htmlspecialchars($code);
		}
	}


	/***************************************************************************
	 set_lang
	 Public: sets the attribute value for lang tags
	 $lang: the lang attribute value eg "en"

	*/

	function set_xhtml_lang($lang="en") {
		if ($this->has_expired) {
			if (isset ($lang) ? $lang : '') {
				$this->xhtml_lang = $lang;
			}
		}
	}

	/***************************************************************************
	 set_encoding
	 Public:
	 $encoding:  eg "UTF-8"

	*/

	function set_encoding($encoding="UTF-8") {
		if ($this->has_expired) {
			if (isset ($encoding) ? $encoding : '') {
				$this->encoding = $encoding;
			}
		}
	}



	/***************************************************************************
	 usexhtml
	 Public: tells the editor to generate well formed xhtml
	 $usexhtml: true or false

	*/

	function usexhtml($usexhtml=true, $encoding="UTF-8", $lang="en") {
		if ($this->has_expired) {
			if ($usexhtml) {
				$this->usexhtml = 'true';
				$this->set_encoding($encoding);
				$this->set_xhtml_lang($lang);
			}
		}
	}

	/***************************************************************************
	 usep
	 Public: tells the editor to use <p> on return instead of <div> (the default)
	 $usep: true or false

	*/

	function usep($usep=true) {
		if ($this->has_expired) {
			if ($usep) {
				$this->usep = 'true';
			}
		}
	}



	/***************************************************************************
	 set_baseURL
	 Public: sets the baseURL
	 $baseURL: the base url of the document being edited. Set this to avoid broken links and images.

	*/

	function set_baseurl($baseURL='') {
		if ($this->has_expired) {
			if (!empty($baseURL)) {
				$this->baseURL = $baseURL;
				$this->baseURL2 = "<base href=\"$baseURL\">";
				$this->domain = str_replace('/','\/',quotemeta($baseURL));
			}
		}
	}


	/***************************************************************************
	 set_stylesheet
	 Public: sets the stylesheet to use with the wysiwyg view. Set only if editing a snippit of code.
	 $stylesheet: the path to the stylesheet to be applied to the WYSIWYG view required if you are only editing a snippit of html. Should look like: '/myfolder/mystyles.css'

	*/

	function set_stylesheet($stylesheet='') {
		if ($this->has_expired) {
		// even if you have set a baseurl, your stylesheet must reside on the server and be addressed from the server's document root. eg: '/myfolder/mystylesheet.css'
			if (!empty($stylesheet)) {
				$this->set_style = $stylesheet;
			}
		}
	}


	/***************************************************************************
	 set_instance_lang
	 Public: sets the language file for this instance
	 $lang_file: the lang file to use eg "ger";

	*/

	function set_instance_lang($lang_file="en-us.php") {
		if ($this->has_expired) {
			if (!empty($lang_file)) {
				if (!file_exists (WP_FILE_DIRECTORY.'lang/'.$lang_file)) {
					die("<p><b>WysiwygPro - Paramater Error:</b> set_instance_lang: the language file you specified for this instance does not exist.");
				}
				$this->instance_lang = $lang_file;
			}
		}
	}

	/***************************************************************************
	 subsequent
	 Public: sets the attribute value for lang tags
	 $lang: the lang attribute value eg "en"

	*/

	function subsequent($subsequent=true) {
		if ($this->has_expired) {
			if ($subsequent) {
				$this->remove('1st instance only');
				$this->subsequent = 'true';
			}
		}
	}


	/***************************************************************************
	 removebuttons
	 Public: sets the buttons and tabs that shouldn't be displayed.
	 $removearray: a comma separated list of buttons that SHOULDN'T be displayed.

	*/

	function removebuttons($removearray='') {
		if ($this->has_expired) {
			if (!empty($removearray)) {
				$this->remove($removearray);
			}
		}
	}

	/***************************************************************************
	 addbutton
	 Public: adds a button to the toolbar

	*/

	function addbutton($title, $location, $function, $url, $width=NULL, $height=NULL, $cid='forecolor') {
		if ($this->has_expired) {
			if ($this->is_gecko) {
				$button = "<td><img class=\"toolButton\" onClick=\"$function\" src=\"$url\"";
			} else {
				$button = "<td><img cid=\"$cid\" class=\"ready\" onclick=\"$function\" src=\"$url\"";
			}
			if (is_int($width)) {
				$button .= ' width="'.$width.'"';
			}
			if (is_int($height)) {
				$button .= ' height="'.$height.'"';
			}
			$button .= " alt=\"$title\" title=\"$title\" type=\"btn\" onMouseOver=\"wp_m_over(this, ##name##);\" onMouseOut=\"wp_m_out(this, ##name##);\" onMouseDown=\"wp_m_down(this, ##name##);\" onMouseUp=\"wp_m_up(this, ##name##);\"></td>";

			if (substr($location, 0, 6) == 'after:') {
				$handle = substr($location, 6);
				$this->WYSIWYGPRO_code = preg_replace("/(<\/td>\s+<!-- end $handle -->)/smi", "\$1\n<!-- begin $title -->\n$button\n<!-- end $title -->\n", $this->WYSIWYGPRO_code);
			} else if (substr($location, 0, 7) == 'before:') {
				$handle = substr($location, 7);
				$this->WYSIWYGPRO_code = preg_replace("/(<!-- begin $handle -->\s+<td>)/smi", "\n<!-- begin $title -->\n$button\n<!-- end $title -->\n\$1", $this->WYSIWYGPRO_code);
			} else {
				die("<p><b>WysiwygPro Paramater Error:</b> You supplied an incorrect argument to addbuttons</p>");
			}
		}
	}


	/***************************************************************************
	 addspacer
	 Public: add a spacer to the toolbar

	*/

	function addspacer($title, $location) {
		if ($this->has_expired) {
			$button = '<td><img class="spacer" width="1" height="22" src="'.WP_WEB_DIRECTORY.'images/spacer.gif" alt=""></td>';
			if (!empty($title)) {
				$before = "\n<!-- begin $title -->\n";
				$after = "\n<!-- end $title -->\n";
			} else {
				$before = "\n";
				$after = "\n";
			}
			if (substr($location, 0, 6) == 'after:') {
				$handle = substr($location, 6);
				$this->WYSIWYGPRO_code = preg_replace("/(<\/td>\s+<!-- end $handle -->)/smi", "\$1$before$button$after", $this->WYSIWYGPRO_code);
			} else if (substr($location, 0, 7) == 'before:') {
				$handle = substr($location, 7);
				$this->WYSIWYGPRO_code = preg_replace("/(<!-- begin $handle -->\s+<td>)/smi", "$before$button$after\$1", $this->WYSIWYGPRO_code);
			} else {
				die("<p><b>WysiwygPro Paramater Error:</b> You supplied an incorrect argument to addspacer</p>");
			}
		}
	}


	/***************************************************************************
	 usefullurls
	 Public: removes code that cleans urls and links by deleting the server name.
	 $dontremoveserver: true or false. if set to true url cleaning wil be disabled

	*/

	function usefullurls($dontremoveserver=true) {
		if ($this->has_expired) {
			if ($dontremoveserver) {
				$this->remove('removeserver');
				$this->dontremoveserver=true;
			}
		}
	}

	/***************************************************************************
	 set_classmenu
	 Public: builds a custom class menu
	 $classes: array of class names and titles where the key in each row is the className and the value is a description eg. array('className' =>' Title to appear in menu', 'anotherClass' => 'Another title');

	*/

	function set_classmenu($classes) {
		if ($this->has_expired) {
			if (!is_array($classes)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your class list is not an array!</p>');
			} else {
				$class_menu = '';
				foreach($classes as $k => $v) {
					if ((!empty($k)) && (!empty($v))) {
						$class_menu.='<div class="off" onClick="parent.wp_change_class(parent.wp_current_obj,\''.$k.'\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr><span class="'.$k.'" style="position: relative; float: none; clear: both; display: block; visibility: visible">'.$v.' ('.$k.')</span></nobr></div>';
					}
				}
				$this->class_menu = $class_menu;
			}
		}
	}


	/***************************************************************************
	 set_fontmenu
	 Public: builds the font menu
	 $classes: comma separated list of fonts to display.

	*/

	function set_fontmenu($fonts) {
		if ($this->has_expired) {
			if (isset ($fonts) ? $fonts : '') {
				$fonts_array = explode (';', $fonts);
				// loop through the array add the fonts
				$fonts_menu = '';
				$num_fonts = sizeof($fonts_array);
				for ($i=0; $i<$num_fonts; $i++) {
					if (!empty($fonts_array[$i])) {
						$font= trim($fonts_array[$i]);
						$fonts_menu.='<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,\''.$font.'\')" title="'.$font.'" onMouseOver="on(this)" onMouseOut="off(this)" style="font-family:'.$font.'"><nobr>'.$font.'</nobr></div>';
					}
				}
				$this->font_menu = $fonts_menu;
			}
		}
	}


	/***************************************************************************
	 set_formatmenu
	 Public: builds the format menu
	 $classes: An array where the key in each row is the block level format tag and the value is a description of that tag.

	*/

	function set_formatmenu($formats) {
		if ($this->has_expired) {
			if (!is_array($formats)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your format list is not an array!</p>');
			} else {
				$format_menu = '';
				foreach($formats as $k => $v) {
					if ((!empty($k)) && (!empty($v))) {
						$format_menu.= '
			<div class="off" onClick="parent.wp_change_format(parent.wp_current_obj,\'&lt;'.$k.'&gt;\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>
			<'.$k.'>'.$v.'</'.$k.'>
			</nobr></div>';
					}
				}
				$this->format_menu = $format_menu;
			}
		}
	}


	/***************************************************************************
	 set_sizemenu
	 Public: builds the font size menu
	 $classes: An array where the key in each row is the size and the value is a description of that size.

	*/

	function set_sizemenu($sizes) {
		if ($this->has_expired) {
			if (!is_array($sizes)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your size list is not an array!</p>');
			} else {
				$size_menu = '';
				foreach($sizes as $k => $v) {
					if ((!empty($k)) && (!empty($v))) {
						$size_menu.= '
			<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,\''.$k.'\')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr><font size="'.$k.'">'.$v.'</font></nobr></div>';
					}
				}
				$this->size_menu = $size_menu;
			}
		}
	}

	/***************************************************************************
	 set_color_swatches
	 Public: adds a color swatches to the color dialog
	 $colors: comma separated list of colors

	*/

	function set_color_swatches($colors) {
		if ($this->has_expired) {
			if (!empty($colors)) {
				$this->color_swatches = str_replace(' ', '', $colors);
			}
		}
	}



	/***************************************************************************
	 disableimgmngr
	 Public: disables the image manager. Users can still insert images but must enter a URL to the image
	 $dsbleimgmngr: true or false

	*/

	function disableimgmngr($dsbleimgmngr=true) {
		if ($this->has_expired) {
			if ($dsbleimgmngr) {
				$this->dsbleimgmngr = true;
				$this->image_window = 'imageoptions.php';
			}
		}
	}


	/***************************************************************************
	 disablelinkmngr
	 Public: disables the link manager. This is public but not documented because this function will automatically be called if no links have been set.
	 $dsbleimgmngr: true or false

	*/

	function disablelinkmngr($disable=true) {
		if ($this->has_expired) {
			if ($disable) {
				$this->hyperlink_function = "callFormatting(##name##,'CreateLink')";
				$this->hyperlink_function2 = "callFormatting(parent.wp_current_obj,'CreateLink')";
			}
		}
	}

	/***************************************************************************
	 disablebookmarkmngr
	 Public: disables the bookmark manager.
	 $dsbleimgmngr: true or false

	*/

	function disablebookmarkmngr($disable=true) {
		if ($this->has_expired) {
			if ($disable) {
				$this->showbookmarkmngr = 'false';
			}
		}
	}


	/***************************************************************************
	 add_insert
	 Public: adds custom objects.
	 $inserts: an array where the key is the description and the value is the html code to insert

	*/

	function set_inserts($inserts) {
		if ($this->has_expired) {
			if (!is_array($inserts)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your custom inserts are not an array!</p>');
			} else {
				$custom_objects = '';
				foreach($inserts as $k => $v) {
					if (!empty($v)) {
						$description = str_replace(array("'","\r\n","\r","\n"),array("\'",'\n','\n','\n'),$k);
						$code = str_replace(array("'","\r\n","\r","\n"),array("\'",'\n','\n','\n'),$v);
						$custom_objects .= $description.'<br><button type="button" onClick="insert(this);">'.$code.'</button><hr>';
					}
				}
				$this->custom_objects = $custom_objects;
			}
		}
	}

	/***************************************************************************
	 set_links
	 Public: generates a list of selectable links to appear in the hyperlink window.
	 $links: an auto indexed 2d array with three values in each row: indentdepth, url, name, e.g. array( array(0, '/myfolder/page1.htm', 'Page One'),	array(0, 'folder', 'Section 1'),	array(1, '/myfolder/page2.htm', 'Page two') );

	*/

	function set_links($links) {
		if ($this->has_expired) {
			if (!is_array($links)) {
				die('<p>WYSIWYGPRO Paramater Error: Your link list is not an array!</p>');
			} else {
				$num_links = sizeof($links);
				for ($i=0; $i<$num_links; $i++) {
					if ((!empty($links[$i][1])) && (!empty($links[$i][2]))) {
						if ($links[$i][0] >= 1) {
							$depth = ($links[$i][0]-1)*23;
							$indent = "<img src=\"".WP_WEB_DIRECTORY."images/branch.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
						} else {
							$depth = 0;
							$indent = '';
						}
						if ($links[$i][1] == 'folder') {
							$this->links.="<nobr><p class=\"filelink\" style=\"height:22px;margin:2px 2px 2px ".($depth+2)."px\">$indent<img src=\"".WP_WEB_DIRECTORY."images/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">".str_replace(' ', '&nbsp;', $links[$i][2])." </p></nobr>".'\n';
						} else {
							$links[$i][2] = str_replace("'", "\'", $links[$i][2]);
							$this->links.="<p><nobr><a class=\"filelink\" id=\"".$links[$i][1]."\" style=\"height:22px; margin:0px 0px 0px ".$depth."px;\" href=\"javascript:highlight(\'".$links[$i][1]."\');localLink(\'".$links[$i][1]."\');\" title=\"URL: ".$links[$i][1]."\">$indent<img src=\"".WP_WEB_DIRECTORY."images/htm_icon.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">".str_replace(' ', '&nbsp;', $links[$i][2])." </a></nobr></p>".'\n';
						}
					}
				}
			}
		}
	}

	/***************************************************************************
	 set_savebutton
	 Public: adds a custom save button to the toolbar

	*/

	function set_savebutton($name, $url=NULL, $width=NULL, $height=NULL) {
		if ($this->has_expired) {
			if (strtolower($name) == 'save') {
				$name= '##save##';
				if ($url == NULL) {
					$url = WP_WEB_DIRECTORY.'images/save.gif';
				}
				if ($width == NULL) {
					$width = 22;
				}
				if ($height == NULL) {
					$height = 22;
				}
			} else if (strtolower($name) == 'send') {
				$name= '##send##';
				if ($url == NULL) {
					$url = WP_WEB_DIRECTORY.'images/send.gif';
				}
				if ($width == NULL) {
					$width = 50;
				}
				if ($height == NULL) {
					$height = 22;
				}
			} else if (strtolower($name) == 'post') {
				$name= '##post##';
				if ($url == NULL) {
					$url = WP_WEB_DIRECTORY.'images/post.gif';
				}
				if ($width == NULL) {
					$width = 50;
				}
				if ($height == NULL) {
					$height = 22;
				}
			}
			if ($this->is_gecko) {
				$save_button = "\n<!-- begin save -->\n<td><input style=\"cursor:default\" class=\"toolButton\" id=\"##name##_wp_save\" name=\"##name##_wp_save\" type=\"image\" src=\"$url\"";
			} else {
				$save_button = "\n<!-- begin save -->\n<td><input cid=\"ignore\" style=\"cursor:default\" class=\"ready\" id=\"##name##_wp_save\" name=\"##name##_wp_save\" type=\"image\" src=\"$url\"";
			}
			if (is_int($width)) {
				$save_button .= ' width="'.$width.'"';
			}
			if (is_int($height)) {
				$save_button .= ' height="'.$height.'"';
			}
			$this->save_button = $save_button." border=\"0\" title=\"$name\" onMouseOver=\"wp_m_over(this, ##name##);\" onMouseOut=\"wp_m_out(this, ##name##);\" onMouseDown=\"wp_m_down(this, ##name##);\" onMouseUp=\"wp_m_up(this, ##name##);\"></td>\n<!-- end save -->\n";
		}
	}


	/***************************************************************************
	 set_img_dir
	 Public:

	*/

	function set_img_dir($name) {
		if ($this->has_expired) {
			if (isset ($name) ? $name : '') {
				$this->instance_img_dir = $name;
			}
		}
	}


	/***************************************************************************
	 set_doc_dir
	 Public:

	*/

	function set_doc_dir($name) {
		if ($this->has_expired) {
			if (isset ($name) ? $name : '') {
				$this->instance_doc_dir = $name;
			}
		}
	}


	/***************************************************************************
	 Return editor
	 Public: returns all necissary DHTML code for displaying the editor
	 $width: width of WYSIWYGPRO
	 $height: height of WYSIWYGPRO

	*/

	function return_editor($width=680, $height=390) {

		// if cache has expired build the editor from scratch, else simply load it from the cache file

		if ($this->has_expired) {

			// BEGIN

			// check we have been sent valid width
			if (!(isset ($width) ? $width : '')) {
				// default width
				$width = 680;
			}

			// check we have been sent valid height settings
			if (isset ($height) ? $height : '') {
				if (!is_int($height)) {
					die("<p><b>WYSIWYGPRO - Paramater Error:</b> Your height paramater is not a valid integer!</p>");
				}
			} else {
				// default height
				$height = 390;
			}


			// initialize variables
			$imenu_height = 182;
			$bmenu_height = 125;
			$tmenu_height = 422;
			$smenu_height = 151;
			$format_list_style = '';
			$font_list_style = '';
			$size_list_style = '';
			$class_list_style = '';

			if ($this->is_ie50 == true) {
				$is_ie50 = 'true';
			} else {
				$is_ie50 = 'false';
			}

			if ($this->is_gecko == true) {
				$height1 = ($height - 81);
				$height2 = ($height - 33);
			} else {
				$height1 = ($height - 83);
				$height2 = ($height - 32);
			}

			// if no save button requested remove the button
			if (empty($this->save_button)) {
				$this->remove ('save');
			}

			// remove custom object button if no inserts set.
			if (empty($this->custom_objects)) {
				$this->remove ('custom');
			}

			// adds class menu to the remove array if no classes set
			if (empty($this->class_menu)) {
				$this->remove ('class');
			}

			if (strstr(WP_WEB_DIRECTORY, 'https:')) {
				$secure = 'src="'.WP_WEB_DIRECTORY.'secure.htm"';
			} else {
				$secure = '';
			}

			if (strstr($this->remove_array,'image')){
				$this->image_window = 'imageoptions.php';
			}

			// remove requested buttons from the code
			if (!empty($this->remove_array)) {
				// if user requests the removal of buttons that also appear on the context menus we must add those to the remove array!
				if ($this->is_ie55) {
					if (strstr($this->remove_array,'pasteword')) {
						$imenu_height -= 24;
						$tmenu_height -= 24;
						$smenu_height -= 24;
						$bmenu_height -= 24;
					}
					if (strstr($this->remove_array,'document')) {
						$imenu_height -= 24;
						$tmenu_height -= 24;
						$smenu_height -= 24;
					}
					if (strstr($this->remove_array,'link')) {
						$imenu_height -= 24;
						$tmenu_height -= 24;
						$smenu_height -= 24;
					}
					if (strstr($this->remove_array,'image')) {
						$imenu_height -= 24;
					}
					if (strstr($this->remove_array,'bookmark')) {
						$bmenu_height -= 24;
					}
					if ((strstr($this->remove_array,'tbl')) || (strstr($this->remove_array,'edittable'))){
						$tmenu_height -= 271;
					}
				}

				// explode the remove array from a comma separated list into an actual array.
				$this->remove_array = explode (',', $this->remove_array);

				// loop through the array and remove the buttons
				$num_removes = sizeof($this->remove_array);
				for ($i=0; $i<$num_removes; $i++) {
					$handle = trim ($this->remove_array[$i]); // trim incase there were spaces after the commas.

					// if any of the drop-down menus are requested for removal we need to hide these menus...
					// but because they are referenced so often in the JavaScript this is tricky...
					// so instead of removing them completely we will just hide them by pasting display:none; in their style attribute.
					// beause we are checking the items in the remove array now this is a good time to do this.
					switch($handle) {
						case 'format':
							$format_list_style = 'display:none;';
							break;
						case 'size':
							$size_list_style = 'display:none;';
							break;
						case 'font':
							$font_list_style = 'display:none;';
							break;
						case 'class':
							$class_list_style = 'display:none;';
							break;
						default:
							// now do the removal
							$this->WYSIWYGPRO_code = preg_replace('/<!--\s+begin '.$handle.'\s+-->.*?<!--\s+end '.$handle.'\s+-->/sm', '', $this->WYSIWYGPRO_code);
							break;
					}
				}
			}

			// load language file
			$lang_include = '';
			if (DEFAULT_LANG != $this->instance_lang) {
				$lang_include = $this->instance_lang;
			} else {
				$lang_include = DEFAULT_LANG;
			}
			include (WP_FILE_DIRECTORY.'lang/'.$lang_include);

			// paste in the requested paramaters and language variables.
			$this->format_menu = $this->wp_replace($this->format_menu, array(
				'normal' => $lang['normal'] ,
				'heading_1' => $lang['heading_1'],
				'heading_2' => $lang['heading_2'],
				'heading_3' => $lang['heading_3'],
				'heading_4' => $lang['heading_4'],
				'heading_5' => $lang['heading_5'],
				'heading_6' => $lang['heading_6'],
				'pre_formatted' => $lang['pre_formatted'],
				'address1' => $lang['address1']
			));
			$this->size_menu = $this->wp_replace($this->size_menu, array(
				'1' => $lang['1'] ,
				'2' => $lang['2'],
				'3' => $lang['3'],
				'4' => $lang['4'],
				'5' => $lang['5'],
				'6' => $lang['6'],
				'7' => $lang['7'],
			));

			$this->WYSIWYGPRO_code = $this->wp_replace($this->WYSIWYGPRO_code, array(

				// configuration paramaters
				'usexhtml' => $this->usexhtml,
				'stylesheet' => $this->set_style,
				'directory' => WP_WEB_DIRECTORY,
				'width' => $width,
				'absheight' => $height,
				'height' => $height1,
				'height2' => $height2,
				'height3' => $height2 + 1,
				'imenu_height' => $imenu_height,
				'bmenu_height' => $bmenu_height,
				'tmenu_height' => $tmenu_height,
				'smenu_height' => $smenu_height,
				'baseURLurl' => $this->baseURL,
				'baseURL' => $this->baseURL2,
				'domain' => $this->domain,
				'domain2' => str_replace('/','\/',quotemeta(DOMAIN_ADDRESS)),
				'imgwindow' => $this->image_window,
				'is_ie50' => $is_ie50,
				'class_menu' => $this->class_menu,
				'format_list_style' => $format_list_style,
				'font_list_style' => $font_list_style,
				'size_list_style' => $size_list_style,
				'class_list_style' => $class_list_style,
				'font_menu' => $this->font_menu,
				'format_menu' => $this->format_menu,
				'size_menu' => $this->size_menu,
				'hyperlink_function' => $this->hyperlink_function,
				'hyperlink_function2' => $this->hyperlink_function2,
				"custom_objects" => $this->custom_objects,
				"links" => $this->links,
				"xhtml_lang" => $this->xhtml_lang,
				"encoding" => $this->encoding,
				"usep" => $this->usep,
				"showbookmarkmngr" => $this->showbookmarkmngr,
				"savebutton" => $this->save_button,
				"subsequent" => $this->subsequent,
				"instance_lang" => $this->instance_lang,
				'color_swatches' => $this->color_swatches,
				'instance_img_dir' => $this->instance_img_dir,
				'instance_doc_dir' => $this->instance_doc_dir,
				'secure' => $secure,
				'name' => $this->name,

				// language
				'please_wait' => $lang['please_wait'],
				'save' => $lang['save'],
				'post' => $lang['post'],
				'send' => $lang['send'],
				'print' => $lang['print'],
				'find_and_replace' => $lang['find_and_replace'],
				'cut' => $lang['cut'],
				'copy' => $lang['copy'],
				'paste' => $lang['paste'],
				'paste_word' => $lang['paste_word'],
				'undo' => $lang['undo'],
				'redo' => $lang['redo'],
				'insert_table' => $lang['insert_table'],
				'table_properties' => $lang['table_properties'],
				'add_row' => $lang['add_row'],
				'delete_row' => $lang['delete_row'],
				'insert_column' => $lang['insert_column'],
				'delete_column' => $lang['delete_column'],
				'merge_cell' => $lang['merge_cell'],
				'unmerge_cell' => $lang['unmerge_cell'],
				'insert_emoticon' => $lang['insert_emoticon'],
				'insert_image' => $lang['insert_image'],
				'horizontal_line' => $lang['horizontal_line'],
				'insert_hyperlink' => $lang['insert_hyperlink'],
				'document_link' => $lang['document_link'],
				'insert_bookmark' => $lang['insert_bookmark'],
				'special_characters' => $lang['special_characters'],
				'insert_object' => $lang['insert_object'],
				'paragraph_format' => $lang['paragraph_format'],
				'style_class' => $lang['style_class'],
				'font_face' => $lang['font_face'],
				'font_size' => $lang['font_size'],
				'bold' => $lang['bold'],
				'italic' => $lang['italic'],
				'underline' => $lang['underline'],
				'align_left' => $lang['align_left'],
				'align_right' => $lang['align_right'],
				'align_center' => $lang['align_center'],
				'justify' => $lang['justify'],
				'numbering' => $lang['numbering'],
				'bullets' => $lang['bullets'],
				'increase_indent' => $lang['increase_indent'],
				'decrease_indent' => $lang['decrease_indent'],
				'font_color' => $lang['font_color'],
				'highlight' => $lang['highlight'],
				'design' => $lang['design'],
				'html_code' => $lang['html_code'],
				'preview' => $lang['preview'],
				'br_tag' => $lang['br_tag'],
				'toggle_guidelines' => $lang['toggle_guidelines'],
				'image_properties' => $lang['image_properties'],
				'bookmark_properties' => $lang['bookmark_properties'],
				'add_row_above' => $lang['add_row_above'],
				'add_row_below' => $lang['add_row_below'],
				'add_column_left' => $lang['add_column_left'],
				'add_column_right' => $lang['add_column_right'],
				'merge_right' => $lang['merge_right'],
				'merge_below' => $lang['merge_below'],
				'unmerge_right' => $lang['unmerge_right'],
				'unmerge_below' => $lang['unmerge_below'],
				'cancel' => $lang['cancel'],
				'default' => $lang['default'],
				'clear_styles' => $lang['clear_styles'],
				'javascript_warning' => $lang['javascript_warning'],
				// javascript
				'guidelines_hidden' => $lang['guidelines_hidden'],
				'guidelines_visible' => $lang['guidelines_visible'],
				'place_cursor_in_table' => $lang['place_cursor_in_table'],
				'only_split_merged_cells' => $lang['only_split_merged_cells'],
				'no_cell_right' => $lang['no_cell_right'],
				'different_row_spans' => $lang['different_row_spans'],
				'no_cell_below' => $lang['no_cell_below'],
				'different_column_spans' => $lang['different_column_spans'],
				'select_hyperlink_text' => $lang['select_hyperlink_text'],
				'upgrade' => $lang['upgrade'],
				'format' => $lang['format'],
				'font' => $lang['font'],
				'class' => $lang['class'],
				'size' => $lang['size1']

			));

			// tidy up comments
			$this->WYSIWYGPRO_code = preg_replace("/<!--\s.*?-->/smi", '', $this->WYSIWYGPRO_code);

			// if we have been asked to save this configuration then save it to a file
			if (!empty($this->save_name)) {
				$fp = fopen (SAVE_DIRECTORY . $this->save_name, 'w');
				@flock ($fp, LOCK_EX);
				fwrite ($fp, $this->WYSIWYGPRO_code);
				@flock ($fp, LOCK_UN);
				fclose ($fp);
			}

		} else {

			// if cache has not expired simply load the data from the cache.
			$this->WYSIWYGPRO_code = implode('', file (SAVE_DIRECTORY . $this->save_name));

		}

		// now insert the code to be edited, this is done after the caching so that the code is not cached.
		return str_replace('##htmlCode##', $this->code, $this->WYSIWYGPRO_code);

	}


	/***************************************************************************
	 print_editor
	 Public: prints the output from return_editor() directly in the browser

	*/

	function print_editor($width=680, $height=390) {
		print $this->return_editor($width,$height);
	}

	// End class
}
?>