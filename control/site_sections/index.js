$(document).ready(function(){ // jquery ready
	// ��� ����� ���������� ������� �����
    if (isGetVarExists('action') == 'addItem') {
		$('#site_sections_form_name').focus();
	}
	// ��� ����� �������������� ������� �����
    else if (isGetVarExists('action') == 'editItem') {
        // $('#site_sections_form_html_code_1').focus();
        
        if (isGetVarExists('itemID')) {
            // ��������� ������ backup'�� ��� ��������� �����
            $.backup({ 'table_name' : 'site_sections', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : ['site_sections_form_name', 'site_sections_form_url', 'site_sections_form_title', 'site_sections_form_navigation', 'site_sections_form_full_navigation', 'site_sections_form_h1', 'site_sections_form_html_code_1', 'site_sections_form_footeranchor', 'site_sections_form_right_menu_services' ]});
        }
	}
    else $('#main_search_value').focus();
    
    // ������ ����� ���������� ������� �����
    $('#site_sections_add_form, #site_sections_edit_form').on('submit', function() {
        if (!checkForm('#site_sections_add_form')) return false;
    });
    
    // ��������� � ���� "��������"
    $('#site_sections_form_name').on('change keyup click', function(event) { checkExistenceByName(event); }); // /��������� � ���� "�������� ������� �����"
    
    // ��������� � select'� "��������������"
    $('#site_sections_form_parent_id').on('change', function(event) { 
        checkExistenceByName(event);
        checkExistenceByURL(event);
    });

    function checkExistenceByName(event) {
        var id = isGetVarExists('itemID');
        if (typeof id === 'undefined') var id = '';
        var name = $.trim($('#site_sections_form_name').val());
        var parent_id = $.trim($('#site_sections_form_parent_id').val());
        // �������� ������ ��������
        var old_value = $('#site_sections_form_name').attr('data-old-value');
        var eventTarget = event.target.nodeName.toLowerCase();
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if ((eventTarget == 'input' && name.length && name != old_value)
            || (eventTarget == 'select')) {
            // ��������� ������ ��������
            $('#site_sections_form_name').attr('data-old-value', name);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/site_sections/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name, 'id': id, 'parent_id': parent_id }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#site_sections_form_name_alert_div').html('� ���� ��� ���������� ������ � ��������� "' + name + '" � � ��������������� � "'+ $.trim($('#site_sections_form_parent_id option:selected').text().replace(/-/g, '').replace('�', '')) +'": <a href="/control/site_sections/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ �������� ��� ������� �����.').removeClass('hidden');
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
    
    // ��������� � ���� "����������"
    $('#site_sections_form_url').on('change keyup click', function(event) {
        checkExistenceByURL(event);
    });
    
    function checkExistenceByURL(event) {
        var id = isGetVarExists('itemID');
        if (typeof id === 'undefined') var id = '';
        var url = $.trim($('#site_sections_form_url').val());
        var parent_id = $.trim($('#site_sections_form_parent_id').val());
        // �������� ������ ��������
        var old_value = $('#site_sections_form_url').attr('data-old-value');
        var eventTarget = event.target.nodeName.toLowerCase();
        // console.log('url: ' + url + ', old_value:' + old_value);
        // if (url.length && url != old_value) {
        if ((eventTarget == 'input' && url.length && url != old_value)
            || (eventTarget == 'select')) {
            // ��������� ������ ��������
            $('#site_sections_form_url').attr('data-old-value', url);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/site_sections/ajax.php', { 'action': 'check_item_for_existence_by_url', 'url': url, 'id': id, 'parent_id': parent_id }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#site_sections_form_url_alert_div').html('� ���� ��� ���������� ������ � ����������� "' + url + '" � � ��������������� � "'+ $.trim($('#site_sections_form_parent_id option:selected').text().replace(/-/g, '').replace('�', '')) +'": <a href="/control/site_sections/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ ���������� ��� �������.').removeClass('hidden');
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
    // /��������� � ���� "��� �����"
	
    // �������: ������ "��������� ���������"
    $(document.body).delegate('.site_sections_edit_save_changes', 'click', function() {
        // ��������� ���� ����� �� �������������
        if (checkForm('#site_sections_edit_form')) {
            // ��������� ���������� ����� ajax
            $.post('/control/site_sections/ajax.php', { 'action': 'edit_item_submit', 'params': decodeURI($('#site_sections_edit_form').serialize()) }, function(data) {
                var d = new Date();
                var day = d.getDate(); if (day <= 9) day = '0' + day;
                var month = d.getMonth() + 1; if (month <= 9) month = '0' + month;
                var minutes = d.getMinutes(); if (minutes <= 9) minutes = '0' + minutes;
                var seconds = d.getSeconds(); if (seconds <= 9) seconds = '0' + seconds;
                var lastUpdateDate = day + '.' + month + '.' + d.getFullYear() + ' ' + d.getHours() + ':' + minutes + ':' + seconds;
                
                if (data == 'success') var dataResult = '<div class="alert alert-success alert_line">��������� ������� ��������� (' + lastUpdateDate + ')</div>';
                else var dataResult = '<div class="alert alert-danger alert_line">��������� �� ���������, ��������� ������ (' + lastUpdateDate + ')</div>';
                
                $('.ajax_result').html(dataResult).fadeIn();
                
                setTimeout(function(){ $('.ajax_result').fadeOut(); }, 5000);
            });
            return false;
        }
    });
    
    // ��� ������ ���������� �������, �������� ������ option � select'�
    if (isGetVarExists('action') == 'addItemSubmit' && $('.alert.alert-danger').html().length > 0) {
        $('#site_sections_form_parent_id option').attr("selected", false);
        $('#site_sections_form_parent_id option[value="'+ $('#site_sections_form_parent_id').attr('data-selected') +'"]').attr("selected", "selected");
    }
    
    // ��� �������� ���������� �������, �������� ��������� �� �������� ���������� ����� 5 ���
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
}); // /jquery ready