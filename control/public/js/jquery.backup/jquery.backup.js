// destination: ������ jquery ��� ������ � backup'��� ��������� ����� � ������� (� ����� �������)
// date of creation: 2015.1.24
// author: romanov.egor@gmail.com
// example of usage
// ��������� ������ backup'�� ��� ��������� �����
// $.backup({ 'table_name' : 'static_sections', 'entry_id' : isGetVarExists('itemID'), 'fields_name' : ['name', 'page_title', 'navigation', 'h1', 'text']});
(function ($) {
    // 
    // ������� ���������:
    // inputs_list
    // { 'table_name' : 'static_sections', 'entry_id' : '11', 'fields_name' : ['name', 'page_title', 'navigation', 'h1', 'text']}
    $.backup = function (inputs_list) {

        // �������� ����������
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
        // /�������� ����������

        // �������� ������ backup'��
        $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'getAllBackups', 'inputs_list' : inputs_list }, function (data) {
            if (data) var result = JSON.parse(data); // console.log(result.length);
            
            // �������� �� ���� �������� ����� �������
            $.each(inputs_list.fields_name, function (index, value) {
                var backupsCount = getBackupsCount(result, value);
                
                // ��������� �� ���� ����� ������
                var content = '<div class="jquery_backups" data-field-id="' + value + '" data-table-name="' + inputs_list.table_name + '" data-entry-id="' + inputs_list.entry_id + '"><a href="#" class="make_backup_button" data-for="' + value + '">������� backup html-����</a> | <a href="#" class="show_all_backups_button" data-for="' + value + '" data-backups-count="' + backupsCount + '">�������� ��� backup&#39;� (' + backupsCount + ')</a> <span class="backup_ajax_status"></span></div><div class="jquery_backups_data">' + getBackupsData(result, value, 6, '') + '</div>';
                
                if ($('#' + value).is('textarea')) {
                    // ����������� ��� texteare � ������� ������� �����
                    if ($('#' + value).hasClass('lined')) $('#' + value).closest('div.linedwrap').after(content);
                    else  $('#' + value).after(content);
                }
                else $('#' + value).after(content);
            }); // /�������� �� ���� �������� ����� �������
        });
        
        // ����� ��� �������� ������ ���������� backup'�� ��� ���������� ���� � �������
        // ������� ���������: 
        // ajax_data - ������ ������ �� ajax � ������� json
        // field_name - id ���� � �������
        function getBackupsCount(ajax_data, field_name) {
            var allBackupsCount = 0;
            if (typeof ajax_data !== "undefined" && ajax_data.length > 0) {
                $.each(ajax_data, function () {
                    if (this.field_name == field_name) allBackupsCount++;
                });
            }
            else allBackupsCount = 0;
            
            return allBackupsCount;
        } // /����� ��� �������� ������ ���������� backup'��
        
        // ����� ������� ��� backup'� ��� ���������� ���� � ������� � ������ ����
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
                        
                        // ���� ����������, �������� ������ ��� ��������� backup
                        if (backup_id_selected && backup_id_selected == this.id) {
                            backupIdSelected = '<span class="backup_just_created">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="/control/public/js/jquery.backup/images/arrow_left_green.png" width="13" height="13" /> &nbsp; <b>backup ������</b></span>';
                        }
                        else backupIdSelected = '';
                        
                        result += '<div id="backup_' + this.id + '" class="backup_item' + hideBackupClass + '">backup �� ' + this.date_add_day + ' ' + this.date_add_month + ' ' + this.date_add_year + ' ' + this.date_add_time + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="show_backup" backupid="showBackup45" href="#">html-���</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="remove_backup" data-backup-id="' + this.id + '" href="#">�������</a>' + backupIdSelected + '<br><div id="html_code45" class="backup_code" style="display:none;margin:10px 0"><b>HTML-���:</b><br><textarea style="width:95%;height:270px" class="form-control">' + this.html_code + '</textarea></div></div>';
                        
                        backupsCountIterator++;
                        
                        if (backupsCountIterator == (max_items_count)) {
                            result += '<a href="#" class="show_all_backups_extend_button">�������� ��� backup\'� (' + (getBackupsCount(ajax_data, field_name) - max_items_count) + ')</a>';
                        }
                    }
                });
            }
            else result = '';
            return result;
        } // /����� ������� ��� backup'� ��� ���������� ���� � �������
       
        // click �� "������� backup html-����"
        $(document.body).delegate('.jquery_backups .make_backup_button', "click", function () {
            // �������� id ����, backup ������� ����� �������
            var tableName = $(this).closest('.jquery_backups').attr('data-table-name');
            var entryId = $(this).closest('.jquery_backups').attr('data-entry-id');
            var fieldName = $(this).closest('.jquery_backups').attr('data-field-id');
            var backupsDataDiv = $(this).closest('.jquery_backups').next('.jquery_backups_data');
            // ������������ ajax preloader
            setAjaxStatus($(this).closest('.jquery_backups').find('.backup_ajax_status'));
            // ������ post-������ ��� �������� backup'�
            $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'makeBackup', 'table_name' : tableName, 'entry_id' : entryId, 'field_name' : fieldName, 'html_code' : $('#' + fieldName).val() }, function (data) { // console.log(data);
                // ��������� ������ backup'�� ��� �������� ����
                getAllBackupsForSpecificField(tableName, entryId, fieldName, backupsDataDiv, data);
                // ���������� ������ ���� backup'�� ��� �������� ����
                $(backupsDataDiv).css('display', 'block');
            });

            return false;
        }); // /click �� "������� backup html-����"
        
        // click �� "�������� ��� backup'�" (��� �������� ���� � �������)
        $(document.body).delegate('.jquery_backups .show_all_backups_button', "click", function () {
            // �������� ���������� backup'��
            var backupsCount = $(this).attr('data-backups-count'); // console.log(backupsCount);
            if (backupsCount > 0) $(this).closest('.jquery_backups').next('.jquery_backups_data').toggle();
            else alert("backup'�� ���.");
            
            return false;
        }); // /click �� "�������� ��� backup'�"
        
        // click �� "�������� ��� backup'� (31)" � ������ backup'��
        $(document.body).delegate('.jquery_backups_data .show_all_backups_extend_button', "click", function () {
            // ���������� ��� backup'� ��� �������� ���� � �������
            $(this).closest('.jquery_backups_data').find('.extended').toggleClass('hidden');
            return false;
        }); // /click �� "�������� ��� backup'� (31)"
        
        // click �� "html-���"
        $(document.body).delegate('.jquery_backups_data .show_backup', "click", function () {
            $(this).closest('.backup_item').find('.backup_code').slideToggle();
            return false;
        }); // /click �� "������� backup"
        
        // click on "�������" (backup)
        $(document.body).delegate('.jquery_backups_data .remove_backup', "click", function () {
            var backupID = $(this).attr('data-backup-id');
            var backupDiv = $(this).closest('.backup_item');
            var backupsDataDiv = $(this).closest('.jquery_backups_data');
            var backupsDiv = $(this).closest('.jquery_backups_data').prev('.jquery_backups');
            if (backupID && confirm("Backup ����� ������ ������������, ������� backup?")) {
                // ������������ ajax preloader
                setAjaxStatus($(backupsDiv).find('.backup_ajax_status'));
                // ������� backup
                $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'removeBackup', 'backup_id' : backupID }, function (data) {
                    if (data == 1) { // console.log(data);
                        $(backupDiv).html('backup ������.').addClass('removed_backups');
                        
                        // ��������� ������ "�������� ��� backup'� (1)"
                        var backupsCount = $(backupsDataDiv).find('.backup_item:not(.removed_backups)').length; // console.log(backupsCount);
                        $(backupsDiv).find('.show_all_backups_button').text('�������� ��� backup\'� (' + backupsCount + ')');
                        $(backupsDiv).find('.show_all_backups_button').attr('data-backups-count', backupsCount);
                        
                        if (backupsCount === 0) $(backupsDataDiv).slideToggle();
                    }
                });
            }
            return false;
        }); // /click on "�������" (backup)
        
        // �������� ������ backup'�� ��� �������� ����
        function getAllBackupsForSpecificField(tableName, entryId, fieldName, backupsDiv, backupIdSelected) {
            $.post('/control/public/js/jquery.backup/jquery.backup.ajax.php', { 'action' : 'getAllBackupsForSpecificField', 'table_name' : tableName, 'entry_id' : entryId, 'field_name' : fieldName }, function (data) {
                if (data) { // console.log(data);
                    // ������� ������� � ������� �� backap'�� �� �������� ���� � �������
                    $(backupsDiv).html('');
                    
                    var result = JSON.parse(data); // console.log(result); // console.log(result.length);
                    var dataForBackupsDiv = getBackupsData(result, fieldName, 6, backupIdSelected);
                    $(backupsDiv).html(dataForBackupsDiv);
                    
                    // ��������� ������ "�������� ��� backup'� (1)"
                    var backupsCount = $(backupsDiv).find('.backup_item').length;
                    $(backupsDiv).prev('.jquery_backups').find('a.show_all_backups_button').text('�������� ��� backup\'� (' + backupsCount + ')');
                    $(backupsDiv).prev('.jquery_backups').find('a.show_all_backups_button').attr('data-backups-count', backupsCount);
                }
            });
        } // /�������� ������ backup'�� ��� �������� ����
        
        // ���������� ���������� AJAX-�������
        // ������������� ���������� ������� ajax ��� ����������� ������� AJAX-��������
        function setAjaxStatus(resultElement) // ID ��������, ���� ��������� ������ AJAX-��������
        {
            // alert(eventElementID);
            $(document).ajaxSend(function ()
            {
                // $(resultElement).html(''); 
            });
            $(document).ajaxStart(function ()
            {
                // ������� ������ preloader's
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
                $(resultElement).html('������ ��� ��������.'); 
            });
        } // /���������� ���������� AJAX-�������
    };
})(jQuery);