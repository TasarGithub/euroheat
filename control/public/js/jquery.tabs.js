// document ready
$(document).ready(function() {
	// �������������� JQUERY UI TABS
	$( "#tabs" ).tabs();
	
	// ���� ������� ������� "JQUERY UI TABS"
	var tabState = $('#tabs_state').val();
	if (tabState) $("#tabs").tabs("option", "active", tabState);
	
	// �������: ���� �� ������� "JQUERY UI TABS"
	$('#tabs').tabs( {
		activate: function(event, ui) {
			// alert(ui.newTab.index());
			$('#tabs_state').val(ui.newTab.index()); // console.log(ui.newTab.index());
            // ��������� � textarea ������ ����� (��� ��� textarea, ������� ���� � ������� �������� � �� ������������������ ��� �������� ��������)
            $('div#tabs-' + (ui.newTab.index() + 1) + ' .lined').linedtextarea();
		}
	});
}); // /document ready