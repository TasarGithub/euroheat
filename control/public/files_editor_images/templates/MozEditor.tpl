<link rel="stylesheet" href="##directory##editor_theme.css" type="text/css" />
<!-- end 1st instance only -->
<div id="##name##_load_message" style="display:block; position:absolute; z-index:1000; text-align: left"> 
	<table width="##width##" height="##height2##">
		<tr> 
			<td align="center" valign="middle"><div style="background-color:#666666; border:1px solid #333333; padding: 10px; width: 100px; color:#FFFFFF; font-family:verdana,arial,helvetica,sans-serif; font-size:12px; font-weight:bold">##please_wait##&nbsp;</div></td>
		</tr>
	</table>
</div>
<!-- begin 1st instance only -->
<script language="JavaScript" type="text/javascript" src="##directory##js/MozScript.js"></script>
<script language="JavaScript" type="text/javascript" src="##directory##js/editorShared.js"></script>
<script language="JavaScript" type="text/javascript" src="##directory##js/dialogEditorShared.js"></script>
<script language="JavaScript" type="text/javascript">
<!--//
var wp_directory = '##directory##';
//-->
</script>
<!-- end 1st instance only -->
<script language="JavaScript" type="text/javascript">
<!--// Hide
var config_##name## = new wp_config(); 
config_##name##.name = '##name##';
config_##name##.instance_lang = '##instance_lang##';
config_##name##.encoding = '##encoding##';
config_##name##.xhtml_lang = '##xhtml_lang##';
config_##name##.useXHTML = ##usexhtml##;
config_##name##.baseURLurl = '##baseURLurl##';
config_##name##.baseURL = '##baseURL##';
config_##name##.instance_img_dir = '##instance_img_dir##';
config_##name##.instance_doc_dir = '##instance_doc_dir##';
//<!-- begin removeserver -->
config_##name##.domain1 = new RegExp("(href=|src=|action=)\"##domain##","gi");
config_##name##.domain2 = new RegExp("(href=|src=|action=)\"##domain2##","gi");
//<!-- end removeserver -->
config_##name##.stylesheet = '##stylesheet##'
config_##name##.imagewindow = "##imgwindow##";
config_##name##.links = '##links##';
config_##name##.custom_inserts = '##custom_objects##';
config_##name##.directory = '##directory##';
config_##name##.usep = ##usep##;
config_##name##.showbookmarkmngr = ##showbookmarkmngr##;
config_##name##.subsequent = ##subsequent##;
config_##name##.color_swatches = '##color_swatches##';
// lang
config_##name##.lang['guidelines_hidden'] = '##guidelines_hidden##';
config_##name##.lang['guidelines_visible'] = '##guidelines_visible##';
config_##name##.lang['place_cursor_in_table'] = '##place_cursor_in_table##';
config_##name##.lang['only_split_merged_cells'] = '##only_split_merged_cells##';
config_##name##.lang['no_cell_right'] = '##no_cell_right##';
config_##name##.lang['different_row_spans'] = '##different_row_spans##';
config_##name##.lang['no_cell_below'] = '##no_cell_below##';
config_##name##.lang['different_column_spans'] = '##different_column_spans##';
config_##name##.lang['select_hyperlink_text'] = '##select_hyperlink_text##';
config_##name##.lang['upgrade'] = '##upgrade##';
config_##name##.lang['format'] = '##format##';
config_##name##.lang['font'] = '##font##';
config_##name##.lang['class'] = '##class##';
config_##name##.lang['size'] = '##size##';
// End -->
</script>
<table id="##name##_container" width="##width##" height="##absheight##" style="border: 1px solid threedshadow; background-color: threedface; table-layout:fixed;" border="0" cellspacing="0" cellpadding="5">
	<tr> 
		<td> <div id="##name##_tab_one" style="display:block;"> 
				<table class="mozToolbar" style="height:24px" border="0" cellpadding="0" cellspacing="0">
					<tr> ##savebutton## 
						<!-- begin print -->
						<td><img class="toolButton" width="22" height="22" onClick="##name##.edit_object.print()" alt="" title="##print##" src="##directory##images/print.gif" /></td>
						<!-- end print -->
						<!-- begin find -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_findit(##name##);" alt="" title="##find_and_replace##" src="##directory##images/find.gif" /></td>
						<!-- end find -->
						<!-- begin spacer1 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer1 -->
						<!-- begin pasteword -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_paste_word_html(##name##);" alt="" title="##paste_word##" src="##directory##images/pasteword.gif" /></td>
						<!-- end pasteword -->
						<!-- begin spacer2 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer2 -->
						<!-- begin undo -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'undo');" alt="" title="##undo##" src="##directory##images/undo.gif" /></td>
						<!-- end undo -->
						<!-- begin redo -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'redo');" alt="" title="##redo##" src="##directory##images/redo.gif" /></td>
						<!-- end redo -->
						<!-- begin spacer3 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer3 -->
						<!-- begin tbl -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_open_table_window(##name##,this);" alt="" title="##insert_table##" src="##directory##images/instable.gif" /></td>
						<!-- begin edittable -->
						<td><img class="toolButton" src="##directory##images/edittable.gif" width="22" height="22" alt="" title="##table_properties##" onClick="wp_open_table_editor(##name##);" /></td>
						<td><img class="toolButton" src="##directory##images/insrow.gif" width="22" height="22" alt="" title="##add_row##" onClick="wp_processRow(##name##,'choose');" /></td>
						<td><img class="toolButton" src="##directory##images/delrow.gif" width="22" height="22" alt="" title="##delete_row##" onClick="wp_processRow(##name##,'remove');" /></td>
						<td><img class="toolButton" src="##directory##images/inscol.gif" width="22" height="22" alt="" title="##insert_column##" onClick="wp_processColumn(##name##,'choose');" /></td>
						<td><img class="toolButton" src="##directory##images/delcol.gif" width="22" height="22" alt="" title="##delete_column##" onClick="wp_processColumn(##name##,'remove');" /></td>
						<td><img class="toolButton" src="##directory##images/mrgcell.gif" width="22" height="22" alt="" title="##merge_cell##" onClick="wp_mergeCell(##name##);" /></td>
						<td><img class="toolButton" src="##directory##images/spltcell.gif" width="22" height="22" alt="" title="##unmerge_cell##" onClick="wp_splitCell(##name##);" /></td>
						<!-- end edittable -->
						<!-- end tbl -->
						<!-- begin spacer4 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer4 -->
						<!-- begin image -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_open_image_window(##name##,this);" alt="" title="##insert_image##" src="##directory##images/image.gif" /></td>
						<!-- end image -->
						<!-- begin smiley -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_insert_smiley(##name##,this);" alt="" title="##insert_emoticon##" src="##directory##images/smiley.gif" /></td>
						<!-- end smiley -->
						<!-- begin ruler -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_open_horizontal_rule_window(##name##,this);" alt="" title="##horizontal_line##" src="##directory##images/icon_rule.gif" /></td>
						<!-- end ruler -->
						<!-- begin link -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_##hyperlink_function##;" alt="" title="##insert_hyperlink##" src="##directory##images/link.gif" /></td>
						<!-- end link -->
						<!-- begin document -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_open_document_window(##name##,this);" alt="" title="##document_link##" src="##directory##images/doc_link.gif" /></td>
						<!-- end document -->
						<!-- begin bookmark -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_open_bookmark_window(##name##,this);" alt="" title="##insert_bookmark##" src="##directory##images/bookmark.gif" type="btn" /></td>
						<!-- end bookmark -->
						<!-- begin special -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_open_special_characters_window(##name##,this);" alt="" title="##special_characters##" src="##directory##images/specialchar.gif" /></td>
						<!-- end special -->
						<!-- begin custom -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_custom_object(##name##,this);" alt="" title="##insert_object##" src="##directory##images/custom.gif" /></td>
						<!-- end custom -->
					</tr>
				</table>
				<table class="mozToolbar" style="height:24" border="0" cellpadding="0" cellspacing="0">
					<tr> 
						<td> <table id="##name##_format_list" style="border: 1px solid #ffffff; font: 11px Verdana; width: 80px; margin-right:2px; ##format_list_style##" border="0" cellspacing="0" cellpadding="0" onClick="wp_show_menu(##name##,'format', this)" onMouseOver="this.style.border = '1px solid highlight'" onMouseOut="this.style.border = '1px solid #ffffff'" title="##paragraph_format##">
								<tr> 
									<td width="70"><div id="##name##_format_list-text" style="background-color:white; width:70px; height:14px; overflow:hidden">##format##</div></td>
									<td width="10"><img src="##directory##images/down_arrow.gif" width="10" height="14" alt="" /></td>
								</tr>
							</table></td>
						<td> <table id="##name##_class_menu" style="border: 1px solid #ffffff; font: 11px Verdana; width: 80px; margin-right:2px; ##class_list_style##" border="0" cellspacing="0" cellpadding="0" onClick="wp_show_menu(##name##,'class', this)" onMouseOver="this.style.border = '1px solid highlight'" onMouseOut="this.style.border = '1px solid #ffffff'" title="##style_class##">
								<tr> 
									<td width="70"><div id="##name##_class_menu-text" style="background-color:white; width:70px; height:14px; overflow:hidden">##class##</div></td>
									<td width="10"><img src="##directory##images/down_arrow.gif" width="10" height="14" alt="" /></td>
								</tr>
							</table></td>
						<td> <table id="##name##_font-face" style="border: 1px solid #ffffff; font: 11px Verdana; width: 80px; margin-right:2px; ##font_list_style##" border="0" cellspacing="0" cellpadding="0" onClick="wp_show_menu(##name##,'font', this)" onMouseOver="this.style.border = '1px solid highlight'" onMouseOut="this.style.border = '1px solid #ffffff'" title="##font_face##">
								<tr> 
									<td width="70"><div id="##name##_font-face-text" style="background-color:white; width:70px; height:14px; overflow:hidden">##font##</div></td>
									<td width="10"><img src="##directory##images/down_arrow.gif" width="10" height="14" alt="" /></td>
								</tr>
							</table></td>
						<td> <table id="##name##_font_size" style="border: 1px solid #ffffff; font: 11px Verdana; width: 40px; margin-right:2px; ##size_list_style##" border="0" cellspacing="0" cellpadding="0" onClick="wp_show_menu(##name##,'size', this)" onMouseOver="this.style.border = '1px solid highlight'" onMouseOut="this.style.border = '1px solid #ffffff'" title="##font_size##">
								<tr> 
									<td width="30"><div id="##name##_font_size-text" style="background-color:white; width:30px; height:14px; overflow:hidden">##size##</div></td>
									<td width="10"><img src="##directory##images/down_arrow.gif" width="10" height="14" alt="" /></td>
								</tr>
							</table></td>
						<!-- begin spacer5 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer5 -->
						<!-- begin bold -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'bold');" alt="" title="##bold##" src="##directory##images/bold.gif" /></td>
						<!-- end bold -->
						<!-- begin italic -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'italic');" alt="" title="##italic##" src="##directory##images/italic.gif" /></td>
						<!-- end italic -->
						<!-- begin underline -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'underline');" alt="" title="##underline##" src="##directory##images/under.gif" /></td>
						<!-- end underline -->
						<!-- begin spacer6 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer6 -->
						<!-- begin left -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'justifyleft');" alt="" title="##align_left##" src="##directory##images/left.gif" /></td>
						<!-- end left -->
						<!-- begin center -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'justifycenter');" alt="" title="##align_center##" src="##directory##images/center.gif" /></td>
						<!-- end center -->
						<!-- begin right -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'justifyright');" alt="" title="##align_right##" src="##directory##images/right.gif" /></td>
						<!-- end right -->
						<!-- begin full -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'justifyfull');" alt="" title="##justify##" src="##directory##images/justify.gif" /></td>
						<!-- end full -->
						<!-- begin spacer7 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer7 -->
						<!-- begin ol -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'insertorderedlist');" alt="" title="##numbering##" src="##directory##images/numlist.gif" /></td>
						<!-- end ol -->
						<!-- begin ul -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'insertunorderedlist');" alt="" title="##bullets##" src="##directory##images/bullist.gif" /></td>
						<!-- end ul -->
						<!-- begin outdent -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'outdent');" alt="" title="##decrease_indent##" src="##directory##images/deindent.gif" /></td>
						<!-- end outdent -->
						<!-- begin indent -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_callFormatting(##name##,'indent');" alt="" title="##increase_indent##" src="##directory##images/inindent.gif" /></td>
						<!-- end indent -->
						<!-- begin spacer8 -->
						<td><img class="spacer" width="1" height="22" src="##directory##images/spacer.gif" alt="" /></td>
						<!-- end spacer8 -->
						<!-- begin color -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_colordialog(##name##,this,'forecolor')"	alt="" title="##font_color##" src="##directory##images/fontcolor.gif" /></td>
						<!-- end color -->
						<!-- begin highlight -->
						<td><img class="toolButton" width="22" height="22" onClick="wp_colordialog(##name##,this,'hilitecolor')" alt="" title="##highlight##" src="##directory##images/bgcolor.gif" /></td>
						<!-- end highlight -->
					</tr>
				</table>
				<iframe id="##name##_editFrame" style="border-top: 2px solid threedshadow; border-left: 1px solid threeddarkshadow; border-right: 1px solid threeddarkshadow; border-bottom: 0px solid threedshadow; background-color:#FFFFFF; height:##height##px; width:100%;display:block" frameborder="0"></iframe>
			</div>
			<div id="##name##_tab_two" style="display:none;"> 
				<textarea class="html_edit_area" style="height:##height2##px;" id="##name##" name="##name##" wrap="off">##htmlCode##</textarea>
			</div>
			<div id="##name##_tab_three" style="display:none;"> 
				<iframe id="##name##_previewFrame" class="html_edit_area" style="height:##height3##px;" frameborder="0"></iframe>
			</div>
			<table id="##name##_tab_table" width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr> 
					<td width="7" style="border-top: 1px solid threeddarkshadow"><img src="##directory##images/spacer.gif" width="1" height="1" alt="" /></td>
					<!-- begin tab -->
					<!-- begin design -->
					<td id="##name##_designTab" class="tbuttonUp" onMouseDown="wp_on_mouse_down_tab(this, ##name##)" onClick="##name##.showDesign();"><nobr>&nbsp;<img src="##directory##images/normal.gif" width="10" height="10" alt="" />&nbsp;##design##&nbsp;&nbsp;</nobr></td>
					<!-- end design -->
					<!-- begin html -->
					<td id="##name##_sourceTab" class="tbuttonDown" onMouseDown="wp_on_mouse_down_tab(this, ##name##)" onClick="##name##.showCode();"><nobr>&nbsp;<img src="##directory##images/html.gif" width="10" height="10" alt="" />&nbsp;##html_code##&nbsp;&nbsp;</nobr></td>
					<!-- end html -->
					<!-- begin preview -->
					<td id="##name##_previewTab" class="tbuttonDown" onMouseDown="wp_on_mouse_down_tab(this, ##name##)" onClick="##name##.showPreview();"><nobr>&nbsp;<img src="##directory##images/preview.gif" width="10" height="10" alt="" />&nbsp;##preview##&nbsp;&nbsp;</nobr></td>
					<!-- end preview -->
					<!-- end tab -->
					<td width="100%" style="border-top: 1px solid threeddarkshadow" class="styled"> <div align="right" id="##name##_messages" class="styled" style="text-decoration:none; cursor: pointer; cursor: hand;" onClick="wp_toggle_table_borders(##name##,this);"  title="##toggle_guidelines##" onMouseOver="this.style.textDecoration='underline'" onMouseOut="this.style.textDecoration='none'"></div></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- drop-down menus -->
<iframe width="200" height="180" id="##name##_menu" frameborder="0" style="visibility: hidden; position: absolute; left: 0px; top: 0px; border: 1px solid #000000;"></iframe>
<div id="##name##_font-menu" style="display:none; border: 1px solid #000000"> 
	<div class="off" onClick="parent.wp_hide_menu(parent.wp_current_obj)" style="font-size:13px; font-family:verdana; color: #000000; background-color:#eeeeee"><nobr><b>X</b> 
		##cancel##</nobr></a></div>
	<div class="off" onClick="parent.wp_change_font(parent.wp_current_obj,'')" onMouseOver="on(this)" onMouseOut="off(this)">##default##</div>
	##font_menu## </div>
<div id="##name##_size-menu" style="display:none; border: 1px solid #000000"> 
	<div class="off" onClick="parent.wp_hide_menu(parent.wp_current_obj)" style="font-size:13px; font-family:verdana; color: #000000; background-color:#eeeeee"><nobr><b>X</b> 
		##cancel##</nobr></div>
	<div class="off" onClick="parent.wp_change_font_size(parent.wp_current_obj,'null')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>##default##</nobr></div>
	##size_menu## </div>
<div id="##name##_format-menu" style="display:none; border: 1px solid #000000"> 
	<div onClick="parent.wp_hide_menu(parent.wp_current_obj)" style="font-size:13px; font-family:verdana; color: #000000; background-color:#eeeeee; cursor: pointer; cursor: hand; "><nobr><b>X</b> 
		##cancel##</nobr></div>
	##format_menu## </div>
<div id="##name##_class-menu" style="display:none"> 
	<div onClick="parent.wp_hide_menu(parent.wp_current_obj)" style="font-size:13px; font-family:verdana; color: #000000; background-color:#eeeeee; cursor: pointer; cursor: hand; "><nobr><b>X</b> 
		##cancel##</nobr></div>
	<div class="off" onClick="parent.wp_change_class(parent.wp_current_obj,'wp_none')" onMouseOver="on(this)" onMouseOut="off(this)"><nobr>##clear_styles##</nobr></div>
	##class_menu## </div>
<script language="JavaScript" type="text/javascript">
<!--//
var ##name## = document.getElementById('##name##');
//-->
</script>
<noscript>
<p>##javascript_warning##</p>
</noscript>
