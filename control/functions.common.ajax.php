<?php
# ����������: ���������� ������� ������� ���������� ��� ajax-��������
# ���� ��������: 2015.4.22
# �����: romanov.egor@gmail.com

# �������� � ������ POST-����������
function preparePOSTVariables()
{
	foreach ($_POST as $key => &$val)
	{
		if (!empty($val))
		{
			if (!is_array($key) and !is_array($val))
			{
                /*
                if ($key != 'html_code')
                {
                    # $_POST[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    # $_POST[$key] = strip_tags($val);
                }
                */
                $_POST[$key] = trim($val);
                $_POST[$key] = getCorrectEnc($val);
			}
		}
	}
} # /�������� � ������ POST-����������

# �������� � ������ GET-����������
function prepareGETVariables()
{
	foreach ($_GET as $key => &$val)
	{
		if (!empty($val))
		{
			if (!is_array($key) and !is_array($val))
			{
                /*
                if ($key != 'html_code')
                {
                    # $_GET[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    # $_GET[$key] = strip_tags($val);
                }
                */
                $_GET[$key] = trim($val);
                $_GET[$key] = getCorrectEnc($val);
			}
		}
	}
} # /�������� � ������ GET-����������

# �������� ������ ��������� ������
function getCorrectEnc($var)
{
	# ��������� �������� ����������
	if (empty($var)) return;
	
	# echo $var.'<hr />';
	if (!empty($var))
	{
		$isUTF8 = detectUTF8($var); # echo $isUTF8.'<br>'.$var.'<hr />';
		# ���� ��� UTF-8
		if (!empty($isUTF8))
		{
			$var2 = iconv('UTF-8', 'windows-1251//TRANSLIT', $var); # echo $var."<br />";
            # ����� ����, ������������ 22.12.2015, ����� ��������� ������ ������������ ��� UTF-8
            # � ��� ��������������� ���� ������ ������
            if (!empty($var2)) $var = $var2;
		}
		
		return $var;		
	}
	# /�������� ������ ��������� ��� ������
	# echo $var.'<hr />';
} # /�������� ������ ��������� ������

# ����������, �������� �� ��������� ������ UTF-8 ��� ���
# ���������� 1 - ���� UTF-8, 0 - ���� �� UTF-8
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
} # /����������, �������� �� ��������� ������ UTF-8 ��� ���