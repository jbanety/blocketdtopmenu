CREATE TABLE `#__etd_topmenu` (
  `id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The relative level in the tree.',
  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
  `image` varchar(255) NOT NULL,
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.',
  `css` varchar(255) NOT NULL COMMENT 'CSS Class',
  `params` text NOT NULL COMMENT 'JSON encoded data for the menu item.',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__etd_topmenu` (`id`, `type`, `parent_id`, `level`, `browserNav`, `access`, `columns`, `distribution`, `manual_distribution`, `width`, `column_widths`, `children_group`, `children_type`, `modules`, `module_hooks`, `css`, `params`, `lft`, `rgt`) VALUES
  (1, 'root', 0, 0, 0, '', 0, '', '', 0, 2);

CREATE TABLE IF NOT EXISTS `#__etd_topmenu_lang` (
  `id_link` int(10) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__etd_topmenu_lang` (`id_link`, `id_lang`, `title`) VALUES
(1, 1, 'Racine');

CREATE TABLE IF NOT EXISTS `#__etd_topmenu_shop` (
  `id_link` int(10) NOT NULL,
  `id_shop` int(10) NOT NULL,
  `published` tinyint(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__etd_topmenu_shop` (`id_link`, `id_shop`, `published`) VALUES
(1, 1, 1);

ALTER TABLE `a2e1_etd_topmenu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_idshop` (`access`),
  ADD KEY `idx_left_right` (`lft`,`rgt`);

ALTER TABLE `a2e1_etd_topmenu_lang`
  ADD PRIMARY KEY (`id_link`,`id_lang`),
  ADD KEY `id_lang` (`id_lang`);

ALTER TABLE `a2e1_etd_topmenu_shop`
  ADD PRIMARY KEY (`id_link`,`id_shop`),
  ADD KEY `published` (`published`),
  ADD KEY `shop_published` (`id_shop`,`published`);


ALTER TABLE `a2e1_etd_topmenu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;