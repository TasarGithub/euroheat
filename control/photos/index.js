$(document).ready(function() { // jquery ready
	// ��� ����� ����������
    if (isGetVarExists('action') == 'addItem') {
		$('#photo_form_url').focus();
	}
	// ��� ����� ��������������
    if (isGetVarExists('action') == 'editItem') {
        // $('#photo_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // ��������� ������ backup'�� ��� ��������� �����
            $.backup({ 'table_name' : 'photos', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'photo_form_name', 'photo_form_url', 'photo_form_title', 'photo_form_navigation', 'photo_form_full_navigation', 'photo_form_h1', 'photo_form_text', 'photo_form_footeranchor' ] });
        }
	}
    // ��� ������
    if (!isGetVarExists('action')) {
        $('#search').focus();
    }
    
    // ������ ����� ���������� �������-������
    $('#photo_form').on('submit', function() {
        if (!checkForm('#photo_form')) return false;
    });

    // ��������� � ���� "��������"
    $('#photo_form_name').on('change keyup click', function(event) { checkExistenceByName(event); });
    
    function checkExistenceByName(event) {
        var name = $.trim($('#photo_form_name').val());
        // �������� ������ ��������
        var old_value = $('#photo_form_name').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (name.length && name != old_value) {
            // ��������� ������ ��������
            $('#photo_form_name').attr('data-old-value', name);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/photos/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#photo_form_name_alert_div').html('� ���� ��� ���������� ������-����� � ��������� "' + name + '": <a href="/control/photos/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ �������� ��� �������.').removeClass('hidden');
                            $('#photo_form_name').focus();
                        }
                        else $('#photo_form_name_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#photo_form_name_alert_div').html('').addClass('hidden');
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
            $.post('/control/photos/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search').attr('old-data', q);
            });
        }
    });
}); // /jquery ready