<?php # НАЗНАЧЕНИЕ: ОБРАБОТЧИК ОНЛАЙН-СЕРВИСА "Добавить отзыв" С ВНУТРЕННИХ
# дата создания: 2015.10.11
# автор: romanov.egor@gmail.com

# тестирование
# sleep(7); exit();

# защита от запроса c другого сайта
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт
header('Content-type: text/html; charset=windows-1251');

# проверка, защита + нужная кодировка GET-переменных
preparePOSTVariables(); # print_r($_POST); exit;

# проверка обязательных переменных
if (empty($_POST['name']) || empty($_POST['activity']) || empty($_POST['text'])) exit();

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
    "Отзыв с сайта:".PHP_EOL.
	"Имя: ".$_POST['name'].PHP_EOL.
	"Сфера деятельности: ".$_POST['activity'].PHP_EOL.
	"Отзыв: ".$_POST['text'].PHP_EOL.
	"Отзыв отправлен со страницы: ".$_POST['url'].PHP_EOL
	;
    
	$messageEn =
	PHP_EOL."***** Tekst-v-transliteracii *****:".PHP_EOL.
	"Otziv s saita:".PHP_EOL.
    "Imya: ".$_POST['name_en'].PHP_EOL.
	"Sfera deyatelnosti: ".$_POST['activity_en'].PHP_EOL.
	"Otziv: ".$_POST['text_en'].PHP_EOL.
	"Otziv otpravlen so stranici: ".$_POST['url'].PHP_EOL
	;

	# echo '<pre>'.$message.'</pre>'; exit;

    # читаем из базы E-mail для online-заявок
    $emailForNotifications = getContent('/app/templates/email_for_notifications.html');
    if (empty($emailForNotifications)) $emailForNotifications = 'info@'.$_SERVER['SERVER_NAME'];

    if (mail($emailForNotifications,
	# if (mail("romanov.egor@gmail.com",
    DOMAIN_SHORT.' - отзыв с сайта ['.date('j '.getRusMonthName(date('m')).' Y G:i').']',
	$message.$messageEn,
	"From: www@".DOMAIN_SHORT.PHP_EOL.
	# "Cc: ".PHP_EOL.
    'MIME-Version: 1.0'.PHP_EOL. 
    'Content-type: text/plain; charset=Windows-1251'.PHP_EOL.
    'Content-Transfer-Encoding: quoted-printable'.PHP_EOL.
    'X-Mailer: PHP'
	)) {
		echo '
			<div class="hilight">
			<p class="bold">Ваш отзыв:</p>
			<p>'.$_POST['text'].'</p>
			</div>
			<table class="table table-striped table-price">
				<tr>
					<th colspan="2" class="em">&nbsp;Ваши данные</th>
				</tr>
				<tr>
					<td class="bold">Контактное имя:</td>
					<td>'.$_POST['name'].'</td>
				</tr>
				<tr>
					<td class="bold">Сфера деятельности:</td>
					<td>'.$_POST['activity'].'</td>
				</tr>
			</table>
			<p>Благодарим за внимание!</p>
		';
        
        # фиксируем текст для админки для менеджеров
        $orderForDB = $message.$messageEn;
		
		# фиксируем заявку в админке
		saveRequestInDB();
	}
	else {
		echo '
		<div style="font-size:150%;text-align:center">
		К сожалению, отправка отзывов через сайт временно не работает.
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
	insert into ".DB_PREFIX."feedback
	(
	`name`,
	`activity`,
    `feedback`,
    `votes_plus`,
    `votes_minus`,
    `date_add`,
    `is_published`
	)
	values 
	(
    :name,
    :activity,
    :feedback,
    0,
    0,
    now(),
    NULL
	)
	"; # echo '<pre>'.$sql."</pre><hr />";
	$result = $dbh->prepare($sql);
	$result->bindParam(':name', $_POST['name']);
	$result->bindParam(':activity', $_POST['activity']);
	$result->bindParam(':feedback', $_POST['text']);
	try {
		if ($result->execute())	{
			$last_insert_id = $dbh->lastInsertId(); # echo $last_insert_id.'<hr />';
			
			if (!empty($last_insert_id)) return $last_insert_id;
			else return;
		}
	}
	catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ФИКСИРУЕМ ЗАЯВКУ В БД

# ПРОВЕРКА И ЗАЩИТА POST-ПЕРЕМЕННЫХ
function preparePOSTVariables()
{
	foreach ($_POST as $key => &$val) {
		if (!empty($val)) {
			if (!is_array($key) and !is_array($val)) {
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
	if (!empty($var)) {
		$isUTF8 = detectUTF8($var); # echo $isUTF8.'<hr />';
		# если это UTF-8
		if (!empty($isUTF8)) {
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