<?php # НАЗНАЧЕНИЕ: ОБРАБОТЧИК ОНЛАЙН-СЕРВИСА "ЗАЯВКА НА ТЕПЛООБМЕННИК"

# https://euroheater.ru/calculator/

# Дата создания: 20 ноября 2018

# Автор: romanov.egor@gmail.com



# тестирование

# sleep(5); exit();



# защита от запроса c другого сайта

if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');



# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт

header('Content-type: text/html; charset=UTF-8');



# проверка, защита + нужная кодировка GET-переменных

preparePOSTVariables(); # print_r($_POST); exit;



# проверка обязательных переменных

if (empty($_POST['name']) || empty($_POST['phone'])) exit();



# подключаем и инициализируем класс для работы с БД через PDO

include($_SERVER['DOCUMENT_ROOT'].'/app/db.connection.pdo.php');



# подключаем файл с функциями

include($_SERVER['DOCUMENT_ROOT'].'/app/library/functions.php');



sendLetterToManager();



# echo '<pre>'.(print_r($_POST, true)).'</pre>';

# exit;



# *************************************************************************************************



# ФУНКЦИИ



# *************************************************************************************************



# ОТПРАВЛЯЕМ ЗАКАЗ МЕНЕДЖЕРУ

function sendLetterToManager()

{

	# print_R($_POST);

    

    global $orderForDB;



	$message =

    'Заявка на теплообменник:'.PHP_EOL.

    (!empty($_POST['heatExchangerType']) ? 'Тип теплообменника: '.$_POST['heatExchangerType'].PHP_EOL : '').

    (!empty($_POST['airSpanding']) ? 'Расход воздуха: '.$_POST['airSpanding'].PHP_EOL : '').

    (!empty($_POST['coolantSpanding']) ? 'Расход теплоносителя: '.$_POST['coolantSpanding'].PHP_EOL : '').

    (!empty($_POST['inputAirTemperature']) ? 'Температура воздуха на входе: '.$_POST['inputAirTemperature'].PHP_EOL : '').

    (!empty($_POST['outputAirTemperature']) ? 'Температура воздуха на выходе: '.$_POST['outputAirTemperature'].PHP_EOL : '').

    (!empty($_POST['inputCoolantTemperature']) ? 'Температура теплоносителя на входе: '.$_POST['inputCoolantTemperature'].PHP_EOL : '').

    (!empty($_POST['outputCoolantTemperature']) ? 'Температура теплоносителя на выходе: '.$_POST['outputCoolantTemperature'].PHP_EOL : '').

    (!empty($_POST['power']) ? 'Требуемая мощность: '.$_POST['power'].PHP_EOL : '').

    (!empty($_POST['ftaLength']) ? 'Длина FTA: '.$_POST['ftaLength'].PHP_EOL : '').

    (!empty($_POST['ftbHength']) ? 'Высота FTB: '.$_POST['ftbHength'].PHP_EOL : '').

    (!empty($_POST['sWidth']) ? 'Ширина S: '.$_POST['sWidth'].PHP_EOL : '').

    (!empty($_POST['inputCdiameter']) ? 'Диаметр подводящих патрубков на входе C: '.$_POST['inputCdiameter'].PHP_EOL : '').

    (!empty($_POST['outputCdiameter']) ? 'Диаметр подводящих патрубков на выходе C: '.$_POST['outputCdiameter'].PHP_EOL : '').

    'Узел регулирования (типовой): '.($_POST['unit'] == 'true' ? 'да' : 'нет').PHP_EOL.

    'ФИО: '.$_POST['name'].PHP_EOL.

    (!empty($_POST['company']) ? 'Компания: '.$_POST['company'].PHP_EOL : '').

    (!empty($_POST['city']) ? 'Город: '.$_POST['city'].PHP_EOL : '').

    'E-mail: '.$_POST['email'].PHP_EOL.

    'Телефон: '.$_POST['phone'].PHP_EOL.

    (!empty($_POST['notes']) ? 'Примечания: '.$_POST['notes'].PHP_EOL : '').

	'Заявка отправлена со страницы: '.$_POST['url'].PHP_EOL

	;

    

	$messageEn =

	PHP_EOL.'***** Tekst-v-transliteracii *****:'.PHP_EOL.

	'Zayavka na teploobmennik: '.PHP_EOL.

    (!empty($_POST['heatExchangerTypeEn']) ? 'Tip teploobmennika: '.$_POST['heatExchangerTypeEn'].PHP_EOL : '').

    (!empty($_POST['airSpandingEn']) ? 'Rashod vozduha: '.$_POST['airSpandingEn'].PHP_EOL : '').

    (!empty($_POST['coolantSpandingEn']) ? 'Rashod teplonositela: '.$_POST['coolantSpandingEn'].PHP_EOL : '').

    (!empty($_POST['inputAirTemperatureEn']) ? 'Temperatura vozduha na vhode: '.$_POST['inputAirTemperatureEn'].PHP_EOL : '').

    (!empty($_POST['outputAirTemperatureEn']) ? 'Temperatura vozduha na vihode: '.$_POST['outputAirTemperatureEn'].PHP_EOL : '').

    (!empty($_POST['inputCoolantTemperatureEn']) ? 'Temperature teplonositela na vhode: '.$_POST['inputCoolantTemperatureEn'].PHP_EOL : '').

    (!empty($_POST['outputCoolantTemperatureEn']) ? 'Temperature teplonositela na vihode: '.$_POST['outputCoolantTemperatureEn'].PHP_EOL : '').

    (!empty($_POST['powerEn']) ? 'Trebuemaa moshnost: '.$_POST['powerEn'].PHP_EOL : '').

    (!empty($_POST['ftaLengthEn']) ? 'Dlina FTA: '.$_POST['ftaLengthEn'].PHP_EOL : '').

    (!empty($_POST['ftbHengthEn']) ? 'Visota FTB: '.$_POST['ftbHengthEn'].PHP_EOL : '').

    (!empty($_POST['sWidthEn']) ? 'Shirina S: '.$_POST['sWidthEn'].PHP_EOL : '').

    (!empty($_POST['inputCdiameterEn']) ? 'Diameter podvodyashih patrubkov na vhode: '.$_POST['inputCdiameterEn'].PHP_EOL : '').

    (!empty($_POST['outputCdiameterEn']) ? 'Diameter podvodyashih patrubkov na vihode: '.$_POST['outputCdiameterEn'].PHP_EOL : '').

    'Uzel regulirovania (tipovoi): '.($_POST['unitEn'] == 'true' ? 'da' : 'net').PHP_EOL.

    'FIO: '.$_POST['nameEn'].PHP_EOL.

    (!empty($_POST['companyEn']) ? 'Kompania: '.$_POST['companyEn'].PHP_EOL : '').

    (!empty($_POST['cityEn']) ? 'Gorod: '.$_POST['cityEn'].PHP_EOL : '').

    'E-mail: '.$_POST['emailEn'].PHP_EOL.

    'Telefon: '.$_POST['phoneEn'].PHP_EOL.

    (!empty($_POST['notesEn']) ? 'Primechania: '.$_POST['notesEn'].PHP_EOL : '').

	'Zayavka otpravlena so stranici: '.$_POST['url'].PHP_EOL

	;



	# echo '<pre>'.$message.'</pre>'; exit;



    # читаем из базы E-mail для online-заявок

    $emailForNotifications = getContent('/app/templates/email_for_notifications.html');

    if (empty($emailForNotifications)) $emailForNotifications = 'info@'.$_SERVER['SERVER_NAME'];

		# НА  1gb требуется емейл , зарегестрированный в 1gb в настройках обратного адреса
		# Читаем из базы шаблонов

		$from = getContent('/app/templates/email-from.html'); //'info-eh@1gb.ru'; 
		
	if (mail($emailForNotifications,

	# if (mail("romanov.egor@gmail.com",

    str_replace('www.', '', $_SERVER['SERVER_NAME']).' - заявка на теплообменник ['.date("j.n.Y G:i").']',

	$message.$messageEn,

		"Mime-Version: 1.0\n".
	 "From: $from\n". //.DOMAIN_SHORT.PHP_EOL.
		"Reply-To: $from\n".
		"Content-Type: text/plain; charset=UTF-8\n".
		"Content-Transfer-Encoding: 8bit"

	)) {

		echo '

			<p>В самое ближайшее время с Вами свяжется менеджер нашей компании.</p>

			<p>Оставайтесь на связи! </p>

			<table class="table table-striped">

				<tr>

					<th colspan="2" class="em">&nbsp;Ваши данные</th>

				</tr>

				<tr>

					<td class="bold">Контактное имя:</td>

					<td>'.$_POST['name'].'</td>

				</tr>

				<tr>

					<td class="bold">Телефон для связи:</td>

					<td>'.$_POST['phone'].'</td>

				</tr>

			</table>

            <p>Благодарим за Ваш выбор '.$_SERVER['SERVER_NAME'].'.</p>

		';

        

        # фиксируем текст для админки для менеджеров

        $orderForDB = $message.$messageEn;

		

		# фиксируем заявку в админке

		saveRequestInDB();

	}

	else

	{

		echo '

		<div style="font-size:150%;text-align:center">

		К сожалению, сервис заказа обратного звонка через сайт временно не работает.

		<br /><br />

		Пожалуйста, свяжитесь с нами по телефону.

		<br /><br />

		Благодарим за Ваш выбор '.$_SERVER['SERVER_NAME'].'.

		</div>

		';

	}

} # /ОТПРАВЛЯЕМ ЗАКАЗ МЕНЕДЖЕРУ



# *************************************************************************************************



# ФИКСИРУЕМ ЗАЯВКУ В БД

function saveRequestInDB()

{

	global $dbh, $orderForDB;

	

	$sql = "

	insert into ".DB_PREFIX."online_requests

	(

	`request_type_id`,

	`date_add`,

    `order_content`,

    `http_x_real_ip`,

    `http_x_forwarded_for`,

    `http_user_agent`,

    `remove_addr`

	)

	values 

	(

	6,

    now(),

    :order_content,

    :http_x_real_ip,

    :http_x_forwarded_for,

    :http_user_agent,

    :remove_addr

	)

	"; # echo '<pre>'.$sql."</pre><hr />";

	$result = $dbh->prepare($sql);

	$result->bindParam(':order_content', $orderForDB);

	$result->bindParam(':http_x_real_ip', $_SERVER['HTTP_X_REAL_IP']);

	$result->bindParam(':http_x_forwarded_for', $_SERVER['HTTP_X_FORWARDED_FOR']);

	$result->bindParam(':http_user_agent', $_SERVER['HTTP_USER_AGENT']);

	$result->bindParam(':remove_addr', $_SERVER['REMOTE_ADDR']);

	try

	{

		if ($result->execute())

		{

			$last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';

			

			if (!empty($last_insert_id))

			{

				return $last_insert_id;

			}

			else return;

		}

	}

	catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }

} # /ФИКСИРУЕМ ЗАЯВКУ В БД



# *************************************************************************************************



# ПРОВЕРКА И ЗАЩИТА POST-ПЕРЕМЕННЫХ

function preparePOSTVariables()

{

	foreach ($_POST as $key => &$val)

	{

		if (!empty($val))

		{

			if (!is_array($key) and !is_array($val))

			{

				$_POST[$key] = trim($val);

				$_POST[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);

                $_POST[$key] = strip_tags($val);

				$_POST[$key] = getCorrectEnc($val);

			}

		}

	}

} # /ПРОВЕРКА И ЗАЩИТА POST-ПЕРЕМЕННЫХ



# *************************************************************************************************



# ПОЛУЧАЕМ НУЖНУЮ КОДИРОВКУ ДАННЫХ

function getCorrectEnc($var)

{

	# проверяем входящие переменные

	if (empty($var)) return;

	

	# echo $var.'<hr />';

	if (!empty($var))

	{

		$isUTF8 = detectUTF8($var); # echo $isUTF8.'<hr />';

		// # если это UTF-8

		// if (!empty($isUTF8))

		// {

		// 	$var = iconv('UTF-8', 'windows-1251//TRANSLIT', $var); # echo $var."<br />"

		// }

	//меняем перекодирование наоборот - из windows-1251  в UTF-8 4/07/2021
	// если это  НЕ UTF-8 
		if (empty($isUTF8))

		{

			$var2 = iconv('windows-1251', 'UTF-8//TRANSLIT', $var); # echo $var."<br />";

            # обход баги, обнаруженной 22.12.2015, когда некоторые данные определяются как UTF-8

            # а при перекодировании дают пустую строку
	// оставляем, на всякий случай
            if (!empty($var2)) $var = $var2;

		}

		

		return $var;		

	}

	# /получаем нужную кодировку для данных

	# echo $var.'<hr />';

} # /ПОЛУЧАЕМ НУЖНУЮ КОДИРОВКУ ДАННЫХ



# *************************************************************************************************



# ОПРЕДЕЛЯЕМ, ЯВЛЯЕТСЯ ЛИ КОДИРОВКА СТРОКЕ UTF-8 ИЛИ НЕТ

# ВОЗВРАЩАЕТ 1 - ЕСЛИ UTF-8, 0 - ЕСЛИ НЕ UTF-8

function detectUTF8($string)

{

    return preg_match('%(?:

        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte

        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs

        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte

        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates

        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3

        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15

        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16

        )+%xs', 

    $string);

} # /ОПРЕДЕЛЯЕМ, ЯВЛЯЕТСЯ ЛИ КОДИРОВКА СТРОКЕ UTF-8 ИЛИ НЕТ



# *************************************************************************************************



# /ФУНКЦИИ



# *************************************************************************************************