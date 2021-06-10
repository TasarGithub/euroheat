$(document).ready(function() { // jquery ready
	// ��� ����� ����������
    if (isGetVarExists('action') == 'addItem') {
		$('#feedback_form_name').focus();
	}
	// ��� ����� ��������������
    if (isGetVarExists('action') == 'editItem') {
        // $('#feedback_form_html_code_1').focus();
      
        if (isGetVarExists('itemID')) {
            // ��������� ������ backup'�� ��� ��������� �����
            $.backup({ 'table_name' : 'feedbacks', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : [ 'feedback_form_name', 'feedback_form_feedback', 'feedback_form_footeranchor', 'feedback_form_full_navigation', 'feedback_form_right_menu_services' ] });
        }
	}
    // ��� ������
    if (!isGetVarExists('action')) {
        $('#search_by_feedbacks').focus();
    }
    
    // ���������� ��������� (������������� UI datepicker)
	$("#feedback_form_date_add").datepicker({
		numberOfMonths: 2,
		dateFormat: "dd.mm.yy",
		showButtonPanel: true
		// altField: "#date_add_hidden",
		// altFormat: "yy-mm-dd 00:00:00"
	});
    
    // ������ ����� ���������� ������
    $('#feedback_form').on('submit', function() {
        if (!checkForm('#feedback_form')) return false;
    });

    // ��� �������� ���������� ������ �������� ��������� �� �������� ���������� ����� 5 ���
    if ($('.col-lg-12 .alert.alert-success').length) {
        if ($('.col-lg-12 .alert.alert-success').html().length > 0) setTimeout(function() { $('.col-lg-12 .alert.alert-success').slideUp(); }, 5000)
    }
    
    // �����
    $('#search_by_feedbacks').bind('keyup click', function() {
        var q = $.trim($('#search_by_feedbacks').val());
        var old_data = $('#search_by_feedbacks').attr('old-data');
        if (q.length > 0 && (!old_data || old_data != q)) {
            $.ajaxQ.abortAll(); // �������� ��� ajax-�������
            $('#resultSet').html('');
            $.post('/control/feedbacks/ajax.php', { 'action': 'search', 'q': q }, function(data) {
                $('#resultSet').html(data);
                $('#search_by_feedbacks').attr('old-data', q);
            });
        }
    });
}); // /jquery ready