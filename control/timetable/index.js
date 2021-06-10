$(document).ready(function() { // jquery ready
    // ��������� � SELECT'� "��������"
    $(document.body).delegate('.timetable_place_id', 'change', function() {
        var place_id = $(this).val();
        if (place_id == 1) $(this).parent('.timetable').find('.timetable_event_id').parent('span').addClass('hidden');
        else $(this).parent('.timetable').find('.timetable_event_id').parent('span').removeClass('hidden');
    }); // /��������� � SELECT'� "��������"

    // �������� � ������� ���������� �� ��
    // �������� �������� GET-���������� place
    var place = isGetVarExists('place'); // console.log('place: ' + place + '\n');
    var show = isGetVarExists('show'); // console.log('show: ' + show + '\n');
    $.post('/control/timetable/ajax.php', { 'action': 'getTimetable', 'place': !place ? '' : place, 'show': !show ? '' : show }, function(data) {
        $('#time_table_list').html('');
        if (data) {
            $('#time_table_list').html(data);
            // �������������� ���������
            $('#time_table_list .timetable_event_date').datepicker({
                numberOfMonths: 2,
                dateFormat: "dd.mm.yy",
                showButtonPanel: true
            });
        }
    });

    // ���� �� ������ "�������� ����"
    $('.timetable_add_date_button').on('click', function() {
        var template_add_date = $('#template_add_date').html();
        $('#time_table_list').prepend(template_add_date);
        // �������������� ���������
        $('#time_table_list .timetable_event_date').datepicker({
            numberOfMonths: 2,
            dateFormat: "dd.mm.yy",
            showButtonPanel: true
        });
        // ������������� ����� � ������ ���� � �����
        $('#time_table_list').find('.timetable_event_date').first().focus();
        return false;
    });

    // ���� �� "��������� ���������": ��������� ��������� ��������� � ���������� ����� AJAX
    $('.timetable_save_changes').click(function(){
        // �������� �� ���� ����� � �������� ��� ������ � ������
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
        // �������� �� ���� ����� � �������� ��� ������ � ������
        // return;

        $('#time_table_list')
        .fadeOut('slow', function() {
            // ��������� ���������
            $.post('/control/timetable/ajax.php', { 'action': 'saveChanges', 'eventsAndDates': result, 'place': !place ? '' : place, 'show': !show ? '' : show }, function(data) {
                $('#time_table_list').html(data);
                $('#time_table_list').fadeIn('slow');
                // �������������� ���������
                $('#time_table_list .timetable_event_date').datepicker({
                    numberOfMonths: 2,
                    dateFormat: "dd.mm.yy",
                    showButtonPanel: true
                });
            });
        });
    }); // /���� �� "��������� ���������": ��������� ��������� ��������� � ���������� ����� AJAX

    // ���� �� "������� ����" � ����������
    $(document.body).delegate('.time_remove_item', 'click', function() {
        var date_id = $(this).closest('.form-group.timetable').data('item-id');
        // ������� �������
        $(this).closest('.well.timetable').fadeTo(500, 0.5, function() {
            $.post('/control/timetable/ajax.php', { 'action': 'remove_date', 'date_id': date_id });
            // ������� html-�������
            $(this).addClass('removed');
            $(this).html('�������');
        });
        return false;
    }); // /���� �� "������� ����" � ����������
}); // /jquery ready