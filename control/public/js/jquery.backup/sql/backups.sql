--
-- Структура таблицы `best_hockey_ru_backups`
--

DROP TABLE IF EXISTS `backups`;
CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный порядковый номер записи',
  `table_name` varchar(255) DEFAULT NULL COMMENT 'Название таблицы (не обязательно полное совпадение)',
  `entry_id` int(11) DEFAULT NULL COMMENT 'уникальный id записи из таблицы table_name',
  `field_name` varchar(255) DEFAULT NULL COMMENT 'Название поля (id) в админке',
  `date_add` datetime DEFAULT NULL COMMENT 'Дата создания backup''а',
  `html_code` longtext COMMENT 'html-код backup''а',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Бэкапы всех разделов в админке' AUTO_INCREMENT=1 ;
