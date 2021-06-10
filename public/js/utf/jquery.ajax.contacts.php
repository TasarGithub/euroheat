<?php # НАЗНАЧЕНИЕ: ОБРАБОТЧИК ОНЛАЙН-СЕРВИСА "Задать вопрос": http://euroheater.ru/kontakty/
# дата создания: 2015.12.17
# автор: romanov.egor@gmail.com

# тестирование
# sleep(3); exit();

# защита от запроса c другого сайта
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт
header('Content-type: text/html; charset=windows-1251');

# проверка, защита + нужная кодировка GET-переменных
preparePOSTVariables(); # print_r($_POST); exit;

# проверка обязательных переменных
if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['text'])) exit('');

# подключаем конфиг
include($_SERVER['DOCUMENT_ROOT'].'/app/config.php');

# подключаем и инициализируем класс для работы с БД через PDO
include($_SERVER['DOCUMENT_ROOT'].'/app/db.connection.pdo.php');

# подключаем файл с функциями
include($_SERVER['DOCUMENT_ROOT'].'/app/library/functions.php');

sendLetterToManager();

# echo '<pre>'.(print_r($_POST, true)).'</pre>';

# ФУНКЦИИ

# ОТПРАВЛЯЕМ ЗАКАЗ МЕНЕДЖЕРУ
function sendLetterToManager()
{
	# print_R($_POST);
    
    global $orderForDB;

	$message =
    # "Вопрос из раздела \"Контакты\":".PHP_EOL.
	"Имя: ".$_POST['name'].PHP_EOL.
	"Телефон для связи: ".$_POST['phone'].PHP_EOL.
	"Вопрос: ".$_POST['text'].PHP_EOL.
	"Вопрос задан со страницы: ".$_POST['url'].PHP_EOL
	;
    
	/* $messageEn =
	PHP_EOL."***** Tekst-v-transliteracii *****:".PHP_EOL.
	"Vopros iz razdela \"Kontakti\":".PHP_EOL.
    "Imya: ".$_POST['name_en'].PHP_EOL.
	"Telefon dlya svyazi: ".$_POST['phone_en'].PHP_EOL.
	"Vopros: ".$_POST['text_en'].PHP_EOL.
	"Vopros zadan so stranici: ".$_POST['url'].PHP_EOL
	; */

	# echo '<pre>'.$message.'</pre>'; exit;

    # читаем из базы E-mail для online-заявок
    $emailForNotifications = getContent('/app/templates/email_for_notifications.html');
    if (empty($emailForNotifications)) $emailForNotifications = 'info@'.$_SERVER['SERVER_NAME'];

    if (mail($emailForNotifications,
    # if (mail("romanov.egor@gmail.com",
    DOMAIN_SHORT.' - вопрос из раздела контакты ['.date('j '.getRusMonthName(date('m')).' Y G:i').']',
	$message.$messageEn,
    'From: www@'.DOMAIN_SHORT.PHP_EOL.
    #'Cc: vashpartner3@gmail.com'.PHP_EOL. # письмо куратору проекта
    'MIME-Version: 1.0'.PHP_EOL.
    'Content-type: text/plain; charset=Windows-1251'.PHP_EOL.
    'Content-Transfer-Encoding: quoted-printable'.PHP_EOL.
    'X-Mailer: PHP'
	)) {
		echo '
			<p>В самое ближайшее время с Вами свяжется менеджер нашей компании.</p>
			<p>Оставайтесь на связи! </p>
			<div class="hilight">
			<p class="bold">Ваш вопрос:</p>
			<p>'.$_POST['text'].'</p>
			</div>
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
		К сожалению, отправка сообщений через сайт временно не работает.
		<br /><br />
		Пожалуйста, свяжитесь с нами по телефону.
		<br /><br />
		Благодарим за Ваш выбор '.$_SERVER['SERVER_NAME'].'.
		</div>
		';
	}
}
# /ОТПРАВЛЯЕМ ЗАКАЗ МЕНЕДЖЕРУ

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
	4,
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
}
# /ФИКСИРУЕМ ЗАЯВКУ В БД

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
                # фикс баги с заменой переноса строки на &#10;
                $_POST[$key] = str_replace(array(PHP_EOL, '&#10;'), ' ', $val);
			}
		}
	}
} # /ПРОВЕРКА И ЗАЩИТА POST-ПЕРЕМЕННЫХ

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

# /ФУНКЦИИ