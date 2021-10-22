<?php

function convertRuLettersToEn($text) # convert ru letters to en
{
    // variables checking
    if (empty($text)) return;
    
    $search  = array('а', 'А', 'б', 'Б', 'в', 'В', 'г', 'Г', 'д', 'Д', 'е', 'Е', 'ё',  'Ё',  'ж', 'Ж',  'з', 'З', 'и', 'И', 'й', 'Й', 'к', 'К', 'л', 'Л', 'м', 'М', 'н', 'Н', 'о', 'О', 'п', 'П', 'р', 'Р', 'с', 'С', 'т', 'Т', 'у', 'У', 'ф', 'Ф', 'х', 'Х', 'ц', 'Ц', 'ч',  'Ч',  'ш',   'Ш', 'щ',  'Щ',  'ъ', 'Ъ', 'ы', 'Ы', 'ь', 'Ь', 'э', 'Э', 'ю',  'Ю',  'я',  'Я');
    $replace = array('a', 'A', 'b', 'B', 'v', 'V', 'g', 'G', 'd', 'D', 'e', 'E', 'jo', 'Jo', 'zh', 'Zh', 'z', 'Z', 'i', 'I', 'j', 'J', 'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'r', 'R', 's', 'S', 't', 'T', 'u', 'U', 'f', 'F', 'h', 'H', 'c', 'C', 'ch', 'Ch', 'sh', 'Sh', 'sh', 'Sh', '',  '',  'y', 'Y', '',  '', 'e', 'E', 'ju', 'Ju', 'ya', 'Ya');
           
    $text = str_replace($search, $replace, $text);
	
	return $text;
} # /convert ru letters to en

# ОБРЕЗАЕМ ТЕКCТ ДО ФИКСИРОВАННОГО КОЛИЧЕСТВА СИМВОЛОВ
function cutText($text, $length)
{
    # проверка переменных
    if (empty($text) || empty($length)) return;
    
    $text = strip_tags($text);
    
    if (strlen($text) > $length)
    {
        $text = substr($text, 0, $length - 10);
        $text[strlen($text)-1] = preg_replace("/[^A-Za-z0-9 ]/", '', $text[strlen($text)-1]);
        $text = trim($text).'..';
    }
    return $text;
} # /ОБРЕЗАЕМ ТЕКCТ ДО ФИКСИРОВАННОГО КОЛИЧЕСТВА СИМВОЛОВ