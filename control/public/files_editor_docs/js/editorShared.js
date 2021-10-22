// Purpose: functions shared between all supported browsers
var wp_version="2.2"
var wp_thisRow = null
var wp_thisCell = null
var wp_thisTable = null
var wp_current_hyperlink = null
var wp_current_obj=null
var wp_debug_mode = false
var wp_inbase = false
var wp_inp = null;
window.onerror = wp_hide_error
function wp_config () {
	this.lang = []
	this.useXHTML = false
	this.usep = false
	this.showbookmarkmngr = true
	this.subsequent = false
	this.border_visible = 1
}
// wp_stringbuilder, wp_gethtml, wp_fixAttibute, wp_fixText, wp_getAttributeValue and wp_appendNodeHTML are based on "getXhtml" by Erik Arvidsson available from http://webfx.eae.net/ used under license.
function wp_StringBuilder(sString) {
	this.append = function (sString) {
		this.length += (this._parts[this._current++] = String(sString)).length
		this._string = null
		return this
	}
	this.toString = function () {
		if (this._string != null)
			return this._string
		var s = this._parts.join("")
		this._parts = [s]
		this._current = 1
		this.length = s.length
		return this._string = s
	}
	this._current	= 0
	this._parts		= []
	this._string	= null
	if (sString != null)
		this.append(sString)
}
function wp_gethtml(node,obj) {
	var sb = new wp_StringBuilder
	wp_inbase = false
	var cn = node.childNodes
	var n = cn.length
	for (var i = 0; i < n; i++) {
		if (wp_inp == cn[i]) {
			i = i+2
		}
		wp_appendNodeHTML(cn[i], sb, obj)
		
	}
	var doctype = ''
	if (obj.useXHTML && !obj.snippit) {
		doctype =  '<?xml version="1.0" encoding="' + obj.encoding + '"?>\n<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n'
	} else if (!obj.useXHTML && !obj.snippit) {
		doctype =  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">\n'
	}
	return doctype + sb.toString()
}
function wp_fixAttribute(value) {
	return String(value).replace(/\&/g, "&amp;").replace(/</g, "&lt;").replace(/\"/g, "&quot;")
}
function wp_fixText(text) {
	return String(text).replace(/\&/g, "&amp;").replace(/</g, "&lt;")
}
function wp_getAttributeValue(attrNode, elementNode, sb) {
	var name = attrNode.nodeName.toLowerCase()
	if (wp_is_ie) {
		if ((name == 'selected') && (attrNode.nodeValue != true)) return
		if ((!attrNode.specified) && 
		(name != 'selected') && 
		(name != 'value') && 
		(name != 'coords') && 
		(name != 'shape')) 
		return
	}
	if (name == "_base_href"
	|| name == "_moz_dirty"
	|| name == "_moz_editor_bogus_node") {
		return
	}
	if (name == 'compact' 
	|| name == 'nowrap'
	|| name == 'ismap'
	|| name == 'declare'
	|| name == 'noshade'
	|| name == 'checked'
	|| name == 'disabled'
	|| name == 'readonly'
	|| name == 'multiple'
	|| name == 'selected'
	|| name == 'noresize'
	|| name == 'defer') {
		var value = name
	} else {
		var value = attrNode.nodeValue
	}
	if ((name == "href") && (value.search("WP_BOOKMARK#") != -1)) {
		var thisLinkArray = value.split("#")
		value = "#"+thisLinkArray[1]
	} 
	if (value == "" && name != 'alt' && name != 'title' && name != 'action') return
	if (name == "class" && value == "wp_none") return
	if (value == "null") return
	if (name != "style") {
		if (!isNaN(value)) {
			value = elementNode.getAttribute(name)
		} else if (name == 'align' || name == 'valign' || name == 'shape') {
			value = elementNode.getAttribute(name).toLowerCase()
		}
		sb.append(" " + name + "=\"" + wp_fixAttribute(value) + "\"")
	} 
}
function wp_appendNodeHTML(node, sb, obj) {
	switch (node.nodeType) {
		case 1:	// ELEMENT		
			if (node.nodeName == "!") {
				if ((node.text.search(/DOCTYPE/gi) != -1) || (node.text.search(/version=\"1.0\" encoding\=/gi) != -1)) {
					sb.append('')
				} else {
					sb.append(node.text)
				}
				break
			}
			var name = node.nodeName
			name = name.toLowerCase()
			if (wp_inbase == true && name == 'body') {
				break
			}
					
			if (name == "base" && !wp_is_ie) {
				if (node.getAttribute('href') == obj.baseURLurl) {
					break
				}	
			} else if (name == "base") {
				wp_inbase = true
				// childNodes
				var cs = node.childNodes
				l = cs.length
				for (var i = 0; i < l; i++) {
					wp_appendNodeHTML(cs[i], sb, obj)
				}
				wp_inbase = false
				break
			}
			if (wp_inp == node) {
				wp_inp = null;
				
				break
			}
			if (name == "p" && wp_is_ie) {
				wp_inp = node
			}
			if (name == "link") {
				if (node.getAttribute('href') == obj.stylesheet) {
					break
				}
			}
			if (name == "meta" && wp_is_ie) {
				if (node.getAttribute('name').toLowerCase() == "generator") {
					break
				}
			}
			if (name == "img") {
				if ((node.getAttribute('name')) && (node.src.search(wp_directory+"/images/bookmark_symbol.gif") != -1)) {
					sb.append('<a name="'+node.getAttribute('name')+'"')
					if (obj.useXHTML) {
						sb.append(' id="'+node.getAttribute('name')+'"></a>')
					} else {
						sb.append('></a>')
					}
					break
				}
				if (!node.getAttribute("alt")) {
					node.setAttribute("alt", "")
				}
			}
			if (name == "area") {
				if (!node.getAttribute("alt")) {
					node.setAttribute("alt", "")
				}
			}
			if (name != 'b'
			&& name != 'i'
			&& name != 'img'
			&& name != 'u'
			&& name != 'a'
			&& name != 'em'
			&& name != 'strong'
			&& name != 's'
			&& name != 'tt'
			&& name != 'code'
			&& name != 'var'
			&& name != 'samp'
			&& name != 'cite'
			&& name != 'dfn'
			&& name != 'del'
			&& name != 'ins'
			&& name != 'kbd'
			&& name != 'span'
			&& name != 'font'
			&& name != 'html') {
				if (wp_is_ie) {
					sb.append("\n")
				} else if (name != 'tbody'
					&& name != 'thead'
					&& name != 'tfoot'
					&& name != 'tr'
					&& name != 'td') {
						sb.append("\n")
				}
			}
			if (((name == "span") || (name == "font")) && (node.style.cssText.length < 1))  {
				var attrs = node.attributes
				var l = attrs.length
				// IE5 fix: count specified attrs
				var n = 0
				for (var i = 0; i < l; i++) {
					if (attrs[i].specified) {
						n ++
					} 
				}
				if (n == 0) {
					if (node.hasChildNodes()) {							
						// childNodes
						var cs = node.childNodes
						l = cs.length
						for (var i = 0; i < l; i++) {
							wp_appendNodeHTML(cs[i], sb, obj)
						}
					}
					break
				} else if (n == 1 && node.className == "wp_none") {
					if (node.hasChildNodes()) {							
						// childNodes
						var cs = node.childNodes
						l = cs.length
						for (var i = 0; i < l; i++) {
							wp_appendNodeHTML(cs[i], sb, obj)
						}
					}
					break
				}
			}
			sb.append("<" + name)
			if (name == "html" && obj.useXHTML) {
				if (!node.getAttribute('xmlns')) {
					sb.append(' xmlns="http://www.w3.org/1999/xhtml"')
				}
				if (!node.getAttribute('xml:lang')) {
					sb.append(' xml:lang="' + obj.xhtml_lang.toLowerCase() + '"')
				}
				if (!node.getAttribute('lang')) {
					sb.append(' lang="' + obj.xhtml_lang.toLowerCase() + '"')
				}
			}
			// inline styles
			if (node.style.cssText.length > 1) {
				sb.append(' style="')
				var propArray = node.style.cssText.split(';')
				var l = propArray.length
				for (var i = 0; i < l; i++) {
					if (propArray[i].length > 1) {
						var propVal = propArray[i].split(':')
						if (obj.border_visible == 1) {
							if (propVal[1] != " null"
							&& propVal[1] != " wp_bogus_font"
							&& propVal[1] != " 1px dashed rgb(127, 124, 117)"
							&& propVal[1] != " #7f7c75 1px dashed"
							&& propVal[0].substr(0,5) != " mso-"
							&& propVal[0].substr(0,4) != "mso-") {
								sb.append(propVal[0].toLowerCase() + ':')
								sb.append(wp_fixAttribute(propVal[1]) + ';')
							}
						} else {
							if (propVal[1] != " null"
							&& propVal[1] != " wp_bogus_font") {							
								sb.append(propVal[0].toLowerCase() + ':')
								sb.append(wp_fixAttribute(propVal[1]) + ';')
							}
						}
					}
				}
				sb.append('"')
			}
			// attributes
			var attrs = node.attributes
			var l = attrs.length
			for (var i = 0; i < l; i++) {
				wp_getAttributeValue(attrs[i], node, sb)
			}
			if (node.canHaveChildren || node.hasChildNodes() || name == 'textarea') {
				sb.append(">")
				if (node.innerHTML == '' || node.innerHTML == ' ' || node.innerHTML == '&nbsp;') {
					sb.append('&nbsp;')
				} else {
					// childNodes
					var cs = node.childNodes
					l = cs.length
					for (var i = 0; i < l; i++) {
						wp_appendNodeHTML(cs[i], sb, obj)
					}
					if ((name == 'body') || (name == 'html') || (name == 'head')) {
						sb.append("\n")
					}
				}
				sb.append("</" + name + ">")
			} else if (name == "script") {
				sb.append(">" + node.text + "</" + name + ">")
			} else if (name == "title" || name == "style" || name == "comment") {
				sb.append(">" + node.innerHTML + "</" + name + ">")
			} else if (obj.useXHTML) {
				sb.append(" />")
			} else { 
				sb.append(">")
			}
			break
		case 3:	// TEXT
			if (node.nodeValue) {
				if (node.nodeValue == '\n' ) break
				var str = node.nodeValue.replace(/\n{2,}/gi, "\n")
				sb.append(wp_column_wrap( wp_fixText(str) ))
			}
			break
		case 4:
			sb.append("<![CDA" + "TA[\n" + node.nodeValue + "\n]" + "]>")
			break
		case 8:
			if (wp_is_ie) {
				if ((node.text.search(/DOCTYPE/gi) != -1) || (node.text.search(/version=\"1.0\" encoding\=/gi) != -1)) {
					sb.append('')
				} else {
					sb.append("<!--" + node.nodeValue + "-->")
				}
			} else {
				if (node.nodeValue.substr(0, 4) == "[if ") {
					return
				} else {
					sb.append("<!--" + node.nodeValue + "-->")
				}
			}
			break
		case 9:	// DOCUMENT
			// childNodes
			var cs = node.childNodes
			l = cs.length
			for (var i = 0; i < l; i++) {
				wp_appendNodeHTML(cs[i], sb, obj)
			}
			break
		case 10:
			sb.append('')
			break
		default:
			if (debug_mode) {
				sb.append("<!--\nUnsupported Node:\n\n" + "nodeType: " + node.nodeType + "\nnodeName: " + node.nodeName + "\n-->")
			}
	}
}
function wp_column_wrap(str) {
	var cut = '\n' 
	var cols = 76 
	var tag_open = '<' 
	var tag_close = '>' 
	var count = 0 
	var in_tag = 0 
	var str_len = str.length 
	var segment_width = 0 
	for (i=0 ; i<=str_len ; i++) {
		if ((segment_width > cols) && (str.substring(i-1,i) == " ") && (str.substring(i,i+1) != '\n')) { 
			str = str.substring(0,i) + cut + str.substring(i,str_len) 
			i += cut.length 
			str_len = str.length 
			segment_width = 0 
		} else if (str.substring(i-1,i) == '\n') {
			segment_width = 0
		} else {
			segment_width++ 
		}
	}
	return str
}
function wp_replace_bookmark (code) {
	code = code.replace(/<a name="([^"]+)[^>]+><\/a>/gi, "<img name=\"$1\" src=\"" + wp_directory + "/images/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">")
	code = code.replace(/<a name="([^"]+)[^>]+>&nbsp;<\/a>/gi, "<img name=\"$1\" src=\"" + wp_directory + "/images/bookmark_symbol.gif\" contenteditable=\"false\" width=\"16\" height=\"13\" title=\"Bookmark: $1\" alt=\"Bookmark: $1\" border=\"0\">")
	code = code.replace(/href="#/gi, "href=\"WP_BOOKMARK#")
	return code
}
function wp_getOffsetTop(elm) {
  var mOffsetTop = elm.offsetTop
  var mOffsetParent = elm.offsetParent
  while(mOffsetParent){
    mOffsetTop += mOffsetParent.offsetTop
    mOffsetParent = mOffsetParent.offsetParent
  }
  return mOffsetTop
}
function wp_getOffsetLeft(elm) {
  var mOffsetLeft = elm.offsetLeft
  var mOffsetParent = elm.offsetParent
  while(mOffsetParent){
    mOffsetLeft += mOffsetParent.offsetLeft
    mOffsetParent = mOffsetParent.offsetParent
  }
  return mOffsetLeft
}
// this project is getting so complex we need to block any errors that I may not have accounted for
function wp_hide_error() {
	if (!wp_debug_mode) {
		return true
	}
}
function wp_make_styles (obj) {
	var styles = ''
	if (obj.stylesheet != '') {
		styles += '<link rel="stylesheet" href="'+obj.stylesheet+'" type="text/css">'
	}
	var stylesheets = obj.edit_object.document.getElementsByTagName('link')
	var l=stylesheets.length
	for (var i=0; i < l; i++) {
		if (stylesheets[i].href) {
			if (stylesheets[i].rel) {
				if (stylesheets[i].rel.toLowerCase() == "stylesheet") {
					styles += '<link rel="stylesheet" href="'+ stylesheets[i].href +'" type="text/css">'
				}
			} else if (stylesheets[i].type) {
				if (stylesheets[i].type.toLowerCase() == "text/css") {
					styles += '<link rel="stylesheet" href="'+ stylesheets[i].href +'" type="text/css">'
				}		
			}
		}
	}	
	var styleTags = obj.edit_object.document.getElementsByTagName('style')
	var l=styleTags.length
	for (var i=0; i < l; i++) {
		styles += '<style type="text/css">'+ styleTags[i].innerHTML +'</style>'
	}
	return styles
}
function wp_show_menu(obj, type, srcElement) {
	if (document.getElementById(obj.name+"_menu").style.visibility=="hidden") {
		wp_current_obj = obj
		parent.command = srcElement.id
		buttonElement = document.getElementById(srcElement.id)
		document.getElementById(obj.name+"_menu").style.left = wp_getOffsetLeft(buttonElement) + 'px'
		document.getElementById(obj.name+"_menu").style.top = wp_getOffsetTop(buttonElement) + buttonElement.offsetHeight + 'px'
		var menu_code
		if (obj.styles == '') {
			obj.styles = wp_make_styles(obj)
		}
		if (wp_is_ie) {
			var border = "border: 1px solid #000000"
		} else {
			var border = ""
		}
		var head = '<style type="text/css">body {background-color:white; padding:0px; margin:0px; ' + border + '} .off { display:block; overflow:hidden; width:249px; border: 2px solid #eeeeee; cursor: pointer; cursor: hand; } .on { display:block; overflow:hidden; width:249px; border: 2px solid highlight; cursor: pointer; cursor: hand; } div {padding: 0px; margin: 1px 0px 0px 0px}</style><script type="text/javascript">function on (elm) {elm.className="on";} function off (elm) {elm.className="off";}</script></head>'
		var head2 = '<html><head><style type="text/css">body {background-color:white; padding:0px; margin:0px; color:black; ' + border + '} .off { display:block; overflow:hidden; width:270px; background-color:white; color:black; padding: 2px; cursor: pointer; cursor: hand; } .on { display:block; overflow:hidden; width:270px; background-color:highlight; color:highlighttext; padding: 2px; cursor: pointer; cursor: hand; } div {text-align:left; padding:0px; margin: 1px 0px 0px 0px}</style><script type="text/javascript">function on (elm) {elm.className="on";} function off (elm) {elm.className="off";}</script></head>'
		if (type=="font") {
			document.getElementById(obj.name+"_menu").width="272"
			document.getElementById(obj.name+"_menu").height="141"
			menu_code = obj.baseURL + '<html><head>' + obj.styles + head2 + '<style>div {font-size:16px}</style><body><div style="margin-top:0px; width:253px; overflow:hidden; left: 0px; top:0px; position:absolute;">'+ document.getElementById(obj.name+"_font-menu").innerHTML +'<div></body></html>'
		} else if (type=="size") {
			document.getElementById(obj.name+"_menu").width="112"
			document.getElementById(obj.name+"_menu").height="202"
			menu_code = obj.baseURL + '<html><head>' + obj.styles + head2 + '<body><div style="margin-top:0px; width:93px; overflow:hidden; left: 0px; top:0px; position:absolute;">'+ document.getElementById(obj.name+"_size-menu").innerHTML +'<div></body></html>'
		} else if (type=="format") {
			document.getElementById(obj.name+"_menu").width="272"
			document.getElementById(obj.name+"_menu").height="202"
			menu_code = obj.baseURL + '<html><head>' + head + obj.styles + '<body><div style="margin:0px; padding:0px; width:253px; overflow:hidden; left: 0px; top:0px; position:absolute;">'+ document.getElementById(obj.name+"_format-menu").innerHTML +'<div></body></html>'
		} else if (type=="class") {
			document.getElementById(obj.name+"_menu").width="272"
			document.getElementById(obj.name+"_menu").height="141"
			menu_code = obj.baseURL + '<html><head>' + head + obj.styles + '<body><div style="margin:0px; padding:0px; width:253px; overflow:hidden; left: 0px; top:0px; position:absolute;">'+ document.getElementById(obj.name+"_class-menu").innerHTML +'<div></body></html>'
		}
		obj.menu_frame.document.open();	
		obj.menu_frame.document.write(menu_code)
		obj.menu_frame.document.close();
		document.getElementById(obj.name+"_menu").style.visibility="visible"
	} else {
		wp_hide_menu(obj)
	}
}
function wp_hide_menu(obj) {
	if (document.getElementById(obj.name+"_menu").style.visibility=="visible") {
		document.getElementById(obj.name+"_menu").style.visibility="hidden"
		obj.menu_frame.document.body.innerHTML = ""
	}
}
function wp_change_font_size(obj,size) {
	wp_hide_menu(obj)	
	obj.edit_object.focus()
	if (size == 'Default') {
		obj.edit_object.document.execCommand("RemoveFormat", false, null)
	} else {
		obj.edit_object.document.execCommand("FontSize", false, size)
	}
}
function wp_change_font(obj,font) {
	wp_hide_menu(obj)
	obj.edit_object.focus()
	if (font == 'Default') {
		obj.edit_object.document.execCommand("RemoveFormat", false, null)
	} else {
		obj.edit_object.document.execCommand("FontName", false, font)
	}
}
function wp_change_format(obj,format) {
	wp_hide_menu(obj)
	obj.edit_object.focus()
	obj.edit_object.document.execCommand("FormatBlock", false, format)
}
function wp_colordialog(obj,srcElement, Action) {
	if (srcElement.className == "disabled") {
		return	
	}
	var action = obj.openDialog(wp_directory + "selcolor.php?action="+Action+"&lang="+obj.instance_lang , 'modal', 296, 352)
}
// opens a modal dialoge, returns the window opject or return value in the case of modal windows.
function wp_docolor(obj,Action,color) {   
	obj.edit_object.focus()
	if (color != null) {
		if (Action == 'hilitecolor') {
			obj.edit_object.document.execCommand("usecss", false, false)
			obj.edit_object.document.execCommand('hilitecolor', false, color)
			obj.edit_object.document.execCommand("usecss", false, true)
		} else {
			obj.edit_object.document.execCommand(Action, false, color)
		}
	}
	obj.edit_object.focus()
}
function wp_paste_word_html(obj) {
	var url = wp_directory + 'pastewin.php?lang='+obj.instance_lang
	var width = 400
	var height = 278
	wp_current_obj = obj
	var pasteWindow = window.open(url ,"pastewin", "dependent=yes,width="+width+"px,height="+height+"px,left="+((screen.width/2)-(width/2))+",top="+((screen.height/2)-(height/2)))
	pasteWindow.focus()
}
function wp_insert_smiley(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	imgwin = obj.openDialog(wp_directory + 'smileys.php?lang='+obj.instance_lang ,'modal',380,316)
}
function wp_open_horizontal_rule_window(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	rulerwin = obj.openDialog(wp_directory + "insert_hr.php?lang="+obj.instance_lang ,'modal',260,212)
}
function wp_custom_object(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	var custom = obj.openDialog (wp_directory + "custom.php?lang="+obj.instance_lang, 'modal',350,500, 'scrollbars=yes')
	if (wp_is_ie) {
		wp_insert_code(obj,custom)
	}
}
function wp_open_special_characters_window(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	specchar = obj.openDialog(wp_directory + "special_characters.php?lang="+obj.instance_lang, 'modal',500,252)
}
function wp_findit(obj) {
	var findwin = obj.openDialog(wp_directory + "find.php?lang="+obj.instance_lang, 'modeless', 318, 146)
}
function wp_toggle_table_borders(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	if (obj.border_visible == 0) {
		wp_show_borders(obj)
	} else {
		wp_hide_borders(obj)
	}
}
function wp_show_borders(obj) {
	if (!obj) {
		obj = wp_current_obj
	}	
	var tables = obj.edit_object.document.getElementsByTagName('TABLE')
	var l=tables.length
	for (var i=0; i < l; i++) {
		if (tables[i].border == 0 || tables[i].border == null) {
			var tableCells = tables[i].getElementsByTagName('TD')
			var m=tableCells.length
			for (var j=0; j < m; j++) {
				if (wp_is_ie) {
					tableCells[j].runtimeStyle.border = "1px dashed #7F7C75"
				} else {
					tableCells[j].style.border = "1px dashed #7F7C75"
				}
			}
		}
	}
	obj.border_visible = 1
	var message = document.getElementById(obj.name + '_messages')
	if (message.innerHTML != obj.lng['guidelines_visible'])
		message.innerHTML = obj.lng['guidelines_visible']
}
function wp_hide_borders(obj) {
	var tableCells = obj.edit_object.document.getElementsByTagName('TD')
	var l=tableCells.length
	for (var i=0; i < l; i++) {
		if (wp_is_ie) {
			var rcsstext = tableCells[i].runtimeStyle.cssText
		} else {
			var rcsstext = tableCells[i].style.cssText
		}
		if (rcsstext.length > 1) {
			var propArray = rcsstext.split(';')
			var pl = propArray.length
			var icsstext = ''
			for (var j = 0; j < pl; j++) {
				if (propArray[j].length > 1) {
					var propVal = propArray[j].split(':')
					if (propVal[1] != " 1px dashed rgb(127, 124, 117)"
					&& propVal[1] != " #7f7c75 1px dashed") {							
						icsstext += propVal[0] + ':'
						icsstext += propVal[1] + ';'
					}
				}
			}
			if (wp_is_ie) {
				tableCells[i].runtimeStyle.cssText = icsstext
			} else {
				tableCells[i].style.cssText = icsstext
			}
		}
	}
	obj.border_visible = 0
	document.getElementById(obj.name+"_messages").innerHTML = obj.lng['guidelines_hidden']
}
/////////////////////////////
// Fancy table editing stuff
/////////////////////////////
// table window
function wp_open_table_window(obj,srcElement) {	
	if (srcElement.className == "disabled") {
		return	
	}
	if (wp_is_ie) {
		var height = 417
	} else {
		var height = 427
	}
	tblwin = obj.openDialog(wp_directory + "table.php?lang="+obj.instance_lang, 'modal', 440, height)
}
function wp_open_table_editor(obj) {
	if (wp_isInsideTable(obj)) {
		wp_getTable(obj)
		editTbl = obj.openDialog(wp_directory + 'edittable.php?lang='+obj.instance_lang, 'modal', 431, 516)
	} else {
		alert(obj.lng['place_cursor_in_table'])
	}
}
// adding or removing table rows
function wp_processRow(obj,action) {
	if (wp_isInsideTable(obj)) {
	wp_getTable(obj)
	var idx = 0
	var rowidx = 0
	var tr = wp_thisRow
	var numcells = tr.childNodes.length
	if (action == "choose") {
		choose = obj.openDialog(wp_directory + "addrow.php?lang="+obj.instance_lang ,'modal',270,150)
		return
	}
	if ((action == "") || (action == null)) {
		return
	}
	if (action == "addabove") {
		while (tr) {
			if (tr.tagName == "TR") {
				rowidx++
				tr = tr.previousSibling
			}
		}
		rowidx-=1
	} else {
		if (action == "addbelow") {
			while (tr) {
				if (tr.tagName == "TR") {
					rowidx++
					tr = tr.previousSibling
				}
			}
		}		
	}
	var tbl = wp_thisTable
	if (!tbl) {
		alert("Could not " + action + " row.")
		return
	}
	if ((action == "addabove") || (action == "addbelow"))  {
		var r = tbl.insertRow(rowidx)
		for (var i = 0; i < numcells; i++) {
			var c = r.appendChild(obj.edit_object.document.createElement("TD") )
			if (wp_thisCell.colSpan) {
				c.colSpan = wp_thisRow.childNodes[i].colSpan
			}
			c.width = wp_thisRow.childNodes[i].width
			c.vAlign = 'top'
			c.innerHTML = obj.tdInners
			if (obj.border_visible == 1) {
				wp_show_borders(obj)
			}
		}
	} else {
		if (wp_thisTable.getElementsByTagName('TR').length == 1) {
			return
		}
		while (tr) {
			if (tr.tagName == "TR") {
				rowidx++
				tr = tr.previousSibling
			}
		}
		rowidx -= 1
		tbl.deleteRow(rowidx)
		}
		wp_thisCell=null
		wp_thisRow=null
		wp_thisTable=null
	}
	obj.edit_object.focus()
}
// adding or removing a column
function wp_processColumn(obj,action) {
	if (wp_isInsideTable(obj)) {
	if (action == "choose") {
		choose = obj.openDialog(wp_directory + "addcolumn.php?lang="+obj.instance_lang ,'modal',270,150)
		return
	}
	//action='addleft'
	if ((action == "") || (action == null)) {
		return
	}
	wp_getTable(obj)
	// store cell index in a var because the cell will be
	// deleted when processing the first row
	var cellidx = wp_thisCell.cellIndex
	var tbl = wp_thisTable
	if (!tbl) {
		alert("Could not " + action + " column.")
		return
	}
	// now we have the table containing the cell
	this.wp_add_remove_columns(obj,tbl, cellidx, action)
	} 
	obj.edit_object.focus()
}
// function for processing columns
function wp_add_remove_columns(obj,tbl, cellidx, action) {
	if (!tbl.childNodes.length)
		return
		var n=tbl.childNodes.length
		for (var i = 0; i < n; i++) {
			if (tbl.childNodes[i].tagName == "TR") {
				var cell = tbl.childNodes[i].childNodes[ cellidx ]
				if (!cell)
					break // can't add cell after cell that doesn't exist
				if (action == "addleft") {
					cell.parentNode.insertBefore( obj.edit_object.document.createElement("TD"), cell)
				} else {
					if (action == "addright") {
						cell.parentNode.insertBefore( obj.edit_object.document.createElement("TD"), cell.nextSibling)
					} else {
					// check for rowspan
						if (cell.rowSpan > 1) {
							i += (cell.rowSpan - 1)
						}
						if (wp_thisRow.getElementsByTagName('TD').length == 1) {
							return
						}
						if (cell.colSpan < 2) { 
							tbl.childNodes[i].removeChild(cell)

						} else {
							cell.colSpan -= 1
						}
					}
				}
			} else {
			// keep looking for a "TR"
			this.wp_add_remove_columns(obj,tbl.childNodes[i], cellidx, action) 
		}
	}
	wp_reprocess_columns(obj)
}
// if there is no other way to split the cell just do it, otherwise (if it could be split vertical or horizontal) ask which way to do it
function wp_splitCell(obj) {
	if (wp_isInsideTable(obj)) {
		wp_getTable(obj)
		if ((wp_thisCell.colSpan < 2) && (wp_thisCell.rowSpan < 2)) {
			alert(obj.lng['only_split_merged_cells'])
		}
		if ((wp_thisCell.colSpan >= 2) && (wp_thisCell.rowSpan < 2)) {
			wp_unMergeRight(obj)
		} else if ((wp_thisCell.rowSpan >= 2) && (wp_thisCell.colSpan < 2)) {
			wp_unMergeDown(obj)
		} else if ((wp_thisCell.rowSpan >= 2) && (wp_thisCell.colSpan >= 2)) {
			choose = obj.openDialog(wp_directory + "unmrgcell.php?lang="+obj.instance_lang ,'modal',270,150)
			return
		} 
	}
}
function wp_mergeCell(obj) {
	if (wp_isInsideTable(obj)) {
		choose = obj.openDialog(wp_directory + "mrgcell.php?lang="+obj.instance_lang ,'modal',270,150)
		return
	}
}
// merge cells  
function wp_mergeRight(obj) {
	if (wp_isInsideTable(obj)) {
		wp_getTable(obj)
		if (!wp_thisCell.nextSibling) {
			alert(obj.lng['no_cell_right'])
			return
		}
		// don't allow user to merge rows with different rowspans
		if (wp_thisCell.rowSpan != wp_thisCell.nextSibling.rowSpan) {
			alert(obj.lng['different_row_spans'])
			return
		}
		if (wp_thisCell.nextSibling.innerHTML.toLowerCase() != obj.tdInners) {
			if (wp_thisCell.innerHTML.toLowerCase() == obj.tdInners) {
				wp_thisCell.innerHTML = wp_thisCell.nextSibling.innerHTML
			} else {
				wp_thisCell.innerHTML += wp_thisCell.nextSibling.innerHTML
			}
		}
		wp_thisCell.width = (((wp_thisCell.width.replace('%','')*100) + (wp_thisCell.nextSibling.width.replace('%','')*100))/100) + '%'
		wp_thisCell.colSpan += wp_thisCell.nextSibling.colSpan
		wp_thisRow.removeChild(wp_thisCell.nextSibling)
		wp_thisCell=null
		wp_thisRow=null
		wp_thisTable=null
	}
	obj.edit_object.focus()
}
// spit cells
function wp_unMergeRight(obj) {
	if (wp_isInsideTable(obj)) {
		wp_getTable(obj)
		if (wp_thisCell.colSpan < 2) {
			alert(obj.lng['only_split_merged_cells'])	
		} else {
			wp_thisCell.colSpan = wp_thisCell.colSpan - 1
			var newCell = wp_thisCell.parentNode.insertBefore(obj.edit_object.document.createElement("TD"), wp_thisCell.nextSibling)
			newCell.rowSpan = wp_thisCell.rowSpan
			if (wp_thisCell.colSpan < 2) {
				wp_thisCell.width = (wp_thisCell.width.replace('%','')/2) + '%'
				newCell.width = wp_thisCell.width 
			} else {
				var newwidth = wp_thisCell.width.replace('%','')/wp_thisCell.colSpan
				wp_thisCell.width = (newwidth*(wp_thisCell.colSpan-1)) + '%'
				newCell.width = newwidth + '%'
			}
			newCell.innerHTML = obj.tdInners
			newCell.vAlign = 'top'
		}
		if (obj.border_visible == 1) {
			wp_show_borders(obj)
		}
		wp_thisCell=null
		wp_thisRow=null
		wp_thisTable=null
	} 
	obj.edit_object.focus()
}
// merge with cell below
function wp_mergeDown(obj) {
	if (wp_isInsideTable(obj)) {
		wp_getTable(obj)
		var numrows = wp_thisTable.getElementsByTagName('TR').length
		var topRowIndex = wp_thisRow.rowIndex
		if (numrows - (topRowIndex + wp_thisCell.rowSpan) <= 0) {
			alert(obj.lng['different_column_spans']) 
			return
		}
		if (!wp_thisRow.nextSibling) {
			alert(obj.lng['no_cell_below']) 
			return
		}
		var bottomCell = wp_thisRow.parentNode.childNodes[ topRowIndex + wp_thisCell.rowSpan ].childNodes[ wp_thisCell.cellIndex ]
		var bottomRow = wp_thisRow.parentNode.childNodes[topRowIndex + wp_thisCell.rowSpan ]
		// don't allow merging rows with different colspans
		if (wp_thisCell.colSpan != bottomCell.colSpan) {
			alert(obj.lng['different_column_spans']) 
			return
		}
		// do the merge
		if (bottomCell.innerHTML.toLowerCase() != obj.tdInners) {
			if (wp_thisCell.innerHTML.toLowerCase() == obj.tdInners) {
				wp_thisCell.innerHTML = bottomCell.innerHTML
			} else {
				wp_thisCell.innerHTML += bottomCell.innerHTML
			}
		}
		wp_thisCell.rowSpan += bottomCell.rowSpan
		wp_thisCell.nextSibling
		bottomRow.removeChild(bottomCell) 
		wp_thisCell=null
		wp_thisRow=null
		wp_thisTable=null
	}
	obj.edit_object.focus()
}
//  unMergeDown
function wp_unMergeDown(obj) {
	if (wp_isInsideTable(obj)) {
		wp_getTable(obj)
		if (wp_thisCell.rowSpan < 2) {
			alert(obj.lng['only_split_merged_cells'])
			return
		}
		var topRowIndex = wp_thisCell.parentNode.rowIndex
		// add a cell to the beginning of the next row
		var newCell = wp_thisRow.parentNode.childNodes[ topRowIndex + wp_thisCell.rowSpan - 1 ].appendChild( obj.edit_object.document.createElement("TD") )
		newCell.width = wp_thisCell.width 
		newCell.innerHTML = obj.tdInners
		newCell.vAlign = 'top'
		newCell.colSpan = wp_thisCell.colSpan
		wp_thisCell.rowSpan -= 1
		if (obj.border_visible == 1) {
			wp_show_borders(obj)
		}
		wp_thisCell=null
		wp_thisRow=null
		wp_thisTable=null
	}
	obj.edit_object.focus()
}
// fixes column widths, alignment and inserts spacers.
// should be called after doing any column manipulation
function wp_reprocess_columns(obj) {
	var nocolumns = 0
	var tableRows = wp_thisTable.getElementsByTagName('TR')
	var tableColumns = tableRows[0].getElementsByTagName('TD')
	// get the number of columns taking into account colspans
	var n=tableColumns.length
	for (var i=0; i < n; i++) {
			if (tableColumns[i].getAttribute('colSpan') >= 2) {
				nocolumns += tableColumns[i].getAttribute('colSpan')
			} else {
				nocolumns +=1
			}
	}
	// calculate the column widths
	var tdwidth = 100/nocolumns
	var tableCells = wp_thisTable.getElementsByTagName('TD')
	// now resize the columns, also insert spacers into cells with no inner html and fix text alignment
	var n=tableCells.length
	for (var i=0; i < n; i++) {
			if (tableCells[i].getAttribute('colSpan') >= 2) {
				tableCells[i].width = (tdwidth*tableCells[i].getAttribute('colSpan')) + '%'
			} else {
				tableCells[i].width = tdwidth + '%'
			}
			if (tableCells[i].innerHTML == '') {
				tableCells[i].innerHTML = obj.tdInners
			}
			if ((tableCells[i].getAttribute('vAlign') == '') || (tableCells[i].getAttribute('vAlign') == null)) {
				tableCells[i].vAlign = 'top'
			}
	}
	if (obj.border_visible == 1) {
		wp_show_borders(obj)
	}
}
///////////////////////////
// Save functions //
///////////////////////////
// function to ensure updates are sent to the textarea before saving, should be called from the save button or the form in an onsubmit statement
function submit_form() {	
	var editors = document.getElementsByTagName("TEXTAREA")
	for (var i=0; i<editors.length; i++) {
		if (editors[i].className == "html_edit_area") {
			wp_prepare_submission(editors[i])
		}
	}
	return true
}
function wp_prepare_submission(obj) {
	if (obj.html_mode==false) {
		wp_send_to_html(obj)
		return true
	} else {
		return true
	}
}
/////////////////////
// Tab view script //
/////////////////////
function wp_showDesign() {
	if (this.html_mode==true) {
		if (document.getElementById(this.name+'_designTab')) {
			document.getElementById(this.name+"_load_message").style.display ='block'
			setTimeout("wp_on_enter_tab_one("+this.name+")",1);
		}
	}
}
function wp_on_enter_tab_one(obj) {
	if (obj.html_mode==true) {	
		var tab_one = document.getElementById(obj.name+'_tab_one').style.display = "block"
		var tab_two = document.getElementById(obj.name+'_tab_two').style.display = "none"
		var tab_three = document.getElementById(obj.name+'_tab_three').style.display = "none"
		if (document.getElementById(obj.name+'_designTab'))
			document.getElementById(obj.name+'_designTab').className = "tbuttonUp"
		
		if (document.getElementById(obj.name+'_sourceTab'))		
			document.getElementById(obj.name+'_sourceTab').className = "tbuttonDown"
		
		if (document.getElementById(obj.name+'_previewTab'))
			document.getElementById(obj.name+'_previewTab').className = "tbuttonDown"
			
		wp_send_to_edit_object(obj)
		obj.html_mode=false
		obj.preview_mode=false
	}
	document.getElementById(obj.name+"_load_message").style.display ='none'
}
function wp_showCode() {
	if (this.html_mode==false || this.preview_mode==true) {
		if (document.getElementById(this.name+'_sourceTab')) {
			document.getElementById(this.name+"_load_message").style.display ='block'
			setTimeout("wp_on_enter_tab_two("+this.name+")",1);
		}
	}
}
function wp_on_enter_tab_two(obj) {
	if (obj.html_mode==false || obj.preview_mode==true) {
		wp_hide_menu(obj)
		var tab_one = document.getElementById(obj.name+'_tab_one').style.display = "none"
		var tab_two = document.getElementById(obj.name+'_tab_two').style.display = "block"
		var tab_three = document.getElementById(obj.name+'_tab_three').style.display = "none"
		obj.html_edit_area.style.visibility = "visible" 
		if (document.getElementById(obj.name+'_designTab'))
 			document.getElementById(obj.name+'_designTab').className = "tbuttonDown"
			
		if (document.getElementById(obj.name+'_sourceTab'))			
			document.getElementById(obj.name+'_sourceTab').className = "tbuttonUp"
			
		if (document.getElementById(obj.name+'_previewTab'))
			document.getElementById(obj.name+'_previewTab').className = "tbuttonDown"
			
		obj.html_mode=true
		if (obj.preview_mode==false) {
			wp_send_to_html(obj)
		}
		obj.preview_mode=false
	}
	document.getElementById(obj.name+"_load_message").style.display ='none'
}
function wp_showPreview() {
	if (this.preview_mode==false) {
		if (document.getElementById(this.name+'_previewTab')) {
			document.getElementById(this.name+"_load_message").style.display ='block'
			setTimeout("wp_on_enter_tab_three("+this.name+")",1);
		}
	}
}
function wp_on_enter_tab_three(obj) {
	if (obj.preview_mode==false) {
		wp_hide_menu(obj)
		var tab_one = document.getElementById(obj.name+'_tab_one').style.display = "none"
		var tab_two = document.getElementById(obj.name+'_tab_two').style.display = "none"
		var tab_three = document.getElementById(obj.name+'_tab_three').style.display = "block"
		if (document.getElementById(obj.name+'_designTab'))
 			document.getElementById(obj.name+'_designTab').className = "tbuttonDown"
		
		if (document.getElementById(obj.name+'_sourceTab'))		
			document.getElementById(obj.name+'_sourceTab').className = "tbuttonDown"
		
		if (document.getElementById(obj.name+'_previewTab'))
			document.getElementById(obj.name+'_previewTab').className = "tbuttonUp"
			
		if (obj.html_mode==false) {
			wp_send_to_html(obj)
		}
		obj.html_mode=true
		obj.preview_mode=true
		wp_send_to_preview(obj)
	}
	document.getElementById(obj.name+"_load_message").style.display ='none'
}
function wp_on_mouse_down_tab(srcElement, obj) {
	//document.getElementById(obj.name+"_load_message").style.display ='block'
	if (srcElement.className != 'tbuttonUp')
		srcElement.className='tbuttonMouseDown'
}
function wp_send_to_preview(obj) {
	obj.previewFrame.document.open()
	obj.previewFrame.document.write(obj.getPreviewCode())
	obj.previewFrame.document.close()
	obj.previewFrame.focus()
}
function updateAllWysiwyg() {
	submit_form()
}
function updateAllHTML() {
	var editors = document.getElementsByTagName("TEXTAREA")
	for (var i=0; i<editors.length; i++) {
		if (editors[i].className == "html_edit_area") {
			wp_send_to_edit_object(editors[i])
		}
	}
}
function wp_InsertAtSelection(code) {
	wp_insert_code(this, code)
}
function wp_SetCode(code) {
	this.html_edit_area.value = code
	wp_send_to_edit_object(this)
}
function wp_GetSelectedText() {
	var selectedText
	if (wp_is_ie) {
		selectedText = this.edit_object.document.selection.createRange().text
	} else {
		selectedText = this.edit_object.getSelection().getRangeAt(0)
	}
	return selectedText
}
function wp_GetCode() {
	if (this.html_mode==false) {
		wp_send_to_html(this)
	} 
	return this.html_edit_area.value
}
function wp_GetPreviewCode() {
	if (this.html_mode==false) {
		wp_send_to_html(this)
	}
	if (this.stylesheet != '') {
		var str = this.baseURL + '<link rel="stylesheet" href="' + this.stylesheet + '" type="text/css">'
	} else {
		var str = this.baseURL
	}
	return str + this.html_edit_area.value
}
function wp_Focus() {
	if (wp_is_ie) {
		var previewFrame = document.frames(this.name+'_previewFrame')
	} else {
		var previewFrame = document.getElementById(this.name+'_previewFrame').contentWindow
	}
	if (this.html_mode==true) {
		this.html_edit_area.focus()
	} else if (this.preview_mode == true) {
		previewFrame.focus()
	} else {
		this.edit_object.focus()
	}
	return true;
}
function wp_initAll() {
	var editors = document.getElementsByTagName("TEXTAREA")
	for (var i=0; i<editors.length; i++) {
		if (editors[i].className == "html_edit_area") {
			wp_editor(editors[i],eval("config_"+editors[i].id))
		}
	}
}
function wp_updateHTML() {
	if (this.html_mode==false) {
		wp_send_to_html(this)
	}
}
function wp_updateWysiwyg() {
	wp_send_to_edit_object(this)
}
window.onload = wp_initAll