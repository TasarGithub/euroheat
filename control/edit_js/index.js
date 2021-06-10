$(document).ready(function(){
	// ��� ����� ���������� �������
    if (isGetVarExists('action') == 'addItem') {
		$('#templates_form_name').focus();
	}
	// ��� ����� �������������� �������
    if (isGetVarExists('action') == 'editItem') {
        $('#templates_form_html_code').focus();
		// ������� ������ ���� backup'��
		getAllBackups();
	}
    
    // ������ ����� ���������� �������
    $('#templates_add_form').on('submit', function() {
        if (!checkForm('#templates_add_form')) return false;
    });
    
    // ��������� � ���� "�������� �������"
    if (isGetVarExists('action') == 'addItem') {
        $('#templates_form_name').on('change keyup click', function() {
            var name = $.trim($('#templates_form_name').val());
            // �������� ������ ��������
            var old_value = $('#templates_form_name').attr('data-old-value');
            // console.log('name: ' + name + ', old_value:' + old_value);
            if (name.length && name != old_value) {
                // ��������� ������ ��������
                $('#templates_form_name').attr('data-old-value', name);
                // �������� ��� ���������� ajax-�������
                $.ajaxQ.abortAll();
                // ��������, ���� �� ������ � ���� � ��������� ������
                $.post('/control/edit_js/ajax.php', { 'action': 'check_template_for_existence_by_name', 'name': name }, function(data) {
                    if (data) {
                        try {
                            var result = JSON.parse(data); // console.log('%o', result);
                            if (result['result'] == 'exists') {
                                $('#templates_form_name_alert_div').html('� ���� ��� ���������� ���� js-���� � ��������� ���������: <a href="/control/edit_js/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ �������� ��� ����� js-����.').removeClass('hidden');
                                $('#templates_form_name').focus();
                            }
                            else $('#templates_form_name_alert_div').html('').addClass('hidden');
                        }
                        catch(err) { console.log(err.message); }
                    }
                    else $('#templates_form_name_alert_div').html('').addClass('hidden');
                });
            }
        }); // /��������� � ���� "�������� �������"
    }
    
    // ��������� � ���� "��� �����"
    $('#templates_form_file_name').on('change keyup click', function() {
        var file_name = $.trim($('#templates_form_file_name').val());
        // �������� ������ ��������
        var old_value = $('#templates_form_file_name').attr('data-old-value');
        // console.log('file_name: ' + file_name + ', old_value:' + old_value);
        if (file_name.length && file_name != old_value) {
            // ��������� ������ ��������
            $('#templates_form_file_name').attr('data-old-value', file_name);
            // �������� ��� ���������� ajax-�������
            $.ajaxQ.abortAll();
            // ��������, ���� �� ������ � ���� � ��������� ������
            $.post('/control/edit_js/ajax.php', { 'action': 'check_template_for_existence_by_file_name', 'file_name': file_name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#templates_form_file_name_alert_div').html('� ���� ��� ���������� ���� js-���� � ��������� ������ �����: <a href="/control/edit_js/?action=editItem&itemID=' + result['id'] + '" target="_blank">��������</a>.<br />���, ������� ������ ��� �����.').removeClass('hidden');
                            // $('#templates_form_file_name').focus();
                        }
                        else
                        {
                            $('#templates_form_file_name_alert_div').html('').addClass('hidden');
                            // ���� � ���� ��� ������� � ��������� ������ �����, �� � �������� ������� ��������� ���� ����������
                            checkFileExistence();                       
                        }
                    }
                    catch(err) { console.log(err.message); }
                }
                else {
                    $('#templates_form_file_name_alert_div').html('').addClass('hidden');
                    // ���� � ���� ��� ������� � ��������� ������ �����, �� � �������� ������� ��������� ���� ����������
                    checkFileExistence();                       
                }
            });
        }
    });
    function checkFileExistence() {
        // �������� ��� ���������� ajax-�������
        $.ajaxQ.abortAll();
        // ��������, ��� �� � ���� ������� � ����� �� ���������
        $.post('/control/edit_js/ajax.php', { 'action': 'checkTemplateForErrorsAddItem', 'file_name': $.trim($('#templates_form_file_name').val()) }, function(data) {
            try {
                var result = JSON.parse(data); // console.log('%o', result);
                if (result['result'] == 'exists') {
                    $('#templates_form_file_name_alert_div').html(result['message']).removeClass('hidden');
                    // $('#templates_form_file_name').focus();
                    return false;
                }
            }
            catch(err) { console.log(err.message); }
        });
    }
    // /��������� � ���� "��� �����"
	
	// ������
    
    // �������: ������ "��������� ���������"
    $(document.body).delegate('#templates_edit_save_changes', 'click', function(){
        // ��������� ���� ����� �� �������������
        if (checkForm('#templates_edit_form')) {
            // ��������� ���������� ����� ajax
            $.post('/control/edit_js/ajax.php', { 'action': 'edit_template_submit', 'name': $('#templates_form_name').val(), 'html_code': $('#templates_form_html_code').val(), 'url': $('#templates_form_file_url').val(), 'id': $('#template_id').val() }, function(data) {
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
    
	// ���� �� ������ "������� backup"
	$('#makeBackup').click(function(){
		// �������� ������� ��� �������� ������ backup'�
		makeBackup();
		return false;
	});
	
	// ����� �� ��������
	$('#searchByName').on('keyup', function(){
		$('#resultSet').load('/control/edit_js/ajax.php', '\
		action=searchTemplates\
		&q=' + $('#searchByName').val()
		,
		function(response){});
	})
	// /����� �� ��������
	
	// �������� ��� backup'�
	$(document.body).delegate('.showAllBackups', "click", function(){
		$('#allBackups').toggle();
		// $(this).html("������ ��� backup'�");
		return false;
	})
	
	// ���� �� ������ "�������� backup"
	$(document.body).delegate('.showBackup', "click", function(){
		var showBackupID = $(this).attr('backupID'); // alert(showBackupID);
		showBackupID = showBackupID.replace('showBackup', ''); // alert(showBackupID);
		
		if (showBackupID) $('#html_code' + showBackupID).slideToggle();
		return false;
	});
	
	// ���� �� ������ "������� backup"
	$(document.body).delegate('.removeBackup', "click", function(){
		var removeBackupID = $(this).attr('backupID'); // alert(removeBackupID);
		removeBackupID = removeBackupID.replace('removeBackup', ''); // alert(removeBackupID);
		var date = $('#date' + removeBackupID).html(); // alert(date);
		if (confirm('������� backup �� [ ' + date +' ] ?')) {
			// �������� ID �������� ��� ��������
			// �������� ������� ��� �������� ������ backup'�
			removeBackup(removeBackupID);
		}
		
		return false;
	});
	
	// ���� �� ������ "�������� ������" - ��������� �� ������
	$('.checkForErrors').click(function()
	{
		var isError = $('.error').length; // alert(isError);
		
		if (isError)
		{
			alert('���������� ��������� ������. ����� ���� ����� ����� ��������� ���� js-����.');
			return false;
		}
	})
	
	// ��������� �������� � ���� "��� �����", ��������� �� ������
	$('#file_name').on('keyup click', function()
	{
		if (isGetVarExists('action') == 'addItem') checkTemplateForErrorsAddItem();
		else if (isGetVarExists('action') == 'editItem') checkTemplateForErrorsEditItem();
	})
	// ��������� �������� ����� ����� �� ������ ������� "�������� ��������" � ��� ������ �������� � ���� "��� �����"
	if (isGetVarExists('action') == 'addItem') $('#file_name').keyup();
	
	// ����������
	// �������� ��� backup'�
	function getAllBackups(){
		var template_id = $('#template_id').val(); // alert(template_id);
		
		$.ajax({
				url: "/control/edit_js/ajax.php", // ���������
				type: 'POST',
				data: { action: 'getAllBackups', template_id: template_id }, // �������� GET ����������
				dataType: 'html',
				beforeSend:
					function(){
						// $('#backupsResult').html(''); 
					},
				success:
					function(data){
						$('#backupsResult').html(data); //��������� ������
					},
				error:
					function(XMLHttpRequest, textStatus, errorThrown) {
						alert("������ ��� ���������� ajax-�������. ����������, ���������� � �������������� �����.");
					},
		});

		return false;
	}
	
	// ������� ����� backup'�
	function makeBackup(){
		var template_id = $('#template_id').val(); // alert(template_id);
		var html_code = $('#templates_form_html_code').val(); // alert(html_code);
		var url = $('#templates_form_file_url').val(); // alert(html_code);
		
		$.ajax({
				url: '/control/edit_js/ajax.php', // ���������     
				type: 'POST',
				data: { 'action': 'makeBackup', 'template_id': template_id, 'html_code': html_code, 'url': url }, // �������� GET ����������
				dataType: 'html',
				beforeSend:
					function(){
						// $('#backupsResult').html(''); 
					},
				success:
					function(data){
						$('#backupsResult').html(data); //��������� ������
					},
				error:
					function(XMLHttpRequest, textStatus, errorThrown) {
						alert("������ ��� ���������� ajax-�������: " + errorThrown + " (" + textStatus + "). ����������, ���������� � �������������� �����.");
					},
		});

		return false;
	}
	
	// ������� backup'�
	function removeBackup(removeBackupID){
		if (removeBackupID)
		{
			$('#' + removeBackupID).html('<b>������.</b>');
			// alert(removeBackupID);
			
			$.ajax({
					url: "/control/edit_js/ajax.php", // ���������                     
					type: 'POST',
					data: { action: 'removeBackup', id: removeBackupID }, // �������� GET ����������
					dataType: 'html',
					beforeSend:
						function(){
							// $('#backupsResult').html(''); 
						},
					success:
						function(data){
							// alert(data);
							// $('#backupsResult').html(data); //��������� ������
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("������ ��� ���������� ajax-�������. ����������, ���������� � �������������� �����.");
						},
			});

			return false;
		}
	}
	
	// ��������� ������ �� ������
	function checkTemplateForErrorsAddItem()
	{
		var filename = $('#file_name').val(); // alert(filename);
		var pathToTemplates = $('#pathToTemplates').val(); // alert(pathToTemplates);
		
		if (filename.length)
		{
			$.ajax({
					url: "/control/edit_js/ajax.php", // ���������                     
					type: 'POST',
					data: { action: 'checkTemplateForErrorsAddItem', filename: filename, pathToTemplates: pathToTemplates }, // �������� GET ����������
					dataType: 'html',
					beforeSend:
						function(){
							$('#ajax_result').html('');
							$('#ajax_result').css('display', 'none');
						},
					success:
						function(data){
							var length = data.length; // alert(length);
							
							if (length){
								$('#ajax_result').html(data); // ��������� ������
								$('#ajax_result').css('display', 'block');
							}
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("������ ��� ���������� ajax-�������. ����������, ���������� � �������������� �����.");
						},
			});

			return false;
		}
	}
	
	// ��������� ������ �� ������
	function checkTemplateForErrorsEditItem()
	{
		var filename = $('#file_name').val(); // alert(filename);
		var pathToTemplates = $('#pathToTemplates').val(); // alert(pathToTemplates);
		
		if (filename.length)
		{
			$.ajax({
					url: "/control/edit_js/ajax.php", // ���������                     
					type: 'POST',
					data: { action: 'checkTemplateForErrorsEditItem', filename: filename, pathToTemplates: pathToTemplates }, // �������� GET ����������
					dataType: 'html',
					beforeSend:
						function(){
							$('#ajax_result').html('');
							$('#ajax_result').css('display', 'none');
						},
					success:
						function(data){
							var length = data.length; // alert(length);
							
							if (length){
								$('#ajax_result').html(data); // ��������� ������
								$('#ajax_result').css('display', 'block');
							}
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("������ ��� ���������� ajax-�������. ����������, ���������� � �������������� �����.");
						},
			});

			return false;
		}
	}
	
	// ���������, �������� �� ���������� � ��������� ��� ������
	function checkTemplatesDirForWriting()
	{
		var pathToTemplates = $('#pathToTemplates').val(); // alert(pathToTemplates);
		if (pathToTemplates.length)
		{
			$.ajax({
					url: "/control/edit_js/ajax.php", // ���������                     
					type: 'POST',
					data: { action: 'checkTemplatesDirForWriting', pathToTemplates: pathToTemplates }, // �������� GET ����������
					dataType: 'html',
					beforeSend:
						function(){
							$('#ajax_result').html('');
							$('#ajax_result').css('display', 'none');
						},
					success:
						function(data){
							var length = data.length; // alert(length);
							
							if (length){
								$('#ajax_result').html(data); // ��������� ������
								$('#ajax_result').css('display', 'block');
							}
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("������ ��� ���������� ajax-�������. ����������, ���������� � �������������� �����.");
						},
			});

			return false;
		}
	}
});