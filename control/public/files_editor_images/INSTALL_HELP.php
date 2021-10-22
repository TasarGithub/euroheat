<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Install Help</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style type="text/css">
body { font-family: verdana; font-size: 10pt }
.highlight { background-color: #FFFFCC }
</style>
</head>

<body>
<h2>РџРѕРјРѕС‰СЊ РІ СѓСЃС‚Р°РЅРѕРІРєРµ</h2>
<p>Р­С‚РѕС‚ С„Р°Р№Р» РґРѕР»Р¶РµРЅ РЅР°С…РѕРґРёС‚СЃСЏ РІ РїР°РїРєРµ 'editor_files' РёР»Рё РЅРµ Р±СѓРґРµС‚ СЂР°Р±РѕС‚Р°С‚СЊ! РќРµ РјРµРЅСЏР№С‚Рµ РёРјСЏ РґР°РЅРЅРѕРіРѕ С„Р°Р№Р»Р°!</p>
<p>Р­С‚РѕС‚ С„Р°Р№Р» РЅСѓР¶РµРЅ РґР»СЏ РїРѕРјРѕС‰Рё Р’Р°Рј РІ СѓСЃС‚Р°РЅРѕРІРєРµ  Рё РЅР°СЃС‚СЂРѕР№РєРµ С„Р°Р№Р»Р° <i><b>config.php</b></i>.</p>
<p>This file might not work for everyone!</p>
<hr>
<p>РЈСЃС‚Р°РЅРѕРІРёС‚Рµ РїР°СЂР°РјРµС‚СЂ <b>WP_FILE_DIRECTORY</b>: <span class="highlight"><nobr>'<?php echo str_replace('\\', '/', getcwd().'/'); ?>'</nobr></span></p>
<hr>
<p>РЈСЃС‚Р°РЅРѕРІРёС‚Рµ РїР°СЂР°РјРµС‚СЂ <b>WP_WEB_DIRECTORY</b>: <span class="highlight"><nobr>'<?php echo preg_replace('/INSTALL_HELP\.php/smi', '', $_SERVER['SCRIPT_NAME']); ?>'</nobr></span></p>
<hr>
<p>РЇ РЅРµ РјРѕРіСѓ РЅР°Р№С‚Рё <b>IMAGE_FILE_DIRECTORY</b> С‚.Рє. СЏ РЅРµ Р·РЅР°СЋ РіРґРµ РЅР°С…РѕРґРёС‚СЃСЏ РїР°РїРєР° РґР»СЏ С…СЂР°РЅРµРЅРёСЏ РёР·РѕР±СЂР°Р¶РµРЅРёР№. РћРЅР° РґРѕР»Р¶РЅР° Р±С‹С‚СЊ Р·Р°РїРёСЃР°РЅР° РїСЂРёРјРµСЂРЅРѕ С‚Р°Рє: <span class="highlight"><nobr>'<?php echo str_replace(array('\\', '/editor_files'), array('/', '/images'), getcwd().'/'); ?>'</nobr></span></p>
<hr>
<p>РЇ РЅРµ РјРѕРіСѓ РЅР°Р№С‚Рё <b>IMAGE_WEB_DIRECTORY</b> С‚.Рє. СЏ РЅРµ Р·РЅР°СЋ РіРґРµ РЅР°С…РѕРґРёС‚СЃСЏ РїР°РїРєР° РґР»СЏ С…СЂР°РЅРµРЅРёСЏ РёР·РѕР±СЂР°Р¶РµРЅРёР№. РћРЅР° РґРѕР»Р¶РЅР° Р±С‹С‚СЊ Р·Р°РїРёСЃР°РЅР° РїСЂРёРјРµСЂРЅРѕ С‚Р°Рє: <span class="highlight"><nobr>'<?php echo preg_replace('/editor_files\/INSTALL_HELP\.php/smi', 'images/', $_SERVER['SCRIPT_NAME']); ?>'</nobr></span></p>
<hr>
<p>РЇ РЅРµ РјРѕРіСѓ РЅР°Р№С‚Рё <b>DOCUMENT_FILE_DIRECTORY</b> С‚.Рє. СЏ РЅРµ Р·РЅР°СЋ РіРґРµ РЅР°С…РѕРґРёС‚СЃСЏ РїР°РїРєР° РґР»СЏ С…СЂР°РЅРµРЅРёСЏ Р·Р°РіСЂСѓР¶РµРЅРЅС‹С… РґРѕРєСѓРјРµРЅС‚РѕРІ. РћРЅР° РґРѕР»Р¶РЅР° Р±С‹С‚СЊ Р·Р°РїРёСЃР°РЅР° РїСЂРёРјРµСЂРЅРѕ С‚Р°Рє: <span class="highlight"><nobr>'<?php echo str_replace(array('\\', '/editor_files'), array('/', '/downloads'), getcwd().'/'); ?>'</nobr></span></p>
<hr>
<p>РЇ РЅРµ РјРѕРіСѓ РЅР°Р№С‚Рё <b>DOCUMENT_WEB_DIRECTORY</b>  С‚.Рє. СЏ РЅРµ Р·РЅР°СЋ РіРґРµ РЅР°С…РѕРґРёС‚СЃСЏ РїР°РїРєР° РґР»СЏ С…СЂР°РЅРµРЅРёСЏ Р·Р°РіСЂСѓР¶РµРЅРЅС‹С… С„Р°Р№Р»РѕРІ. РћРЅР° РґРѕР»Р¶РЅР° Р±С‹С‚СЊ Р·Р°РїРёСЃР°РЅР° РїСЂРёРјРµСЂРЅРѕ С‚Р°Рє: <span class="highlight"><nobr>'<?php echo preg_replace('/editor_files\/INSTALL_HELP\.php/smi', 'downloads/', $_SERVER['SCRIPT_NAME']); ?>'</nobr></span></p>
<hr>
<p>РџРѕР»СЊР·СѓР№С‚РµСЃСЊ!</p>
</body>
</html>
