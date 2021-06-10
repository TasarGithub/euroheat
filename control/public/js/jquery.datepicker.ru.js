/* 
Russian (UTF-8) initialisation for the jQuery UI date picker plugin.
Written by Andrew Stromnov (stromnov@gmail.com). 
���������: http://api.jqueryui.com/1.8/datepicker/#method-setDate (���������, �� �� ��� ��������)
*/
jQuery(function($){
	$.datepicker.regional['ru'] = {
			closeText: '�������',
			prevText: '&#x3c;����',
			nextText: '����&#x3e;',
			currentText: '�������',
			monthNames: ['������','�������','����','������','���','����', '����','������','��������','�������','������','�������'],
			monthNamesShort: ['������','�������','�����','������','���','����','����','�������','��������','�������','������','�������'],
			dayNames: ['�����������','�����������','�������','�����','�������','�������','�������'],
			dayNamesShort: ['���','���','���','���','���','���','���'],
			dayNamesMin: ['��','��','��','��','��','��','��'],
			weekHeader: '��',
			dateFormat: 'dd-mm-yy',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});
