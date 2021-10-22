$(document).ready(function() { // jquery ready
	// для формы добавления
    if (isGetVarExists('action') == 'addItem') {
		$('#feedback_form_name').focus();
	}
	// для формы редактирования
    if (isGetVarExists('action') == 'editItem') {
        // $('#feedback_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // формируем список backup'ов для указанных полей
            $.backup({ 'table_name' : 'feedbacks', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'feedback_form_name', 'feedback_form_feedback', 'feedback_form_footeranchor', 'feedback_form_full_navigation', 'feedback_form_right_menu_services' ] });
        }
	}
    // для списка
    if (!isGetVarExists('action')) {
        $('#search_by_feedbacks').focus();
    }
    
    // подключаем календарь (инициализация UI datepicker)
	$("#feedback_form_date_add").datepicker({
		numberOfMonths: 2,
		dateFormat: "dd.mm.yy",
		showButtonPanel: true
		// altField: "#date_add_hidden",
		// altFormat: "yy-mm-dd 00:00:00"
	});
    
    // субмит формы добавления отзыва
    $('#feedback_form').on('submit', function() {
        if (!checkForm('#feedback_form')) return false;
    });

    // при успешном добавлении отзыва скрываем сообщение об успешном добавлении через 5 сек
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
    // поиск
    $('#search_by_feedbacks').bind('keyup click', function() {
        var q = $.trim($('#search_by_feedbacks').val());
        var old_data = $('#search_by_feedbacks').attr('old-data');
        if (q.length > 0 && (!old_data || old_data != q)) {
            $.ajaxQ.abortAll(); // отменяем все ajax-запросы
            $('#resultSet').html('');
            $.post('/control/feedbacks/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search_by_feedbacks').attr('old-data', q);
            });
        }
    });
}); // /jquery ready