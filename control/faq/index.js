$(document).ready(function() { // jquery ready
	// ��� ����� ����������
    if (isGetVarExists('action') == 'addItem') {
		$('#faq_form_url').focus();
	}
	// ��� ����� ��������������
    if (isGetVarExists('action') == 'editItem') {
        // $('#faq_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // ��������� ������ backup'�� ��� ��������� �����
            $.backup({ 'table_name' : 'faq', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'faq_form_name', 'faq_form_url', 'faq_form_title', 'faq_form_navigation', 'faq_form_full_navigation', 'faq_form_h1', 'faq_form_text', 'faq_form_footeranchor', 'faq_form_right_menu_services' ] });
        }
	}
    // ��� ������
    if (!isGetVarExists('action')) {
        $('#search').focus();
    }
    
    // ������ ����� ���������� �������-������
    $('#faq_form').on('submit', function() {
        if (!checkForm('#faq_form')) return false;
    });

    // ��������� � ���� "��������"
    $('#faq_form_name').on('change keyup click', function(event) { checkExistenceByName(event); });
    
    function checkExistenceByName() {
        var name = $.trim($('#faq_form_name').val());
        // �������� ������ ��������
        var old_value = $('#faq_form_name').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (name.length && name != old_value) {
            // ��������� ������ ��������
            $('#faq_form_name').attr('data-old-value', name);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/faq/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#faq_form_name_alert_div').html('� ���� ��� ���������� ������-����� � ��������� "' + name + '": <a href="/control/faq/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ �������� ��� �������.').removeClass('hidden');
                            $('#faq_form_name').focus();
                        }
                        else $('#faq_form_name_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#faq_form_name_alert_div').html('').addClass('hidden');
            });
        }
    }
    
    // ��� �������� ���������� �������-������ �������� ��������� �� �������� ���������� ����� 5 ���
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
            $.post('/control/faq/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search').attr('old-data', q);
            });
        }
    });
}); // /jquery ready