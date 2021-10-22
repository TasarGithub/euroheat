// document ready
$(document).ready(function() {
	// Р�РќР�Р¦Р�РђР›Р�Р—Р�Р РЈР•Рњ JQUERY UI TABS
	$( "#tabs" ).tabs();
	
	// Р•РЎР›Р� Р’Р«Р‘Р РђРќРђ Р’РљР›РђР”РљРђ "JQUERY UI TABS"
	var tabState = $('#tabs_state').val();
	if (tabState) $("#tabs").tabs("option", "active", tabState);
	
	// РЎРћР‘Р«РўР�Р•: РљР›Р�Рљ РќРђ Р’РљР›РђР”РљР• "JQUERY UI TABS"
	$('#tabs').tabs( {
		activate: function(event, ui) {
			// alert(ui.newTab.index());
			$('#tabs_state').val(ui.newTab.index()); // console.log(ui.newTab.index());
            // РґРѕР±Р°РІР»СЏРµРј Рє textarea РЅРѕРјРµСЂР° СЃС‚СЂРѕРє (РґР»СЏ С‚РµС… textarea, РєРѕС‚РѕСЂС‹Рµ Р±С‹Р»Рё РІ СЃРєСЂС‹С‚С‹С… СЃРєР»Р°РґРєР°С… Рё РЅРµ РёРЅРёС†РёР°Р»РёР·РёСЂРѕРІР°Р»РёСЃСЊ РїСЂРё Р·Р°РіСЂСѓР·РєРµ СЃС‚СЂР°РЅРёС†С‹)
            $('div#tabs-' + (ui.newTab.index() + 1) + ' .lined').linedtextarea();
		}
	});
}); // /document ready