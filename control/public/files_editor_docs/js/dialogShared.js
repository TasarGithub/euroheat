// Purpose: functions shared between all dialog windows
var wp_current_obj = null
if (window.dialogArguments) {
	obj = dialogArguments.wp_current_obj
	wp_current_obj = obj
	parentWindow = dialogArguments
} else if (parent.window.dialogArguments) {
	obj = parent.window.obj
	wp_current_obj = obj
	parentWindow = parent.window.parentWindow
} else if (window.opener) {
	obj = window.opener.wp_current_obj
	wp_current_obj = obj
	parentWindow = window.opener
} else if (parent.window.opener) {
	obj = parent.window.obj
	wp_current_obj = obj
	parentWindow = parent.window.parentWindow
} else {
	obj = null
	wp_current_obj = null
	parentWindow = null
}
function colordialog(Action) {
	colorwin = wp_openDialog(parentWindow.wp_directory+"selcolor.php?action="+Action+"&lang=" + obj.instance_lang ,'modal' , 296, 352)
}
function onClose() {
	if (parentWindow.wp_directory) {
		obj.edit_object.focus()
	}
}
function hideLoadMessage() {
	document.getElementById('dialogLoadMessage').style.display = 'none'
}
function showLoadMessage() {
	document.getElementById('dialogLoadMessage').style.display = 'block'
}
window.onerror = hide_error;
function hide_error() {
	if (!parentWindow.wp_debug_mode) {
		return true;
	}
}