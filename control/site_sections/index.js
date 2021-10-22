$(document).ready(function(){ // jquery ready

	// для формы добавления раздела сайта

    if (isGetVarExists('action') == 'addItem') {

		$('#site_sections_form_name').focus();

	}

	// для формы редактирования раздела сайта

    else if (isGetVarExists('action') == 'editItem') {

        // $('#site_sections_form_html_code_1').focus();

        

        if (isGetVarExists('itemID')) {

            // формируем список backup'ов для указанных полей

            $.backup({ 'table_name' : 'site_sections', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : ['site_sections_form_name', 'site_sections_form_url', 'site_sections_form_title', 'site_sections_form_navigation', 'site_sections_form_full_navigation', 'site_sections_form_h1', 'site_sections_form_html_code_1', 'site_sections_form_footeranchor', 'site_sections_form_right_menu_services' ]});

        }

	}

    else $('#main_search_value').focus();

    

    // субмит формы добавления раздела сайта

    $('#site_sections_add_form, #site_sections_edit_form').on('submit', function() {

        if (!checkForm('#site_sections_add_form')) return false;

    });

    

    // изменения в поле "Название"

    $('#site_sections_form_name').on('change keyup click', function(event) { checkExistenceByName(event); }); // /изменения в поле "Название раздела сайта"

    

    // изменения в select'е "Принадлежность"

    $('#site_sections_form_parent_id').on('change', function(event) { 

        checkExistenceByName(event);

        checkExistenceByURL(event);

    });



    function checkExistenceByName(event) {

        var id = isGetVarExists('itemID');

        if (typeof id === 'undefined') var id = '';

        var name = $.trim($('#site_sections_form_name').val());

        var parent_id = $.trim($('#site_sections_form_parent_id').val());

        // получаем старое значение

        var old_value = $('#site_sections_form_name').attr('data-old-value');

        var eventTarget = event.target.nodeName.toLowerCase();

        // console.log('name: ' + name + ', old_value:' + old_value);

        // if (name.length && name != old_value) {

        if ((eventTarget == 'input' && name.length && name != old_value)

            || (eventTarget == 'select')) {

            // фиксируем старое значение

            $('#site_sections_form_name').attr('data-old-value', name);

            // отменяем все предыдущие ajax-запросы

            // $.ajaxQ.abortAll();

            // провряем, есть ли шаблон в базе с указанным именем

            $.post('/control/site_sections/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name, 'id': id, 'parent_id': parent_id }, function(data) {

                if (data) {

                    try {

                        var result = JSON.parse(data); // console.log('%o', result);

                        if (result['result'] == 'exists') {

                            $('#site_sections_form_name_alert_div').html('В базе уже существует раздел с названием "' + name + '" и с принадлежностью к "'+ $.trim($('#site_sections_form_parent_id option:selected').text().replace(/-/g, '').replace('»', '')) +'": <a href="/control/site_sections/?action=editItem&itemID=' + result['id'] + '" target="_blank">смотреть</a>.<br />Пжл, укажите другое название для раздела сайта.').removeClass('hidden');

                            $('#site_sections_form_name').focus();

                        }

                        else $('#site_sections_form_name_alert_div').html('').addClass('hidden');

                    }

                    catch(err) { console.log(err.message); }

                }

                else $('#site_sections_form_name_alert_div').html('').addClass('hidden');

            });

        }

    }

    

    // изменения в поле "Директория"

    $('#site_sections_form_url').on('change keyup click', function(event) {

        checkExistenceByURL(event);

    });

    

    function checkExistenceByURL(event) {

        var id = isGetVarExists('itemID');

        if (typeof id === 'undefined') var id = '';

        var url = $.trim($('#site_sections_form_url').val());

        var parent_id = $.trim($('#site_sections_form_parent_id').val());

        // получаем старое значение

        var old_value = $('#site_sections_form_url').attr('data-old-value');

        var eventTarget = event.target.nodeName.toLowerCase();

        // console.log('url: ' + url + ', old_value:' + old_value);

        // if (url.length && url != old_value) {

        if ((eventTarget == 'input' && url.length && url != old_value)

            || (eventTarget == 'select')) {

            // фиксируем старое значение

            $('#site_sections_form_url').attr('data-old-value', url);

            // отменяем все предыдущие ajax-запросы

            // $.ajaxQ.abortAll();

            // провряем, есть ли шаблон в базе с указанным именем

            $.post('/control/site_sections/ajax.php', { 'action': 'check_item_for_existence_by_url', 'url': url, 'id': id, 'parent_id': parent_id }, function(data) {

                if (data) {

                    try {

                        var result = JSON.parse(data); // console.log('%o', result);

                        if (result['result'] == 'exists') {

                            $('#site_sections_form_url_alert_div').html('В базе уже существует раздел с директорией "' + url + '" и с принадлежностью к "'+ $.trim($('#site_sections_form_parent_id option:selected').text().replace(/-/g, '').replace('»', '')) +'": <a href="/control/site_sections/?action=editItem&itemID=' + result['id'] + '" target="_blank">смотреть</a>.<br />Пжл, укажите другую директорию для раздела.').removeClass('hidden');

                            $('#site_sections_form_url').focus();

                        }

                        else $('#site_sections_form_url_alert_div').html('').addClass('hidden');

                    }

                    catch(err) { console.log(err.message); }

                }

                else $('#site_sections_form_url_alert_div').html('').addClass('hidden');

            });

        }

    }

    // /изменения в поле "Имя файла"

	

    // Шаблоны: ссылка "Сохранить изменения"

    $(document.body).delegate('.site_sections_edit_save_changes', 'click', function() {

        // проверяем поля формы на заполненность

        if (checkForm('#site_sections_edit_form')) {

            // сохраняем информацию через ajax

            $.post('/control/site_sections/ajax.php', { 'action': 'edit_item_submit', 'params': decodeURI($('#site_sections_edit_form').serialize()) }, function(data) {

                var d = new Date();

                var day = d.getDate(); if (day <= 9) day = '0' + day;

                var month = d.getMonth() + 1; if (month <= 9) month = '0' + month;

                var minutes = d.getMinutes(); if (minutes <= 9) minutes = '0' + minutes;

                var seconds = d.getSeconds(); if (seconds <= 9) seconds = '0' + seconds;

                var lastUpdateDate = day + '.' + month + '.' + d.getFullYear() + ' ' + d.getHours() + ':' + minutes + ':' + seconds;

                // console.log('data: ', data);

                if (data == 'success') var dataResult = '<div class="alert alert-success alert_line">изменения успешно сохранены (' + lastUpdateDate + ')</div>';
                

                else var dataResult = '<div class="alert alert-danger alert_line">изменения НЕ сохранены, произошла ошибка (' + lastUpdateDate + ')</div>';

                

                $('.ajax_result').html(dataResult).fadeIn();

                

                setTimeout(function(){ $('.ajax_result').fadeOut(); }, 5000);

            });

            return false;

        }

    });

    

    // при ошибке добавления раздела, выделаем нужный option в select'е

    if (isGetVarExists('action') == 'addItemSubmit' && $('.alert.alert-danger').html().length > 0) {

        $('#site_sections_form_parent_id option').attr("selected", false);

        $('#site_sections_form_parent_id option[value="'+ $('#site_sections_form_parent_id').attr('data-selected') +'"]').attr("selected", "selected");

    }

    

    // при успешном добавлении раздела, скрываем сообщение об успешном добавлении через 5 сек

    if ($('.col-lg-12 .alert.alert-success').length) {

        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)

    }

    

}); // /jquery ready