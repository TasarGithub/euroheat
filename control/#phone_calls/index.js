$(document).ready(function() { // jquery ready
    // ���������� ��������� (������������� UI datepicker)
	$("#search_by_date_from, #search_by_date_to").datepicker({
		numberOfMonths: 2,
		dateFormat: "dd.mm.yy",
		showButtonPanel: true
		// altField: "#date_add_hidden",
		// altFormat: "yy-mm-dd 00:00:00"
	});

    // ������� ������ ����������
    prepareCallsRecords();

    // ����� �� ������� �� ��������� ���
    $('#search_by_date_button').bind('click', function() {
        var search_by_date_from = $.trim($('#search_by_date_from').val());
        var search_by_date_to = $.trim($('#search_by_date_to').val());
        if (search_by_date_from.length > 0 || search_by_date_to.length > 0) {
            $.ajaxQ.abortAll(); // �������� ��� ajax-�������
            $('.sorting a').removeClass('active'); // ������� ��������� ���������� ������
            $('#resultSet, #statistics').html('');
            $('#resultSet').html('');
            $.post('/control/phone_calls/ajax.php', { 'action': 'search_by_date', 'date_from': search_by_date_from, 'date_to': search_by_date_to }, function(data) {
                try { // json
                    var result = JSON.parse(data);
                    $('#resultSet').html(result['result_set']);
                    $('#statistics').html(result['statistics']);
                    // ������� ������ ����������
                    prepareCallsRecords();
                } catch(error) { // not json
                    $('#resultSet').html(data);
                }
            });
        }
        else if (search_by_date_from.length == 0 && search_by_date_to.length == 0) {
            showHint('#search_by_date_from', '���, ������� ��� ������� "���� �" ��� "���� ��". ������ ����: ��.��.����', 'auto bottom');
        }
    }); // /����� �� ������� �� ��������� ���

    // ����������: �������� | ����������� | ������
    $('.sorting a').bind('click', function() {
        var search_by_date_from = $.trim($('#search_by_date_from').val());
        var search_by_date_to = $.trim($('#search_by_date_to').val());
        var typeOfSorting = $(this).data('sorting-type');
        if (typeOfSorting) {
            if (search_by_date_from.length > 0 || search_by_date_to.length > 0) {
                $.ajaxQ.abortAll(); // �������� ��� ajax-�������
                $('#resultSet, #statistics').html('');
                $('#resultSet').html('');
                $.post('/control/phone_calls/ajax.php', { 'action': 'search_by_sorting', 'date_from': search_by_date_from, 'date_to': search_by_date_to, 'type_of_sorting': typeOfSorting }, function(data) {
                    try { // json
                        var result = JSON.parse(data);
                        $('#resultSet').html(result['result_set']);
                        $('#statistics').html(result['statistics']);
                        // ������� ������ ����������
                        prepareCallsRecords();
                    } catch(error) { // not json
                        $('#resultSet').html(data);
                    }
                });
            }
            else if (search_by_date_from.length == 0 && search_by_date_to.length == 0) {
                showHint('#search_by_date_from', '���, ������� ��� ������� "���� �" ��� "���� ��". ������ ����: ��.��.����', 'auto bottom');
            }
        }
        else console.log('Error: variable "typeOfSorting" is not defined for "sorting click" event.');
        // �������� ���������� ������
        $('.sorting a').removeClass('active');
        $(this).addClass('active');
    }); // /����������: �������� | ����������� | ������

    // ����� �� ������� �� ��������� ���. ���������� ������� ������� Enter
    $('#search_by_date_from, #search_by_date_to').keypress(function(e) {
        if (e.which == 13) { $('#search_by_date_button').click(); return false; }
    });

    // ���������� ������ "��������� � Excel"
    $('#export_to_excel').bind('click', function() {
        var search_by_date_from = $.trim($('#search_by_date_from').val());
        var search_by_date_to = $.trim($('#search_by_date_to').val());
        // ������� ����������
        $('#statistics').html('');
        if (search_by_date_from.length > 0 || search_by_date_to.length > 0) {
            $.ajaxQ.abortAll(); // �������� ��� ajax-�������
            $('#resultSet').html('');
            $.post('/control/phone_calls/ajax.php', { 'action': 'export_to_excel', 'date_from': search_by_date_from, 'date_to': search_by_date_to }, function(data) {
                try { // json
                    var result = JSON.parse(data);
                    if (result['result'] == 'success') {
                        window.location.href = result['path_to_file'];
                        $('#resultSet').html('<div style="font-size:18px">���� �������� �����.</div><div style="margin-top:15px;font-size:18px">���� ���������� �� ��������, ���, <a href="' + result['path_to_file'] + '">��������� �� ������</a>.</div>');
                    }
                } catch(error) { // not json
                    $('#resultSet').html(data);
                }
            });
        }
        else if (search_by_date_from.length == 0 && search_by_date_to.length == 0) {
            showHint('#search_by_date_from', '���, ������� ��� ������� "���� �" ��� "���� ��". ������ ����: ��.��.����', 'auto bottom');
        }
    }); // ���������� ������ "��������� � Excel"

}); // /jquery ready

// �������

// ������� ������ ����������
function prepareCallsRecords() {
    // ������������� jquery jplayer + jquery miniaudioplayer
    $('.audio').mb_miniPlayer({
        width: 300,
        inLine: true,
        id3: true,
        addShadow: false,
        pauseOnWindowBlur: false,
        downloadPage: null,
        onReady : function (player, $controlsBox) {
            var id = $controlsBox[0]['id'];
            // ������������� ������� title
            $('#' + id).find('.map_download').attr('title', '������� ������ ���������');
            // ������������� ��� ����� ��� ����������
            var call_record = $('#' + id).closest('.for_player').data('call-record');
            $('#' + id).find('.map_download').attr('download', call_record);
        }
    });

    // ��������� ���������������� ������� ��� ������������ jquery miniaudioplayer
    $('.mbMiniPlayer').each(function() {
        $(this).addClass('play');
        var td = $(this).closest('td');
        var position = td.position();
        // console.log('left: ' + position.left + ', top: ' + position.top);
        // $(this).closest('.mbMiniPlayer').css({'left' : position.left + 23, 'top' : position.top + 9 });
        $(this).closest('.mbMiniPlayer').css({'left' : position.left + 9, 'top' : position.top + 9 });
    });

    // �������� �� ���� ������� "������� ������ ���������" � ������� ������ �� ����� �����
    $('.map_download').each(function() {
        var download = $(this).attr('download').replace('get_call_record.php?call_record=', '');
        $(this).attr('download', download);
    });
} // /������� ������ ����������

// /�������