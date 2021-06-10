$(document).ready(function() { // jquery ready
	// ��� ����� ����������
    if (isGetVarExists('action') == 'addItem') {
		$('#show_form_url').focus();
	}
	// ��� ����� ��������������
    if (isGetVarExists('action') == 'editItem') {
        // $('#show_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // ��������� ������ backup'�� ��� ��������� �����
            $.backup({ 'table_name' : 'shows', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'show_form_name', 'show_form_url', 'show_form_title', 'show_form_navigation', 'show_form_full_navigation', 'show_form_h1', 'show_form_text', 'show_form_text_for_slider_on_main_page', 'show_form_footeranchor' ] });
        }
	}
    // ��� ������
    if (!isGetVarExists('action')) {
        $('#search').focus();
    }
    
    // ������ ����� ���������� ���
    $('#show_form').on('submit', function() {
        if (!checkForm('#show_form')) return false;
    });

    // ��������� � ���� "��������"
    $('#show_form_name').on('change keyup click', function(event) { checkExistenceByName(event); });
    
    function checkExistenceByName(event) {
        var name = $.trim($('#show_form_name').val());
        // �������� ������ ��������
        var old_value = $('#show_form_name').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (name.length && name != old_value) {
            // ��������� ������ ��������
            $('#show_form_name').attr('data-old-value', name);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/shows/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#show_form_name_alert_div').html('� ���� ��� ���������� ��� � ��������� "' + name + '": <a href="/control/shows/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ �������� ��� ���.').removeClass('hidden');
                            $('#show_form_name').focus();
                        }
                        else $('#show_form_name_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#show_form_name_alert_div').html('').addClass('hidden');
            });
        }
    }
    
    // ��� �������� ���������� ��� �������� ��������� �� �������� ���������� ����� 5 ���
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
    // �����
    $('#search').bind('keyup click', function() {
        var q = $.trim($('#search').val());
        var old_data = $('#search').attr('old-data');
        if (q.length > 0 && (!old_data || old_data != q)) {
            $.ajaxQ.abortAll(); // �������� ��� ajax-�������
            $('#resultSet').html('');
            $.post('/control/shows/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search').attr('old-data', q);
            });
        }
    });
}); // /jquery ready