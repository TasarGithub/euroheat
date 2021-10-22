--
-- РЎС‚СЂСѓРєС‚СѓСЂР° С‚Р°Р±Р»РёС†С‹ `best_hockey_ru_backups`
--

DROP TABLE IF EXISTS `backups`;
CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'РЈРЅРёРєР°Р»СЊРЅС‹Р№ РїРѕСЂСЏРґРєРѕРІС‹Р№ РЅРѕРјРµСЂ Р·Р°РїРёСЃРё',
  `table_name` varchar(255) DEFAULT NULL COMMENT 'РќР°Р·РІР°РЅРёРµ С‚Р°Р±Р»РёС†С‹ (РЅРµ РѕР±СЏР·Р°С‚РµР»СЊРЅРѕ РїРѕР»РЅРѕРµ СЃРѕРІРїР°РґРµРЅРёРµ)',
  `entry_id` int(11) DEFAULT NULL COMMENT 'СѓРЅРёРєР°Р»СЊРЅС‹Р№ id Р·Р°РїРёСЃРё РёР· С‚Р°Р±Р»РёС†С‹ table_name',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'РќР°Р·РІР°РЅРёРµ РїРѕР»СЏ (id) РІ Р°РґРјРёРЅРєРµ',
  `date_add` datetime DEFAULT NULL COMMENT 'Р”Р°С‚Р° СЃРѕР·РґР°РЅРёСЏ backup''Р°',
  `html_code` longtext COMMENT 'html-РєРѕРґ backup''Р°',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Р‘СЌРєР°РїС‹ РІСЃРµС… СЂР°Р·РґРµР»РѕРІ РІ Р°РґРјРёРЅРєРµ' AUTO_INCREMENT=1 ;
