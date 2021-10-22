// скрипты jquery для обработки форм

$(document).ready(function() { // jquery ready
    // Устанавливаем фокус в модальном окне "Заказать звонок"
    $('#callback_modal').on('shown.bs.modal', function () { $('#callback_form_name').focus(); });
    // Устанавливаем фокус в модальном окне "Расчет стоимости аренды"
    $('#calc_modal').on('shown.bs.modal', function () { $('#calc_form_name').focus(); });
    // Устанавливаем фокус в модальном окне "Заявка на аренду"
    $('#rent_modal').on('shown.bs.modal', function () { $('#rent_form_name').focus(); });
    // Устанавливаем фокус в модальном окне "Добавить отзыв"
    $('#review_modal').on('shown.bs.modal', function () { $('#feedback_form_name').focus(); });
    // Устанавливаем фокус в модальном окне "Добавить отзыв"
    $('#full_price_modal').on('shown.bs.modal', function () { $('#full_price_modal_name').focus(); });

    // ****************************************************************************************

    // Модальное окно "Заказать звонок". Обработчик нажатия клавиши Enter
    $('#callback_form_name, #callback_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#callback_form_submit_button').click(); return false; }
    });

    // Модальное окно "Заказать звонок". Подтверждение
    $('#callback_form_submit_button').click(function() {
        // setting container for ajax preloader
        setAjaxStatus('#callback_form_ajax_preloader.modal_ajax_preloader');

        var name = $.trim($('#callback_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#callback_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#callback_form_name').focus();
            return false;
        }

        var phone = $.trim($('#callback_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#callback_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#callback_form_phone').focus();
            return false;
        }

        if (name && phone) {
            $.post('/public/js/jquery.ajax.callback.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'phone': phone, 'phone_en': convertRuLettersToEn(phone), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#callback_modal #myModalLabel').html('Ваша заявка отправлена!');
                    $('#callback_modal .modal-footer').html('');
                    $('#callback_modal .modal-body').html(data);
                    // цель для метрики
                    yaCounter22280926.reachGoal('callback-data');
                }
            });
        }
    }); // /Модальное окно "Заказать звонок". Подтверждение

    // ****************************************************************************************

    // Модальное окно "Расчет стоимости аренды". Обработчик нажатия клавиши Enter
    $('#calc_form_name, #calc_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#calc_form_submit_button').click(); return false; }
    });

    // Модальное окно "Расчет стоимости аренды". Подтверждение
    $('#calc_form_submit_button').click(function() {
        // setting container for ajax preloader
        setAjaxStatus('#calc_form_ajax_preloader');

        var name = $.trim($('#calc_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#calc_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#calc_form_name').focus();
            return false;
        }

        var phone = $.trim($('#calc_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#calc_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#calc_form_phone').focus();
            return false;
        }

        if (name && phone) {
            $.post('/public/js/jquery.ajax.calculate.cost-of-rent.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'phone': phone, 'phone_en': convertRuLettersToEn(phone), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#calc_modal #myModalLabel').html('Ваша заявка отправлена!');
                    $('#calc_modal .modal-footer').html('');
                    $('#calc_modal .modal-body').html(data);
                    // цель для метрики
                     yaCounter22280926.reachGoal('raschet-data');
                }
            });
        }
    }); // /Модальное окно "Расчет стоимости аренды". Подтверждение

    // ****************************************************************************************

    // Форма "Расчет калькуляции на аренду ДЭС" (/raschet/). Обработчик нажатия клавиши Enter
    $('#calculation_form_name, #calculation_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#calculation_form_submit_button').click(); return false; }
    });

    // Форма "Расчет калькуляции на аренду ДЭС" (/raschet/). Подтверждение
    $('#calculation_form_submit_button').click(function() {
        // setting container for ajax preloader
        setAjaxStatus('#calculation_form_ajax_preloader');

        var name = $.trim($('#calculation_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#calculation_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#calculation_form_name').focus();
            return false;
        }

        var phone = $.trim($('#calculation_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#calculation_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#calculation_form_phone').focus();
            return false;
        }

        var power = $.trim($('#calculation_form_power option:selected').text());
        var duration = $.trim($('#calculation_form_duration option:selected').text());

        if (name && phone) {
            $.post('/public/js/jquery.ajax.calculation.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'phone': phone, 'phone_en': convertRuLettersToEn(phone), 'power': power, 'power_en': convertRuLettersToEn(power), 'duration': duration, 'duration_en': convertRuLettersToEn(duration), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#calculation_form').html(data);
                    // цель для метрики
                    // yaCounter34243715.reachGoal('calc');
                }
            });
        }
    }); // /Форма "Расчет калькуляции на аренду ДЭС" (/raschet/). Подтверждение

    // ****************************************************************************************

    // Модальное окно "Заявка на аренду". Обработчик нажатия клавиши Enter
    $('#rent_form_name, #rent_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#rent_form_submit_button').click(); return false; }
    });

    // Модальное окно "Заявка на аренду". Подтверждение
    $('#rent_form_submit_button').click(function() {
        // setting container for ajax preloader
        setAjaxStatus('#rent_form_ajax_preloader');

        var name = $.trim($('#rent_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#rent_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#rent_form_name').focus();
            return false;
        }

        var phone = $.trim($('#rent_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#rent_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#rent_form_phone').focus();
            return false;
        }

        if (name && phone) {
            $.post('/public/js/jquery.ajax.order.rent.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'phone': phone, 'phone_en': convertRuLettersToEn(phone), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#rent_modal #myModalLabel').html('Ваша заявка отправлена!');
                    $('#rent_modal .modal-footer').html('');
                    $('#rent_modal .modal-body').html(data);
                    // цель для метрики
                    yaCounter22280926.reachGoal('request-data');
                }
            });
        }
    }); // /Модальное окно "Заказать звонок". Подтверждение

    // ****************************************************************************************

    // Модальное окно "Добавить отзыв". Обработчик нажатия клавиши Enter
    $('#feedback_form_name, #feedback_form_activity').keypress(function(e) {
        if (e.which == 13) { $('#feedback_form_submit_button').click(); return false; }
    });

    // Модальное окно "Добавить отзыв". Подтверждение
    $('#feedback_form_submit_button').click(function() {
        // setting container for ajax preloader
        setAjaxStatus('#feedback_form_ajax_preloader');

        var name = $.trim($('#feedback_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#feedback_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#feedback_form_name').focus();
            return false;
        }

        var activity = $.trim($('#feedback_form_activity').val());
        if (!activity) {
            showMessage('#feedback_form_activity', 'Пожалуйста, укажите сферу деятельности.')
            $('#feedback_form_activity').focus();
            return false;
        }

        var text = $.trim($('#feedback_form_text').val());
        if (!text) {
            showMessage('#feedback_form_text', 'Пожалуйста, укажите Ваш отзыв.')
            $('#feedback_form_text').focus();
            return false;
        }

        if (name && activity && text) {
            $.post('/public/js/jquery.ajax.feedback.add.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'activity': activity, 'activity_en': convertRuLettersToEn(activity), 'text': text, 'text_en': convertRuLettersToEn(text), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#review_modal #myModalLabel').html('Ваш отзыв отправлен!');
                    $('#review_modal .modal-footer').html('');
                    $('#review_modal .modal-body').html(data);
                    // цель для метрики
                    yaCounter22280926.reachGoal('review-data');
                }
            });
        }
    }); // /Модальное окно "Добавить отзыв". Подтверждение

    // ****************************************************************************************

    // Форма "Задать вопрос" в разделе "Контакты". Обработчик нажатия клавиши Enter
    $('#contacts_form_name, #contacts_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#contacts_form_submit_button').click(); return false; }
    });

    // Форма "Задать вопрос" в разделе "Контакты". Подтверждение
    $('#contacts_form_submit_button').click(function() {
        
        // setting container for ajax preloader

        setAjaxStatus('#contacts_form_ajax_preloader');

        var name = $.trim($('#contacts_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#contacts_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#contacts_form_name').focus();
            return false;
        }

        var phone = $.trim($('#contacts_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#contacts_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#contacts_form_phone').focus();
            return false;
        }
        var email = $.trim($('#contacts_form_email').val());
                if (!email || !/@/.test(email) || !isEmailValid(email)) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#contacts_form_email').offset().top - 150 }, 100);
                    showMessage('#contacts_form_email', 'Пожалуйста, укажите свой E-mail');
                    $('#contacts_form_email').focus();
                    return false;
                }

        var text = $.trim($('#contacts_form_text').val());
        if (!text) {
            showMessage('#contacts_form_text', 'Пожалуйста, укажите Ваш вопрос.')
            $('#contacts_form_text').focus();
            return false;
        }

        if (name && phone  && email && text) {
            $.post('/public/js/jquery.ajax.contacts.php', { 
            'name': name, 'name_en': convertRuLettersToEn(name), 
            'phone': phone, 'phone_en': convertRuLettersToEn(phone), 
            'email': email, 'email_en': convertRuLettersToEn(email),
             'text': text, 'text_en': convertRuLettersToEn(text), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#contacts_form #contacts_form_label').html('Ваш вопрос отправлен!');
                    $('#contacts_form #contacts_form_body').html(data);
                    // цель для метрики
                    yaCounter22280926.reachGoal('contacts-data');
                }
            });
        }
    }); // /Форма "Задать вопрос" в разделе "Контакты"

    // ****************************************************************************************

    // Модальное окно "Запросить полный прайс-лист". Обработчик нажатия клавиши Enter
    $('#full_price_modal_name, #full_price_modal_email, #full_price_modal_phone').keypress(function(e) {
        if (e.which == 13) { $('#full_price_modal_submit_button').click(); return false; }
    });

    // Модальное окно "Запросить полный прайс-лист". Подтверждение
    $('#full_price_modal_submit_button').click(function() {
        // setting container for ajax preloader
        setAjaxStatus('#rent_form_ajax_preloader');

        var name = $.trim($('#full_price_modal_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#full_price_modal_name', 'Пожалуйста, укажите контактное имя.')
            $('#full_price_modal_name').focus();
            return false;
        }

        // E-mail
        var email = $.trim($('#full_price_modal_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#full_price_modal_email').offset().top - 150 }, 100);
            showMessage('#full_price_modal_email', 'Пожалуйста, укажите свой E-mail');
            $('#full_price_modal_email').focus();
            return false;
        }

        var phone = $.trim($('#full_price_modal_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#full_price_modal_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#full_price_modal_phone').focus();
            return false;
        }

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.full.price.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'email': email, 'email_en': convertRuLettersToEn(email), 'phone': phone, 'phone_en': convertRuLettersToEn(phone), 'url': window.location.href }, function(data) {
                if (data) {
                    $('#full_price_modal_title').html('Ваша заявка отправлена!');
                    $('#full_price_modal_body').html(data);
                    $('#full_price_modal_footer').html('');
                    // цель для метрики
                    // yaCounter22280926.reachGoal('request-data');
                }
            });
        }
    }); // /Модальное окно "Запросить полный прайс-лист". Подтверждение

    // ****************************************************************************************

    // Форма "Заявка на теплообменник". Обработчик нажатия клавиши Enter
    $('#request_form_name, #request_form_company, #request_form_city, #request_form_email, #request_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#request_form_submit_button').click(); return false; }
    });

    // Форма "Заявка на теплообменник". Подтверждение
    $('#request_form_submit_button').on('click', function() {
        // setting container for ajax preloader
        setAjaxStatus('#request_form_ajax_preloader');
        
        // Тип теплообменника
        var heatExchangerType = $('input[name=request_form_heat_exchanger_type]:checked').val();
        /* if (!heatExchangerType) {
            // прокручиваем
            $('html, body').animate({
                scrollTop: $('.heat_exchanger_type:first').offset().top - 250,
                finish: showMessageTop('.heat_exchanger_type:first', 'Пожалуйста, выберите тип теплообменника.')
            }, 100);
            return false;
        } */

        // Расход воздуха
        var airSpanding = $.trim($('#request_form_air_spending').val());
        /* if (!airSpanding) {
            showMessage('#request_form_air_spending', 'Пожалуйста, укажите расход воздуха.')
            $('#request_form_air_spending').focus();
            return false;
        } */

        // Расход теплоносителя
        var coolantSpanding = $.trim($('#request_form_coolant_spending').val());
        /* if (!coolantSpanding) {
            showMessage('#request_form_coolant_spending', 'Пожалуйста, укажите расход теплоносителя.')
            $('#request_form_coolant_spending').focus();
            return false;
        } */

        // Температура воздуха на входе
        var inputAirTemperature = $.trim($('#request_form_input_air_temperature').val());

        // Температура воздуха на выходе
        var outputAirTemperature = $.trim($('#request_form_output_air_temperature').val());

        // Температура теплоносителя на входе
        var inputCoolantTemperature = $.trim($('#request_form_input_coolant_temperature').val());

        // Температура теплоносителя на выходе
        var outputCoolantTemperature = $.trim($('#request_form_output_coolant_temperature').val());

        // Требуемая мощность
        var power = $.trim($('#request_form_power').val());

        // Длина FTA
        var ftaLength = $.trim($('#request_form_fta_length').val());

        // Высота FTB
        var ftbHength = $.trim($('#request_form_ftb_height').val());

        // Ширина S
        var sWidth = $.trim($('#request_form_s_width').val());

        // Диаметр подводящих патрубков на входе C
        var inputCdiameter = $.trim($('#request_form_input_s_diameter').val());

        // Диаметр подводящих патрубков на выходе C
        var outputCdiameter = $.trim($('#request_form_output_s_diameter').val());

        // Узел регулирования (типовой)
        var unit = $.trim($('#request_form_unit').is(':checked'));

        // ФИО
        var name = $.trim($('#request_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#request_form_name').offset().top - 150 }, 100);
            showMessage('#request_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#request_form_name').focus();
            return false;
        }

        // Компания
        var company = $.trim($('#request_form_company').val());

        // Город
        var city = $.trim($('#request_form_city').val());

        // E-mail
        var email = $.trim($('#request_form_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#request_form_email').offset().top - 150 }, 100);
            showMessage('#request_form_email', 'Пожалуйста, укажите свой E-mail');
            $('#request_form_email').focus();
            return false;
        }

        // Телефон
        var phone = $.trim($('#request_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#request_form_phone').offset().top - 150 }, 100);
            showMessage('#request_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#request_form_phone').focus();
            return false;
        }

        // Примечания
        var notes = $.trim($('#request_form_notes').val());

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.request.heat.exchanger.php', {
                'heatExchangerType': heatExchangerType,
                    'heatExchangerTypeEn': convertRuLettersToEn(heatExchangerType),
                'airSpanding': airSpanding,
                    'airSpandingEn': convertRuLettersToEn(airSpanding),
                'coolantSpanding': coolantSpanding,
                    'coolantSpandingEn': convertRuLettersToEn(coolantSpanding),
                'inputAirTemperature': inputAirTemperature,
                    'inputAirTemperatureEn': convertRuLettersToEn(inputAirTemperature),
                'outputAirTemperature': outputAirTemperature,
                    'outputAirTemperatureEn': convertRuLettersToEn(outputAirTemperature),
                'inputCoolantTemperature': inputCoolantTemperature,
                    'inputCoolantTemperatureEn': convertRuLettersToEn(inputCoolantTemperature),
                'outputCoolantTemperature': outputCoolantTemperature,
                    'outputCoolantTemperatureEn': convertRuLettersToEn(outputCoolantTemperature),
                'power': power,
                    'powerEn': convertRuLettersToEn(power),
                'ftaLength': ftaLength,
                    'ftaLengthEn': convertRuLettersToEn(ftaLength),
                'ftbHength': ftbHength,
                    'ftbHengthEn': convertRuLettersToEn(ftbHength),
                'sWidth': sWidth,
                    'sWidthEn': convertRuLettersToEn(sWidth),
                'inputCdiameter': inputCdiameter,
                    'inputCdiameterEn': convertRuLettersToEn(inputCdiameter),
                'outputCdiameter': outputCdiameter,
                    'outputCdiameterEn': convertRuLettersToEn(outputCdiameter),
                'unit': unit,
                    'unitEn': convertRuLettersToEn(unit),
                'name': name,
                    'nameEn': convertRuLettersToEn(name),
                'company': company,
                    'companyEn': convertRuLettersToEn(company),
                'city': city,
                    'cityEn': convertRuLettersToEn(city),
                'email': email,
                    'emailEn': convertRuLettersToEn(email),
                'phone': phone,
                    'phoneEn': convertRuLettersToEn(phone),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'url': window.location.href
                }, function(data) {
                if (data) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#h1').offset().top - 150 }, 100);
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#request_form').html(data);
                    // цель для метрики
                    yaCounter22280926.reachGoal('to-data');
                }
            });
        }
    });

    // ****************************************************************************************

    // Форма "Расчет водяного нагревателя". Обработчик нажатия клавиши Enter
    $('#water_heater_form_name, #water_heater_form_company, #water_heater_form_city, #water_heater_form_email, #water_heater_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#water_heater_form_submit_button').click(); return false; }
    });

    // Форма "Расчет водяного нагревателя". Подтверждение
    $('#water_heater_form_submit_button').on('click', function() {
        // setting container for ajax preloader
        setAjaxStatus('#water_heater_form_ajax_preloader');

        // Данные по размерам: FTA, мм
        var sizesFTA = $('#water_heater_form_fta').val();
        // Данные по размерам: FTB, мм
        var sizesFTB = $('#water_heater_form_ftb').val();
        // Данные по размерам: A, мм
        var sizesA = $('#water_heater_form_a').val();
        // Данные по размерам: B, мм
        var sizesB = $('#water_heater_form_b').val();
        // Данные по размерам: S, мм
        var sizesS = $('#water_heater_form_s').val();
        // Данные по размерам: Диаметр E
        var sizesDiameterE = $('#water_heater_form_e_diameter').val();
        // Данные по размерам: Диаметр U
        var sizesDiameterU = $('#water_heater_form_input_u_diameter').val();
        // Данные по размерам: Рядность
        var sizesLane = $('#water_heater_form_lane').val();
        // Данные по размерам: Шаг ламели, мм
        var sizesLamellaStep = $('#water_heater_form_lamella_step').val();
        
        // Материалы: материал трубки
        var tubeMaterial = $('#water_heater_form_tube_material').val();
        // Материалы: материал ламелей
        var lamellaMaterial = $('#water_heater_form_lamella_material').val();

        // Техническое задание: расход воздуха
        var airSpending = $('#water_heater_form_air_spending').val();
        // Техническое задание: температура воздуха на входе
        var inputAirTemperature = $('#water_heater_form_input_air_temperature').val();
        // Техническое задание: температура воздуха на выходе
        var outputAirTemperature = $('#water_heater_form_output_air_temperature').val();
        // Техническое задание: тип теплоносителя
        var coolantType = $('#water_heater_form_coolant_type').val();
        // Техническое задание: температура теплоносителя на входе
        var inputCoolantTemperature = $('#water_heater_form_input_coolant_temperature').val();
        // Техническое задание: температура теплоносителя на выходе
        var outputCoolantTemperature = $('#water_heater_form_output_coolant_temperature').val();
        // Техническое задание: мощность
        var power = $('#water_heater_form_power').val();
        // Техническое задание: дополнительная информация
        var notes = $('#water_heater_form_notes').val();

        // ФИО
        var name = $.trim($('#water_heater_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#water_heater_form_name').offset().top - 150 }, 100);
            showMessage('#water_heater_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#water_heater_form_name').focus();
            return false;
        }

        // Компания
        var company = $.trim($('#water_heater_form_company').val());

        // Город
        var city = $.trim($('#water_heater_form_city').val());

        // E-mail
        var email = $.trim($('#water_heater_form_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#water_heater_form_email').offset().top - 150 }, 100);
            showMessage('#water_heater_form_email', 'Пожалуйста, укажите свой E-mail');
            $('#water_heater_form_email').focus();
            return false;
        }

        // Телефон
        var phone = $.trim($('#water_heater_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#water_heater_form_phone').offset().top - 150 }, 100);
            showMessage('#water_heater_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#water_heater_form_phone').focus();
            return false;
        }

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.request.water.heater.php', {
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTB': sizesFTB,
                    'sizesFTBEn': convertRuLettersToEn(sizesFTB),
                'sizesA': sizesA,
                    'sizesAEn': convertRuLettersToEn(sizesA),
                'sizesB': sizesB,
                    'sizesBEn': convertRuLettersToEn(sizesB),
                'sizesS': sizesS,
                    'sizesSEn': convertRuLettersToEn(sizesS),
                'sizesDiameterE': sizesDiameterE,
                    'sizesDiameterEEn': convertRuLettersToEn(sizesDiameterE),
                'sizesDiameterU': sizesDiameterU,
                    'sizesDiameterUEn': convertRuLettersToEn(sizesDiameterU),
                'sizesLane': sizesLane,
                    'sizesLaneEn': convertRuLettersToEn(sizesLane),
                'sizesLamellaStep': sizesLamellaStep,
                    'sizesLamellaStepEn': convertRuLettersToEn(sizesLamellaStep),
                'tubeMaterial': tubeMaterial,
                    'tubeMaterialEn': convertRuLettersToEn(tubeMaterial),
                'lamellaMaterial': lamellaMaterial,
                    'lamellaMaterialEn': convertRuLettersToEn(lamellaMaterial),
                'airSpending': airSpending,
                    'airSpendingEn': convertRuLettersToEn(airSpending),
                'inputAirTemperature': inputAirTemperature,
                    'inputAirTemperatureEn': convertRuLettersToEn(inputAirTemperature),
                'outputAirTemperature': outputAirTemperature,
                    'outputAirTemperatureEn': convertRuLettersToEn(outputAirTemperature),
                'coolantType': coolantType,
                    'coolantTypeEn': convertRuLettersToEn(coolantType),
                'inputCoolantTemperature': inputCoolantTemperature,
                    'inputCoolantTemperatureEn': convertRuLettersToEn(inputCoolantTemperature),
                'outputCoolantTemperature': outputCoolantTemperature,
                    'outputCoolantTemperatureEn': convertRuLettersToEn(outputCoolantTemperature),
                'power': power,
                    'powerEn': convertRuLettersToEn(power),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'name': name,
                    'nameEn': convertRuLettersToEn(name),
                'company': company,
                    'companyEn': convertRuLettersToEn(company),
                'city': city,
                    'cityEn': convertRuLettersToEn(city),
                'email': email,
                    'emailEn': convertRuLettersToEn(email),
                'phone': phone,
                    'phoneEn': convertRuLettersToEn(phone),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'url': window.location.href
                }, function(data) {
                if (data) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#h1').offset().top - 150 }, 100);
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#water_heater_form').html(data);
                    $('.water_heater_desc').remove();
                    // цель для метрики
                    yaCounter22280926.reachGoal('vn-data');
                }
            });
        }
    });

    // ****************************************************************************************

    // Форма "Расчет водяного охладителя". Обработчик нажатия клавиши Enter
    $('#water_cooler_form_name, #water_cooler_form_company, #water_cooler_form_city, #water_cooler_form_email, #water_cooler_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#water_cooler_form_submit_button').click(); return false; }
    });

    // Форма "Расчет водяного охладителя". Подтверждение
    $('#water_cooler_form_submit_button').on('click', function() {
        // setting container for ajax preloader
        setAjaxStatus('#water_cooler_form_ajax_preloader');

        // Данные по размерам: FTA, мм
        var sizesFTA = $('#water_cooler_form_fta').val();
        // Данные по размерам: FTB, мм
        var sizesFTB = $('#water_cooler_form_ftb').val();
        // Данные по размерам: A, мм
        var sizesA = $('#water_cooler_form_a').val();
        // Данные по размерам: B, мм
        var sizesB = $('#water_cooler_form_b').val();
        // Данные по размерам: S, мм
        var sizesS = $('#water_cooler_form_s').val();
        // Данные по размерам: Диаметр E
        var sizesDiameterE = $('#water_cooler_form_e_diameter').val();
        // Данные по размерам: Диаметр U
        var sizesDiameterU = $('#water_cooler_form_input_u_diameter').val();
        // Данные по размерам: Рядность
        var sizesLane = $('#water_cooler_form_lane').val();
        // Данные по размерам: Шаг ламели, мм
        var sizesLamellaStep = $('#water_cooler_form_lamella_step').val();
        // Данные по размерам: Поддон и каплеуловитель
        var tray = $('#water_heater_tray').val();

        // Материалы: материал трубки
        var tubeMaterial = $('#water_cooler_form_tube_material').val();
        // Материалы: материал ламелей
        var lamellaMaterial = $('#water_cooler_form_lamella_material').val();

        // Техническое задание: расход воздуха
        var airSpending = $('#water_cooler_form_air_spending').val();
        // Техническое задание: влажность воздуха, %
        var airHumidity = $('#water_cooler_form_air_humidity').val();
        // Техническое задание: тип теплоносителя
        var coolantType = $('#water_cooler_form_coolant_type').val();
        // Техническое задание: температура воздуха на входе
        var inputAirTemperature = $('#water_cooler_form_input_air_temperature').val();
        // Техническое задание: температура воздуха на выходе
        var outputAirTemperature = $('#water_cooler_form_output_air_temperature').val();
        // Техническое задание: температура теплоносителя на входе
        var inputCoolantTemperature = $('#water_cooler_form_input_coolant_temperature').val();
        // Техническое задание: температура теплоносителя на выходе
        var outputCoolantTemperature = $('#water_cooler_form_output_coolant_temperature').val();
        // Техническое задание: мощность
        var power = $('#water_cooler_form_power').val();
        // Техническое задание: дополнительная информация
        var notes = $('#water_cooler_form_notes').val();

        // ФИО
        var name = $.trim($('#water_cooler_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#water_cooler_form_name').offset().top - 150 }, 100);
            showMessage('#water_cooler_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#water_cooler_form_name').focus();
            return false;
        }

        // Компания
        var company = $.trim($('#water_cooler_form_company').val());

        // Город
        var city = $.trim($('#water_cooler_form_city').val());

        // E-mail
        var email = $.trim($('#water_cooler_form_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#water_cooler_form_email').offset().top - 150 }, 100);
            showMessage('#water_cooler_form_email', 'Пожалуйста, укажите свой E-mail');
            $('#water_cooler_form_email').focus();
            return false;
        }

        // Телефон
        var phone = $.trim($('#water_cooler_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#water_cooler_form_phone').offset().top - 150 }, 100);
            showMessage('#water_cooler_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#water_cooler_form_phone').focus();
            return false;
        }

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.request.water.cooler.php', {
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTB': sizesFTB,
                    'sizesFTBEn': convertRuLettersToEn(sizesFTB),
                'sizesA': sizesA,
                    'sizesAEn': convertRuLettersToEn(sizesA),
                'sizesB': sizesB,
                    'sizesBEn': convertRuLettersToEn(sizesB),
                'sizesS': sizesS,
                    'sizesSEn': convertRuLettersToEn(sizesS),
                'sizesDiameterE': sizesDiameterE,
                    'sizesDiameterEEn': convertRuLettersToEn(sizesDiameterE),
                'sizesDiameterU': sizesDiameterU,
                    'sizesDiameterUEn': convertRuLettersToEn(sizesDiameterU),
                'sizesLane': sizesLane,
                    'sizesLaneEn': convertRuLettersToEn(sizesLane),
                'sizesLamellaStep': sizesLamellaStep,
                    'sizesLamellaStepEn': convertRuLettersToEn(sizesLamellaStep),
                'tray': tray,
                    'trayEn': convertRuLettersToEn(tray),
                'tubeMaterial': tubeMaterial,
                    'tubeMaterialEn': convertRuLettersToEn(tubeMaterial),
                'lamellaMaterial': lamellaMaterial,
                    'lamellaMaterialEn': convertRuLettersToEn(lamellaMaterial),
                'airSpending': airSpending,
                    'airSpendingEn': convertRuLettersToEn(airSpending),
                'airHumidity': airHumidity,
                    'airHumidityEn': convertRuLettersToEn(airHumidity),
                'inputAirTemperature': inputAirTemperature,
                    'inputAirTemperatureEn': convertRuLettersToEn(inputAirTemperature),
                'outputAirTemperature': outputAirTemperature,
                    'outputAirTemperatureEn': convertRuLettersToEn(outputAirTemperature),
                'coolantType': coolantType,
                    'coolantTypeEn': convertRuLettersToEn(coolantType),
                'inputCoolantTemperature': inputCoolantTemperature,
                    'inputCoolantTemperatureEn': convertRuLettersToEn(inputCoolantTemperature),
                'outputCoolantTemperature': outputCoolantTemperature,
                    'outputCoolantTemperatureEn': convertRuLettersToEn(outputCoolantTemperature),
                'power': power,
                    'powerEn': convertRuLettersToEn(power),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'name': name,
                    'nameEn': convertRuLettersToEn(name),
                'company': company,
                    'companyEn': convertRuLettersToEn(company),
                'city': city,
                    'cityEn': convertRuLettersToEn(city),
                'email': email,
                    'emailEn': convertRuLettersToEn(email),
                'phone': phone,
                    'phoneEn': convertRuLettersToEn(phone),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'url': window.location.href
                }, function(data) {
                if (data) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#h1').offset().top - 150 }, 100);
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#water_cooler_form').html(data);
                    // $('.water_heater_desc').remove();
                    // цель для метрики
                    yaCounter22280926.reachGoal('vo-data');
                }
            });
        }
    });

    // ****************************************************************************************

    // Форма "Расчет и подбор испарителя". Обработчик нажатия клавиши Enter
    $('#evaporator_form_name, #evaporator_form_company, #evaporator_form_city, #evaporator_form_email, #evaporator_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#evaporator_form_submit_button').click(); return false; }
    });

    // Форма "Расчет и подбор испарителя". Подтверждение
    $('#evaporator_form_submit_button').on('click', function() {
        // setting container for ajax preloader
        setAjaxStatus('#evaporator_form_ajax_preloader');

        // Данные по размерам: FTA, мм
        var sizesFTA = $('#evaporator_form_fta').val();
        // Данные по размерам: FTB, мм
        var sizesFTB = $('#evaporator_form_ftb').val();
        // Данные по размерам: A, мм
        var sizesA = $('#evaporator_form_a').val();
        // Данные по размерам: B, мм
        var sizesB = $('#evaporator_form_b').val();
        // Данные по размерам: S, мм
        var sizesS = $('#evaporator_form_s').val();
        // Данные по размерам: Диаметр E
        var sizesDiameterE = $('#evaporator_form_e_diameter').val();
        // Данные по размерам: Диаметр U
        var sizesDiameterU = $('#evaporator_form_input_u_diameter').val();
        // Данные по размерам: Рядность
        var sizesLane = $('#evaporator_form_lane').val();
        // Данные по размерам: Шаг ламели, мм
        var sizesLamellaStep = $('#evaporator_form_lamella_step').val();
        // Данные по размерам: Поддон и каплеуловитель
        var tray = $('#water_heater_tray').val();

        // Материалы: материал трубки
        var tubeMaterial = $('#evaporator_form_tube_material').val();
        // Материалы: материал ламелей
        var lamellaMaterial = $('#evaporator_form_lamella_material').val();

        // Техническое задание: расход воздуха
        var airSpending = $('#evaporator_form_air_spending').val();
        // Техническое задание: влажность воздуха, %
        var airHumidity = $('#evaporator_form_air_humidity').val();
        // Техническое задание: температура воздуха на входе
        var inputAirTemperature = $('#evaporator_form_input_air_temperature').val();
        // Техническое задание: температура воздуха на выходе
        var outputAirTemperature = $('#evaporator_form_output_air_temperature').val();
        // Техническое задание: тип фреона
        var freonType = $('#evaporator_form_freon_type').val();
        // Техническое задание: температура испарения фреона, °C
        var freonEvaporationTemperature = $('#evaporator_form_freon_evaporation_temperature').val();
        // Техническое задание: мощность
        var power = $('#evaporator_form_power').val();
        // Техническое задание: дополнительная информация
        var notes = $('#evaporator_form_notes').val();

        // ФИО
        var name = $.trim($('#evaporator_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#evaporator_form_name').offset().top - 150 }, 100);
            showMessage('#evaporator_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#evaporator_form_name').focus();
            return false;
        }

        // Компания
        var company = $.trim($('#evaporator_form_company').val());

        // Город
        var city = $.trim($('#evaporator_form_city').val());

        // E-mail
        var email = $.trim($('#evaporator_form_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#evaporator_form_email').offset().top - 150 }, 100);
            showMessage('#evaporator_form_email', 'Пожалуйста, укажите свой E-mail');
            $('#evaporator_form_email').focus();
            return false;
        }

        // Телефон
        var phone = $.trim($('#evaporator_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#evaporator_form_phone').offset().top - 150 }, 100);
            showMessage('#evaporator_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#evaporator_form_phone').focus();
            return false;
        }

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.request.evaporator.php', {
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTB': sizesFTB,
                    'sizesFTBEn': convertRuLettersToEn(sizesFTB),
                'sizesA': sizesA,
                    'sizesAEn': convertRuLettersToEn(sizesA),
                'sizesB': sizesB,
                    'sizesBEn': convertRuLettersToEn(sizesB),
                'sizesS': sizesS,
                    'sizesSEn': convertRuLettersToEn(sizesS),
                'sizesDiameterE': sizesDiameterE,
                    'sizesDiameterEEn': convertRuLettersToEn(sizesDiameterE),
                'sizesDiameterU': sizesDiameterU,
                    'sizesDiameterUEn': convertRuLettersToEn(sizesDiameterU),
                'sizesLane': sizesLane,
                    'sizesLaneEn': convertRuLettersToEn(sizesLane),
                'sizesLamellaStep': sizesLamellaStep,
                    'sizesLamellaStepEn': convertRuLettersToEn(sizesLamellaStep),
                'tray': tray,
                    'trayEn': convertRuLettersToEn(tray),
                'tubeMaterial': tubeMaterial,
                    'tubeMaterialEn': convertRuLettersToEn(tubeMaterial),
                'lamellaMaterial': lamellaMaterial,
                    'lamellaMaterialEn': convertRuLettersToEn(lamellaMaterial),
                'airSpending': airSpending,
                    'airSpendingEn': convertRuLettersToEn(airSpending),
                'airHumidity': airHumidity,
                    'airHumidityEn': convertRuLettersToEn(airHumidity),
                'inputAirTemperature': inputAirTemperature,
                    'inputAirTemperatureEn': convertRuLettersToEn(inputAirTemperature),
                'outputAirTemperature': outputAirTemperature,
                    'outputAirTemperatureEn': convertRuLettersToEn(outputAirTemperature),
                'freonType': freonType,
                    'freonTypeEn': convertRuLettersToEn(freonType),
                'freonEvaporationTemperature': freonEvaporationTemperature,
                    'freonEvaporationTemperatureEn': convertRuLettersToEn(freonEvaporationTemperature),
                'power': power,
                    'powerEn': convertRuLettersToEn(power),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'name': name,
                    'nameEn': convertRuLettersToEn(name),
                'company': company,
                    'companyEn': convertRuLettersToEn(company),
                'city': city,
                    'cityEn': convertRuLettersToEn(city),
                'email': email,
                    'emailEn': convertRuLettersToEn(email),
                'phone': phone,
                    'phoneEn': convertRuLettersToEn(phone),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'url': window.location.href
                }, function(data) {
                if (data) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#h1').offset().top - 150 }, 100);
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#evaporator_form').html(data);
                    // $('.water_heater_desc').remove();
                    // цель для метрики
                    yaCounter22280926.reachGoal('i-data');
                }
            });
        }
    });

    // ****************************************************************************************

    // Форма "Расчет и подбор конденсатора". Обработчик нажатия клавиши Enter
    $('#refrigerator_form_name, #refrigerator_form_company, #refrigerator_form_city, #refrigerator_form_email, #refrigerator_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#refrigerator_form_submit_button').click(); return false; }
    });

    // Форма "Расчет и подбор конденсатора". Подтверждение
    $('#refrigerator_form_submit_button').on('click', function() {
        // setting container for ajax preloader
        setAjaxStatus('#refrigerator_form_ajax_preloader');

        // Данные по размерам: FTA, мм
        var sizesFTA = $('#refrigerator_form_fta').val();
        // Данные по размерам: FTB, мм
        var sizesFTB = $('#refrigerator_form_ftb').val();
        // Данные по размерам: A, мм
        var sizesA = $('#refrigerator_form_a').val();
        // Данные по размерам: B, мм
        var sizesB = $('#refrigerator_form_b').val();
        // Данные по размерам: S, мм
        var sizesS = $('#refrigerator_form_s').val();
        // Данные по размерам: Диаметр E
        var sizesDiameterE = $('#refrigerator_form_e_diameter').val();
        // Данные по размерам: Диаметр U
        var sizesDiameterU = $('#refrigerator_form_input_u_diameter').val();
        // Данные по размерам: Рядность
        var sizesLane = $('#refrigerator_form_lane').val();
        // Данные по размерам: Шаг ламели, мм
        var sizesLamellaStep = $('#refrigerator_form_lamella_step').val();
        // Данные по размерам: Поддон и каплеуловитель
        var tray = $('#water_heater_tray').val();

        // Материалы: материал трубки
        var tubeMaterial = $('#refrigerator_form_tube_material').val();
        // Материалы: материал ламелей
        var lamellaMaterial = $('#refrigerator_form_lamella_material').val();

        // Техническое задание: расход воздуха
        var airSpending = $('#refrigerator_form_air_spending').val();
        // Техническое задание: влажность воздуха, %
        var airHumidity = $('#refrigerator_form_air_humidity').val();
        // Техническое задание: температура воздуха на входе
        var inputAirTemperature = $('#refrigerator_form_input_air_temperature').val();
        // Техническое задание: температура воздуха на выходе
        var outputAirTemperature = $('#refrigerator_form_output_air_temperature').val();
        // Техническое задание: тип фреона
        var freonType = $('#refrigerator_form_freon_type').val();
        // Техническое задание: температура испарения фреона, °C
        var freonEvaporationTemperature = $('#refrigerator_form_freon_evaporation_temperature').val();
        // Техническое задание: мощность
        var power = $('#refrigerator_form_power').val();
        // Техническое задание: дополнительная информация
        var notes = $('#refrigerator_form_notes').val();

        // ФИО
        var name = $.trim($('#refrigerator_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#refrigerator_form_name').offset().top - 150 }, 100);
            showMessage('#refrigerator_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#refrigerator_form_name').focus();
            return false;
        }

        // Компания
        var company = $.trim($('#refrigerator_form_company').val());

        // Город
        var city = $.trim($('#refrigerator_form_city').val());

        // E-mail
        var email = $.trim($('#refrigerator_form_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#refrigerator_form_email').offset().top - 150 }, 100);
            showMessage('#refrigerator_form_email', 'Пожалуйста, укажите свой E-mail');
            $('#refrigerator_form_email').focus();
            return false;
        }

        // Телефон
        var phone = $.trim($('#refrigerator_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#refrigerator_form_phone').offset().top - 150 }, 100);
            showMessage('#refrigerator_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#refrigerator_form_phone').focus();
            return false;
        }

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.request.refrigerator.php', {
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTB': sizesFTB,
                    'sizesFTBEn': convertRuLettersToEn(sizesFTB),
                'sizesA': sizesA,
                    'sizesAEn': convertRuLettersToEn(sizesA),
                'sizesB': sizesB,
                    'sizesBEn': convertRuLettersToEn(sizesB),
                'sizesS': sizesS,
                    'sizesSEn': convertRuLettersToEn(sizesS),
                'sizesDiameterE': sizesDiameterE,
                    'sizesDiameterEEn': convertRuLettersToEn(sizesDiameterE),
                'sizesDiameterU': sizesDiameterU,
                    'sizesDiameterUEn': convertRuLettersToEn(sizesDiameterU),
                'sizesLane': sizesLane,
                    'sizesLaneEn': convertRuLettersToEn(sizesLane),
                'sizesLamellaStep': sizesLamellaStep,
                    'sizesLamellaStepEn': convertRuLettersToEn(sizesLamellaStep),
                'tray': tray,
                    'trayEn': convertRuLettersToEn(tray),
                'tubeMaterial': tubeMaterial,
                    'tubeMaterialEn': convertRuLettersToEn(tubeMaterial),
                'lamellaMaterial': lamellaMaterial,
                    'lamellaMaterialEn': convertRuLettersToEn(lamellaMaterial),
                'airSpending': airSpending,
                    'airSpendingEn': convertRuLettersToEn(airSpending),
                'airHumidity': airHumidity,
                    'airHumidityEn': convertRuLettersToEn(airHumidity),
                'inputAirTemperature': inputAirTemperature,
                    'inputAirTemperatureEn': convertRuLettersToEn(inputAirTemperature),
                'outputAirTemperature': outputAirTemperature,
                    'outputAirTemperatureEn': convertRuLettersToEn(outputAirTemperature),
                'freonType': freonType,
                    'freonTypeEn': convertRuLettersToEn(freonType),
                'freonEvaporationTemperature': freonEvaporationTemperature,
                    'freonEvaporationTemperatureEn': convertRuLettersToEn(freonEvaporationTemperature),
                'power': power,
                    'powerEn': convertRuLettersToEn(power),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'name': name,
                    'nameEn': convertRuLettersToEn(name),
                'company': company,
                    'companyEn': convertRuLettersToEn(company),
                'city': city,
                    'cityEn': convertRuLettersToEn(city),
                'email': email,
                    'emailEn': convertRuLettersToEn(email),
                'phone': phone,
                    'phoneEn': convertRuLettersToEn(phone),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'url': window.location.href
                }, function(data) {
                if (data) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#h1').offset().top - 150 }, 100);
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#refrigerator_form').html(data);
                    // $('.water_heater_desc').remove();
                    // цель для метрики
                    yaCounter22280926.reachGoal('c-data');
                }
            });
        }
    });

    // ****************************************************************************************

    // Форма "Расчет водяного нагревателя". Обработчик нажатия клавиши Enter
    $('#steam_heat_exchanger_form_name, #steam_heat_exchanger_form_company, #steam_heat_exchanger_form_city, #steam_heat_exchanger_form_email, #steam_heat_exchanger_form_phone').keypress(function(e) {
        if (e.which == 13) { $('#steam_heat_exchanger_form_submit_button').click(); return false; }
    });

    // Форма "Расчет водяного нагревателя". Подтверждение
    $('#steam_heat_exchanger_form_submit_button').on('click', function() {
        // setting container for ajax preloader
        setAjaxStatus('#steam_heat_exchanger_form_ajax_preloader');

        // Данные по размерам: FTA, мм
        var sizesFTA = $('#steam_heat_exchanger_form_fta').val();
        // Данные по размерам: FTB, мм
        var sizesFTB = $('#steam_heat_exchanger_form_ftb').val();
        // Данные по размерам: A, мм
        var sizesA = $('#steam_heat_exchanger_form_a').val();
        // Данные по размерам: B, мм
        var sizesB = $('#steam_heat_exchanger_form_b').val();
        // Данные по размерам: S, мм
        var sizesS = $('#steam_heat_exchanger_form_s').val();
        // Данные по размерам: Диаметр E
        var sizesDiameterE = $('#steam_heat_exchanger_form_e_diameter').val();
        // Данные по размерам: Диаметр U
        var sizesDiameterU = $('#steam_heat_exchanger_form_input_u_diameter').val();
        // Данные по размерам: Рядность
        var sizesLane = $('#steam_heat_exchanger_form_lane').val();
        // Данные по размерам: Шаг ламели, мм
        var sizesLamellaStep = $('#steam_heat_exchanger_form_lamella_step').val();

        // Материалы: материал трубки
        var tubeMaterial = $('#steam_heat_exchanger_form_tube_material').val();
        // Материалы: материал ламелей
        var lamellaMaterial = $('#steam_heat_exchanger_form_lamella_material').val();

        // Техническое задание: расход воздуха
        var airSpending = $('#steam_heat_exchanger_form_air_spending').val();
        // Техническое задание: температура воздуха на входе
        var inputAirTemperature = $('#steam_heat_exchanger_form_input_air_temperature').val();
        // Техническое задание: температура воздуха на выходе
        var outputAirTemperature = $('#steam_heat_exchanger_form_output_air_temperature').val();
        // Техническое задание: температура пара
        var steamTemperature = $('#steam_heat_exchanger_form_steam_temperature').val();
        // Техническое задание: мощность
        var power = $('#steam_heat_exchanger_form_power').val();
        // Техническое задание: дополнительная информация
        var notes = $('#steam_heat_exchanger_form_notes').val();

        // ФИО
        var name = $.trim($('#steam_heat_exchanger_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#steam_heat_exchanger_form_name').offset().top - 150 }, 100);
            showMessage('#steam_heat_exchanger_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#steam_heat_exchanger_form_name').focus();
            return false;
        }

        // Компания
        var company = $.trim($('#steam_heat_exchanger_form_company').val());

        // Город
        var city = $.trim($('#steam_heat_exchanger_form_city').val());

        // E-mail
        var email = $.trim($('#steam_heat_exchanger_form_email').val());
        if (!email || !/@/.test(email) || !isEmailValid(email)) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#steam_heat_exchanger_form_email').offset().top - 150 }, 100);
            showMessage('#steam_heat_exchanger_form_email', 'Пожалуйста, укажите свой E-mail');
            $('#steam_heat_exchanger_form_email').focus();
            return false;
        }

        // Телефон
        var phone = $.trim($('#steam_heat_exchanger_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            // Прокручиваем
            $('html, body').animate({ scrollTop: $('#steam_heat_exchanger_form_phone').offset().top - 150 }, 100);
            showMessage('#steam_heat_exchanger_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#steam_heat_exchanger_form_phone').focus();
            return false;
        }

        if (name && email && phone) {
            $.post('/public/js/jquery.ajax.request.steam.heat.exchanger.php', {
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTA': sizesFTA,
                    'sizesFTAEn': convertRuLettersToEn(sizesFTA),
                'sizesFTB': sizesFTB,
                    'sizesFTBEn': convertRuLettersToEn(sizesFTB),
                'sizesA': sizesA,
                    'sizesAEn': convertRuLettersToEn(sizesA),
                'sizesB': sizesB,
                    'sizesBEn': convertRuLettersToEn(sizesB),
                'sizesS': sizesS,
                    'sizesSEn': convertRuLettersToEn(sizesS),
                'sizesDiameterE': sizesDiameterE,
                    'sizesDiameterEEn': convertRuLettersToEn(sizesDiameterE),
                'sizesDiameterU': sizesDiameterU,
                    'sizesDiameterUEn': convertRuLettersToEn(sizesDiameterU),
                'sizesLane': sizesLane,
                    'sizesLaneEn': convertRuLettersToEn(sizesLane),
                'sizesLamellaStep': sizesLamellaStep,
                    'sizesLamellaStepEn': convertRuLettersToEn(sizesLamellaStep),
                'tubeMaterial': tubeMaterial,
                    'tubeMaterialEn': convertRuLettersToEn(tubeMaterial),
                'lamellaMaterial': lamellaMaterial,
                    'lamellaMaterialEn': convertRuLettersToEn(lamellaMaterial),
                'airSpending': airSpending,
                    'airSpendingEn': convertRuLettersToEn(airSpending),
                'inputAirTemperature': inputAirTemperature,
                    'inputAirTemperatureEn': convertRuLettersToEn(inputAirTemperature),
                'outputAirTemperature': outputAirTemperature,
                    'outputAirTemperatureEn': convertRuLettersToEn(outputAirTemperature),
                'steamTemperature': steamTemperature,
                    'steamTemperatureEn': convertRuLettersToEn(steamTemperature),
                'power': power,
                    'powerEn': convertRuLettersToEn(power),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'name': name,
                    'nameEn': convertRuLettersToEn(name),
                'company': company,
                    'companyEn': convertRuLettersToEn(company),
                'city': city,
                    'cityEn': convertRuLettersToEn(city),
                'email': email,
                    'emailEn': convertRuLettersToEn(email),
                'phone': phone,
                    'phoneEn': convertRuLettersToEn(phone),
                'notes': notes,
                    'notesEn': convertRuLettersToEn(notes),
                'url': window.location.href
                }, function(data) {
                if (data) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#h1').offset().top - 150 }, 100);
                    $('#h1').html('Ваша заявка отправлена!');
                    $('#steam_heat_exchanger_form').html(data);
                    $('.steam_heat_exchanger_desc').remove();
                    // цель для метрики
                    yaCounter22280926.reachGoal('pn-data');
                }
            });
        }
    });

    // ****************************************************************************************


    // ****************************************************************************************

    // Форма "Задать вопрос" в разделе "аналоги т/о". Обработчик нажатия клавиши Enter
    $('#analog_form_name, #analog_form_phone, #analog_form_email').keypress(function(e) {
        if (e.which == 13) { $('#analog_form_submit_button').click(); return false; }
    });

    // Форма "Задать вопрос" в разделе "Аналоги т/о". Подтверждение
    $('#analog_form_submit_button').click(function() {
        console.log('analog_form_submit_button: ');

        // setting container for ajax preloader
        setAjaxStatus('#analog_form_ajax_preloader');

        var name = $.trim($('#analog_form_name').val());
        if (!name || name.length < 3 || !hasDifferentLetters(name)) {
            showMessage('#analog_form_name', 'Пожалуйста, укажите контактное имя.')
            $('#analog_form_name').focus();
            return false;
        }

        var phone = $.trim($('#analog_form_phone').val());
        if (!phone || (phone.match(/\d/g) ? phone.match(/\d/g).length : 0) < 10) {
            showMessage('#analog_form_phone', 'Пожалуйста, укажите свой телефон с кодом.<br />Например: (495) 134-23-53.');
            $('#analog_form_phone').focus();
            return false;
        }

        var email = $.trim($('#analog_form_email').val());
                if (!email || !/@/.test(email) || !isEmailValid(email)) {
                    // Прокручиваем
                    $('html, body').animate({ scrollTop: $('#analog_form_email').offset().top - 150 }, 100);
                    showMessage('#analog_form_email', 'Пожалуйста, укажите свой E-mail');
                    $('#analog_form_email').focus();
                    return false;
                }

        var text = $.trim($('#analog_form_text').val());
        if (!text) {
            showMessage('#analog_form_text', 'Пожалуйста, укажите Ваш вопрос.')
            $('#analog_form_text').focus();
            return false;
        }



        if (name && phone && email && text) {
     

            $.post('/public/js/jquery.ajax.analog.php', { 'name': name, 'name_en': convertRuLettersToEn(name), 'phone': phone, 'phone_en': convertRuLettersToEn(phone), 
            'email': email, 'email_en': convertRuLettersToEn(email),
            'text': text, 'text_en': convertRuLettersToEn(text), 'url': window.location.href }, function(data) {
     

                if (data) {
                    $('#analog_form #analog_form_label').html('Ваш вопрос отправлен!');
                    $('#analog_form #analog_form_body').html(data);
                    // цель для метрики
                   // yaCounter22280926.reachGoal('contacts-data');
                }
            });
        }
    }); // /Форма "Задать вопрос" в разделе "Контакты"

    // ****************************************************************************************



}); // /jquery ready