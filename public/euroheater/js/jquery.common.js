// jquery ready

$(document).ready(function() {

    var pathname = window.location.pathname;

    // инициализация плагина jquery owl-carousel
    var owl1 = $('.owl-carousel_b_photos');
    owl1.owlCarousel({
        margin: 10,
        loop: true,
        nav: true,
        dots: false,
        responsive: {
          300: { items: 1 },
          770: { items: 2 },
          1000: { items: 3 }
        }
    });
    var owl2 = $('.owl-carousel_b_reviews');
    owl2.owlCarousel({
        margin: 10,
        loop: true,
        nav: true,
        dots: false,
        responsive: {
          300: { items: 1 },
          770: { items: 3 },
          1000: { items: 5 }
        }
    });
    var owl3 = $('.owl-carousel_b_reviews_txt');
    owl3.owlCarousel({
        margin: 10,
        loop: true,
        nav: false,
        dots: true,
        responsive: {
          300: { items: 1 },
          770: { items: 1 },
          1000: { items: 1 }
        }
    });
    var owl4 = $('.owl-carousel_b_clients');
    owl4.owlCarousel({
        margin: 10,
        loop: true,
        nav: true,
        dots: false,
        responsive: {
          300: { items: 1 },
          770: { items: 3 },
          1000: { items: 5 }
        }
    });

    // запуск jquery.fancybox
    $(".fancybox").fancybox({
        helpers : {
            title: {
                type: 'inside',
                position: 'bottom'
            }
        }
    });
      
    // переключатель меню для мобильной версии
		$('.switch_menu').click(function(){
						$("body").toggleClass("menu_active");
		});

				// раскрытие текста на клик по Подробнее
		$('.read_more').on('click', function() {
		$(this).closest('div').find('.hidden-text').fadeIn();
		$(this).remove();
		return false;
		});

		// показываем подсказку
		/*
		$('.slogan').hover(function(){
		jQuery(this).find('.hover-tip').fadeToggle();
		});      
		*/

    // плавающая шапка
    // if (!navigator.userAgent.match(/(iPod|iPhone)/)) {
    //     $('body').removeClass('no_float');
    //     if($('.header').length > 0) {
    //         $(window).scroll(function() {
    //             if ($(this).scrollTop() > 10) $('.header').addClass("active");
    //             else $('.header').removeClass("active");
    //         });
    //     }
		//  }
		 
		// if (typeof enable_floating_hat === "undefined" || enable_floating_hat == 0) $('body').addClass('no_float');
		// плавающая шапка

    // При закрытии модального окна bootstrap, убираем все подсказки
    $('.modal').on('hidden.bs.modal', function () {
        $(".qtip").remove();
    });

    // Отзывы клиентов. Клик по + или -
    $('.customer-feedback-vote').click(function() {
        var action = $(this).data('action'); // console.log(action);
        var id = $(this).parent().data('feedback-id'); // console.log(id);
        var that = this;
        if (id && action) {
            $.post('/public/js/jquery.ajax.feedback.php', { 'id': id, 'action': action }, function(data) {
                if (data) {
                    try {
                        var result = JSON.parse(data);
                        if (result['result'] == 'already_voted') showMessageLeft(that, 'Вы уже голосовали за этот отзыв.');
                        else {
                            if (result['votes_plus']) $('.feedback-votes-plus-' + result['id']).html('+' + result['votes_plus']);
                            if (result['votes_minus']) $('.feedback-votes-minus-' + result['id']).html('-' + result['votes_minus']);
                        }
                    }
                    catch(err) { console.log(err); }
                    // скрываем все подсказки
                    $(".qtip").remove();
                }
            });
        }
        return false;
    }); // /Отзывы клиентов. Клик по + или -

    if (pathname == '/raschet/') $('#calculation_form_name').focus();

}); // jquery ready

// НАСТРАИВАЕМ ГЛОБАЛЬНЫЕ ПЕРЕХВАТЧИКИ СОБЫТИЙ AJAX
function setAjaxStatus(resultElementID) {
    $(document).ajaxSend(function () {
    });
    $(document).ajaxStart(function () {
        $(resultElementID).html('<img src="/public/images/preloader_25x25.gif" border="0" />');
    });
    $(document).ajaxSuccess(function () {
        $(resultElementID).html('');
    });
    $(document).ajaxStop(function () {
        $(resultElementID).html('');
    });
    $(document).ajaxComplete(function () {
        $(resultElementID).html('');
    });
    $(document).ajaxError(function () {
        $(resultElementID).html('Ошибка при загрузке.');
    });
}
// ПОКАЗАТЬ СООБЩЕНИЕ JQUERY.QTIP
function showMessage(element, message) {
    if (!element)return;
    if (!message)return;
    $(element).qtip({
        content: message,
        show: {solo: true, ready: true},
        style: {classes: 'qtip-text-hint'},
        position: {my: 'left center', at: 'right center', target: $(element)}
    });
}
// ПОКАЗАТЬ СООБЩЕНИЕ JQUERY.QTIP СЛЕВА
function showMessageLeft(element, message) {
    if (!element)return;
    if (!message)return;
    $(element).qtip({
        content: message,
        show: {solo: true, ready: true},
        style: {classes: 'qtip-text-hint'},
        position: {my: 'right center', at: 'left center', target: $(element)}
    });
}
// ПОКАЗАТЬ СООБЩЕНИЕ JQUERY.QTIP СВЕРХУ
function showMessageTop(element, message) {
    if (!element)return;
    if (!message)return;
    $(element).qtip({
        content: message,
        show: {solo: true, ready: true},
        style: {classes: 'qtip-text-hint'},
        position: {my: 'bottom center', at: 'top center', target: $(element)}
    });
}
// ПРОВЕРЯЕМ, ЯВЛЯЮТСЯ ЛИ ПЕРВЫЕ 3 БУКВЫ СТРОКИ РАЗНЫМИ
function hasDifferentLetters(var_name) {
    if (typeof var_name === "undefined")return;
    if (var_name[0] != var_name[1] || var_name[0] != var_name[2] || var_name[1] != var_name[2])return 1;
}
// КОНВЕРТИРУЕМ РУССКИЕ БУКВЫ В АНГЛИЙСКИЕ
function convertRuLettersToEn(text) {
    if (!text || typeof text === "undefined")return '';
    text = text.replace(/а/g, 'a').replace(/А/g, 'A').replace(/б/g, 'b').replace(/Б/g, 'B').replace(/в/g, 'v').replace(/В/g, 'V').replace(/г/g, 'g').replace(/Г/g, 'G').replace(/д/g, 'd').replace(/Д/g, 'D').replace(/е/g, 'e').replace(/Е/g, 'E').replace(/ё/g, 'jo').replace(/Ё/g, 'Jo').replace(/ж/g, 'zh').replace(/Ж/g, 'Zh').replace(/з/g, 'z').replace(/З/g, 'Z').replace(/и/g, 'i').replace(/И/g, 'I').replace(/й/g, 'j').replace(/Й/g, 'J').replace(/к/g, 'k').replace(/К/g, 'K').replace(/л/g, 'l').replace(/Л/g, 'L').replace(/м/g, 'm').replace(/М/g, 'M').replace(/н/g, 'n').replace(/Н/g, 'N').replace(/о/g, 'o').replace(/О/g, 'O').replace(/п/g, 'p').replace(/П/g, 'P').replace(/р/g, 'r').replace(/Р/g, 'R').replace(/с/g, 's').replace(/С/g, 'S').replace(/т/g, 't').replace(/Т/g, 'T').replace(/у/g, 'u').replace(/У/g, 'U').replace(/ф/g, 'f').replace(/Ф/g, 'F').replace(/х/g, 'h').replace(/Х/g, 'H').replace(/ц/g, 'c').replace(/Ц/g, 'C').replace(/ч/g, 'ch').replace(/Ч/g, 'Ch').replace(/ш/g, 'sh').replace(/Ш/g, 'Sh').replace(/щ/g, 'sh').replace(/Щ/g, 'Sh').replace(/ъ/g, '').replace(/Ъ/g, '').replace(/ы/g, 'y').replace(/Ы/g, 'Y').replace(/ь/g, '').replace(/Ь/g, '').replace(/э/g, 'e').replace(/Э/g, 'E').replace(/ю/g, 'ju').replace(/Ю/g, 'Ju').replace(/я/g, 'ya').replace(/Я/g, 'Ya');
    return text;
}
// ПРОВЕРЯЕМ, СООТВЕТСТВУЕТ ЛИ EMAIL ШАБЛОНУ xxx@yyy.xx
function isEmailValid(email) {
    if (typeof email === "undefined") return;
    if(/(.+)@(.+){2,}\.(.+){2,}/.test(email)) return 1;
    else return;
}
// ЗАГЛУШКА ДЛЯ УДАЛЕННОГО КОДА GOOGLE ANALYTICS
function ga(a, b, c, d, e) {}

// ФУНКЦИЯ ВЗЯТА ИЗ /_templates/functions.js
function nextPhoto(go) {
    var holder = $("photoholder");
    var src = { 0: '' };
    src = holder.src.split("/");
    var avail = $("photoavail");
    avail = avail.innerHTML;
    var photos = { 0: '' };
    photos = avail.toString().split("###");
    var newname = -1;
    if (photos.length > 1) {
        for (var i = 0; i < photos.length; i=i+1) {
            if (photos[i] == src[ src.length - 1 ]) {
                if (go > 0) {
                    if (i == (photos.length - 1)) {
                        newname = photos[0];
                    } else {
                        newname = photos[i+1];
                    }
                } else {
                    if (i == 0) {
                        newname = photos[ photos.length - 1 ];
                    } else {
                        newname = photos[i-1];
                    }
                }
            }
        }
        holder.src = '/_images/photos/' + newname;
    }
}

// ОПРЕДЕЛЯЕМ, СУЩЕСТВУЕТ ЛИ GET-ПЕРЕМЕННАЯ
function isGetVarExists(var_name){
    if (typeof var_name === "undefined") return;

    var url = location.search; // console.log(url);
    url = url.replace('#', '');
    url = url.replace('?', ''); // console.log(url);

    if (url == var_name) return 1; // example: /?calc

    var vars = url.split("&");  // console.log(vars); // example: /?a=1&calc
    for (var i=0; i < vars.length; i++){
        if (vars[i].indexOf('=') != -1) var vars2 = vars[i].split("=");
        else var vars2 = vars[i];
        if (vars2[0] == var_name || vars2 == var_name) return vars2[1]; // content of variable
    }
    return;
}

// ОПРЕДЕЛЯЕМ, ЯВЛЯЕТСЯ ЛИ СТРОКА ЧИСЛОМ
function isInteger(s){
    if (typeof s !== "undefined"){
        return (s.toString().search(/^-?[0-9]+$/) == 0);
    }
}