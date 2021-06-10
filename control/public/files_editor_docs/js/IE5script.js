// Purpose: functions specific to Internet Explorer
function wp_divReturn(obj) {
	var sel = obj.edit_object.document.selection.createRange()
	parentTag = wp_skipInline(sel)
	if (parentTag.tagName == "TD"
	|| parentTag.tagName == "THEAD"
	|| parentTag.tagName == "TFOOT" 
	|| parentTag.tagName == "BODY"
	|| parentTag.tagName == "HTML" 
	|| parentTag.tagName == "P") {
		obj.edit_object.document.execCommand("FormatBlock", false, "<div>")
	}
	return true
}
function wp_skipInline(sel) {
	var foo = sel.parentElement()
	while((foo.tagName == "B"
	|| foo.tagName == "I" 
	|| foo.tagName == "U" 
	|| foo.tagName == "EM"
	|| foo.tagName == "STRONG"
	|| foo.tagName == "S"
	|| foo.tagName == "TT"
	|| foo.tagName == "CODE"
	|| foo.tagName == "VAR"
	|| foo.tagName == "SAMP"
	|| foo.tagName == "CITE"
	|| foo.tagName == "DFN"
	|| foo.tagName == "DEL"
	|| foo.tagName == "INS"
	|| foo.tagName == "KBD"
	|| foo.tagName == "SPAN"
	|| foo.tagName == "FONT") && (foo.tagName!="HTML")) {
		foo = foo.parentElement
	}
	return foo
}
function wp_editor(obj,config) {
	// Strings:
	obj.name = config.name
	obj.instance_lang = config.instance_lang
	obj.encoding = config.encoding
	obj.xhtml_lang = config.xhtml_lang
	obj.baseURLurl = config.baseURLurl
	obj.baseURL = config.baseURL
	if (config.domain1) {
		obj.domain1 = config.domain1
		obj.domain2 = config.domain2
	}
	obj.instance_img_dir = config.instance_img_dir
	obj.instance_doc_dir = config.instance_doc_dir
	obj.imagewindow = config.imagewindow
	obj.links = config.links
	obj.custom_inserts = config.custom_inserts
	obj.stylesheet = config.stylesheet
	obj.styles = ''
	obj.color_swatches = config.color_swatches
	// lang
	obj.lng = config.lang
	//integers:
	obj.imenu_height = config.imenu_height
	obj.bmenu_height = config.bmenu_height
	obj.smenu_height = config.smenu_height
	obj.tmenu_height = config.tmenu_height
	obj.border_visible = config.border_visible
	// booleen
	obj.usep = config.usep
	if (obj.usep) {
		obj.tdInners = '<p>&nbsp;</p>';
	} else {
		obj.tdInners = '<div>&nbsp;</div>';
	}
	obj.showbookmarkmngr = config.showbookmarkmngr
	obj.snippit = true
	obj.html_mode=false
	obj.preview_mode=false
	obj.safe = true
	obj.initfocus = false
	obj.subsequent =config.subsequent
	obj.useXHTML = config.useXHTML
	// methods
	obj.getCode = wp_GetCode
	obj.getPreviewCode = wp_GetPreviewCode
	obj.setCode = wp_SetCode
	obj.insertAtSelection = wp_InsertAtSelection
	obj.getSelectedText = wp_GetSelectedText
	obj.moveFocus = wp_Focus
	obj.openDialog = wp_openDialog
	obj.showPreview = wp_showPreview
	obj.showCode = wp_showCode
	obj.showDesign = wp_showDesign
	obj.updateHTML = wp_updateHTML
	obj.updateWysiwyg = wp_updateWysiwyg
	// objects:
	obj.menu_frame = document.frames(obj.name+"_menu")
	obj.font_face = eval("document.all."+obj.name+"_font_face")
	obj.font_size = eval("document.all."+obj.name+"_font_size")
	obj.format_list = eval("document.all."+obj.name+"_format_list")
	obj.class_menu = eval("document.all."+obj.name+"_class_menu")
	obj.html_edit_area = eval("document.getElementById('"+obj.name+"')")
	var tbar=eval("document.getElementById('"+obj.name+"_tab_one')")
	var tbarimages = document.getElementById(obj.name+"_tab_one").getElementsByTagName('IMG')
	obj.tbarimages = tbarimages
	obj.tbarlength = tbarimages.length
	obj.edit_object = document.frames(obj.name+"_editFrame")
	obj.editFrame = obj.edit_object
	obj.previewFrame = document.frames(obj.name+"_previewFrame")
	var container = document.getElementById(obj.name+"_container")
	if (!wp_is_ie50) {
		var table = document.getElementById(obj.name+"_toolbar1")
		var n = table.all.length
		for (i=0; i<n; i++) {
			table.all[i].unselectable = "on"
		}
		var table = document.getElementById(obj.name+"_toolbar2")
		var n = table.all.length
		for (i=0; i<n; i++) {
			table.all[i].unselectable = "on"
		}
		var tab_table = document.getElementById(obj.name+'_tab_table')
		var n=tab_table.all.length
		for (i=0; i<n; i++) {
			tab_table.all[i].unselectable = "on"
		}
		var hidden = document.getElementById(obj.name+'_hidden')
		var n=hidden.all.length
		for (i=0; i<n; i++) {
			hidden.all[i].unselectable = "on"
		}
	}
	kids = container.getElementsByTagName('IMG')
	if (document.getElementById(obj.name+"_wp_save")) {
		save = document.getElementById(obj.name+"_wp_save")
		save.onMouseOver = wp_m_over
		save.onMouseOut = wp_m_out
		save.onMouseDown = wp_m_down
		save.onMouseUp = wp_m_up
	}
	obj.edit_object.document.designMode="on"
	wp_next(obj)
}
function wp_next (obj) {
	// baseURL
	var str = obj.html_edit_area.value
	if (obj.baseURL != '') {
		str = obj.baseURL + str
	}
	obj.html_edit_area.value = str
	try {
		obj.edit_object.document.open()
	} catch (e) {
		document.location.reload()
	}
	obj.edit_object.document.write(str)
	obj.edit_object.document.close()
	obj.edit_object.document.execCommand("2D-Position", true, true)
	obj.edit_object.document.execCommand("LiveResize", true, true)
	obj.edit_object.document.execCommand("MultipleSelection", true, true)
	if (obj.stylesheet != '') {
		obj.edit_object.document.createStyleSheet(obj.stylesheet)
	}
	// detect keypress
	obj.edit_object.document.onkeydown = function () {
		// make the enter keypress use <div> instead of <p> as the default.
		if (obj.edit_object.event.keyCode == 13) { // ENTER
			//var sel=obj.edit_object.document.selection.createRange()
			if ((obj.html_mode==false) && (obj.safe) && (!obj.usep) && (obj.edit_object.document.selection.type != "Control")) {
				wp_divReturn(obj)
			}
		}
		// make the tab key create tabs rather moving the focus away from the editor, which just pisses off people who are used to MS Word.
		if (obj.edit_object.event.keyCode == 9) { // TAB
			if ((obj.html_mode==false) && (obj.safe)) {
				var sel = obj.edit_object.document.selection.createRange() 
				sel.pasteHTML(' &nbsp;&nbsp;&nbsp; ')
				return false
			}
		}
	}
	if (!wp_is_ie50) {
		// context menus
		obj.edit_object.document.oncontextmenu = function () {
			if (obj.safe) {
				wp_current_obj = obj
				var sel=obj.edit_object.document.selection.createRange()
				if (obj.edit_object.document.selection.type == "Control") {
					if (sel(0).tagName == "IMG") {
						if ((sel(0).getAttribute('name')) && (sel(0).src.search(wp_directory+"/images/bookmark_symbol.gif") != -1)) {
							var menu = document.getElementById(obj.name+"_bookmarkMenu")
							oHeight = obj.bmenu_height + 2
							oWidth=230
						} else {
							var menu = document.getElementById(obj.name+"_imageMenu")
							oHeight = obj.imenu_height + 2
							oWidth=230
						}
					} else {
						if (sel(0).tagName == "TABLE") {
							var menu = document.getElementById(obj.name+"_standardMenu")
							oHeight = obj.smenu_height + 2
							oWidth=230
						} else {
							return false
						}
					}
				} else {					
					if (wp_isInsideTable(obj, sel)) {
						var menu = document.getElementById(obj.name+"_tableMenu")
						oHeight = obj.tmenu_height + 2
						oWidth=270
					} else {
						var menu = document.getElementById(obj.name+"_standardMenu")
						oHeight = obj.smenu_height + 2
						oWidth=230
					} 
				}
				// make inactive menu items disabled
				var menuRows = menu.getElementsByTagName('TR')
				if (menu == document.getElementById(obj.name+"_tableMenu")) {
					wp_getTable(obj, sel)
				}
				var n=menuRows.length
				for (var i=0; i < n; i++) {
					var cmd = menuRows[i].cid
					menuRows[i].getElementsByTagName('TD')[0].getElementsByTagName('IMG')[0].style.filter = "progid:DXImageTransform.Microsoft.MaskFilter() progid:DXImageTransform.Microsoft.MaskFilter(color=#AAAAAA)"				
					if (cmd == 'unmergeright') {
						if (wp_thisCell.colSpan >= 2) {
							menuRows[i].disabled=false
							menuRows[i].getElementsByTagName('TD')[0].getElementsByTagName('IMG')[0].style.filter = ""
						} else {
							menuRows[i].disabled=true
						}
					} else if (cmd == 'mergeright') {
						if ((!wp_thisCell.nextSibling) || (wp_thisCell.rowSpan != wp_thisCell.nextSibling.rowSpan)) {
							menuRows[i].disabled=true
						} else {
							menuRows[i].disabled=false
							menuRows[i].getElementsByTagName('TD')[0].getElementsByTagName('IMG')[0].style.filter = ""
						}
					} else if (cmd == 'mergebelow') {
						var numrows = wp_thisTable.getElementsByTagName('TR').length
						var topRowIndex = wp_thisRow.rowIndex
						if ((!wp_thisRow.nextSibling) || (numrows - (topRowIndex + wp_thisCell.rowSpan) <= 0)) {
							menuRows[i].disabled=true
						} else {
							menuRows[i].disabled=false
							menuRows[i].getElementsByTagName('TD')[0].getElementsByTagName('IMG')[0].style.filter = ""
						}
					} else if (cmd == 'unmergebelow') {
						if (wp_thisCell.rowSpan < 2) {
							menuRows[i].disabled=true
						} else {
							menuRows[i].disabled=false
							menuRows[i].getElementsByTagName('TD')[0].getElementsByTagName('IMG')[0].style.filter = ""
						}
					} else {				
						if (obj.edit_object.document.queryCommandEnabled(cmd)) {
							menuRows[i].disabled=false
							menuRows[i].getElementsByTagName('TD')[0].getElementsByTagName('IMG')[0].style.filter = ""
						} else {
							menuRows[i].disabled=true
						}
					}
				}
				// now actually make the menus
				var oPopUpBody = wp_oPopUp.document.body
				var evnt = obj.edit_object.event
				oPopUpBody.innerHTML = menu.innerHTML
				wp_oPopUp.show(evnt.screenX, evnt.screenY, oWidth, oHeight)
				//document.body.onmouseup = closePopup
				return false
			} else {
				return true
			}
		}
	}
	obj.edit_object.document.body.onmouseup = function () {
		wp_set_button_states(obj)
		wp_hide_menu(obj)
	}
	if (obj.border_visible == 1) {
		wp_show_borders(obj) 
	} 
	// the editor is now ready to use
	document.getElementById(obj.name+"_load_message").style.display ='none'
}
// functions for sending html between edit_object and the textarea
function  wp_send_to_html(obj) {
	//obj.edit_object.document.body.innerHTML = obj.edit_object.document.body.innerHTML.replace(/\&nbsp;/gi, '<!-- WP_SPACEHOLDER -->');
	if (obj.html_edit_area.value.search(/<body/gi) != -1) {
		obj.snippit = false
		obj.html_edit_area.value = wp_gethtml(obj.edit_object.document,obj)
	} else {
		obj.snippit = true
		obj.html_edit_area.value = wp_gethtml(obj.edit_object.document.body,obj)
	}
	var str=obj.html_edit_area.value
	RegExp.multiline = true
	if (obj.domain1 && obj.domain2) {
		str = str.replace(obj.domain1, '$1"')
		str = str.replace(obj.domain2, '$1"')
	}
	str = str.replace(/<div><\/div>/gi, "<div>&nbsp;</div>")
	str = str.replace(/<p><\/p>/gi, "<p>&nbsp;</p>")
	str = str.replace(/ style=\"\"/gi, "")
	//str = str.replace(/<\!-- WP_SPACEHOLDER -->/gi, '&nbsp;');
	obj.html_edit_area.value = str 
	if (obj.html_mode==true) {
		obj.html_edit_area.focus()
	}
}
function wp_send_to_edit_object(obj) { 
	obj.html_edit_area.value = wp_replace_bookmark (obj.html_edit_area.value)
	obj.edit_object.document.open()
	obj.edit_object.document.write(obj.html_edit_area.value)
	obj.edit_object.document.close()
	wp_next(obj)
	if (obj.border_visible == 1) {
		wp_show_borders(obj) 
	} 
	obj.styles = wp_make_styles (obj)
	if (obj.html_mode==false) {
		obj.edit_object.focus()
	}
}
// context menu mouse overs //
function wp_menuover(srcElement) {
	tds=srcElement.getElementsByTagName('TD')
	tds[0].style.backgroundColor = "highlight"
	tds[1].style.backgroundColor = "highlight"
	tds[1].style.color = "highlighttext"
}
function wp_menuout(srcElement) {
	tds=srcElement.getElementsByTagName('TD')
	tds[0].style.backgroundColor = "threedface"
	tds[1].style.backgroundColor = "#F9F8F7"
	tds[1].style.color = "#000000"
}
function wp_closePopup() {
  wp_oPopUp.hide()
}
// Catch and execute the commands sent from the buttons and tools
function wp_callFormatting(obj,sFormatString) {
	obj.edit_object.focus()
	if (wp_is_ie50) {
		obj.edit_object.document.execCommand(sFormatString)
	} else {
		document.execCommand(sFormatString)
	}
	wp_set_button_states(obj)
}
function wp_change_class(obj,classname) {	
	wp_hide_menu(obj)
	obj.edit_object.focus()
	var sel = obj.edit_object.document.selection.createRange() 
	if (classname == 'wp_none') {	
		var foo = sel.parentElement();
		while(!foo.className&&foo.tagName!="HTML") {
			foo = foo.parentElement;
		}
		if (foo.getAttribute('class') != 'wp_none' && foo.getAttribute('class') != '') {
			foo.className = classname;
		}
	}
	if (obj.edit_object.document.selection.type == "Control") {
		sel(0).setAttribute('class', classname)	
	} else {
		obj.edit_object.document.execCommand("FontName", false, 'wp_bogus_font')
		var spans = obj.edit_object.document.getElementsByTagName('SPAN')
		var fonts = obj.edit_object.document.getElementsByTagName('FONT')
		wp_set_class(spans, classname)
		wp_set_class(fonts, classname)
	}
} 
function wp_set_class(arr, classname) {
	var l = arr.length
	for (var i=0; i < l; i++) {
		if (arr[i].style.fontFamily) {
			if (arr[i].style.fontFamily == 'wp_bogus_font') {
				arr[i].className = classname
				arr[i].style.fontFamily = null
			}
		}
		if (arr[i].getAttribute("face")) {
			if (arr[i].getAttribute("face") == 'wp_bogus_font') {
				arr[i].removeAttribute('face')
				arr[i].className = classname
			}
		}
	}
}
// returns true if cursor is inside a hyperlink
// requires the current selection
function wp_isInsideLink(obj, sel) {
	if (!obj.edit_object.document.queryCommandEnabled("InsertHorizontalRule")) {
		return false
	}
	var thisA = sel.parentElement()
	while(thisA.tagName!="A"&&thisA.tagName!="HTML") {
			thisA = thisA.parentElement
	}
	if (thisA.tagName == "A") {
		return true
	} else {
		return false
	}
}
// lets try to make a custom hyperlink window!!
function wp_open_hyperlink_window(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	var sel = obj.edit_object.document.selection.createRange()
	if ((sel.text == '') && (!wp_isInsideLink(obj, sel))) {
		alert(obj.lng['select_hyperlink_text'])
		return
	}
	var thisTarget = ""
	var thisTitle = ""
	// check if hyperlink exists and if so populate the link dialog so that user can edit the existing link
	if (obj.edit_object.document.selection.type == "Control") {
		if (sel(0).tagName == "IMG") {
			this_href = sel(0).parentNode
		} else {
			return
		}
	} else {	
		this_href = sel.parentElement()
	}
	while(this_href.tagName!="A"&&this_href.tagName!="HTML") {
			this_href = this_href.parentElement
	}
	if (this_href.tagName == "A") {
		var thisLink = this_href.getAttribute("HREF")
		if (thisLink.search("WP_BOOKMARK#") != -1) {
			var thisLinkArray = thisLink.split("#")
			wp_current_hyperlink = "#"+thisLinkArray[1]
		} else {
			wp_current_hyperlink = thisLink
		}
		if (this_href.getAttribute("target")) {
			thisTarget = this_href.getAttribute("target")
		}
		if (this_href.getAttribute("title")) {
			thisTitle = this_href.getAttribute("title")
		}
	} else {
		wp_current_hyperlink = ''
	}
	var szURL= wp_directory +  "dialog_frame.php?target="+thisTarget+"&title="+thisTitle+"&lang="+obj.instance_lang+"&window="+wp_directory+"hyperlink.php"
	linkwin = obj.openDialog (szURL ,'modal',650,394)
}
// link to a document
function wp_open_document_window(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	var sel = obj.edit_object.document.selection.createRange()
	if ((sel.text == '') && (!wp_isInsideLink(obj,sel))) {
		alert(obj.lng['select_hyperlink_text'])
		return
	}
	// check if hyperlink exists and if so populate the link dialog so that user can edit the existing link
	if (obj.edit_object.document.selection.type == "Control") {
		if (sel(0).tagName == "IMG") {
			this_href = sel(0).parentNode
		} else {
			return
		}
	} else {	
		this_href = sel.parentElement()
	}
	while(this_href.tagName!="A"&&this_href.tagName!="HTML") {
			this_href = this_href.parentElement
	}
	if (this_href.tagName == "A") {
		var thisLink = this_href.getAttribute("HREF")
		if (thisLink.search("WP_BOOKMARK#") != -1) {
			var thisLinkArray = thisLink.split("#")
			wp_current_hyperlink = "#"+thisLinkArray[1]
		} else {
			wp_current_hyperlink = thisLink
		}
	} else {
		wp_current_hyperlink = ''
	}
	var szURL= wp_directory + "dialog_frame.php?window="+wp_directory+"document.php&instance_doc_dir="+obj.instance_doc_dir+"&lang="+obj.instance_lang
	docwin = obj.openDialog(szURL ,'modal',730,466)
}
// this creates the hyperlink html from data sent from the hyperlink window
function wp_hyperlink(obj,iHref,iTarget,iTitle) {
	// if no link data sent then unlink any existing link
	if (iHref=="") { 
			wp_callFormatting(obj, "Unlink")
			obj.edit_object.focus()
			return
	} else if(iHref=="file://") { 
			wp_callFormatting(obj, "Unlink")
			obj.edit_object.focus()
			return
	} else if(iHref=="http://") { 
			wp_callFormatting(obj, "Unlink")
			obj.edit_object.focus()
			return
	} else if(iHref=="https://") { 
			wp_callFormatting(obj, "Unlink")
			return
	} else if(iHref=="mailto:") { 
			wp_callFormatting(obj, "Unlink")
			obj.edit_object.focus()
			return
	} else { 
		if (iHref.substring(0,1) == "#") {
			iHref="WP_BOOKMARK"+iHref
		}
		var sel = obj.edit_object.document.selection.createRange()
		// create link
		sel.execCommand("CreateLink",false,iHref)
		// add target inf
		if (obj.edit_object.document.selection.type == "Control") {
			var sel = obj.edit_object.document.selection.createRange()
			if (sel(0).tagName == "IMG") {
				this_href = sel(0).parentNode
				sel(0).border = 0
			}
		} else {
			this_href = sel.parentElement()
		}
		while(this_href.tagName!="A"&&this_href.tagName!="HTML") {
			this_href = this_href.parentElement
		}
		if (this_href.tagName == "A") {
			if (iTarget != '') {
				this_href.target=iTarget
			}
			if (iTitle != '') {
				this_href.title=iTitle
			}
		}
	}
	obj.edit_object.focus()
}
// insert image
function wp_open_image_window(obj,srcElement) {
	if (srcElement.className == "disabled") {
		return	
	}
	var szURL
	// detect if an image is selected and if it is populate image dialoge
	if (obj.edit_object.document.selection.type == "Control") {
		var sel = obj.edit_object.document.selection.createRange()
		if (sel(0).tagName == "IMG") {
			if ((sel(0).getAttribute('name')) && (sel(0).src.search(wp_directory+"/images/bookmark_symbol.gif") != -1)) {
				szURL= wp_directory + obj.imagewindow + "?lang="+obj.instance_lang
			} else {
				var image = sel(0).src
				var width = sel(0).getAttribute('width')
				var height = sel(0).getAttribute('height')
				var alt = sel(0).getAttribute('alt')
				var align = sel(0).getAttribute('align')
				var mtop = sel(0).style.marginTop 
				var mbottom = sel(0).style.marginBottom 
				var mleft = sel(0).style.marginLeft 
				var mright = sel(0).style.marginRight 
				var thisIHeight = sel(0).getAttribute('height')
				var iborder = sel(0).getAttribute('border')
				szURL= wp_directory + 'dialog_frame.php' + '?image=' + image +'&width=' + width +'&height=' + height + '&alt=' + alt + '&align=' + align + '&mtop=' + mtop + '&mbottom=' + mbottom + '&mleft=' + mleft + '&mright=' + mright + '&border=' + iborder + "&lang="+obj.instance_lang +"&window="+wp_directory+"imageoptions.php" 
			}
		} else {
			return
		}
	} else {
		szURL= wp_directory + 'dialog_frame.php?window=' + wp_directory + obj.imagewindow + "&instance_img_dir="+obj.instance_img_dir+"&lang="+obj.instance_lang 
	}
	imgwin = obj.openDialog(szURL ,'modal',730,466)
}
// create the image html
function wp_create_image_html(obj,iurl, iwidth, iheight, ialign, ialt, iborder, imargin) {
	if (iurl == ""){
		return
	}
	if (document.getElementById(obj.name+'_class_menu-text').innerHTML != "Class")
		document.getElementById(obj.name+'_class_menu-text').innerHTML = "Class"
	var sel = obj.edit_object.document.selection.createRange()
	sel.execCommand("InsertImage",false, iurl)
	if (obj.edit_object.document.selection.type == "Control") {
		var sel = obj.edit_object.document.selection.createRange()
		if (sel(0).tagName == "IMG") {
			if ((iwidth != '') && (iheight!='') && (iwidth != 0) && (iheight!=0) && (iheight!=null)) {
				sel(0).width = iwidth
				sel(0).height = iheight
			}
			if ((ialign != '') && (ialign!=0) && (ialign!=null)) {
				sel(0).align = ialign
			}
			if ((iborder != '') && (iborder!=null)) {
				sel(0).border = iborder
			}
			sel(0).alt = ialt
			sel(0).title = ialt
			if ((imargin != '') && (imargin!=null)) {
				sel(0).style.margin = imargin
			}
			sel(0).src = sel(0).src
		}
	}
	obj.edit_object.focus()
}
// create the horizontal rule html
function wp_create_hr(obj,code) {
	if (code == ""){
		return
	}
	var sel = obj.edit_object.document.selection.createRange()
	obj.edit_object.focus()
	sel.pasteHTML(code)
}
function wp_insert_code(obj,code) {
	if ((code != "") && (code != null)) {
		obj.edit_object.focus()
		if (obj.edit_object.document.selection.type == "Control") {
			obj.edit_object.document.execCommand('delete')
		}
		obj.edit_object.document.selection.createRange().pasteHTML(code)
	}
	if (obj.border_visible == 1) {
		wp_show_borders(obj)
	}
	obj.edit_object.focus()
}
function wp_open_bookmark_window(obj,srcElement) {	
	if (srcElement.className == "disabled") {
		return	
	}
	var arr = ''
	if (obj.edit_object.document.selection.type == "Control") {
		var sel = obj.edit_object.document.selection.createRange()
		if (sel(0).tagName == "IMG") {
			if ((sel(0).getAttribute('name')) && (sel(0).src.search(wp_directory+"/images/bookmark_symbol.gif") != -1)) {
				arr= sel(0).name
			}
		} 
	}
	bookwin = obj.openDialog(wp_directory + "bookmark.php?bookmark="+arr+"&lang="+obj.instance_lang, 'modal', 300, 106)
}
function wp_create_bookmark (obj,name) {
	if ((name != '') && (name!= null)) {
		wp_insert_code(obj,'<img name="'+name+'" src="' + wp_directory + '/images/bookmark_symbol.gif" contenteditable="false" width="16" height="13" alt="Bookmark: '+name+'" title="Bookmark: '+name+'" border="0">')
	}
}
////////////////////////////
// Table editing features //
////////////////////////////
// there is some really messy stuff below here!
// returns true if cursor is inside a table
function wp_isInsideTable(obj, sel) {
	if (sel == null) {
		sel = obj.edit_object.document.selection.createRange()
	}
	if (sel.type == "Control") {
		return false
	}
	if (!obj.edit_object.document.queryCommandEnabled("InsertHorizontalRule")) {
		return false
	}
	var thisTD = sel.parentElement()
	while(thisTD.tagName!="TD"&&thisTD.tagName!="HTML") {
			thisTD = thisTD.parentElement
	}
	if (thisTD.tagName == "TD") {
		return true
	} else {
		return false
	}
}
// finds the current table, row and cell and puts them in global variables that the other table functions and the table editing window can use.
// requires the current selection
function wp_getTable(obj, sel) {
	if (sel == null) {
		sel = obj.edit_object.document.selection.createRange()
	}
	wp_thisCell = sel.parentElement()
	while(wp_thisCell.tagName!="TD"&&wp_thisCell.tagName!="HTML") {
			wp_thisCell = wp_thisCell.parentElement
	}
	wp_thisRow = wp_thisCell
	while(wp_thisRow.tagName!="TR"&&wp_thisRow.tagName!="HTML") {
		wp_thisRow = wp_thisRow.parentElement
	}
	wp_thisTable = wp_thisRow
	while(wp_thisTable.tagName!="TABLE"&&wp_thisTable.tagName!="HTML") {
			wp_thisTable = wp_thisTable.parentElement
	}
}
// creates the table html for the insert table window
function wp_insertTable(obj,rows,cols,attrs) {
	obj.edit_object.focus()
	// generate column widths
	var tdwidth = 100/cols
	tdwidth +="%"
		var code = "<table" + attrs + ">\r\n"
		for (var i = 0; i < rows; i++) {
			code += "\t<tr>\r\n"
			for (var j = 0; j < cols; j++) {
				// spacers are autoinserted to ensure table displays as expected. column widths are also set.
				code += "\t\t<td valign=\"top\" width=\"" + tdwidth + "\">"+obj.tdInners+"</td>\r\n"
			}
			code += "\t</tr>\r\n"
		}
		code += "</table>\r\n"
	var sel = obj.edit_object.document.selection.createRange()
	sel.pasteHTML(code)
	if (obj.border_visible == 1) {
		wp_show_borders(obj)
	}
	obj.edit_object.focus()
}
/////////////////////////
// CSS style functions //
/////////////////////////
// these fucntions are still a little messey!!!!!!
// mouse over button style
function wp_m_over(element, obj) {
	if (obj.initfocus == false) {
		obj.edit_object.focus()
		obj.initfocus = true
	}
	var cmd = element.getAttribute("cid")
	if (element.className=="disabled") {
		return
	}
	if ((cmd=="edittable") || (cmd=="splitcell")) {
		cmd="inserthorizontalrule"
	}
	if (cmd == "border") {
		if (obj.border_visible) {
			element.className="latched"
			element.style.backgroundColor="threedface"
		} else {
			element.className="over"
			element.style.backgroundColor="threedface"
		}
		return
	} else if (cmd=="ignore") {
		element.className="over"
		element.style.backgroundColor="threedface"
		return	
	} else if ((cmd=="undo") ||  (cmd=="redo")) {
		element.className="over"
		element.style.backgroundColor="threedface"
		return
	} else if (obj.edit_object.document.queryCommandState(cmd)) {
		element.className="latched"
		element.style.backgroundColor = "threedface"
		return
	}
	element.className="over"
	element.style.backgroundColor = "threedface"
}
// mouse out button style
function wp_m_out(element, obj) {
	var cmd = element.getAttribute("cid")
	if (element.className=="disabled") {
		return
	}
	if ((cmd=="edittable") || (cmd=="splitcell")) {
		cmd="inserthorizontalrule"
	}
	if (cmd == "border") {
		if (obj.border_visible) {
			element.className="latched"
			element.style.backgroundColor="#eeeeee"
		} else {
			element.className="ready"
			element.style.backgroundColor="threedface"
		}
		return
	} else if (cmd=="ignore") {
		element.className="ready"
		element.style.backgroundColor = "threedface"
		return	
	} else if ((cmd=="undo") ||  (cmd=="redo")) {
		element.className="ready"
		element.style.backgroundColor="threedface"
		return
	} else if (!obj.edit_object.document.queryCommandEnabled(cmd)) {
		element.className="disabled"
		element.style.backgroundColor=""
		return
	} else if (obj.edit_object.document.queryCommandState(cmd)) {
		element.className="latched"
		element.style.backgroundColor = "#eeeeee"
		return
	}
	element.className="ready"
	element.style.backgroundColor = "threedface"
}
// mouse down button style
function wp_m_down(element) {
	if (element.className == "disabled") {
		return
	}
	element.className="down"
	element.style.backgroundColor = "threedface"
}
// mouse up button style
function wp_m_up(element) {
	var style=element.className
	if (style=="disabled") {
		return
	} else {
		if (style=="latched") {
			return
		}
	}
	element.className="over"
	element.style.backgroundColor = "threedface"
}
///////////////////////
// Set button states //
///////////////////////
// This changes the states of buttons everytime the selection changes, so that buttons that cannot be used based on the current user selection appear disabled.
// this is the crappiest slowest function in zeusedit, it really needs an overhaul!!
function wp_set_button_states(obj) {
	var sel=obj.edit_object.document.selection.createRange()
	sel.select()
	var inside_link = wp_isInsideLink(obj, sel)
	var inside_table = wp_isInsideTable(obj, sel)
	if (inside_table) {
		var
		wp_thisCell = sel.parentElement()
		while(wp_thisCell.tagName!="TD"&&wp_thisCell.tagName!="HTML") {
			wp_thisCell = wp_thisCell.parentElement
		}
	}	
	// evalute and set the toolbar button states
	for(var i = 0; i < obj.tbarlength; i++) {
		var pbtn = obj.tbarimages(i)
		if ((pbtn.type=="btn")) {
			var cmd = pbtn.getAttribute("cid")
			if (!obj.safe) {
				pbtn.className="disabled"
				pbtn.style.backgroundColor=""
			} else if ((cmd=="edittable") || (cmd == 'splitcell')) {
			// table editing buttons
				if (inside_table) {
					if (cmd == 'splitcell') {
						if ((wp_thisCell.rowSpan >= 2) || (wp_thisCell.colSpan >=2)) {
							pbtn.className="ready"
							pbtn.style.backgroundColor="threedface"
						} else {
							pbtn.className="disabled"
							pbtn.style.backgroundColor=""
						}
					} else {
						pbtn.className="ready"
						pbtn.style.backgroundColor="threedface"
					}
				} else {
					pbtn.className="disabled"
					pbtn.style.backgroundColor=""
				}
			} else if (cmd == "border") {
				if (obj.border_visible) {
					pbtn.className="latched"
					pbtn.style.backgroundColor="#eeeeee"
				} else {
					pbtn.className="ready"
					pbtn.style.backgroundColor="threedface"
				}
			} else if (cmd=="createlink") {
				if ((inside_link) || (sel.text != '')) {
					pbtn.className="ready"
					pbtn.style.backgroundColor="threedface"
				} else {
					pbtn.className="disabled"
					pbtn.style.backgroundColor=""
				}
			} else if ((cmd=="undo") ||  (cmd=="redo")) {
					pbtn.className="ready"
					pbtn.style.backgroundColor="threedface"
			} else if (obj.edit_object.document.queryCommandState(cmd)) {
				pbtn.className="latched"
				pbtn.style.backgroundColor="#eeeeee"
			} else if (!obj.edit_object.document.queryCommandEnabled(cmd)) {
				pbtn.className="disabled"
				pbtn.style.backgroundColor=""
			} else {	
				pbtn.className="ready"
				pbtn.style.backgroundColor="threedface"
			}
		}
	}
	var font_face_value = obj.edit_object.document.queryCommandValue('FontName')
	var font_size_value = obj.edit_object.document.queryCommandValue('FontSize')
	var format_list_value = obj.edit_object.document.queryCommandValue('FormatBlock')
	if (obj.edit_object.document.selection.type == "Control") {
		var class_menu_value = ''
	} else {
		var foo = sel.parentElement()
		while(!foo.className&&foo.tagName!="HTML") {
			foo = foo.parentElement
		}
		var class_menu_value = foo.className
	}
	var font_face_text = document.getElementById(obj.name+'_font-face-text')
	if (font_face_value) {
		if (font_face_text.innerHTML != font_face_value) 
			font_face_text.innerHTML = font_face_value
	} else {
		if (font_face_text.innerHTML != obj.lng['font'])
			font_face_text.innerHTML = obj.lng['font']
	}
	var font_size_text = document.getElementById(obj.name+'_font_size-text')
	if (font_size_value) {
		if (font_size_text.innerHTML != font_size_value)
			font_size_text.innerHTML = font_size_value
	} else {
		if (font_size_text.innerHTML != obj.lng['size'])
			font_size_text.innerHTML = obj.lng['size']
	}
	var format_list_text = document.getElementById(obj.name+'_format_list-text')
	if (format_list_value) {
		if (format_list_text.innerHTML != format_list_value)
			format_list_text.innerHTML = format_list_value
	} else {
		if (format_list_text.innerHTML = obj.lng['format'])
			format_list_text.innerHTML = obj.lng['format']
	}
	var class_menu_text = document.getElementById(obj.name+'_class_menu-text')
	if (class_menu_value) {
		if (class_menu_value == "wp_none") {
			if (class_menu_text.innerHTML != obj.lng['class'])
				class_menu_text.innerHTML = obj.lng['class']
		} else if (class_menu_text.innerHTML != class_menu_value) {
			class_menu_text.innerHTML = class_menu_value
		}
	} else {
		if (class_menu_text.innerHTML != obj.lng['class'])
			class_menu_text.innerHTML = obj.lng['class']
	}
}