// Purpose: functions specific to Gecko based browsers that support Midas
var wp_is_ie50 = false;
var wp_isMidasEnabled = false;
function wp_editor(obj,config) {
	// strings:
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
	// integers
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
	obj.html_edit_area = document.getElementById(obj.name)
	obj.format_list=document.getElementById(obj.name+'_format_list')
	obj.font_face=document.getElementById(obj.name+'_font_face')
	obj.font_size=document.getElementById(obj.name+'_font_size')
	obj.class_menu=document.getElementById(obj.name+'_class_menu')
	obj.foo = obj.html_edit_area.value
	obj.menu_frame = document.getElementById(obj.name+"_menu").contentWindow
	obj.edit_object = document.getElementById(obj.name+'_editFrame').contentWindow
	obj.editFrame = obj.edit_object
	obj.previewFrame = document.getElementById(obj.name+"_previewFrame").contentWindow
	if (obj.stylesheet != '') {
		str = obj.baseURL + '<link rel="stylesheet" href="' + obj.stylesheet + '" type="text/css">'
	} else {
		str = obj.baseURL
	}
	try {
		obj.edit_object.document.open()
	} catch (e) {
		wp_isMidasEnabled = false
		wp_fail(obj)
		return
	}
	obj.edit_object.document.write(str)
	obj.edit_object.document.close()
	if (obj.html_edit_area.value.search(/<body/gi) != -1) {
		obj.snippit = false
	} else {
		obj.snippit = true
	}
 	kids = document.getElementsByTagName('IMG')
	for (var i=0; i < kids.length; i++) {
	if (kids[i].className == "toolButton") {
		kids[i].onmouseover = wp_m_over
		kids[i].onmouseout = wp_m_out
		kids[i].onmousedown = wp_m_down
		kids[i].onmouseup = wp_m_up
		}
	}
	if (document.getElementById(obj.name+'_wp_save')) {
		save = document.getElementById(obj.name+'_wp_save')
		save.onmouseover = wp_m_over
		save.onmouseout = wp_m_out
		save.onmousedown = wp_m_down
		save.onmouseup = wp_m_up
	}
	wp_load_data(obj.name)
}
function wp_load_data(name) {
	var obj = document.getElementById(name)
	if (obj.edit_object.document.body) {
		wp_send_to_edit_object(obj)
	} else {
		setTimeout("wp_load_data('"+name+"')",100)
	}
}
function wp_next(obj) {
	wp_send_to_edit_object(obj)
}
function wp_fail(obj) {
	var tab_one = document.getElementById(obj.name+'_tab_one')
	var tab_two = document.getElementById(obj.name+'_tab_two')
	var tab_three = document.getElementById(obj.name+'_tab_three')
	var tab_table = document.getElementById(obj.name+'_tab_table')
	tab_one.style.display = "none"
	tab_two.style.display = "block"
	tab_three.style.display = "none"
	obj.html_edit_area.style.visibility = "visible"
	tab_table.style.display="none"		
	obj.html_edit_area.value = obj.foo
	obj.html_mode=true
	document.getElementById(obj.name+'_load_message').style.display ='none'
	if (obj.subsequent == false) {
		alert(obj.lng['upgrade'])
	}
	return true
}
function wp_insertNodeAtSelection(win, insertNode) {
	var sel = win.getSelection()
	var range = sel.getRangeAt(0)
	sel.removeAllRanges()
	range.deleteContents()
	var container = range.startContainer
	var pos = range.startOffset
	range=document.createRange()
	if (container.nodeType==3 && insertNode.nodeType==3) {
		container.insertData(pos, insertNode.nodeValue)
		range.setEnd(container, pos+insertNode.length)
		range.setStart(container, pos+insertNode.length)
	} else {
		var afterNode
		if (container.nodeType==3) {
			var textNode = container
			container = textNode.parentNode
			var text = textNode.nodeValue
			var textBefore = text.substr(0,pos)
			var textAfter = text.substr(pos)
			var beforeNode = document.createTextNode(textBefore)
			var afterNode = document.createTextNode(textAfter)
			container.insertBefore(afterNode, textNode)
			container.insertBefore(insertNode, afterNode)
			container.insertBefore(beforeNode, insertNode)
			container.removeChild(textNode)
		} else {
			afterNode = container.childNodes[pos]
			container.insertBefore(insertNode, afterNode)
		}
		if (insertNode.tagName) {
			if (insertNode.tagName == 'IMG') {
				range.selectNode(insertNode)
			} else {
				range.setEnd(afterNode, 0)
				range.setStart(afterNode, 0)
			}
		} else {
			range.setEnd(afterNode, 0)
			range.setStart(afterNode, 0)
		}		
	}
	sel.addRange(range)
	win.focus()
}
// functions for sending html between edit_object and the textarea
function wp_send_to_html(obj) {
	var str1 = obj.edit_object.document.body.innerHTML
	str1 = str1.replace(/\&nbsp;/gi, '<!-- WP_SPACEHOLDER -->');
	str1 = str1.replace(/<<(.*?)>>/gi, "<$1>")
	str1 = str1.replace(/<\/<(.*?)>>/gi, "</$1>")
	obj.edit_object.document.body.innerHTML = str1	
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
	str = str.replace(/ type=\"_moz\"/gi, '')
	str = str.replace(/ style=\"\"/gi, "")
	str = str.replace(/<\!-- WP_SPACEHOLDER -->/gi, '&nbsp;');
	str = str.replace(/<b>/gi, '<strong>');
	str = str.replace(/<b /gi, '<strong ');
	str = str.replace(/<\/b>/gi, '</strong>');
	str = str.replace(/<i>/gi, '<em>');
	str = str.replace(/<i /gi, '<em ');
	str = str.replace(/<\/i>/gi, '</em>');
	obj.html_edit_area.value = str 
	if (obj.html_mode==true) {
		obj.html_edit_area.focus()
	}
}
function wp_send_to_edit_object(obj) {
	obj.html_edit_area.value = wp_replace_bookmark (obj.html_edit_area.value) 
	var str = obj.html_edit_area.value
	str = str.replace(/<strong>/gi, '<b>');
	str = str.replace(/<strong /gi, '<b ');
	str = str.replace(/<\/strong>/gi, '</b>');
	str = str.replace(/<em>/gi, '<i>');
	str = str.replace(/<em /gi, '<i ');
	str = str.replace(/<\/em>/gi, '</i>');
	obj.html_edit_area.value = str
	if (obj.html_edit_area.value.search(/<body/gi) != -1) {
		obj.snippit = false
	} else {
		obj.snippit = true
	}
	if ((!obj.snippit) && (obj.html_edit_area.value != '')) {
		var str = obj.html_edit_area.value
		// we cannot write to the document again in Mozilla or we could crash the browser so we need to be creative.
		var htmlseparator = new RegExp("<html[^>]*?>","gi")
		var htmlsplit=str.split(htmlseparator)
		var bodyseparator = new RegExp("<body[^>]*?>","gi")
		var bodysplit=str.split(bodyseparator)
		var headsplit = str.split("<head>")
		var head = ''
		var html = ''
		var bodyc = ''
		if (headsplit.length>1) {
			var head2 = headsplit[1].split("</head>")
			head = head2[0]
		} 
		if (bodysplit.length>1) {
			var body2 = bodysplit[1].split("</body>")
			bodyc = body2[0]
		} 
		obj.edit_object.document.body.innerHTML = bodyc
		// head contents
		var headtag = obj.edit_object.document.getElementsByTagName('HEAD')
		if (obj.stylesheet != '') {
			var headcontent = obj.baseURL + '<link rel="stylesheet" href="' + obj.stylesheet + '" type="text/css">' + head
		} else {
			var headcontent = obj.baseURL + head
		}
		headtag[0].innerHTML = headcontent
	} else {
		obj.edit_object.document.body.innerHTML = obj.html_edit_area.value
	}
	if (obj.border_visible == 1) {
		obj.edit_object.document.onload =  wp_show_borders(obj)
	}
	// system check 1
	try {
		obj.edit_object.document.designMode = "on"
	} catch (e) {
		wp_isMidasEnabled = false
		wp_fail(obj)
		return
	}
	// system check 2
	try {
		obj.edit_object.document.execCommand("undo", false, null)
		wp_isMidasEnabled = true
	} catch (e) {
		wp_isMidasEnabled = false
		wp_fail(obj)
		return
	}
	obj.edit_object.document.execCommand("usecss", false, true)
	obj.styles = wp_make_styles (obj)
	if (obj.html_mode==false) {
		obj.edit_object.focus()
	}
	document.getElementById(obj.name+'_load_message').style.display ='none'
} 
// Catch and execute the commands sent from the buttons and tools
function wp_callFormatting(obj,sFormatString) {
	if (sFormatString == "CreateLink") {
	  var szURL = prompt("Enter a URL:", "")
		obj.edit_object.document.execCommand("CreateLink",false,szURL)
	} else {
		obj.edit_object.document.execCommand(sFormatString, false, null)
	}
	obj.edit_object.focus()
}
function wp_change_class(obj,classname) {	
	var sel = obj.edit_object.getSelection()
 	var range = sel.getRangeAt(0)
	var container = range.startContainer
	if (classname == 'wp_none') {	
		var foo = container.parentNode;
		while(!foo.className&&foo.tagName!="BODY") {
			foo = foo.parentNode;
		}
		if (foo.getAttribute('class') != 'wp_none' && foo.getAttribute('class') != '') {
			foo.className = classname;
		}
	}
	obj.edit_object.document.execCommand("FontName", false, 'wp_bogus_font')
	var spans = obj.edit_object.document.getElementsByTagName('SPAN')
	var fonts = obj.edit_object.document.getElementsByTagName('FONT')
	wp_set_class(spans, classname)
	wp_set_class(fonts, classname)
	wp_hide_menu(obj)
	obj.edit_object.focus()
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
function wp_isInsideLink(obj) {
	var sel = obj.edit_object.getSelection()
  var range = sel.getRangeAt(0)
	var container = range.startContainer
	if (container.nodeType != 1) {
		var textNode = container
    	container = textNode.parentNode
	}
	thisA = container
	while(thisA.tagName!="A"&&thisA.tagName!="BODY") {
			thisA = thisA.parentNode
	}
	if (thisA.tagName == "A") {
		return true
	} else {
		return false
	}
}
// lets try to make a custom hyperlink window!!
function wp_open_hyperlink_window(obj,srcElement) {
	var sel = obj.edit_object.getSelection()
 	var range = sel.getRangeAt(0)
	var container = range.startContainer
	if ((range == '') && (container.nodeType != 1) && (!wp_isInsideLink(obj))) {
		alert(obj.lng['select_hyperlink_text'])
		return
	}
	var thisTarget = ""
	var thisTitle = ""
	if (wp_isInsideLink(obj)) {
		var container = range.startContainer
		if (container.nodeType != 1) {
			var textNode = container
				container = textNode.parentNode
		}
		thisA = container
		while(thisA.tagName!="A"&&thisA.tagName!="BODY") {
				thisA = thisA.parentNode
		}
		if (thisA.tagName == "A") {
			var thisLink = thisA.getAttribute("HREF")
			if (thisLink.search("WP_BOOKMARK#") != -1) {
				var thisLinkArray = thisLink.split("#")
				wp_current_hyperlink = "#"+thisLinkArray[1]
			} else {
				wp_current_hyperlink = thisLink
			}
			if (thisA.getAttribute("target")) {
				thisTarget = thisA.getAttribute("target")
			}
			if (thisA.getAttribute("title")) {
				thisTitle = thisA.getAttribute("title")
			}
		} else {
			wp_current_hyperlink = ''
		}
	} else {
		wp_current_hyperlink = ''
	}
	var szURL=wp_directory + "hyperlink.php?target="+thisTarget+"&title="+thisTitle + "&lang="+obj.instance_lang
	linkwin = obj.openDialog(szURL ,"modal",650,396)
}
// link to a document
function wp_open_document_window(obj,srcElement) {
	var sel = obj.edit_object.getSelection()
 	var range = sel.getRangeAt(0)
	var container = range.startContainer
	if ((range == '') && (container.nodeType != 1) && (!wp_isInsideLink(obj))) {
		alert(obj.lng['select_hyperlink_text'])
		return
	}
	if (wp_isInsideLink(obj)) {
		var container = range.startContainer
		if (container.nodeType != 1) {
			var textNode = container
				container = textNode.parentNode
		}
		thisA = container
		while(thisA.tagName!="A"&&thisA.tagName!="BODY") {
				thisA = thisA.parentNode
		}
		if (thisA.tagName == "A") {
			var thisLink = thisA.getAttribute("HREF")
			if (thisLink.search("WP_BOOKMARK#") != -1) {
				var thisLinkArray = thisLink.split("#")
				wp_current_hyperlink = "#"+thisLinkArray[1]
			} else {
				wp_current_hyperlink = thisLink
			}
		} else {
			wp_current_hyperlink = ''
		}
	} else {
		wp_current_hyperlink = ''
	}
	var szURL=wp_directory + "document.php?instance_doc_dir="+obj.instance_doc_dir+"&lang="+obj.instance_lang
	docwin = obj.openDialog(szURL ,"modal",730,466)
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
			obj.edit_object.focus()
			return
	} else if(iHref=="mailto:") { 
			wp_callFormatting(obj, "Unlink")
			obj.edit_object.focus()
			return
	} else { 
		if (iHref.substring(0,1) == "#") {
			iHref="WP_BOOKMARK"+iHref
		}
		var range = obj.edit_object.getSelection().getRangeAt(0)
		var container = range.startContainer
		var pos = range.startOffset
		var imageNode = null
		if (container.tagName) {
		var images = container.getElementsByTagName('IMG');
		var cn = container.childNodes
			if (cn[pos].tagName == 'IMG') {
				cn[pos].setAttribute('border', 0);
			}
		}
		if (wp_isInsideLink(obj)) {
			var sel = obj.edit_object.getSelection()
			var range = sel.getRangeAt(0)
			var container = range.startContainer
			if (container.nodeType != 1) {
				var textNode = container
				container = textNode.parentNode
			}
			thisA = container
			while(thisA.tagName!="A"&&thisA.tagName!="BODY") {
					thisA = thisA.parentNode
			}
			if (thisA.tagName == "A") {
				thisA.setAttribute('href',iHref)
				thisA.setAttribute('target',iTarget)
				thisA.setAttribute('title',iTitle)
			} 
		} else {
			obj.edit_object.document.execCommand("CreateLink",false,'WP_TEMP_LINK_'+iHref)
			var links = obj.edit_object.document.getElementsByTagName('A')
			var l=links.length
			for (var i=0; i < l; i++) {
				if (links[i].getAttribute('href')) {
					if (links[i].getAttribute('href').search('WP_TEMP_LINK_') != -1) {
						links[i].setAttribute('href',iHref)
						if (iTitle != '') {
							links[i].setAttribute('title',iTitle)
						}
						if (iTarget != '') {
							links[i].setAttribute('target',iTarget)
						}
						var sel = obj.edit_object.getSelection()
						var range = sel.getRangeAt(0)
						sel.removeAllRanges()
						break
					}
				}
			}
		}
	}
	obj.edit_object.focus()
}
// insert image
function wp_open_image_window(obj) {
	var szURL
	var range = obj.edit_object.getSelection().getRangeAt(0)
	var container = range.startContainer
	var pos = range.startOffset
	var imageNode = null
	if (container.tagName) {
		var images = container.getElementsByTagName('IMG');
		var cn = container.childNodes
		if (cn[pos].tagName == 'IMG') {
			imageNode = cn[pos]
		}
	}
	if (imageNode) {
		if ((imageNode.getAttribute('name')) && (imageNode.src.search(wp_directory+"/images/bookmark_symbol.gif") != -1)) {
			szURL= wp_directory + obj.imagewindow + "?lang="+obj.instance_lang
		} else {
			var image = imageNode.src
			var width = imageNode.getAttribute('width')
			var height = imageNode.getAttribute('height')
			var alt = imageNode.getAttribute('alt')
			var align = imageNode.getAttribute('align')
			var mtop = imageNode.style.marginTop 
			var mbottom = imageNode.style.marginBottom 
			var mleft = imageNode.style.marginLeft 
			var mright = imageNode.style.marginRight 
			var thisIHeight = imageNode.getAttribute('height')
			var iborder = imageNode.getAttribute('border')
			szURL= wp_directory + 'imageoptions.php' + '?image=' + image +'&width=' + width +'&height=' + height + '&alt=' + alt + '&align=' + align + '&mtop=' + mtop + '&mbottom=' + mbottom + '&mleft=' + mleft + '&mright=' + mright + '&border=' + iborder + "&lang="+obj.instance_lang 
		}
	} else {
		szURL= wp_directory + obj.imagewindow + "?instance_img_dir="+obj.instance_img_dir+"&lang="+obj.instance_lang 
	}
	imgwin = obj.openDialog(szURL ,"modal",730,466)
}
// create the image html
function wp_create_image_html(obj, iurl, iwidth, iheight, ialign, ialt, iborder, imargin) {
	if (iurl == ""){
		return
	}
	obj.edit_object.focus()
	img = obj.edit_object.document.createElement("img")
	img.setAttribute("src", iurl)
	if ((iwidth != '') && (iheight!='') && (iwidth != 0) && (iheight!=0) && (iheight!=null)) {
		img.setAttribute("width", iwidth)
		img.setAttribute("height", iheight)
	}
	if ((ialign != '') && (ialign!=0) && (ialign!=null)) {
		img.setAttribute("align", ialign)
	}
	if ((iborder != '') && (iborder!=null)) {
		img.setAttribute("border", iborder)
	}
	img.setAttribute("alt", ialt)
	img.setAttribute("title", ialt)
	if ((imargin != '') && (imargin!=null)) {
		img.setAttribute("style", 'margin:'+imargin)
	}
	img.src=img.src
	wp_insertNodeAtSelection(obj.edit_object, img)
	imgwin.close()
	obj.edit_object.focus()
}
// create the horizontal rule html
function wp_create_hr(obj,align, color, size, width,percent2) {
	obj.edit_object.focus()
	hr = obj.edit_object.document.createElement("hr")
	if (align!='') {
		hr.setAttribute("align", align)
	}	
 	if (color != "") {
 		hr.setAttribute("color", color)
		hr.style.backgroundColor = color
		hr.setAttribute("noshade", "noshade")
	} 
	if (size != "") {
 		hr.setAttribute("size", size)
	}
	if (width != "") {
 		hr.setAttribute("width", width+percent2)
	}
	wp_insertNodeAtSelection(obj.edit_object, hr)
	obj.edit_object.focus()
}
function wp_insert_code(obj,code) {
	if ((code != "") && (code != null)) {
		obj.edit_object.focus()
		span = obj.edit_object.document.createElement("SPAN")
		span.innerHTML = code
		wp_insertNodeAtSelection(obj.edit_object, span)
	}
	if (obj.border_visible == 1) {
		wp_show_borders(obj)
	}
	obj.edit_object.focus()
}
function wp_open_bookmark_window(obj,srcElement) {	
	var szURL
	var range = obj.edit_object.getSelection().getRangeAt(0)
	var container = range.startContainer
	var pos = range.startOffset
	var imageNode = null
	var arr= ''
	if (container.tagName) {
		var images = container.getElementsByTagName('IMG')
		var cn = container.childNodes
		if (cn[pos].tagName == 'IMG') {
			imageNode = cn[pos]
			if ((imageNode.getAttribute('name')) && (imageNode.src.search(wp_directory+"/images/bookmark_symbol.gif") != -1)) {
				arr = imageNode.name
			}
		}
	}
	bookwin = obj.openDialog(wp_directory + "bookmark.php?bookmark="+arr+"&lang="+obj.instance_lang, "modal", 300, 106)
}
function wp_create_bookmark (obj,name) {
	if ((name != '') && (name!= null)) {
		img = obj.edit_object.document.createElement("img")
		img.setAttribute('src', wp_directory + '/images/bookmark_symbol.gif')
		img.setAttribute('name', name)
		img.setAttribute('width', 16)
		img.setAttribute('height', 13)
		img.setAttribute('alt', 'Bookmark: ' + name)
		img.setAttribute('title', 'Bookmark: ' + name)
		img.setAttribute('border', 0)
		wp_insertNodeAtSelection(obj.edit_object, img)
	}
}
////////////////////////////
// Table editing features //
////////////////////////////
// there is some really messy stuff below here!
// returns true if cursor is inside a table
function wp_isInsideTable(obj) {
	var sel = obj.edit_object.getSelection()
  var range = sel.getRangeAt(0)
	var container = range.startContainer
	if (container.nodeType != 1) {
		var textNode = container
    	container = textNode.parentNode
	}
	thisTD = container
	while(thisTD.tagName!="TD"&&thisTD.tagName!="BODY") {
			thisTD = thisTD.parentNode
	}
	if (thisTD.tagName == "TD") {
		return true
	} else {
		return false
	}
}
// finds the current table, row and cell and puts them in global variables that the other table functions and the table editing window can use.
// requires the current selection
function wp_getTable(obj) {
	var sel = obj.edit_object.getSelection()
 	var range = sel.getRangeAt(0)
	var container = range.startContainer
	if (container.nodeType != 1) {
		var textNode = container
    	container = textNode.parentNode
	}
	wp_thisCell = container
	while(wp_thisCell.tagName!="TD"&&wp_thisCell.tagName!="BODY") {
		wp_thisCell = wp_thisCell.parentNode
	}
	wp_thisRow = wp_thisCell
	while(wp_thisRow.tagName!="TR"&&wp_thisRow.tagName!="BODY") {
		wp_thisRow = wp_thisRow.parentNode
	}
	wp_thisTable = wp_thisRow
	while(wp_thisTable.tagName!="TABLE"&&wp_thisTable.tagName!="BODY") {
			wp_thisTable = wp_thisTable.parentNode
	}
}
// edit table window
// creates the table html for the insert table window
function wp_insertTable(obj,rows,cols,width,percent1, height,percent2,  border, bordercolor, bgcolor, cellpadding, cellspacing, bCollapse) {
	//edit_object.focus()
		// generate column widths
	table = obj.edit_object.document.createElement("table")
	if (border!='') {
		table.setAttribute("border", border)
	}	
 	if (bordercolor != "") {
 		table.setAttribute("bordercolor", bordercolor)
	} 
	if (cellpadding != "") {
 		table.setAttribute("cellpadding", cellpadding)
	}
	if (cellspacing != "") {
 		table.setAttribute("cellspacing", cellspacing)
	}
 	if (bgcolor != "") {
 		table.setAttribute("bgcolor", bgcolor)
	}
 	if (width != "") {
 		table.setAttribute("width", width+percent1)
	}
 	if (height != "") {
 		table.setAttribute("height", height+percent2)
	}
 	if (bCollapse == true) {
 		table.style.borderCollapse = "collapse"
	}
	var tdwidth = 100/cols
	tdwidth +="%"
	for (var i = 0; i < rows; i++) {
		row = obj.edit_object.document.createElement("tr")
    for (var j = 0; j < cols; j++) {
			cell = obj.edit_object.document.createElement("td")
			cell.setAttribute("valign", 'top')
			cell.setAttribute("width", tdwidth)
			cell.innerHTML = obj.tdInners
			row.appendChild(cell)
  	}
    table.appendChild(row)
	}
	obj.edit_object.focus()
	wp_insertNodeAtSelection(obj.edit_object, table)
	if (obj.border_visible == 1) {
		wp_show_borders(obj)
	}
	wp_send_to_html(obj)
	wp_send_to_edit_object(obj)
}
/////////////////////////
// CSS style functions //
/////////////////////////
// mouse over button style
function wp_m_over() {
	this.className = "over"
}
// mouse out button style
function wp_m_out() {
	this.className = "ready"
}
// mouse down button style
function wp_m_down() {
	this.className = "down"
}
// mouse up button style
function wp_m_up() {
	this.className = "over"
}
function wp_set_button_states() {
	return true
}