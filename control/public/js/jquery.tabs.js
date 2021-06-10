// document ready
$(document).ready(function() {
	// ИНИЦИАЛИЗИРУЕМ JQUERY UI TABS
	$( "#tabs" ).tabs();
	
	// ЕСЛИ ВЫБРАНА ВКЛАДКА "JQUERY UI TABS"
	var tabState = $('#tabs_state').val();
	if (tabState) $("#tabs").tabs("option", "active", tabState);
	
	// СОБЫТИЕ: КЛИК НА ВКЛАДКЕ "JQUERY UI TABS"
	$('#tabs').tabs( {
		activate: function(event, ui) {
			// alert(ui.newTab.index());
			$('#tabs_state').val(ui.newTab.index()); // console.log(ui.newTab.index());
            // добавляем к textarea номера строк (для тех textarea, которые были в скрытых складках и не инициализировались при загрузке страницы)
            $('div#tabs-' + (ui.newTab.index() + 1) + ' .lined').linedtextarea();
		}
	});
}); // /document ready