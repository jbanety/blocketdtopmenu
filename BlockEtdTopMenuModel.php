<?php
/**
 * @package     blocketdtopmenu
 *
 * @version     1.0.1
 * @copyright   Copyright (C) 2014 Jean-Baptiste Alleaume. Tous droits réservés.
 * @license     http://alleau.me/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class BlockEtdTopMenuModel extends ObjectModel {

	protected static $_location;
	protected static $_location_id;

	static function getLinks($include_root=false,$published=false,$id_lang=false, $id_shop=false) {

        $context = Context::getContext();
		$cache = Cache::getInstance();
		
		$key = 'blocketdtopmenumodel_getLinks';
		if ($include_root)
			$key .= '_include_root';
		if ($published)
			$key .= '_published';
		if ($id_lang)
			$key .= '_' . $id_lang;
        else
            $key .= '_' . $context->language->id;
		if ($id_shop)
			$key .= '_' . $id_shop;
			
		//$key = md5($key);

		if (!$cache->exists($key)) {
			$links = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT a.*, b.title, c.published
				FROM ' . _DB_PREFIX_ . 'etd_topmenu AS a
				LEFT JOIN ' . _DB_PREFIX_ . 'etd_topmenu_lang AS b ON b.id_link = a.id
				LEFT JOIN ' . _DB_PREFIX_ . 'etd_topmenu_shop AS c ON c.id_link = a.id
				WHERE
					' . (!$include_root ? 'a.id > 1 AND' : '') . '
					' . ($published ? 'c.published = 1 AND' : '') . '
					b.id_lang = '.($id_lang ? (int)$id_lang : (int)$context->language->id).'
					AND c.id_shop = '.($id_shop ? (int)$id_shop : (int)$context->shop->id).'
				ORDER BY a.lft ASC
			');

			$cache->set($key, $links);
			
		} else {
			$links = $cache->get($key);
		}
		
		return $links;
	}

	static function getLink($id_link, $id_shop = false) {
	
		$key = 'blocketdtopmenumodel_getlink_' . (int) $id_link;
		if ($id_shop)
			$key .= '_' . $id_shop;

		if (!Cache::isStored($key)) {
			$links = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.*, b.title, b.id_lang, c.published
			FROM ' . _DB_PREFIX_ . 'etd_topmenu AS a
			LEFT JOIN ' . _DB_PREFIX_ . 'etd_topmenu_lang AS b ON b.id_link = a.id
			LEFT JOIN ' . _DB_PREFIX_ . 'etd_topmenu_shop AS c ON c.id_link = a.id
			WHERE
				a.id > 1
				AND a.id = '.(int) $id_link . '
				AND c.id_shop = '.($id_shop ? (int)$id_shop : (int)Context::getContext()->shop->id).'
			');
		
			$link = $links[0];
			$link['params'] = json_decode($link['params']);
			$link['title'] = array();
	
			foreach ($links as $tmp) {
				$link['title'][(int)$tmp['id_lang']] = $tmp['title'];
			}
		
			Cache::store($key, $link);
		}
		
		return Cache::retrieve($key);
	}

	static function deleteLink($pk, $children = true) {

		$k = 'id';
		$db = Db::getInstance();

		// Get the node by id.
		$node = self::_getNode($pk);
		if (empty($node)) {
			return false;
		}

		// Should we delete all children along with the node?
		if ($children) {
			// Delete the node and all of its children.
			$sql = 'select id from ' . _DB_PREFIX_ . 'etd_topmenu where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
			$ids = $db->executeS($sql);

			$tuples = array();
			foreach ($ids as $row) {
				$tuples[] = $row[$k];
			}

			$sql = 'delete from ' . _DB_PREFIX_ . 'etd_topmenu where id IN ('.implode(',',$tuples).')';
			$db->execute($sql);
			$sql = 'delete from ' . _DB_PREFIX_ . 'etd_topmenu_lang where id_link IN ('.implode(',',$tuples).')';
			$db->execute($sql);
			$sql = 'delete from ' . _DB_PREFIX_ . 'etd_topmenu_shop where id_link IN ('.implode(',',$tuples).')';
			$db->execute($sql);

			// Compress the left values.
			$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set lft = lft - ' . (int) $node['width'] . ' where lft > ' . (int) $node['rgt'];
			$db->execute($sql);

			// Compress the right values.
			$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set rgt = rgt - ' . (int) $node['width'] . ' where rgt > ' . (int) $node['rgt'];
			$db->execute($sql);
		}
		// Leave the children and move them up a level.
		else
		{
			// Delete the node.
			$sql = 'select id from ' . _DB_PREFIX_ . 'etd_topmenu where lft = ' . (int) $node['lft'];
			$ids = $db->executeS($sql);

			$tuples = array();
			foreach ($ids as $row) {
				$tuples[] = $row[$k];
			}

			$sql = 'delete from ' . _DB_PREFIX_ . 'etd_topmenu where id IN ('.implode(',',$tuples).')';
			$db->execute($sql);
			$sql = 'delete from ' . _DB_PREFIX_ . 'etd_topmenu_lang where id_link IN ('.implode(',',$tuples).')';
			$db->execute($sql);
			$sql = 'delete from ' . _DB_PREFIX_ . 'etd_topmenu_shop where id_link IN ('.implode(',',$tuples).')';
			$db->execute($sql);


			// Shift all node's children up a level.
			$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set lft = lft - 1, rgt = rgt - 1, level = level - 1 where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
			$db->execute($sql);

			// Adjust all the parent values for direct children of the deleted node.
			$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set parent_id = ' . (int) $node['parent_id'] . ' where parent_id = ' . (int) $node[$k];
			$db->execute($sql);

			// Shift all of the left values that are right of the node.
			$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set lft = lft - 2 where lft > ' . (int) $node['rgt'];
			$db->execute($sql);

			// Shift all of the right values that are right of the node.
			$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set rgt = rgt - 2 where lft > ' . (int) $node['rgt'];
			$db->execute($sql);

		}

        // On vide le cache.
        self::cleanCache();

		return true;
	}

	static function storeLink($link, $update=false) {

		$db = Db::getInstance();

		if ($update && is_int($link['id']) && $link['id'] > 0) {



			// Set the new parent id if parent id not matched and put in last position
			if ($link['old_parent_id'] != $link['parent_id'] ) {
				self::setLocation($link['parent_id'], 'last-child');
			}

			/*
			 * If we have a given primary key then we assume we are simply updating this
			 * node in the tree.  We should assess whether or not we are moving the node
			 * or just updating its data fields.
			 */

			// If the location has been set, move the node to its new location.
			if (self::$_location_id > 0) {

				$newnode = self::moveByReference(self::$_location_id, self::$_location, $link['id']);
				if ($newnode === false) {
					// Error message set in move method.
					return false;
				}

				$parent_id = $newnode['parent_id'];
				$level = $newnode['level'];
				$lft = $newnode['lft'];
				$rgt = $newnode['rgt'];

			} else {
				$node = self::_getNode($link['id']);
				$parent_id = $node['parent_id'];
				$level = $node['level'];
				$lft = $node['lft'];
				$rgt = $node['rgt'];
			}

			$sql = "UPDATE `" . _DB_PREFIX_ . "etd_topmenu` SET `type` = '".$db->escape($link['type'])."', `parent_id` = " . (int) $parent_id . ", `level` = " . (int) $level . ",
					`browserNav` = " . (int) $link['browserNav'] . ", `access` = ". (int) $link['access'] . ", `columns` = ". (int) $link['columns'] . ", `distribution` = '" . $db->escape($link['distribution']) . "', `manual_distribution` = '" . $db->escape($link['manual_distribution']) . "', `width` = " . (int) $link['width'] . ", `column_widths` = '" . $db->escape($link['column_widths']) . "', `children_group` = " . (int) $link['children_group'] . ", `children_type` = '" . $db->escape($link['children_type']) . "', `modules` = " . (int) $link['modules'] . ", `module_hooks` = " . (int) $link['module_hooks'] . ", `css` = '" . $db->escape($link['css']) . "', `params` = '" . $db->escape($link['params']) . "', `lft` = " . (int) $lft . ", `rgt` = " . (int) $rgt . " WHERE `id` = " . (int) $link['id'];

			$db->execute($sql);

			$sql = "delete from `" . _DB_PREFIX_ . "etd_topmenu_lang` where id_link = " . (int) $link['id'];
			$db->execute($sql);

			$sql = "delete from `" . _DB_PREFIX_ . "etd_topmenu_shop` where id_link = " . (int) $link['id'];
			$db->execute($sql);

			$id_link = (int) $link['id'];

		} else {

			self::setLocation($link['parent_id'], 'last-child');

			/*
			 * We are inserting a node somewhere in the tree with a known reference
			 * node.  We have to make room for the new node and set the left and right
			 * values before we insert the row.
			 */
			if (self::$_location_id >= 0) {

				// We are inserting a node relative to the last root node.
				if (self::$_location_id == 0) {

					// Get the last root node as the reference node.
					$sql = 'select id, parent_id, level, lft, rgt from ' . _DB_PREFIX_ . 'etd_topmenu where parent_id = 0 order by lft desc';
					$reference = $db->getRow($sql);

				}
				// We have a real node set as a location reference.
				else
				{
					// Get the reference node by primary key.
					if (!$reference = self::_getNode(self::$_location_id)) {
						return false;
					}
				}

				// Get the reposition data for shifting the tree and re-inserting the node.
				if (!($repositionData = self::_getTreeRepositionData($reference, 2, self::$_location))) {
					return false;
				}

				// Create space in the tree at the new location for the new node in left ids.
				$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set lft = lft + 2 where ' . $repositionData['left_where'];
				$db->execute($sql);

				// Create space in the tree at the new location for the new node in right ids.
				$sql = 'update ' . _DB_PREFIX_ . 'etd_topmenu set rgt = rgt + 2 where ' . $repositionData['right_where'];
				$db->execute($sql);

				// Set the object values.
				$parent_id = $repositionData['new_parent_id'];
				$level = $repositionData['new_level'];
				$lft = $repositionData['new_lft'];
				$rgt = $repositionData['new_rgt'];

			} else {
				// Negative parent ids are invalid
				return false;
			}

			$sql = "INSERT INTO `" . _DB_PREFIX_ . "etd_topmenu` (`type`, `parent_id`, `level`, `browserNav`, `access`, `columns`, `distribution`, `manual_distribution`, `width`, `column_widths`, `children_group`, `children_type`, `modules`, `module_hooks`, `css`, `params`, `lft`, `rgt`)
					VALUES ('".$db->escape($link['type'])."', " . (int) $parent_id . ", " . (int) $level . ", " . (int) $link['browserNav'] . ", ". (int) $link['access'] . ", ". (int) $link['columns'] . ", '" . $db->escape($link['distribution']) . "', '" . $db->escape($link['manual_distribution']) . "', " . (int) $link['width'] . ", '" . $db->escape($link['column_widths']) . "', " . (int) $link['children_group'] . ", '" . $db->escape($link['children_type']) . "', " . (int) $link['modules'] . ", " . (int) $link['module_hooks'] . ", '" . $db->escape($link['css']) . "', '" . $db->escape($link['params']) . "', " . (int) $lft . ", " . (int) $rgt . ")";
			$db->execute($sql);
			$id_link = $db->Insert_ID();
		}

		foreach ($link['title'] as $id_lang => $value) {
			$sql = 'INSERT INTO `'._DB_PREFIX_.'etd_topmenu_lang` (`id_link`, `id_lang`, `title`)
					VALUES('.(int)$id_link.', '.(int)$id_lang.', "'.pSQL($value).'")';
			$db->execute($sql);
		}

		foreach ($link['shops'] as $id_shop) {
			$sql = 'INSERT INTO `'._DB_PREFIX_.'etd_topmenu_shop` (`id_link`, `id_shop`, `published`)
				VALUES('.(int)$id_link.', '.(int)$id_shop.', '.(int) $link['published'].')';
			$db->execute($sql);
		}

		/*if (!self::rebuild($id_link, $lft, $level)) {
			return false;
		}*/

        // On vide le cache.
        self::cleanCache();

		return $id_link;
	}

	static function orderDown($pk) {

		$db = Db::getInstance();

		// Get the node by primary key.
		$node = self::_getNode($pk);

		if (empty($node)) {
			return false;
		}

		// Get the right sibling node.
		$sibling = self::_getNode($node['rgt'] + 1, 'left');

		if (empty($sibling)) {
			return false;
		}

		// Get the primary keys of child nodes.
		$sql = 'select id from '._DB_PREFIX_.'etd_topmenu where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
		$res = $db->executeS($sql);

		$children = array();

		foreach ($res as $child) {
			$children[] = $child['id'];
		}

		if (count($children)) {

			// Shift left and right values for the node and it's children.
			$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft + ' . (int) $sibling['width'] . ', rgt = rgt + ' . (int) $sibling['width'] . '  where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
			$db->execute($sql);

			$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft - ' . (int) $node['width'] . ', rgt = rgt +- ' . (int) $node['width'] . '  where (lft BETWEEN ' . (int) $sibling['lft'] . ' AND ' . (int) $sibling['rgt'] . ') AND id NOT IN (' . implode(',', $children) . ')';
			$db->execute($sql);

		}

        // On vide le cache.
        self::cleanCache();

		return true;
	}

	static function orderUp($pk) {

		$db = Db::getInstance();

		// Get the node by primary key.
		$node = self::_getNode($pk);

		if (empty($node)) {
			return false;
		}

		// Get the left sibling node.
		$sibling = self::_getNode($node['lft'] - 1, 'right');

		if (empty($sibling)) {
			return false;
		}

		// Get the primary keys of child nodes.
		$sql = 'select id from '._DB_PREFIX_.'etd_topmenu where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
		$res = $db->executeS($sql);

		$children = array();

		foreach ($res as $child) {
			$children[] = $child['id'];
		}
		//var_dump($node, $sibling, $children);
		if (count($children)) {
			// Shift left and right values for the node and it's children.
			$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft - ' . (int) $sibling['width'] . ', rgt = rgt - ' . (int) $sibling['width'] . '  where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
			$db->execute($sql);
			//echo htmlspecialchars($sql).'<br>';

			$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft + ' . (int) $node['width'] . ', rgt = rgt + ' . (int) $node['width'] . '  where (lft BETWEEN ' . (int) $sibling['lft'] . ' AND ' . (int) $sibling['rgt'] . ') AND id NOT IN (' . implode(',', $children) . ')';
			$db->execute($sql);
			//echo htmlspecialchars($sql).'<br>';die;
		}

        // On vide le cache.
        self::cleanCache();

		return true;
	}

	static function moveByReference($referenceId, $position = 'after', $pk = null) {

		// Initialise variables.
		$db = Db::getInstance();

		// Get the node by id.
		if (!$node = self::_getNode($pk)) {
			// Error message set in getNode method.
			return false;
		}

		// Get the ids of child nodes.
		$sql = 'select id from '._DB_PREFIX_.'etd_topmenu where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
		$res = $db->executeS($sql);
		$children = array();
		foreach ($res as $child) {
			$children[] = $child['id'];
		}

		// Cannot move the node to be a child of itself.
		if (in_array($referenceId, $children)) {
			return false;
		}

		/*
		 * Move the sub-tree out of the nested sets by negating its left and right values.
		 */
		$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft * (-1), rgt = rgt * (-1) where lft BETWEEN ' . (int) $node['lft'] . ' AND ' . (int) $node['rgt'];
		$db->execute($sql);

		/*
		 * Close the hole in the tree that was opened by removing the sub-tree from the nested sets.
		 */
		// Compress the left values.
		$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft - ' . (int) $node['width'] . ' where lft > ' . (int) $node['rgt'];
		$db->execute($sql);

		// Compress the right values.
		$sql = 'update '._DB_PREFIX_.'etd_topmenu set rgt = rgt - ' . (int) $node['width'] . ' where rgt > ' . (int) $node['rgt'];
		$db->execute($sql);

		// We are moving the tree relative to a reference node.
		if ($referenceId) {

			// Get the reference node by primary key.
			if (!$reference = self::_getNode($referenceId)) {
				return false;
			}

			// Get the reposition data for shifting the tree and re-inserting the node.
			if (!$repositionData = self::_getTreeRepositionData($reference, $node['width'], $position)) {
				return false;
			}
		} else { // We are moving the tree to be the last child of the root node

			// Get the last root node as the reference node.
			$sql = 'select id, parent_id, level, lft, rgt from '._DB_PREFIX_.'etd_topmenu where parent_id = 0 order by lft desc';
			$reference = $db->getRow($sql);

			// Get the reposition data for re-inserting the node after the found root.
			if (!$repositionData = self::_getTreeRepositionData($reference, $node['width'], 'last-child')) {
				return false;
			}
		}

		/*
		 * Create space in the nested sets at the new location for the moved sub-tree.
		 */
		// Shift left values.
		$sql = 'update '._DB_PREFIX_.'etd_topmenu set lft = lft + ' . (int) $node['width'] . ' where ' . $repositionData['left_where'];
		$db->execute($sql);

		// Shift right values.
		$sql = 'update '._DB_PREFIX_.'etd_topmenu set rgt = rgt + ' . (int) $node['width'] . ' where ' . $repositionData['right_where'];
		$db->execute($sql);

		/*
		 * Calculate the offset between where the node used to be in the tree and
		 * where it needs to be in the tree for left ids (also works for right ids).
		 */
		$offset = $repositionData['new_lft'] - $node['lft'];
		$levelOffset = $repositionData['new_level'] - $node['level'];

		// Move the nodes back into position in the tree using the calculated offsets.
		$sql = 'update '._DB_PREFIX_.'etd_topmenu set rgt = ' . (int) $offset . ' - rgt, lft = ' . (int) $offset . ' - lft, level = level + ' . (int) $levelOffset . ' where lft < 0';
		$db->execute($sql);

		// Set the correct parent id for the moved node if required.
		if ($node['parent_id'] != $repositionData['new_parent_id']) {

			$sql = 'update '._DB_PREFIX_.'etd_topmenu set parent_id = ' . (int) $repositionData->new_parent_id . ' where id = ' . $node['id'];
			$db->execute($sql);

		}

		// Set the object values.
		$newnode = array(
			'parent_id' => $repositionData['new_parent_id'],
			'level' => $repositionData['new_level'],
			'lft' => $repositionData['new_lft'],
			'rgt' => $repositionData['new_rgt']
		);

		return $newnode;
	}

	static function rebuild($parentId = null, $leftId = 0, $level = 0) {

		$db = Db::getInstance();

		// If no parent is provided, try to find it.
		if ($parentId === null) {

			// Get the root item.
			$parentId = self::getRootId();
			if ($parentId === false) {
				return false;
			}
		}

		// Build the structure of the recursive query.
		$sql = 'select id from ' . _DB_PREFIX_ . 'etd_topmenu where parent_id = ' . (int) $parentId . ' order by parent_id, lft';

		// Find all children of this node.
		$children = $db->executeS($sql);

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// Execute this function recursively over all children
		foreach ($children as $node) {
			/*
			 * $rightId is the current right value, which is incremented on recursion return.
			 * Increment the level for the children.
			 */
			$rightId = self::rebuild($node['id'], $rightId, $level + 1);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) {
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$sql = 'UPDATE ' . _DB_PREFIX_ . 'etd_topmenu SET level = ' . (int) $level . ', lft = ' . (int) $leftId . ', rgt = ' . (int) $rightId . ' WHERE id = ' . (int) $parentId;
		$db->execute($sql);

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	static function getRootId() {

		$db = Db::getInstance();

		// Test for a unique record with parent_id = 0
		$sql = 'select id from ' . _DB_PREFIX_ . 'etd_topmenu where parent_id = 0';
		$result = $db->executeS($sql);

		if (count($result) == 1) {
			return $result[0];
		}

		// Test for a unique record with lft = 0
		$sql = 'select id from ' . _DB_PREFIX_ . 'etd_topmenu where lft = 0';
		$result = $db->executeS($sql);

		if (count($result) == 1) {
			return $result[0];
		}

		return false;
	}

	static function setLocation($referenceId, $position = 'after') {

		// Make sure the location is valid.
		if (($position != 'before') && ($position != 'after') && ($position != 'first-child') && ($position != 'last-child')) {
			return false;
		}

		// Set the location properties.
		self::$_location = $position;
		self::$_location_id = $referenceId;
	}

	static function _getNode($id, $key = null) {
		$db = Db::getInstance();

		// Determine which key to get the node base on.
		switch ($key) {
			case 'parent':
				$k = 'parent_id';
				break;

			case 'left':
				$k = 'lft';
				break;

			case 'right':
				$k = 'rgt';
				break;

			default:
				$k = 'id';
				break;
		}

		// Get the node data.
		$sql = 'select id, parent_id, level, lft, rgt from ' . _DB_PREFIX_ . 'etd_topmenu where '.$k.' = '. (int) $id;
		$row = $db->getRow($sql);

		// Check for no $row returned
		if (empty($row)) {
			return false;
		}

		// Do some simple calculations.
		$row['numChildren'] = (int) ($row['rgt'] - $row['lft'] - 1) / 2;
		$row['width'] = (int) $row['rgt'] - $row['lft'] + 1;

		return $row;
	}

	protected static function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before') {

		// Make sure the reference an object with a left and right id.
		if (!is_array($referenceNode) || !(isset($referenceNode['lft']) && isset($referenceNode['rgt']))) {
			return false;
		}

		// A valid node cannot have a width less than 2.
		if ($nodeWidth < 2) {
			return false;
		}

		$data = array();

		// Run the calculations and build the data object by reference position.
		switch ($position)
		{
			case 'first-child':
				$data['left_where'] = 'lft > ' . $referenceNode['lft'];
				$data['right_where'] = 'rgt >= ' . $referenceNode['lft'];

				$data['new_lft'] = $referenceNode['lft'] + 1;
				$data['new_rgt'] = $referenceNode['lft'] + $nodeWidth;
				$data['new_parent_id'] = $referenceNode['id'];
				$data['new_level'] = $referenceNode['level'] + 1;
				break;

			case 'last-child':
				$data['left_where'] = 'lft > ' . ($referenceNode['rgt']);
				$data['right_where'] = 'rgt >= ' . ($referenceNode['rgt']);

				$data['new_lft'] = $referenceNode['rgt'];
				$data['new_rgt'] = $referenceNode['rgt'] + $nodeWidth - 1;
				$data['new_parent_id'] = $referenceNode['id'];
				$data['new_level'] = $referenceNode['level'] + 1;
				break;

			case 'before':
				$data['left_where'] = 'lft >= ' . $referenceNode['lft'];
				$data['right_where'] = 'rgt >= ' . $referenceNode['lft'];

				$data['new_lft'] = $referenceNode['lft'];
				$data['new_rgt'] = $referenceNode['lft'] + $nodeWidth - 1;
				$data['new_parent_id'] = $referenceNode['parent_id'];
				$data['new_level'] = $referenceNode['level'];
				break;

			default:
			case 'after':
				$data['left_where'] = 'lft > ' . $referenceNode['rgt'];
				$data['right_where'] = 'rgt > ' . $referenceNode['rgt'];

				$data['new_lft'] = $referenceNode['rgt'] + 1;
				$data['new_rgt'] = $referenceNode['rgt'] + $nodeWidth;
				$data['new_parent_id'] = $referenceNode['parent_id'];
				$data['new_level'] = $referenceNode['level'];
				break;
		}

		return $data;
	}

    protected static function cleanCache($template, $cache_id = null, $compile_id = null) {

        // On vide le cache Prestashop.
        $cache = Cache::getInstance();
        $cache->delete("blocketdtopmenumodel_*");

        // On vide le cache smarty.
        //Tools::clearSmartyCache();

		if (Configuration::get('PS_SMARTY_CLEAR_CACHE') == 'never')
			return 0;

		if ($cache_id === null)
			$cache_id = 'blocketdtopmenu';

		Tools::enableCache();
		$number_of_template_cleared = Tools::clearCache(Context::getContext()->smarty, self::getTemplatePath($template), $cache_id, $compile_id);
		Tools::restoreCacheSettings();

		return $number_of_template_cleared;

    }

	/**
	 * Get realpath of a template of current module (check if template is overriden too)
	 *
	 * @since 1.5.0
	 * @param string $template
	 * @return string
	 */
	protected static function getTemplatePath($template)
	{
		$overloaded = self::_isTemplateOverloadedStatic($template);
		if ($overloaded === null)
			return null;

		if ($overloaded)
			return $overloaded;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.'blocketdtopmenu/views/templates/hook/'.$template))
			return _PS_MODULE_DIR_.'blocketdtopmenu/views/templates/hook/'.$template;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.'blocketdtopmenu/views/templates/front/'.$template))
			return _PS_MODULE_DIR_.'blocketdtopmenu/views/templates/front/'.$template;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.'blocketdtopmenu/'.$template))
			return _PS_MODULE_DIR_.'blocketdtopmenu/'.$template;
		else
			return null;
	}

	protected static function _isTemplateOverloadedStatic($template)
	{
		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blocketdtopmenu/'.$template))
			return _PS_THEME_DIR_.'modules/blocketdtopmenu/'.$template;
		elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blocketdtopmenu/views/templates/hook/'.$template))
			return _PS_THEME_DIR_.'modules/blocketdtopmenu/views/templates/hook/'.$template;
		elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blocketdtopmenu/views/templates/front/'.$template))
			return _PS_THEME_DIR_.'modules/blocketdtopmenu/views/templates/front/'.$template;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.'blocketdtopmenu/views/templates/hook/'.$template))
			return false;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.'blocketdtopmenu/views/templates/front/'.$template))
			return false;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.'blocketdtopmenu/'.$template))
			return false;
		return null;
	}

}