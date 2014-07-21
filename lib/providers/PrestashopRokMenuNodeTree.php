<?php
/**
 * @version   $Id: RokMenuNodeTree.php 4585 2012-10-27 01:44:54Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!class_exists('PrestashopRokMenuNodeTree')) {
    /**
     * Rok Nav Menu Tree Class.
     */
    class PrestashopRokMenuNodeTree extends RokMenuNodeTree {

		public function findPrestashopNode($type, $params) {
			if (is_string($params)) {
				$params = json_decode($params);
			}
			$iterator = $this->getIterator();
			$childrenIterator = new RecursiveIteratorIterator(new RokMenuPrestashopFilter($iterator, $type, $params), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($childrenIterator as $child) {

				if ($child->getType() !== $type)
					continue;

				$childParams = $child->getParams();
				if (is_string($childParams))
					$childParams = json_decode($childParams);
				$result = true;
				foreach(get_object_vars($params) as $k => $v) {

					if (!property_exists($childParams, $k)) {
						$result = false;
						break;
					}

					if ($childParams->$k !== $v) {
						$result = false;
						break;
					}

				}

				if ($result) {
					$childref = &$child;
					return $childref;
				}
			}
			return false;
		}

    }
}
