--
-- ��������� ������� `best_hockey_ru_backups`
--

DROP TABLE IF EXISTS `backups`;
CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '���������� ���������� ����� ������',
  `table_name` varchar(255) DEFAULT NULL COMMENT '�������� ������� (�� ����������� ������ ����������)',
  `entry_id` int(11) DEFAULT NULL COMMENT '���������� id ������ �� ������� table_name',
  `field_name` varchar(255) DEFAULT NULL COMMENT '�������� ���� (id) � �������',
  `date_add` datetime DEFAULT NULL COMMENT '���� �������� backup''�',
  `html_code` longtext COMMENT 'html-��� backup''�',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='������ ���� �������� � �������' AUTO_INCREMENT=1 ;
