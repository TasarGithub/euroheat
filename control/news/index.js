$(document).ready(function() { // jquery ready
	// ��� ����� ����������
    if (isGetVarExists('action') == 'addItem') {
        checkExistenceByDateAdd();
        $('#news_form_page_title').focus();
	}
	// ��� ����� ��������������
    if (isGetVarExists('action') == 'editItem') {
        // $('#news_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // ��������� ������ backup'�� ��� ��������� �����
            $.backup({ 'table_name' : 'news', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'news_form_page_title', 'news_form_full_navigation',  'news_form_h1', 'news_form_text', 'news_form_footeranchor' ] });
        }
	}
    // ��� ������
    if (!isGetVarExists('action')) {
        $('#search_by_news').focus();
    }
    
    // ���������� ��������� (������������� UI datepicker)
	$("#news_form_date_add").datepicker({
		numberOfMonths: 2,
		dateFormat: "dd.mm.yy",
		showButtonPanel: true
		// altField: "#date_add_hidden",
		// altFormat: "yy-mm-dd 00:00:00"
	});
    
    // ������ ����� ���������� �������
    $('#news_form').on('submit', function() {
        if (!checkForm('#news_form')) return false;
    });

    // ��������� � ���� "��������"
    $('#news_form_h1').on('change keyup click', function(event) { checkExistenceByName(event); });
    // ��������� � ���� "����"
    $('#news_form_date_add').on('change keyup click focus', function(event) { checkExistenceByDateAdd(event); });

    function checkExistenceByName() {
        var name = $.trim($('#news_form_h1').val());
        // �������� ������ ��������
        var old_value = $('#news_form_h1').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (name.length && name != old_value) {
            // ��������� ������ ��������
            $('#news_form_h1').attr('data-old-value', name);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/news/ajax.php', { 'action': 'check_item_for_existence_by_name', 'name': name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#news_form_h1_alert_div').html('� ���� ��� ���������� ������� � ���������� h1 "' + name + '": <a href="/control/news/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ �������� ��� �������.').removeClass('hidden');
                            $('#news_form_h1').focus();
                        }
                        else $('#news_form_h1_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#news_form_h1_alert_div').html('').addClass('hidden');
            });
        }
    }

    function checkExistenceByDateAdd() {
        var date_add = $.trim($('#news_form_date_add').val());
        // �������� ������ ��������
        var old_value = $('#news_form_date_add').attr('data-old-value');
        // console.log('name: ' + name + ', old_value:' + old_value);
        // if (name.length && name != old_value) {
        if (date_add.length && date_add != old_value) {
            // ��������� ������ ��������
            $('#news_form_date_add').attr('data-old-value', date_add);
            // �������� ��� ���������� ajax-�������
            // $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/news/ajax.php', { 'action': 'check_item_for_existence_by_date_add', 'id': isGetVarExists('itemID'), 'date_add': date_add }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#news_form_date_add_alert_div').html('� ���� ��� ���������� ������� � ����� "' + date_add + '": <a href="/control/news/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ ���� ��� �������.').removeClass('hidden');
                            $('#news_form_date_add').focus();
                        }
                        else $('#news_form_date_add_alert_div').html('').addClass('hidden');
                    }
                    catch(err) { console.log(err.message); }
                }
                else $('#news_form_date_add_alert_div').html('').addClass('hidden');
            });
        }
    }

    // ��� ������ ���������� �������, �������� ������ option � select'�
    if (isGetVarExists('action') == 'addItemSubmit' && $('.alert.alert-danger').html().length > 0) {
        $('#news_form_cars_types_id option').attr("selected", false);
        $('#news_form_cars_types_id option[value="'+ $('#news_form_cars_types_id').attr('data-selected') +'"]').attr("selected", "selected");
    }
    
    // ��� �������� ���������� ������� �������� ��������� �� �������� ���������� ����� 5 ���
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
    // �����
    $('#search_by_news').bind('keyup click', function() {
        var q = $.trim($('#search_by_news').val());
        var old_data = $('#search_by_news').attr('old-data');
        if (q.length > 0 && (!old_data || old_data != q)) {
            $.ajaxQ.abortAll(); // �������� ��� ajax-�������
            $('#resultSet').html('');
            $.post('/control/news/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search_by_news').attr('old-data', q);
            });
        }
    });
}); // /jquery ready