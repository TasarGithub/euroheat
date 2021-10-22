$(document).ready(function() { // jquery ready
    // ИЗМЕНЕНИЕ В SELECT'Е "ПЛОЩАДКА"
    $(document.body).delegate('.timetable_place_id', 'change', function() {
        var place_id = $(this).val();
        if (place_id == 1) $(this).parent('.timetable').find('.timetable_event_id').parent('span').addClass('hidden');
        else $(this).parent('.timetable').find('.timetable_event_id').parent('span').removeClass('hidden');
    }); // /ИЗМЕНЕНИЕ В SELECT'Е "ПЛОЩАДКА"

    // ПОЛУЧАЕМ И ВЫВОДИМ РАСПИСАНИЕ ИЗ БД
    // получаем значение GET-переменной place
    var place = isGetVarExists('place'); // console.log('place: ' + place + '\n');
    var show = isGetVarExists('show'); // console.log('show: ' + show + '\n');
    $.post('/control/timetable/ajax.php', { 'action': 'getTimetable', 'place': !place ? '' : place, 'show': !show ? '' : show }, function(data) {
        $('#time_table_list').html('');
        if (data) {
            $('#time_table_list').html(data);
            // ИНИЦИАЛИЗИРУЕМ КАЛЕНДАРЬ
            $('#time_table_list .timetable_event_date').datepicker({
                numberOfMonths: 2,
                dateFormat: "dd.mm.yy",
                showButtonPanel: true
            });
        }
    });

    // КЛИК ПО КНОПКЕ "ДОБАВИТЬ ДАТУ"
    $('.timetable_add_date_button').on('click', function() {
        var template_add_date = $('#template_add_date').html();
        $('#time_table_list').prepend(template_add_date);
        // ИНИЦИАЛИЗИРУЕМ КАЛЕНДАРЬ
        $('#time_table_list .timetable_event_date').datepicker({
            numberOfMonths: 2,
            dateFormat: "dd.mm.yy",
            showButtonPanel: true
        });
        // устанавливаем фокус в первое поле с датой
        $('#time_table_list').find('.timetable_event_date').first().focus();
        return false;
    });

    // КЛИК НА "СОХРАНИТЬ ИЗМЕНЕНИЯ": СОХРАНЯЕМ РЕЗУЛЬТАТ ИЗМЕНЕНИЯ В РАСПИСАНИИ ЧЕРЕЗ AJAX
    $('.timetable_save_changes').click(function(){
        // проходим по всем датам и собираем все данные в строку
        var result = '';
        $('#time_table_list div.well.timetable').each(function(index){
            var date = $(this).find('.timetable_event_date').val();
            var time = $(this).find('.timetable_event_time').val();
            var show = $(this).find('.timetable_event_id').val();
            var place = $(this).find('.timetable_place_id').val();
            // console.log('index: ' + index + ", show: " + show + ', date: ' + date);
            if (place && date) {
                // result += show + '*' + place + '*' + date + '*' + time + ';';
                result += place + '*' + date + '*' + time + '*' + (!show ? '' : show) + ';';
            }
        }); // console.log(result);
        // проходим по всем датам и собираем все данные в строку
        // return;

        $('#time_table_list')
        .fadeOut('slow', function() {
            // СОХРАНЯЕМ ИЗМЕНЕНИЯ
            $.post('/control/timetable/ajax.php', { 'action': 'saveChanges', 'eventsAndDates': result, 'place': !place ? '' : place, 'show': !show ? '' : show }, function(data) {
                $('#time_table_list').html(data);
                $('#time_table_list').fadeIn('slow');
                // ИНИЦИАЛИЗИРУЕМ КАЛЕНДАРЬ
                $('#time_table_list .timetable_event_date').datepicker({
                    numberOfMonths: 2,
                    dateFormat: "dd.mm.yy",
                    showButtonPanel: true
                });
            });
        });
    }); // /КЛИК НА "СОХРАНИТЬ ИЗМЕНЕНИЯ": СОХРАНЯЕМ РЕЗУЛЬТАТ ИЗМЕНЕНИЯ В РАСПИСАНИИ ЧЕРЕЗ AJAX

    // КЛИК НА "УДАЛИТЬ ДАТУ" В РАСПИСАНИИ
    $(document.body).delegate('.time_remove_item', 'click', function() {
        var date_id = $(this).closest('.form-group.timetable').data('item-id');
        // удаляем событие
        $(this).closest('.well.timetable').fadeTo(500, 0.5, function() {
            $.post('/control/timetable/ajax.php', { 'action': 'remove_date', 'date_id': date_id });
            // удаляем html-элемент
            $(this).addClass('removed');
            $(this).html('Удалено');
        });
        return false;
    }); // /КЛИК НА "УДАЛИТЬ ДАТУ" В РАСПИСАНИИ
}); // /jquery ready