$(document).ready(function() { // jquery ready
    // set ajax preloader
    setAjaxStatus('#main_ajax_preloader');

    if ($(window).width() < 1050) $('.navbar-brand').css('font-size', '12px');
    
    // ��������� � textarea ������ �����
    $(".lined").linedtextarea();
    
    // ���������� tab'�� ��� textarea
    $("textarea").keydown(function(e) {
        if(e.keyCode === 9) { // tab was pressed
            // get caret position/selection
            var start = this.selectionStart;
            var end = this.selectionEnd;

            var $this = $(this);
            var value = $this.val();

            // set textarea value to: text before caret + tab + text after caret
            $this.val(value.substring(0, start)
                        + "   "
                        + value.substring(end));

            // put caret at right position again (add one for the tab)
            this.selectionStart = this.selectionEnd = start + 3;

            // prevent the focus lose
            e.preventDefault();
        }
    }); // /���������� tab'�� ��� textarea

    // ���������� ��������� ��� ���������
    $(document.body).delegate('[data-toggle="popover"]', 'mouseenter mouseleave',
        function(event) {
            if (event.type === 'mouseenter') {
                $(this).popover({ placement: 'auto bottom', html: true }).popover('show');
            }
            else if (event.type === 'mouseleave') $('[aria-describedby]').popover('destroy');
        }
    ); // /���������� ��������� ��� ���������

}); // /jquery ready

// ��������� ��� �������� ����� � ������� form_required �� �������������
function checkForm(formID) {
    if (!formID) return; // �������� �������� ����������
    if (formID.length) {
        var errors = 0;
        $.each($(formID + ' *'), function() {
             if ($(this).hasClass('form_required')) { // console.log($(this));
                var label = $(this).attr('data-required-label');
                if (!$(this).val()) {
                    $('[aria-describedby]').popover('destroy');
                    if (label) $(this).attr('data-content', label);
                    else $(this).attr('data-content', '���, ��������� ����');
                    $(this).attr('title', '');
                    // ������������ �� ������� �����
                    $('html, body').animate({ scrollTop: $(this).offset().top - 150 }, 500);
                    var that = this;
                    setTimeout(function(){ 
                        $(that).focus(); 
                        $(that).popover({ placement: 'auto right', html: true }).popover('show');
                    }, 500);
                    errors = 1;
                    return false;
                }
            }
        });
        if (!errors) return 1;
    }
}

// ������� ������� "enter" � ���� ��������� �������
$('#main_search_value').keypress(function(e) {
    var key = e.which;
    if(key == 13) searchForTemplates(); // the enter key code
});

// ���� �� ������ ����� �� ��������� �������
$('#main_search_button').on('click', function() {
    searchForTemplates();
});

// �������� ������
function searchForTemplates() {
    var q = $('#main_search_value').val();
    var region = $('#main_search_region').val();
    if (q) {
        $.post('/control/templates/ajax.php', { 'action': 'searchTemplates', 'q': q }, function(data) {
            try { 
                // var result = JSON.parse(data); // console.log(result);
                $('h1.page-header').html('����� �� ��������');
                $('#main_content').html(data);
            } catch(e) { console.log('error in searchForTemplates function: ' + e); }
        });
    }
    $('#main_search_value').focus().select();
    return false;
} // /�������� ������

// ���������� ���������� AJAX-�������
// ������������� ���������� ������� ajax ��� ����������� ������� AJAX-��������
function setAjaxStatus(resultElementID) { // ID ��������, ���� ��������� ������ AJAX-��������
    // show preloader after 1 second: 
    var $loader = $(resultElementID);
    var timer;
    $loader.hide();
    
	// alert(eventElementID);
	$(document).ajaxSend(function(){});
	$(document).ajaxStart(function(){
		// ������� ������ preloader'�
        // $('.project_ajax_preloader, #all_projects_ajax_preloader').html('');
        if (timer) clearTimeout(timer);
        timer = setTimeout(function()
        {
            // ��������� preloader
            $(resultElementID).html('<img src="/control/public/images/ajax_preloader_1.gif" border="0" style="width:20px;height:20px" /> &nbsp; <span id="ajax_preloader_span"></span>');
            $loader.show();
        },
        1000);
	});
	$(document).ajaxSuccess(function(){ $(resultElementID).html(''); });
	$(document).ajaxStop(function(){ 
        clearTimeout(timer);
        $loader.hide();
    });
	$(document).ajaxComplete(function(){ $(resultElementID).html(''); });
	$(document).ajaxError(function(){ $(resultElementID).html('������ ��� ��������.'); });

} // /���������� ���������� AJAX-�������

// ����������, ���������� �� GET-����������
function isGetVarExists(var_name) {
	if (typeof var_name === "undefined") return;
	
	var url = location.search; // console.log(url);
	url = url.replace('#', ''); 
	url = url.replace('?', ''); // console.log(url);
	
	if (url == var_name) return 1; // example: /?calc
	
	var vars = url.split("&");  // console.log(vars); // example: /?a=1&calc
	for (var i=0; i < vars.length; i++){
		var vars2 = vars[i].split("="); // console.log(vars2[0]);
		if (vars2[0] == var_name) return vars2[1]; // content of variable
	}
	return;
} // /����������, ���������� �� GET-����������

// �������� ��� ���������� ajax-�������
$.ajaxQ = (function(){
  var id = 0, Q = {};

  $(document).ajaxSend(function(e, jqx){
    jqx._id = ++id;
    Q[jqx._id] = jqx;
  });
  $(document).ajaxComplete(function(e, jqx){
    delete Q[jqx._id];
  });

  return {
    abortAll: function(){
      var r = [];
      $.each(Q, function(i, jqx){
        r.push(jqx._id);
        jqx.abort();
      });
      return r;
    }
  };
})(); // /�������� ��� ���������� ajax-�������