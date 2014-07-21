<?php
/**
 * @package     blocketdtopmenu
 *
 * @version     1.0
 * @copyright   Copyright (C) 2014 Jean-Baptiste Alleaume. Tous droits réservés.
 * @license     http://alleau.me/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

/**
 *
 */
class EtdPrestashopFormatter extends AbstractPrestashopRokMenuFormatter {
	function format_subnode(&$node)     {

		$child_type = $node->getChildrenType();
		if ($child_type == 'modules' || $child_type == 'modulehooks')
			$node->addListItemClass('parent');

		$current = false;

		if ($node->getType() == $this->current_node->type) {

			$nodeParams = $node->getParams();
			if (is_string($nodeParams))
				$nodeParams = json_decode($nodeParams);
			$current = true;

			foreach(get_object_vars($this->current_node->params) as $k => $v) {

				if (!property_exists($nodeParams, $k)) {
					$current = false;
					break;
				}

				if ($nodeParams->$k !== $v) {
					$current = false;
					break;
				}

			}
		}

		if ($current) {
			$node->addListItemClass('current');
		}

		if (array_key_exists($node->getId(), $this->active_branch)) {
			$node->addListItemClass('active');
		}

	}
}
