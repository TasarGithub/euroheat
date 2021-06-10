$(document).ready(function() { // jquery ready
	// для формы добавления
    if (isGetVarExists('action') == 'addItem') {
		$('#news_form_page_title').focus();
	}
	// для формы редактирования
    if (isGetVarExists('action') == 'editItem') {
        // $('#news_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // формируем список backup'ов для указанных полей
            $.backup({ 'table_name' : 'news', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'news_form_page_title', 'news_form_full_navigation',  'news_form_h1', 'news_form_text', 'news_form_footeranchor' ] });
        }
	}
    // для списка
    if (!isGetVarExists('action')) {
        $('#search_by_news').focus();
    }
    
    // подключаем календарь (инициализация UI datepicker)
	$("#news_form_date_add").datepicker({
		numberOfMonths: 2,
		dateFormat: "dd.mm.yy",
		showButtonPanel: true
		// altField: "#date_add_hidden",
		// altFormat: "yy-mm-dd 00:00:00"
	});
    
    // субмит формы добавления новости
    $('#news_form').on('submit', function() {
        if (!checkForm('#news_form')) return false;
    });

    // изменения в поле "Название"
    $('#news_form_h1').on('change keyup click', function(event) { checkExistenceByName(event); });
    
    function checkExistenceByName(event) {
        var name = $.trim($('#news_form_h1').val());
        // получаем старое значение
        var old_value = $('#news_form_h1').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (name.length && name != old_value) {
            // фиксируем старое значение
            $('#news_form_h1').attr('data-old-value', name);
            // отменяем все предыдущие ajax-запросы
            // $.ajaxQ.abortAll();
            // провряем, есть ли шаблон в базе с указанным именем
            $.post('/control/news/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#news_form_h1_alert_div').html('В базе уже существует новость с заголовком h1 "' + name + '": <a href="/control/news/?action=editItem&itemID=' + result['id'] + '" target="_blank">смотреть</a>.<br />Пжл, укажите другое название для новости.').removeClass('hidden');
                            $('#news_form_h1').focus();
                        }
                        else $('#news_form_h1_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#news_form_h1_alert_div').html('').addClass('hidden');
            });
        }
    }
    
    // при ошибке добавления новости, выделаем нужный option в select'е
    if (isGetVarExists('action') == 'addItemSubmit' && $('.alert.alert-danger').html().length > 0) {
        $('#news_form_cars_types_id option').attr("selected", false);
        $('#news_form_cars_types_id option[value="'+ $('#news_form_cars_types_id').attr('data-selected') +'"]').attr("selected", "selected");
    }
    
    // при успешном добавлении новости скрываем сообщение об успешном добавлении через 5 сек
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
    // поиск
    $('#search_by_news').bind('keyup click', function() {
        var q = $.trim($('#search_by_news').val());
        var old_data = $('#search_by_news').attr('old-data');
        if (q.length > 0 && (!old_data || old_data != q)) {
            $.ajaxQ.abortAll(); // отменяем все ajax-запросы
            $('#resultSet').html('');
            $.post('/control/news/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search_by_news').attr('old-data', q);
            });
        }
    });
}); // /jquery ready