// destination: модуль jquery для работы с backup'ами выбранных полей в админке (в любом разделе)
// date of creation: 2015.1.24
// author: romanov.egor@gmail.com
// example of usage
// формируем список backup'ов для указанных полей
// $.backup({ 'table_name' : 'static_sections', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : ['name', 'page_title', 'navigation', 'h1', 'text']});
(function ($) {
    // 
    // входные параметры:
    // inputs_list
    // { 'table_name' : 'static_sections', 'entry_id' : '11', 'fields_name' : ['name', 'page_title', 'navigation', 'h1', 'text']}
    $.backup = function (inputs_list) {

        // проверка переменных
        if (inputs_list === 'undefined') { 
            console.log('jquery module "backup" can not start because "inputs_list" variable is not defined.');
            return;
        }
        if (inputs_list.table_name === 'undefined') { 
            console.log('jquery module "backup" can not start because "table_name" variable is not defined.');
            return;
        }
        if (inputs_list.entry_id === 'undefined') { 
            console.log('jquery module "backup" can not start because "entry_id" variable is not defined.');
            return;
        }
        if (inputs_list.fields_name === 'undefined') { 
            console.log('jquery module "backup" can not start because "fields_name" variable is not defined.');
            return;
        }
        // /проверка переменных

        // получаем список backup'ов
        $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'getAllBackups', 'inputs_list' : inputs_list }, function (data) {
            if (data) var result = JSON.parse(data); // console.log(result.length);
            
            // проходим по всем указанны полям админки
            $.each(inputs_list.fields_name, function (index, value) {
                var backupsCount = getBackupsCount(result, value);
                
                // добавляем ко всем полям ссылки
                var content = '<div class="jquery_backups" data-field-id="' + value + '" data-table-name="' + inputs_list.table_name + '" data-entry-id="' + inputs_list.entry_id + '"><a href="#" class="make_backup_button" data-for="' + value + '">сделать backup html-кода</a> | <a href="#" class="show_all_backups_button" data-for="' + value + '" data-backups-count="' + backupsCount + '">показать все backup&#39;ы (' + backupsCount + ')</a> <span class="backup_ajax_status"></span></div><div class="jquery_backups_data">' + getBackupsData(result, value, 6, '') + '</div>';
                
                if ($('#' + value).is('textarea')) {
                    // модификация для texteare с выводом номеров строк
                    if ($('#' + value).hasClass('lined')) $('#' + value).closest('div.linedwrap').after(content);
                    else  $('#' + value).after(content);
                }
                else $('#' + value).after(content);
            }); // /проходим по всем указанны полям админки
        });
        
        // метод под подсчету общего количества backup'ов для выбранного поля в админке
        // входные параметры: 
        // ajax_data - массив данных из ajax в формате json
        // field_name - id поля в админке
        function getBackupsCount(ajax_data, field_name) {
            var allBackupsCount = 0;
            if (typeof ajax_data !== "undefined" && ajax_data.length > 0) {
                $.each(ajax_data, function () {
                    if (this.field_name == field_name) allBackupsCount++;
                });
            }
            else allBackupsCount = 0;
            
            return allBackupsCount;
        } // /метод под подсчету общего количества backup'ов
        
        // метод выводит все backup'ы для указанного поля в админке в скртом поле
        function getBackupsData(ajax_data, field_name, max_items_count, backup_id_selected) {
            var result = '';
            if (typeof ajax_data !== "undefined" && ajax_data.length > 0) {
                var backupsCountIterator = 0;
                var hideBackupClass = '';
                var backupIdSelected = '';
                $.each(ajax_data, function () {
                    if (this.field_name == field_name) {
                        if (backupsCountIterator > (max_items_count - 1)) hideBackupClass = ' hidden extended';
                        else hideBackupClass = '';
                        
                        // если необходимо, выделяем только что созданный backup
                        if (backup_id_selected && backup_id_selected == this.id) {
                            backupIdSelected = '<span class="backup_just_created">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="/control/public/js/jquery.backup/images/arrow_left_green.png" width="13" height="13" /> &nbsp; <b>backup создан</b></span>';
                        }
                        else backupIdSelected = '';
                        
                        result += '<div id="backup_' + this.id + '" class="backup_item' + hideBackupClass + '">backup от ' + this.date_add_day + ' ' + this.date_add_month + ' ' + this.date_add_year + ' ' + this.date_add_time + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="show_backup" backupid="showBackup45" href="#">html-код</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="remove_backup" data-backup-id="' + this.id + '" href="#">удалить</a>' + backupIdSelected + '<br><div id="html_code45" class="backup_code" style="display:none;margin:10px 0"><b>HTML-КОД:</b><br><textarea style="width:95%;height:270px" class="form-control">' + this.html_code + '</textarea></div></div>';
                        
                        backupsCountIterator++;
                        
                        if (backupsCountIterator == (max_items_count)) {
                            result += '<a href="#" class="show_all_backups_extend_button">показать все backup\'ы (' + (getBackupsCount(ajax_data, field_name) - max_items_count) + ')</a>';
                        }
                    }
                });
            }
            else result = '';
            return result;
        } // /метод выводит все backup'ы для указанного поля в админке
       
        // click на "сделать backup html-кода"
        $(document.body).delegate('.jquery_backups .make_backup_button', "click", function () {
            // получаем id поля, backup которго нужно сделать
            var tableName = $(this).closest('.jquery_backups').attr('data-table-name');
            var entryId = $(this).closest('.jquery_backups').attr('data-entry-id');
            var fieldName = $(this).closest('.jquery_backups').attr('data-field-id');
            var backupsDataDiv = $(this).closest('.jquery_backups').next('.jquery_backups_data');
            // устаналиваем ajax preloader
            setAjaxStatus($(this).closest('.jquery_backups').find('.backup_ajax_status'));
            // делаем post-запрос для создания backup'а
            $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'makeBackup', 'table_name' : tableName, 'entry_id' : entryId, 'field_name' : fieldName, 'html_code' : $('#' + fieldName).val() }, function (data) { // console.log(data);
                // обновляем список backup'ов для текущего поля
                getAllBackupsForSpecificField(tableName, entryId, fieldName, backupsDataDiv, data);
                // отображаем список всех backup'ов для текущего поля
                $(backupsDataDiv).css('display', 'block');
            });

            return false;
        }); // /click на "сделать backup html-кода"
        
        // click на "показать все backup'ы" (для текущего поля в админке)
        $(document.body).delegate('.jquery_backups .show_all_backups_button', "click", function () {
            // получаем количество backup'ов
            var backupsCount = $(this).attr('data-backups-count'); // console.log(backupsCount);
            if (backupsCount > 0) $(this).closest('.jquery_backups').next('.jquery_backups_data').toggle();
            else alert("backup'ов нет.");
            
            return false;
        }); // /click на "показать все backup'ы"
        
        // click на "показать все backup'ы (31)" в списке backup'ов
        $(document.body).delegate('.jquery_backups_data .show_all_backups_extend_button', "click", function () {
            // показываем все backup'ы для текущего поля в админке
            $(this).closest('.jquery_backups_data').find('.extended').toggleClass('hidden');
            return false;
        }); // /click на "показать все backup'ы (31)"
        
        // click на "html-код"
        $(document.body).delegate('.jquery_backups_data .show_backup', "click", function () {
            $(this).closest('.backup_item').find('.backup_code').slideToggle();
            return false;
        }); // /click на "сделать backup"
        
        // click on "удалить" (backup)
        $(document.body).delegate('.jquery_backups_data .remove_backup', "click", function () {
            var backupID = $(this).attr('data-backup-id');
            var backupDiv = $(this).closest('.backup_item');
            var backupsDataDiv = $(this).closest('.jquery_backups_data');
            var backupsDiv = $(this).closest('.jquery_backups_data').prev('.jquery_backups');
            if (backupID && confirm("Backup будет удален безвозвратно, удалить backup?")) {
                // устаналиваем ajax preloader
                setAjaxStatus($(backupsDiv).find('.backup_ajax_status'));
                // удаляем backup
                $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'removeBackup', 'backup_id' : backupID }, function (data) {
                    if (data == 1) { // console.log(data);
                        $(backupDiv).html('backup удален.').addClass('removed_backups');
                        
                        // обновляем ссылку "показать все backup'ы (1)"
                        var backupsCount = $(backupsDataDiv).find('.backup_item:not(.removed_backups)').length; // console.log(backupsCount);
                        $(backupsDiv).find('.show_all_backups_button').text('показать все backup\'ы (' + backupsCount + ')');
                        $(backupsDiv).find('.show_all_backups_button').attr('data-backups-count', backupsCount);
                        
                        if (backupsCount === 0) $(backupsDataDiv).slideToggle();
                    }
                });
            }
            return false;
        }); // /click on "удалить" (backup)
        
        // получаем список backup'ов для текущего поля
        function getAllBackupsForSpecificField(tableName, entryId, fieldName, backupsDiv, backupIdSelected) {
            $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'getAllBackupsForSpecificField', 'table_name' : tableName, 'entry_id' : entryId, 'field_name' : fieldName }, function (data) {
                if (data) { // console.log(data);
                    // очищаем область с данными по backap'ам по текущему полю в админке
                    $(backupsDiv).html('');
                    
                    var result = JSON.parse(data); // console.log(result); // console.log(result.length);
                    var dataForBackupsDiv = getBackupsData(result, fieldName, 6, backupIdSelected);
                    $(backupsDiv).html(dataForBackupsDiv);
                    
                    // обновляем ссылку "показать все backup'ы (1)"
                    var backupsCount = $(backupsDiv).find('.backup_item').length;
                    $(backupsDiv).prev('.jquery_backups').find('a.show_all_backups_button').text('показать все backup\'ы (' + backupsCount + ')');
                    $(backupsDiv).prev('.jquery_backups').find('a.show_all_backups_button').attr('data-backups-count', backupsCount);
                }
            });
        } // /получаем список backup'ов для текущего поля
        
        // ОБРАБОТЧИК ГЛОБАЛЬНЫХ AJAX-СОБЫТИЙ
        // устанавливаем глобальные события ajax для отображения статуса AJAX-запросов
        function setAjaxStatus(resultElement) // ID элемента, куда выводится статус AJAX-запросов
        {
            // alert(eventElementID);
            $(document).ajaxSend(function ()
            {
                // $(resultElement).html(''); 
            });
            $(document).ajaxStart(function ()
            {
                // очищаем другие preloader's
                $('.jquery_backups .backup_ajax_status').html('');

                $(resultElement).html('<img src="/control/public/js/jquery.backup/images/preloader_1_25x25.gif" width="13" height="13" border="0" />'); 
            });
            $(document).ajaxSuccess(function ()
            {
                $(resultElement).html(''); 
            });
            $(document).ajaxStop(function ()
            {
                $(resultElement).html(''); 
            });
            $(document).ajaxComplete(function ()
            {
                $(resultElement).html(''); 
            });
            $(document).ajaxError(function ()
            {
                $(resultElement).html('Ошибка при загрузке.'); 
            });
        } // /ОБРАБОТЧИК ГЛОБАЛЬНЫХ AJAX-СОБЫТИЙ
    };
})(jQuery);