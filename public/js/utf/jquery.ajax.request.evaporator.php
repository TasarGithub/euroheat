<?php # НАЗНАЧЕНИЕ: ОБРАБОТЧИК ОНЛАЙН-СЕРВИСА "РАСЧЕТ И ПОДБОР ИСПАРИТЕЛЯ"
# https://euroheater.ru/raschet-isparitelya/
# Дата создания: 24 декабря 2018
# Автор: romanov.egor@gmail.com

# тестирование
# sleep(5); exit();

# защита от запроса c другого сайта
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт
header('Content-type: text/html; charset=windows-1251');

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
    # 'Заявка на расчет и подбор испарителя:'.PHP_EOL.
    (!empty($_POST['sizesFTA']) ? 'FTA, мм: '.$_POST['sizesFTA'].PHP_EOL : '').
    (!empty($_POST['sizesFTB']) ? 'FTB, мм: '.$_POST['sizesFTB'].PHP_EOL : '').
    (!empty($_POST['sizesA']) ? 'A, мм: '.$_POST['sizesA'].PHP_EOL : '').
    (!empty($_POST['sizesB']) ? 'B, мм: '.$_POST['sizesB'].PHP_EOL : '').
    (!empty($_POST['sizesS']) ? 'S, мм: '.$_POST['sizesS'].PHP_EOL : '').
    (!empty($_POST['sizesDiameterE']) ? 'Диаметр E: '.$_POST['sizesDiameterE'].PHP_EOL : '').
    (!empty($_POST['sizesDiameterU']) ? 'Диаметр U: '.$_POST['sizesDiameterU'].PHP_EOL : '').
    (!empty($_POST['sizesLane']) ? 'Рядность: '.$_POST['sizesLane'].PHP_EOL : '').
    (!empty($_POST['sizesLamellaStep']) ? 'Шаг ламели, мм: '.$_POST['sizesLamellaStep'].PHP_EOL : '').
    (!empty($_POST['tray']) ? 'Поддон и каплеуловитель, мм: '.$_POST['tray'].PHP_EOL : '').
    (!empty($_POST['tubeMaterial']) ? 'Материал трубки: '.$_POST['tubeMaterial'].PHP_EOL : '').
    (!empty($_POST['lamellaMaterial']) ? 'Материал ламелей: '.$_POST['lamellaMaterial'].PHP_EOL : '').
    (!empty($_POST['airSpending']) ? 'Расход воздуха: '.$_POST['airSpending'].PHP_EOL : '').
    (!empty($_POST['airHumidity']) ? 'Влажность воздуха, %: '.$_POST['airHumidity'].PHP_EOL : '').
    (!empty($_POST['inputAirTemperature']) ? 'Температура воздуха на входе: '.$_POST['inputAirTemperature'].PHP_EOL : '').
    (!empty($_POST['outputAirTemperature']) ? 'Температура воздуха на выходе: '.$_POST['outputAirTemperature'].PHP_EOL : '').
    (!empty($_POST['freonType']) ? 'Тип фреона: '.$_POST['freonType'].PHP_EOL : '').
    (!empty($_POST['freonEvaporationTemperature']) ? 'Температура испарения фреона, °C: '.$_POST['freonEvaporationTemperature'].PHP_EOL : '').
    (!empty($_POST['power']) ? 'Мощность: '.$_POST['power'].PHP_EOL : '').
    (!empty($_POST['notes']) ? 'Дополнительная информация: '.$_POST['notes'].PHP_EOL : '').
    'ФИО: '.$_POST['name'].PHP_EOL.
    (!empty($_POST['company']) ? 'Компания: '.$_POST['company'].PHP_EOL : '').
    (!empty($_POST['city']) ? 'Город: '.$_POST['city'].PHP_EOL : '').
    'E-mail: '.$_POST['email'].PHP_EOL.
    'Телефон: '.$_POST['phone'].PHP_EOL.
	'Заявка отправлена со страницы: '.$_POST['url'].PHP_EOL
	;
    
	/* $messageEn =
	PHP_EOL.'***** Tekst-v-transliteracii *****:'.PHP_EOL.
	'Zayavka na raschet i podbor isparitela: '.PHP_EOL.
    (!empty($_POST['heatExchangerTypeEn']) ? 'Tip teploobmennika: '.$_POST['heatExchangerTypeEn'].PHP_EOL : '').

    (!empty($_POST['sizesFTAEn']) ? 'Dannie po razmeram. FTA, mm: '.$_POST['sizesFTAEn'].PHP_EOL : '').
    (!empty($_POST['sizesFTBEn']) ? 'Dannie po razmeram. FTB, mm: '.$_POST['sizesFTBEn'].PHP_EOL : '').
    (!empty($_POST['sizesAEn']) ? 'Dannie po razmeram. A, mm: '.$_POST['sizesAEn'].PHP_EOL : '').
    (!empty($_POST['sizesBEn']) ? 'Dannie po razmeram. B, mm: '.$_POST['sizesBEn'].PHP_EOL : '').
    (!empty($_POST['sizesSEn']) ? 'Dannie po razmeram. S, mm: '.$_POST['sizesSEn'].PHP_EOL : '').
    (!empty($_POST['sizesDiameterEEn']) ? 'Dannie po razmeram. Diametr E: '.$_POST['sizesDiameterEEn'].PHP_EOL : '').
    (!empty($_POST['sizesDiameterUEn']) ? 'Dannie po razmeram. Diametr U: '.$_POST['sizesDiameterUEn'].PHP_EOL : '').
    (!empty($_POST['sizesLaneEn']) ? 'Dannie po razmeram. Radnost: '.$_POST['sizesLaneEn'].PHP_EOL : '').
    (!empty($_POST['sizesLamellaStepEn']) ? 'Dannie po razmeram. Shag lameli, мм: '.$_POST['sizesLamellaStepEn'].PHP_EOL : '').
    (!empty($_POST['trayEn']) ? 'Dannie po razmeram. Poddon i kapleulovitel, mm: '.$_POST['trayEn'].PHP_EOL : '').

    (!empty($_POST['tubeMaterialEn']) ? 'Materiali. Mterial trubki: '.$_POST['tubeMaterialEn'].PHP_EOL : '').
    (!empty($_POST['lamellaMaterialEn']) ? 'Materiali. Material lamelei: '.$_POST['lamellaMaterialEn'].PHP_EOL : '').

    (!empty($_POST['airSpendingEn']) ? 'Tehnicheskoe zadanie. Rashod vozduha: '.$_POST['airSpendingEn'].PHP_EOL : '').
    (!empty($_POST['airHumidity']) ? 'Tehnicheskoe zadanie. Vlajnost vozduha, %: '.$_POST['airHumidityEn'].PHP_EOL : '').
    (!empty($_POST['inputAirTemperatureEn']) ? 'Tehnicheskoe zadanie. Temperatura vozduha na vhode: '.$_POST['inputAirTemperatureEn'].PHP_EOL : '').
    (!empty($_POST['outputAirTemperatureEn']) ? 'Tehnicheskoe zadanie. Temperatura vozduha na vihode: '.$_POST['outputAirTemperatureEn'].PHP_EOL : '').
    (!empty($_POST['freonType']) ? 'Tehnicheskoe zadanie. Tip freona: '.$_POST['freonType'].PHP_EOL : '').
    (!empty($_POST['freonEvaporationTemperatureEn']) ? 'Tehnicheskoe zadanie. Temperatura isparenia freona, °C: '.$_POST['freonEvaporationTemperatureEn'].PHP_EOL : '').
    (!empty($_POST['powerEn']) ? 'Tehnicheskoe zadanie. Moshnost: '.$_POST['powerEn'].PHP_EOL : '').
    (!empty($_POST['notesEn']) ? 'Tehnicheskoe zadanie. Dopolnitelnaa informacia: '.$_POST['notesEn'].PHP_EOL : '').
    'FIO: '.$_POST['nameEn'].PHP_EOL.
    (!empty($_POST['companyEn']) ? 'Kompania: '.$_POST['companyEn'].PHP_EOL : '').
    (!empty($_POST['cityEn']) ? 'Gorod: '.$_POST['cityEn'].PHP_EOL : '').
    'E-mail: '.$_POST['emailEn'].PHP_EOL.
    'Telefon: '.$_POST['phoneEn'].PHP_EOL.
	'Zayavka otpravlena so stranici: '.$_POST['url'].PHP_EOL
	; */

	# echo '<pre>'.$message.'</pre>'; exit;

    # читаем из базы E-mail для online-заявок
    $emailForNotifications = getContent('/app/templates/email_for_notifications.html');
    if (empty($emailForNotifications)) $emailForNotifications = 'info@'.$_SERVER['SERVER_NAME'];
    
	if (mail($emailForNotifications,
	# if (mail("romanov.egor@gmail.com",
    str_replace('www.', '', $_SERVER['SERVER_NAME']).' - заявка на расчет и подбор испарителя ['.date("j.n.Y G:i").']',
	# $message.$messageEn,
	$message,
	'From: www@'.str_replace('www.', '', $_SERVER['SERVER_NAME']).PHP_EOL.
	#'Cc: vashpartner3@gmail.com'.PHP_EOL. # письмо куратору проекта
    'MIME-Version: 1.0'.PHP_EOL. 
    'Content-type: text/plain; charset=Windows-1251'.PHP_EOL.
    'Content-Transfer-Encoding: quoted-printable'.PHP_EOL.
    'X-Mailer: PHP'
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
        # $orderForDB = $message.$messageEn;
        $orderForDB = $message;

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
	11,
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
                $_POST[$key] = str_replace('&#34;', '"', $val);
                $_POST[$key] = str_replace('&#39;', "'", $val);
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
		# если это UTF-8
		if (!empty($isUTF8))
		{
			$var = iconv('UTF-8', 'windows-1251//TRANSLIT', $var); # echo $var."<br />"
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