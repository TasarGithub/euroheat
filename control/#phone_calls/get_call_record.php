<?php

# проверка входящих переменных
if (empty($_GET['call_record'])) exit('Variable "call_record" is empty.');

$fullPathToFile = 'http://mango.forboss.ru/public/web-services/get-call-record/?pwd=kUWhsAVWTC0P68Mz224&file_name='.$_GET['call_record'];
# echo 'full path to file: '.$fullPathToFile.'<br />';

header('Content-type: audio/mpeg');
# header('Content-Length: '.filesize($fullPathToFile));
# header("Content-Transfer-Encoding: binary");
# header('Content-Disposition: inline;filename="'.$_GET['file_name'].'"');
# ader('X-Pad: avoid browser bug');
# header('Cache-Control: no-cache');
readfile($fullPathToFile);