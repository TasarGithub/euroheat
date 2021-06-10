$(document).ready(function(){
	// для формы добавления шаблона
    if (isGetVarExists('action') == 'addItem') {
		$('#templates_form_name').focus();
	}
	// для формы редактирования шаблона
    if (isGetVarExists('action') == 'editItem') {
        $('#templates_form_html_code').focus();
		// выводим список всех backup'ов
		getAllBackups();
	}
    
    // субмит формы добавления шаблона
    $('#templates_add_form').on('submit', function() {
        if (!checkForm('#templates_add_form')) return false;
    });
    
    // изменения в поле "Название шаблона"
    if (isGetVarExists('action') == 'addItem') {
        $('#templates_form_name').on('change keyup click', function() {
            var name = $.trim($('#templates_form_name').val());
            // получаем старое значение
            var old_value = $('#templates_form_name').attr('data-old-value');
            // console.log('name: ' + name + ', old_value:' + old_value);
            if (name.length && name != old_value) {
                // фиксируем старое значение
                $('#templates_form_name').attr('data-old-value', name);
                // отменяем все предыдущие ajax-запросы
                $.ajaxQ.abortAll();
                // провряем, есть ли шаблон в базе с указанным именем
                $.post('/control/edit_js/ajax.php', { 'action': 'check_template_for_existence_by_name', 'name': name }, function(data) {
                    if (data) {
                        try {
                            var result = JSON.parse(data); // console.log('%o', result);
                            if (result['result'] == 'exists') {
                                $('#templates_form_name_alert_div').html('В базе уже существует файл js-кода с указанным названием: <a href="/control/edit_js/?action=editItem&itemID=' + result['id'] + '" target="_blank">смотреть</a>.<br />Пжл, укажите другое название для файла js-кода.').removeClass('hidden');
                                $('#templates_form_name').focus();
                            }
                            else $('#templates_form_name_alert_div').html('').addClass('hidden');
                        }
                        catch(err) { console.log(err.message); }
                    }
                    else $('#templates_form_name_alert_div').html('').addClass('hidden');
                });
            }
        }); // /изменения в поле "Название шаблона"
    }
    
    // изменения в поле "Имя файла"
    $('#templates_form_file_name').on('change keyup click', function() {
        var file_name = $.trim($('#templates_form_file_name').val());
        // получаем старое значение
        var old_value = $('#templates_form_file_name').attr('data-old-value');
        // console.log('file_name: ' + file_name + ', old_value:' + old_value);
        if (file_name.length && file_name != old_value) {
            // фиксируем старое значение
            $('#templates_form_file_name').attr('data-old-value', file_name);
            // отменяем все предыдущие ajax-запросы
            $.ajaxQ.abortAll();
            // провряем, есть ли шаблон в базе с указанным именем
            $.post('/control/edit_js/ajax.php', { 'action': 'check_template_for_existence_by_file_name', 'file_name': file_name }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data); // console.log('%o', result);
                        if (result['result'] == 'exists') {
                            $('#templates_form_file_name_alert_div').html('В базе уже существует файл js-кода с указанным именем файла: <a href="/control/edit_js/?action=editItem&itemID=' + result['id'] + '" target="_blank">смотреть</a>.<br />Пжл, укажите другое имя файла.').removeClass('hidden');
                            // $('#templates_form_file_name').focus();
                        }
                        else
                        {
                            $('#templates_form_file_name_alert_div').html('').addClass('hidden');
                            // если в базе нет шаблона с указанным именем файла, но в файловой системе указанный файл существует
                            checkFileExistence();                       
                        }
                    }
                    catch(err) { console.log(err.message); }
                }
                else {
                    $('#templates_form_file_name_alert_div').html('').addClass('hidden');
                    // если в базе нет шаблона с указанным именем файла, но в файловой системе указанный файл существует
                    checkFileExistence();                       
                }
            });
        }
    });
    function checkFileExistence() {
        // отменяем все предыдущие ajax-запросы
        $.ajaxQ.abortAll();
        // провряем, нет ли в базе шаблона с таким же названием
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
    // /изменения в поле "Имя файла"
	
	// ЛОГИКА
    
    // Шаблоны: ссылка "Сохранить изменения"
    $(document.body).delegate('#templates_edit_save_changes', 'click', function(){
        // проверяем поля формы на заполненность
        if (checkForm('#templates_edit_form')) {
            // сохраняем информацию через ajax
            $.post('/control/edit_js/ajax.php', { 'action': 'edit_template_submit', 'name': $('#templates_form_name').val(), 'html_code': $('#templates_form_html_code').val(), 'url': $('#templates_form_file_url').val(), 'id': $('#template_id').val() }, function(data) {
                var d = new Date();
                var day = d.getDate(); if (day <= 9) day = '0' + day;
                var month = d.getMonth() + 1; if (month <= 9) month = '0' + month;
                var minutes = d.getMinutes(); if (minutes <= 9) minutes = '0' + minutes;
                var seconds = d.getSeconds(); if (seconds <= 9) seconds = '0' + seconds;
                var lastUpdateDate = day + '.' + month + '.' + d.getFullYear() + ' ' + d.getHours() + ':' + minutes + ':' + seconds;
                
                if (data == 'success') var dataResult = '<div class="alert alert-success alert_line">изменения успешно сохранены (' + lastUpdateDate + ')</div>';
                else var dataResult = '<div class="alert alert-danger alert_line">изменения НЕ сохранены, произошла ошибка (' + lastUpdateDate + ')</div>';
                
                $('.ajax_result').html(dataResult).fadeIn();
                
                setTimeout(function(){ $('.ajax_result').fadeOut(); }, 5000);
            });
            return false;
        }
    });
    
	// клик на ссылку "Сделать backup"
	$('#makeBackup').click(function(){
		// вызываем функция для создания нового backup'а
		makeBackup();
		return false;
	});
	
	// поиск по шаблонам
	$('#searchByName').on('keyup', function(){
		$('#resultSet').load('/control/edit_js/ajax.php', '\
		action=searchTemplates\
		&q=' + $('#searchByName').val()
		,
		function(response){});
	})
	// /поиск по шаблонам
	
	// показать все backup'ы
	$(document.body).delegate('.showAllBackups', "click", function(){
		$('#allBackups').toggle();
		// $(this).html("скрыть все backup'ы");
		return false;
	})
	
	// клик на ссылку "Смотреть backup"
	$(document.body).delegate('.showBackup', "click", function(){
		var showBackupID = $(this).attr('backupID'); // alert(showBackupID);
		showBackupID = showBackupID.replace('showBackup', ''); // alert(showBackupID);
		
		if (showBackupID) $('#html_code' + showBackupID).slideToggle();
		return false;
	});
	
	// клик на ссылку "Удалить backup"
	$(document.body).delegate('.removeBackup', "click", function(){
		var removeBackupID = $(this).attr('backupID'); // alert(removeBackupID);
		removeBackupID = removeBackupID.replace('removeBackup', ''); // alert(removeBackupID);
		var date = $('#date' + removeBackupID).html(); // alert(date);
		if (confirm('Удалить backup от [ ' + date +' ] ?')) {
			// получаем ID элемента для удаления
			// вызываем функция для создания нового backup'а
			removeBackup(removeBackupID);
		}
		
		return false;
	});
	
	// клик на кнопку "Добавить шаблон" - проверяем на ошибки
	$('.checkForErrors').click(function()
	{
		var isError = $('.error').length; // alert(isError);
		
		if (isError)
		{
			alert('Необходимо устранить ошибку. После чего можно будет сохранить файл js-кода.');
			return false;
		}
	})
	
	// изменение значения в поле "Имя файла", проверяем на ошибки
	$('#file_name').on('keyup click', function()
	{
		if (isGetVarExists('action') == 'addItem') checkTemplateForErrorsAddItem();
		else if (isGetVarExists('action') == 'editItem') checkTemplateForErrorsEditItem();
	})
	// запускаем проверку имени файла на случай нажатия "Обновить страницу" с уже вбитым значение в поле "Имя файла"
	if (isGetVarExists('action') == 'addItem') $('#file_name').keyup();
	
	// ФУНКЦИОНАЛ
	// показать все backup'ы
	function getAllBackups(){
		var template_id = $('#template_id').val(); // alert(template_id);
		
		$.ajax({
				url: "/control/edit_js/ajax.php", // обращение
				type: 'POST',
				data: { action: 'getAllBackups', template_id: template_id }, // передаем GET переменные
				dataType: 'html',
				beforeSend:
					function(){
						// $('#backupsResult').html(''); 
					},
				success:
					function(data){
						$('#backupsResult').html(data); //загружаем данные
					},
				error:
					function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Ошибка при выполнении ajax-запроса. Пожалуйста, обратитесь к администратору сайта.");
					},
		});

		return false;
	}
	
	// создать новый backup'а
	function makeBackup(){
		var template_id = $('#template_id').val(); // alert(template_id);
		var html_code = $('#templates_form_html_code').val(); // alert(html_code);
		var url = $('#templates_form_file_url').val(); // alert(html_code);
		
		$.ajax({
				url: '/control/edit_js/ajax.php', // обращение     
				type: 'POST',
				data: { 'action': 'makeBackup', 'template_id': template_id, 'html_code': html_code, 'url': url }, // передаем GET переменные
				dataType: 'html',
				beforeSend:
					function(){
						// $('#backupsResult').html(''); 
					},
				success:
					function(data){
						$('#backupsResult').html(data); //загружаем данные
					},
				error:
					function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Ошибка при выполнении ajax-запроса: " + errorThrown + " (" + textStatus + "). Пожалуйста, обратитесь к администратору сайта.");
					},
		});

		return false;
	}
	
	// удалить backup'а
	function removeBackup(removeBackupID){
		if (removeBackupID)
		{
			$('#' + removeBackupID).html('<b>удален.</b>');
			// alert(removeBackupID);
			
			$.ajax({
					url: "/control/edit_js/ajax.php", // обращение                     
					type: 'POST',
					data: { action: 'removeBackup', id: removeBackupID }, // передаем GET переменные
					dataType: 'html',
					beforeSend:
						function(){
							// $('#backupsResult').html(''); 
						},
					success:
						function(data){
							// alert(data);
							// $('#backupsResult').html(data); //загружаем данные
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Ошибка при выполнении ajax-запроса. Пожалуйста, обратитесь к администратору сайта.");
						},
			});

			return false;
		}
	}
	
	// проверяем шаблон на ошибки
	function checkTemplateForErrorsAddItem()
	{
		var filename = $('#file_name').val(); // alert(filename);
		var pathToTemplates = $('#pathToTemplates').val(); // alert(pathToTemplates);
		
		if (filename.length)
		{
			$.ajax({
					url: "/control/edit_js/ajax.php", // обращение                     
					type: 'POST',
					data: { action: 'checkTemplateForErrorsAddItem', filename: filename, pathToTemplates: pathToTemplates }, // передаем GET переменные
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
								$('#ajax_result').html(data); // загружаем данные
								$('#ajax_result').css('display', 'block');
							}
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Ошибка при выполнении ajax-запроса. Пожалуйста, обратитесь к администратору сайта.");
						},
			});

			return false;
		}
	}
	
	// проверяем шаблон на ошибки
	function checkTemplateForErrorsEditItem()
	{
		var filename = $('#file_name').val(); // alert(filename);
		var pathToTemplates = $('#pathToTemplates').val(); // alert(pathToTemplates);
		
		if (filename.length)
		{
			$.ajax({
					url: "/control/edit_js/ajax.php", // обращение                     
					type: 'POST',
					data: { action: 'checkTemplateForErrorsEditItem', filename: filename, pathToTemplates: pathToTemplates }, // передаем GET переменные
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
								$('#ajax_result').html(data); // загружаем данные
								$('#ajax_result').css('display', 'block');
							}
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Ошибка при выполнении ajax-запроса. Пожалуйста, обратитесь к администратору сайта.");
						},
			});

			return false;
		}
	}
	
	// проверяем, доступна ли директория с шаблонами для записи
	function checkTemplatesDirForWriting()
	{
		var pathToTemplates = $('#pathToTemplates').val(); // alert(pathToTemplates);
		if (pathToTemplates.length)
		{
			$.ajax({
					url: "/control/edit_js/ajax.php", // обращение                     
					type: 'POST',
					data: { action: 'checkTemplatesDirForWriting', pathToTemplates: pathToTemplates }, // передаем GET переменные
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
								$('#ajax_result').html(data); // загружаем данные
								$('#ajax_result').css('display', 'block');
							}
						},
					error:
						function(XMLHttpRequest, textStatus, errorThrown) {
							alert("Ошибка при выполнении ajax-запроса. Пожалуйста, обратитесь к администратору сайта.");
						},
			});

			return false;
		}
	}
});