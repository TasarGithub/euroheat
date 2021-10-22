$(document).ready(function() { // jquery ready
	// для формы добавления
    if (isGetVarExists('action') == 'addItem') {
		$('#articles_form_url').focus();
	}
	// для формы редактирования
    if (isGetVarExists('action') == 'editItem') {
        // $('#articles_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // формируем список backup'ов для указанных полей
            $.backup({ 'table_name' : 'articles', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'articles_form_name', 'articles_form_url', 'articles_form_title', 'articles_form_full_navigation', 'articles_form_navigation', 'articles_form_h1', 'articles_form_text', 'articles_form_footeranchor', 'articles_form_right_menu_services' ] });
        }
	}
    // для списка
    if (!isGetVarExists('action')) {
        $('#search').focus();
    }
    
    // субмит формы добавления позиции
    $('#articles_form').on('submit', function() {
        if (!checkForm('#articles_form')) return false;
    });

    // изменения в поле "Название"
    $('#articles_form_name').on('change keyup click', function(event) { checkExistenceByName(event); });
    
    function checkExistenceByName(event) {
        var name = $.trim($('#articles_form_name').val());
        // получаем старое значение
        var old_value = $('#articles_form_name').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (name.length && name != old_value) {
            // фиксируем старое значение
            $('#articles_form_name').attr('data-old-value', name);
            // отменяем все предыдущие ajax-запросы
            // $.ajaxQ.abortAll();
            // провряем, есть ли шаблон в базе с указанным именем
            $.post('/control/articles/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#articles_form_name_alert_div').html('В базе уже существует статья с названием "' + name + '": <a href="/control/articles/?action=editItem&itemID=' + result['id'] + '" target="_blank">смотреть</a>.<br />Пжл, укажите другое название для статьи.').removeClass('hidden');
                            $('#articles_form_name').focus();
                        }
                        else $('#articles_form_name_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#articles_form_name_alert_div').html('').addClass('hidden');
            });
        }
    }
    
    // при успешном добавлении позиции скрываем сообщение об успешном добавлении через 5 сек
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
    // поиск
    $('#search').bind('keyup click', function() {
        var q = $.trim($('#search').val());
        var old_data = $('#search').attr('old-data');
        if (q.length > 0 && (!old_data || old_data != q)) {
            $.ajaxQ.abortAll(); // отменяем все ajax-запросы
            $('#resultSet').html('');
            $.post('/control/articles/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search').attr('old-data', q);
            });
        }
    });
}); // /jquery ready