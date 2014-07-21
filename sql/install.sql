CREATE TABLE `#__etd_topmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'The display title of the menu item.',
  `path` varchar(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.',
  `link` varchar(1024) NOT NULL COMMENT 'The actually link the menu item refers to.',
  `type` varchar(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The published state of the menu link.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The relative level in the tree.',
  `id_shop` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__shop.id',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'The relative ordering of the menu item in the tree.',
  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
  `access` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.',
  `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.',
  `params` text NOT NULL COMMENT 'JSON encoded data for the menu item.',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `home` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicates if this menu item is the home or default page.',
  `id_lang` int(10) NOT NULL DEFAULT '0' COMMENT 'FK to #__lang.id',
  PRIMARY KEY (`id`),
  KEY `idx_idshop` (`id_shop`,`published`,`access`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_path` (`path`(255)),
  KEY `idx_lang` (`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO  `#__etd_topmenu` ( `id`, `title`, `path` ,`link` ,`type` ,`published` ,`parent_id` ,`level` ,`id_shop` ,`ordering` ,`browserNav` ,`access` ,`img` ,`params` ,`lft` ,`rgt` ,`home` ,`id_lang` )
VALUES ( '1',  'root',  '',  '',  '',  '1',  '0',  '0',  '0',  '0',  '0',  '0',  '',  '',  '0',  '1',  '0',  '0' )