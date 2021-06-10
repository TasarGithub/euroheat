$(document).ready(function() { // jquery ready
    // Подключаем календарь (инициализация UI datepicker)
	$("#search_by_date_from, #search_by_date_to").datepicker({
		numberOfMonths: 2,
		dateFormat: "dd.mm.yy",
		showButtonPanel: true
		// altField: "#date_add_hidden",
		// altFormat: "yy-mm-dd 00:00:00"
	});

    // Выводим записи разговоров
    prepareCallsRecords();

    // Поиск по звонкам по диапазону дат
    $('#search_by_date_button').bind('click', function() {
        var search_by_date_from = $.trim($('#search_by_date_from').val());
        var search_by_date_to = $.trim($('#search_by_date_to').val());
        if (search_by_date_from.length > 0 || search_by_date_to.length > 0) {
            $.ajaxQ.abortAll(); // отменяем все ajax-запросы
            $('.sorting a').removeClass('active'); // убираем выделение сортировки цветом
            $('#resultSet, #statistics').html('');
            $('#resultSet').html('');
            $.post('/control/phone_calls/ajax.php', { 'action': 'search_by_date', 'date_from': search_by_date_from, 'date_to': search_by_date_to }, function(data) {
                try { // json
                    var result = JSON.parse(data);
                    $('#resultSet').html(result['result_set']);
                    $('#statistics').html(result['statistics']);
                    // выводим записи разговоров
                    prepareCallsRecords();
                } catch(error) { // not json
                    $('#resultSet').html(data);
                }
            });
        }
        else if (search_by_date_from.length == 0 && search_by_date_to.length == 0) {
            showHint('#search_by_date_from', 'Пжл, укажите как минимум "дату с" или "дату по". Формат даты: ДД.ММ.ГГГГ', 'auto bottom');
        }
    }); // /Поиск по звонкам по диапазону дат

    // Сортировка: принятые | пропущенные | чейзер
    $('.sorting a').bind('click', function() {
        var search_by_date_from = $.trim($('#search_by_date_from').val());
        var search_by_date_to = $.trim($('#search_by_date_to').val());
        var typeOfSorting = $(this).data('sorting-type');
        if (typeOfSorting) {
            if (search_by_date_from.length > 0 || search_by_date_to.length > 0) {
                $.ajaxQ.abortAll(); // отменяем все ajax-запросы
                $('#resultSet, #statistics').html('');
                $('#resultSet').html('');
                $.post('/control/phone_calls/ajax.php', { 'action': 'search_by_sorting', 'date_from': search_by_date_from, 'date_to': search_by_date_to, 'type_of_sorting': typeOfSorting }, function(data) {
                    try { // json
                        var result = JSON.parse(data);
                        $('#resultSet').html(result['result_set']);
                        $('#statistics').html(result['statistics']);
                        // выводим записи разговоров
                        prepareCallsRecords();
                    } catch(error) { // not json
                        $('#resultSet').html(data);
                    }
                });
            }
            else if (search_by_date_from.length == 0 && search_by_date_to.length == 0) {
                showHint('#search_by_date_from', 'Пжл, укажите как минимум "дату с" или "дату по". Формат даты: ДД.ММ.ГГГГ', 'auto bottom');
            }
        }
        else console.log('Error: variable "typeOfSorting" is not defined for "sorting click" event.');
        // выделяем сортировку цветом
        $('.sorting a').removeClass('active');
        $(this).addClass('active');
    }); // /Сортировка: принятые | пропущенные | чейзер

    // Поиск по звонкам по диапазону дат. Обработчик нажатия клавиши Enter
    $('#search_by_date_from, #search_by_date_to').keypress(function(e) {
        if (e.which == 13) { $('#search_by_date_button').click(); return false; }
    });

    // Обработчик кнопки "Выгрузить в Excel"
    $('#export_to_excel').bind('click', function() {
        var search_by_date_from = $.trim($('#search_by_date_from').val());
        var search_by_date_to = $.trim($('#search_by_date_to').val());
        // очищаем статистику
        $('#statistics').html('');
        if (search_by_date_from.length > 0 || search_by_date_to.length > 0) {
            $.ajaxQ.abortAll(); // отменяем все ajax-запросы
            $('#resultSet').html('');
            $.post('/control/phone_calls/ajax.php', { 'action': 'export_to_excel', 'date_from': search_by_date_from, 'date_to': search_by_date_to }, function(data) {
                try { // json
                    var result = JSON.parse(data);
                    if (result['result'] == 'success') {
                        window.location.href = result['path_to_file'];
                        $('#resultSet').html('<div style="font-size:18px">Файл выгрузки готов.</div><div style="margin-top:15px;font-size:18px">Если скачивание не началось, пжл, <a href="' + result['path_to_file'] + '">перейдите по ссылке</a>.</div>');
                    }
                } catch(error) { // not json
                    $('#resultSet').html(data);
                }
            });
        }
        else if (search_by_date_from.length == 0 && search_by_date_to.length == 0) {
            showHint('#search_by_date_from', 'Пжл, укажите как минимум "дату с" или "дату по". Формат даты: ДД.ММ.ГГГГ', 'auto bottom');
        }
    }); // Обработчик кнопки "Выгрузить в Excel"

}); // /jquery ready

// ФУНКЦИИ

// ВЫВОДИМ ЗАПИСИ РАЗГОВОРОМ
function prepareCallsRecords() {
    // Инициализация jquery jplayer + jquery miniaudioplayer
    $('.audio').mb_miniPlayer({
        width: 300,
        inLine: true,
        id3: true,
        addShadow: false,
        pauseOnWindowBlur: false,
        downloadPage: null,
        onReady : function (player, $controlsBox) {
            var id = $controlsBox[0]['id'];
            // устанавливаем атрибут title
            $('#' + id).find('.map_download').attr('title', 'Скачать запись разговора');
            // устанавливаем имя файла для скачивания
            var call_record = $('#' + id).closest('.for_player').data('call-record');
            $('#' + id).find('.map_download').attr('download', call_record);
        }
    });

    // формируем позиционирование значков для проигрывания jquery miniaudioplayer
    $('.mbMiniPlayer').each(function() {
        $(this).addClass('play');
        var td = $(this).closest('td');
        var position = td.position();
        // console.log('left: ' + position.left + ', top: ' + position.top);
        // $(this).closest('.mbMiniPlayer').css({'left' : position.left + 23, 'top' : position.top + 9 });
        $(this).closest('.mbMiniPlayer').css({'left' : position.left + 9, 'top' : position.top + 9 });
    });

    // проходим по всем ссылкам "скачать запись разговора" и убираем лишнее из имени файла
    $('.map_download').each(function() {
        var download = $(this).attr('download').replace('get_call_record.php?call_record=', '');
        $(this).attr('download', download);
    });
} // /ВЫВОДИМ ЗАПИСИ РАЗГОВОРОМ

// /ФУНКЦИИ