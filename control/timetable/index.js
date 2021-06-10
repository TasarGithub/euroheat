$(document).ready(function() { // jquery ready
    // ÈÇÌÅÍÅÍÈÅ Â SELECT'Å "ÏËÎÙÀÄÊÀ"
    $(document.body).delegate('.timetable_place_id', 'change', function() {
        var place_id = $(this).val();
        if (place_id == 1) $(this).parent('.timetable').find('.timetable_event_id').parent('span').addClass('hidden');
        else $(this).parent('.timetable').find('.timetable_event_id').parent('span').removeClass('hidden');
    }); // /ÈÇÌÅÍÅÍÈÅ Â SELECT'Å "ÏËÎÙÀÄÊÀ"

    // ÏÎËÓ×ÀÅÌ È ÂÛÂÎÄÈÌ ĞÀÑÏÈÑÀÍÈÅ ÈÇ ÁÄ
    // ïîëó÷àåì çíà÷åíèå GET-ïåğåìåííîé place
    var place = isGetVarExists('place'); // console.log('place: ' + place + '\n');
    var show = isGetVarExists('show'); // console.log('show: ' + show + '\n');
    $.post('/control/timetable/ajax.php', { 'action': 'getTimetable', 'place': !place ? '' : place, 'show': !show ? '' : show }, function(data) {
        $('#time_table_list').html('');
        if (data) {
            $('#time_table_list').html(data);
            // ÈÍÈÖÈÀËÈÇÈĞÓÅÌ ÊÀËÅÍÄÀĞÜ
            $('#time_table_list .timetable_event_date').datepicker({
                numberOfMonths: 2,
                dateFormat: "dd.mm.yy",
                showButtonPanel: true
            });
        }
    });

    // ÊËÈÊ ÏÎ ÊÍÎÏÊÅ "ÄÎÁÀÂÈÒÜ ÄÀÒÓ"
    $('.timetable_add_date_button').on('click', function() {
        var template_add_date = $('#template_add_date').html();
        $('#time_table_list').prepend(template_add_date);
        // ÈÍÈÖÈÀËÈÇÈĞÓÅÌ ÊÀËÅÍÄÀĞÜ
        $('#time_table_list .timetable_event_date').datepicker({
            numberOfMonths: 2,
            dateFormat: "dd.mm.yy",
            showButtonPanel: true
        });
        // óñòàíàâëèâàåì ôîêóñ â ïåğâîå ïîëå ñ äàòîé
        $('#time_table_list').find('.timetable_event_date').first().focus();
        return false;
    });

    // ÊËÈÊ ÍÀ "ÑÎÕĞÀÍÈÒÜ ÈÇÌÅÍÅÍÈß": ÑÎÕĞÀÍßÅÌ ĞÅÇÓËÜÒÀÒ ÈÇÌÅÍÅÍÈß Â ĞÀÑÏÈÑÀÍÈÈ ×ÅĞÅÇ AJAX
    $('.timetable_save_changes').click(function(){
        // ïğîõîäèì ïî âñåì äàòàì è ñîáèğàåì âñå äàííûå â ñòğîêó
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
        // ïğîõîäèì ïî âñåì äàòàì è ñîáèğàåì âñå äàííûå â ñòğîêó
        // return;

        $('#time_table_list')
        .fadeOut('slow', function() {
            // ÑÎÕĞÀÍßÅÌ ÈÇÌÅÍÅÍÈß
            $.post('/control/timetable/ajax.php', { 'action': 'saveChanges', 'eventsAndDates': result, 'place': !place ? '' : place, 'show': !show ? '' : show }, function(data) {
                $('#time_table_list').html(data);
                $('#time_table_list').fadeIn('slow');
                // ÈÍÈÖÈÀËÈÇÈĞÓÅÌ ÊÀËÅÍÄÀĞÜ
                $('#time_table_list .timetable_event_date').datepicker({
                    numberOfMonths: 2,
                    dateFormat: "dd.mm.yy",
                    showButtonPanel: true
                });
            });
        });
    }); // /ÊËÈÊ ÍÀ "ÑÎÕĞÀÍÈÒÜ ÈÇÌÅÍÅÍÈß": ÑÎÕĞÀÍßÅÌ ĞÅÇÓËÜÒÀÒ ÈÇÌÅÍÅÍÈß Â ĞÀÑÏÈÑÀÍÈÈ ×ÅĞÅÇ AJAX

    // ÊËÈÊ ÍÀ "ÓÄÀËÈÒÜ ÄÀÒÓ" Â ĞÀÑÏÈÑÀÍÈÈ
    $(document.body).delegate('.time_remove_item', 'click', function() {
        var date_id = $(this).closest('.form-group.timetable').data('item-id');
        // óäàëÿåì ñîáûòèå
        $(this).closest('.well.timetable').fadeTo(500, 0.5, function() {
            $.post('/control/timetable/ajax.php', { 'action': 'remove_date', 'date_id': date_id });
            // óäàëÿåì html-ıëåìåíò
            $(this).addClass('removed');
            $(this).html('Óäàëåíî');
        });
        return false;
    }); // /ÊËÈÊ ÍÀ "ÓÄÀËÈÒÜ ÄÀÒÓ" Â ĞÀÑÏÈÑÀÍÈÈ
}); // /jquery ready