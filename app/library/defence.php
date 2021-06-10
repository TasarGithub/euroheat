<?php # ����� �� 2010.12.23, romanov.egor@gmail.com, �������� ���������������� ������

class defence
{
    # ������ �������������:
    # $_POST['name'] = clearUserData($_POST['name'], 50);
    function clearUserData($string, $maxLength = NULL)
	{
        $string = trim($string);
        $string = strip_tags($string);
        $string = htmlspecialchars($string, ENT_QUOTES);
        # ������� �������� ������� ��������� ��� ����������� ����� � ������� strip_tags
        # ����� 30.12.2010 �� "php-architect's Guide to PHP Security", ���. 62.
        /*
        preg_replace(
        "!<([A-Z]\w*)
        (?:\s* (?:\w+) \s* = \s* (?(?=[�\�]) ([�\�])(?:.*?\2)+ | (?:[^\s>]*) ) )*
        \s* (\s/)? >!ix",
        "<\1\5>", $input);
        */
        
        # ���� ������� ���� �����, �������� ��
        if ($maxLength) $string = substr($string, 0, $maxLength);
        
        # �������� �� ������� ������� �����
        $dataArray = explode(" ", $string);
        $dataArrayCount = count($dataArray);
        if ($dataArrayCount > 0)
		{
            for ($i=0;$i<$dataArrayCount;$i++) if (strlen($dataArray[$i]) > 255) $dataArray[$i] = substr($dataArray[$i], 0, 255);
            $string = implode(" ", $dataArray);
        }
        
        return $string;
    }
	
	# �� �����: ������� �����
	# �� ������: ����� � ��������������
	function convertTextFromRuToEn($text)
	{
		if (!empty($text))
		{
			setlocale(LC_ALL, 'ru_RU.CP1251');

			$text = trim($text);
			
			$text = str_replace("�", "a", $text);
			$text = str_replace("�", "A", $text);
			
			$text = str_replace("�", "b", $text);
			$text = str_replace("�", "B", $text);
			
			$text = str_replace("�", "v", $text);
			$text = str_replace("�", "V", $text);
			
			$text = str_replace("�", "g", $text);
			$text = str_replace("�", "G", $text);
			
			$text = str_replace("�", "d", $text);
			$text = str_replace("�", "D", $text);
			
			$text = str_replace("�", "e", $text);
			$text = str_replace("�", "E", $text);
			
			$text = str_replace("�", "jo", $text);
			$text = str_replace("�", "Jo", $text);
			
			$text = str_replace("�", "zh", $text);
			$text = str_replace("�", "Zh", $text);
			
			$text = str_replace("�", "z", $text);
			$text = str_replace("�", "Z", $text);
			
			$text = str_replace("�", "i", $text);
			$text = str_replace("�", "I", $text);
			
			$text = str_replace("�", "j", $text);
			$text = str_replace("�", "J", $text);
			
			$text = str_replace("�", "k", $text);
			$text = str_replace("�", "K", $text);
			
			$text = str_replace("�", "l", $text);
			$text = str_replace("�", "L", $text);
			
			$text = str_replace("�", "m", $text);
			$text = str_replace("�", "M", $text);
			
			$text = str_replace("�", "n", $text);
			$text = str_replace("�", "N", $text);
			
			$text = str_replace("�", "o", $text);
			$text = str_replace("�", "O", $text);
			
			$text = str_replace("�", "p", $text);
			$text = str_replace("�", "P", $text);
			
			$text = str_replace("�", "r", $text);
			$text = str_replace("�", "R", $text);
			
			$text = str_replace("�", "s", $text);
			$text = str_replace("�", "S", $text);
			
			$text = str_replace("�", "t", $text);
			$text = str_replace("�", "T", $text);
			
			$text = str_replace("�", "u", $text);
			$text = str_replace("�", "U", $text);
			
			$text = str_replace("�", "f", $text);
			$text = str_replace("�", "F", $text);
			
			$text = str_replace("�", "h", $text);
			$text = str_replace("�", "H", $text);
			
			$text = str_replace("�", "c", $text);
			$text = str_replace("�", "C", $text);
			
			$text = str_replace("�", "ch", $text);
			$text = str_replace("�", "Ch", $text);
			
			$text = str_replace("�", "sh", $text);
			$text = str_replace("�", "Sh", $text);
			
			$text = str_replace("�", "sch", $text);
			$text = str_replace("�", "Sch", $text);
			
			$text = str_replace("�", "", $text);
			$text = str_replace("�", "", $text);
			
			$text = str_replace("�", "y", $text);
			$text = str_replace("�", "Y", $text);
			
			$text = str_replace("�", "", $text);
			$text = str_replace("�", "", $text);
			
			$text = str_replace("�", "e", $text);
			$text = str_replace("�", "E", $text);
			
			$text = str_replace("�", "ju", $text);
			$text = str_replace("�", "Ju", $text);
			
			$text = str_replace("�", "ya", $text);
			$text = str_replace("�", "Ya", $text);
			
			return $text;
		}
	}
}