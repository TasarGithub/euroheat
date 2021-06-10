<?php # НАЗНАЧЕНИЕ: ОБРАБОТЧИК ОНЛАЙН-СЕРВИСА РАБОТЫ С ОТЗЫВАМИ С ГЛАВНОЙ И ВНУТРЕННИХ
# дата создания: 2015.10.9
# автор: romanov.egor@gmail.com

# тестирование
# sleep(3); echo '<pre>'.(print_r($_POST, true)).'</pre>'; exit('exit');

# защита от запроса c другого сайта
if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');

# указываем кодировку, которую будет отдавать javascript'у ajax-скрипт
header('Content-type: text/html; charset=windows-1251');

# проверка, защита + нужная кодировка GET-переменных
preparePOSTVariables(); # print_r($_POST); exit;

# подключаем и инициализируем класс для работы с БД через PDO
include($_SERVER['DOCUMENT_ROOT'].'/app/db.connection.pdo.php');

# echo '<pre>'.(print_r($_POST, true)).'</pre>';

# проверяем переменные
if (empty($_POST['id'])) return;

if (empty($_SERVER['REMOTE_ADDR'])) return;

if (empty($_POST['action'])) return;

if ($_POST['action'] != 'plus' && $_POST['action'] != 'minus') return;
# /проверяем переменные

$result = checkIP(); # echo 'result: '.$result;

if (!empty($result))
{
	$array['result'] = 'already_voted';
	/*
	$votes = getVotes(); # print_r($votes);
	$array['votes_plus'] = $votes['votes_plus'];
	$array['votes_minus'] = $votes['votes_minus'];
	*/
}
else
{
	setVote();
	setIP();
	$votes = getVotes(); # print_r($votes);
	$array['result'] = 'success';
	$array['id'] = $_POST['id'];
	$array['votes_plus'] = $votes['votes_plus'];
	$array['votes_minus'] = $votes['votes_minus'];
}

echo json_encode($array);

# ФУНКЦИИ

# ПРОВЕРЯЕМ, ГОЛОСОВАЛ ЛИ ДАННЫЙ IP ПО ДАННОМУ ОТЗЫВУ
function checkIP()
{
	# print_R($_POST);
	
	global $dbh;
	
	$sql = "
	select id
	from ".DB_PREFIX."feedback_votes_ips
	where ip = :ip
		  and feedback_id = :feedback_id
	limit 1
	"; # echo $sql."<hr />";
	$result = $dbh->prepare($sql);
	$result->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
	$result->bindParam(':feedback_id', $_POST['id'], PDO::PARAM_INT);
	try {
		if ($result->execute()) {
			$_ = $result->fetch(); # print_r($_);
			if (!empty($_['id'])) return 1;
			else return;
		}
	}
	catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ПРОВЕРЯЕМ, ГОЛОСОВАЛ ЛИ ДАННЫЙ IP ПО ДАННОМУ ОТЗЫВУ

# ФИКСИРУЕМ ОТЗЫВ В БД
function setVote()
{
	global $dbh;
	
	if ($_POST['action'] == 'plus') {
		$sql = "
		update ".DB_PREFIX."feedback
		set votes_plus = 1 + votes_plus
		where id = :id
		"; # echo $sql."<hr />";
	}
	elseif ($_POST['action'] == 'minus') {
		$sql = "
		update ".DB_PREFIX."feedback
		set votes_minus = votes_minus + 1
		where id = :id
		"; # echo $sql."<hr />";
	}
	$result = $dbh->prepare($sql);
	$result->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
	try {
		if ($result->execute())	{ return 1;	}
	}
	catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ФИКСИРУЕМ ОТЗЫВ В БД

# ФИКСИРУЕМ IP В БД
function setIP()
{
	global $dbh;
	
	$sql = "
	insert into ".DB_PREFIX."feedback_votes_ips
	(
	feedback_id,
	ip
	)
	values
	(
	:feedback_id,
	:ip
	)
	"; # echo $sql."<hr />";
	$result = $dbh->prepare($sql);
	$result->bindParam(':feedback_id', $_POST['id'], PDO::PARAM_INT);
	$result->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
	try	{
		if ($result->execute()) { return 1;	}
	}
	catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
}
# /ФИКСИРУЕМ ОТЗЫВ В БД

# ПОЛУЧАЕМ СПИСОК ГОЛОСОВ
function getVotes()
{
	global $dbh;
	
	$sql = "
	select votes_plus,
		   votes_minus
	from ".DB_PREFIX."feedback
	where id = :id
	"; # echo $sql."<hr />";
	$result = $dbh->prepare($sql);
	$result->bindParam(':id', $_POST['id'], PDO::PARAM_INT); # echo $eventID.'<hr />';
	try	{
		if ($result->execute())	{
			$_ = $result->fetch(); # print_r($_);
			return $_;
		}
	}
	catch (PDOException $e) { if (DB_SHOW_ERRORS) { echo "Ошибка в SQL-запросе:<br /><br />".$sql."<br /><br />".$e->getMessage(); } }
} # /ПОЛУЧАЕМ СПИСОК ГОЛОСОВ

# ПРОВЕРКА И ЗАЩИТА GET-ПЕРЕМЕННЫХ
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
}
# /ПРОВЕРКА И ЗАЩИТА GET-ПЕРЕМЕННЫХ

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
}
# /ПОЛУЧАЕМ НУЖНУЮ КОДИРОВКУ ДАННЫХ

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
}
# /ОПРЕДЕЛЯЕМ, ЯВЛЯЕТСЯ ЛИ КОДИРОВКА СТРОКЕ UTF-8 ИЛИ НЕТ

# /ФУНКЦИИ