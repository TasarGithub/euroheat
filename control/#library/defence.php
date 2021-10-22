<?php # Класс от 2010.12.23, romanov.egor@gmail.com, проверка пользовательских данных

class defence
{
    # пример использования:
    # $_POST['name'] = clearUserData($_POST['name'], 50);
    function clearUserData($string, $maxLength = NULL)
	{
        $string = trim($string);
        $string = strip_tags($string);
        $string = htmlspecialchars($string, ENT_QUOTES);
        # функция удаления опасных атрибутов для разрешенных тегов в функции strip_tags
        # взята 30.12.2010 из "php-architect's Guide to PHP Security", стр. 62.
        /*
        preg_replace(
        "!<([A-Z]\w*)
        (?:\s* (?:\w+) \s* = \s* (?(?=[“\’]) ([“\’])(?:.*?\2)+ | (?:[^\s>]*) ) )*
        \s* (\s/)? >!ix",
        "<\1\5>", $input);
        */
        
        # если указана макс длина, обрезаем ее
        if ($maxLength) $string = substr($string, 0, $maxLength);
        
        # проверка на слишком длинные слова
        $dataArray = explode(" ", $string);
        $dataArrayCount = count($dataArray);
        if ($dataArrayCount > 0)
		{
            for ($i=0;$i<$dataArrayCount;$i++) if (strlen($dataArray[$i]) > 255) $dataArray[$i] = substr($dataArray[$i], 0, 255);
            $string = implode(" ", $dataArray);
        }
        
        return $string;
    }
	
	# на входе: русский текст
	# на выходе: текст в транслитерации
	function convertTextFromRuToEn($text)
	{
		if (!empty($text))
		{
			setlocale(LC_ALL, 'ru_RU.CP1251');

			$text = trim($text);
			
			$text = str_replace("а", "a", $text);
			$text = str_replace("А", "A", $text);
			
			$text = str_replace("б", "b", $text);
			$text = str_replace("Б", "B", $text);
			
			$text = str_replace("в", "v", $text);
			$text = str_replace("В", "V", $text);
			
			$text = str_replace("г", "g", $text);
			$text = str_replace("Г", "G", $text);
			
			$text = str_replace("д", "d", $text);
			$text = str_replace("Д", "D", $text);
			
			$text = str_replace("е", "e", $text);
			$text = str_replace("Е", "E", $text);
			
			$text = str_replace("ё", "jo", $text);
			$text = str_replace("Ё", "Jo", $text);
			
			$text = str_replace("ж", "zh", $text);
			$text = str_replace("Ж", "Zh", $text);
			
			$text = str_replace("з", "z", $text);
			$text = str_replace("З", "Z", $text);
			
			$text = str_replace("и", "i", $text);
			$text = str_replace("И", "I", $text);
			
			$text = str_replace("й", "j", $text);
			$text = str_replace("Й", "J", $text);
			
			$text = str_replace("к", "k", $text);
			$text = str_replace("К", "K", $text);
			
			$text = str_replace("л", "l", $text);
			$text = str_replace("Л", "L", $text);
			
			$text = str_replace("м", "m", $text);
			$text = str_replace("М", "M", $text);
			
			$text = str_replace("н", "n", $text);
			$text = str_replace("Н", "N", $text);
			
			$text = str_replace("о", "o", $text);
			$text = str_replace("О", "O", $text);
			
			$text = str_replace("п", "p", $text);
			$text = str_replace("П", "P", $text);
			
			$text = str_replace("р", "r", $text);
			$text = str_replace("Р", "R", $text);
			
			$text = str_replace("с", "s", $text);
			$text = str_replace("С", "S", $text);
			
			$text = str_replace("т", "t", $text);
			$text = str_replace("Т", "T", $text);
			
			$text = str_replace("у", "u", $text);
			$text = str_replace("У", "U", $text);
			
			$text = str_replace("ф", "f", $text);
			$text = str_replace("Ф", "F", $text);
			
			$text = str_replace("х", "h", $text);
			$text = str_replace("Х", "H", $text);
			
			$text = str_replace("ц", "c", $text);
			$text = str_replace("Ц", "C", $text);
			
			$text = str_replace("ч", "ch", $text);
			$text = str_replace("Ч", "Ch", $text);
			
			$text = str_replace("ш", "sh", $text);
			$text = str_replace("Ш", "Sh", $text);
			
			$text = str_replace("щ", "sch", $text);
			$text = str_replace("Щ", "Sch", $text);
			
			$text = str_replace("ъ", "", $text);
			$text = str_replace("Ъ", "", $text);
			
			$text = str_replace("ы", "y", $text);
			$text = str_replace("Ы", "Y", $text);
			
			$text = str_replace("ь", "", $text);
			$text = str_replace("Ь", "", $text);
			
			$text = str_replace("э", "e", $text);
			$text = str_replace("Э", "E", $text);
			
			$text = str_replace("ю", "ju", $text);
			$text = str_replace("Ю", "Ju", $text);
			
			$text = str_replace("я", "ya", $text);
			$text = str_replace("Я", "Ya", $text);
			
			return $text;
		}
	}
}