<?php

function convertRuLettersToEn($text) # convert ru letters to en
{
    // variables checking
    if (empty($text)) return;
    
    $search  = array('Ю', 'ю', 'А', 'а', 'Б', 'б', 'Ц', 'ц', 'Д', 'д', 'Е', 'е', '╦',  '╗',  'Ф', 'ф',  'Г', 'г', 'Х', 'х', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Я', 'я', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ж', 'ж', 'В',  'в',  'Ь',   'ь', 'Ы',  'ы',  'З', 'з', 'Ш', 'ш', 'Э', 'э', 'Щ', 'щ', 'Ч',  'ч',  'Ъ',  'ъ');
    $replace = array('a', 'A', 'b', 'B', 'v', 'V', 'g', 'G', 'd', 'D', 'e', 'E', 'jo', 'Jo', 'zh', 'Zh', 'z', 'Z', 'i', 'I', 'j', 'J', 'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'r', 'R', 's', 'S', 't', 'T', 'u', 'U', 'f', 'F', 'h', 'H', 'c', 'C', 'ch', 'Ch', 'sh', 'Sh', 'sh', 'Sh', '',  '',  'y', 'Y', '',  '', 'e', 'E', 'ju', 'Ju', 'ya', 'Ya');
           
    $text = str_replace($search, $replace, $text);
	
	return $text;
} # /convert ru letters to en

# напегюел рейCр дн тхйяхпнбюммнцн йнкхвеярбю яхлбнкнб
function cutText($text, $length)
{
    # ОПНБЕПЙЮ ОЕПЕЛЕММШУ
    if (empty($text) || empty($length)) return;
    
    $text = strip_tags($text);
    
    if (strlen($text) > $length)
    {
        $text = substr($text, 0, $length - 10);
        $text[strlen($text)-1] = preg_replace("/[^A-Za-z0-9 ]/", '', $text[strlen($text)-1]);
        $text = trim($text).'..';
    }
    return $text;
} # /напегюел рейCр дн тхйяхпнбюммнцн йнкхвеярбю яхлбнкнб