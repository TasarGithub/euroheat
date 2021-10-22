<?php



### РћРўР›РђР”РљРђ

# print_r($_GET);

# print_r($_POST);



# $a = unserialize($_POST['form']); print_r($a);



# sleep(5);



# Р·Р°С‰РёС‚Р° РѕС‚ Р·Р°РїСЂРѕСЃР° c РґСЂСѓРіРѕРіРѕ СЃР°Р№С‚Р°

if (!stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])) exit('');



# СѓРєР°Р·С‹РІР°РµРј РєРѕРґРёСЂРѕРІРєСѓ, РєРѕС‚РѕСЂСѓСЋ Р±СѓРґРµС‚ РѕС‚РґР°РІР°С‚СЊ javascript'Сѓ ajax-СЃРєСЂРёРїС‚

header('Content-type: text/html; charset=windows-1251');



# РїРѕРґРєР»СЋС‡Р°РµРј Рё РёРЅРёС†РёР°Р»РёР·РёСЂСѓРµРј РєР»Р°СЃСЃ РґР»СЏ СЂР°Р±РѕС‚С‹ СЃ Р‘Р” С‡РµСЂРµР· PDO

include($_SERVER['DOCUMENT_ROOT'].'/control/db.connection.pdo.php');



# РїРѕРґРєР»СЋС‡Р°РµРј РєРѕРЅС„РёРі

include($_SERVER['DOCUMENT_ROOT'].'/control/config.control.php');



# РїРѕРґРєР»СЋС‡Р°РµРј С„СѓРЅРєС†РёРё РѕР±С‰РµРіРѕ РЅР°Р·РЅР°С‡РµРЅРёСЏ РґР»СЏ ajax-СЃРєСЂРёРїС‚РѕРІ

include($_SERVER['DOCUMENT_ROOT'].'/control/functions.common.ajax.php');



# РїРѕРґРєР»СЋС‡Р°РµРј РѕР±С‰РёРµ С„СѓРЅРєС†РёРё РґР»СЏ index.php Рё ajax.php

include('common.functions.php');



# РїСЂРѕРІРµСЂРєР° + РЅСѓР¶РЅР°СЏ РєРѕРґРёСЂРѕРІРєР° POST-РїРµСЂРµРјРµРЅРЅС‹С…

preparePOSTVariables(); # print_r($_POST); exit;



# РїРѕРґРіРѕС‚Р°РІР»РёРІР°РµРј РїРѕР»СЏ С„РѕСЂРјС‹

if (!empty($_POST['params'])) parse_str($_POST['params'], $params); # print_r($params);



# Р›РћР“Р�РљРђ

# РїСЂРѕРІСЂСЏРµРј, СЃСѓС‰РµСЃС‚РІСѓРµС‚ Р»Рё РЅРѕРІРѕСЃС‚СЊ РїРѕ РЅР°Р·РІР°РЅРёСЋ

if ($_POST['action'] == '') {

    /*

    $sql = '

    select id

    from '.DB_PREFIX.'news

    where h1 = :name

    '; # echo '<pre>'.$sql."</pre><hr />";

    $sth = $dbh->prepare($sql);

    $sth->bindParam(':name', $_POST['name']);

    $sth->execute();

    if ($_ = $sth->fetchColumn())

    {

        $result = array('result' => 'exists', 'id' => $_);

        echo json_encode($result);

    }

    */

}



# /Р›РћР“Р�РљРђ



# Р¤РЈРќРљР¦Р�Р�



# /Р¤РЈРќРљР¦Р�Р�