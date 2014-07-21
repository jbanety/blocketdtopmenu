<?php
/**
 * @version   $Id: RokMenuProviderJoomla16.php 9104 2013-04-04 02:26:54Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
require_once(dirname(__FILE__) . '/JoomlaRokMenuNode.php');

if (!class_exists('RokMenuProviderPrestashop')) {


	class RokMenuProviderPrestashop extends AbstractRokMenuProvider
	{

		const ROOT_ID = 1;

		/**
		 * @var array
		 */
		protected $current_node = null;

		protected function getMenuItems()
		{
			//Cache this basd on access level
			/*$conf = JFactory::getConfig();
			if ($conf->get('caching') && $this->args["cache"]) {
				$user  = JFactory::getUser();
				$cache = JFactory::getCache('mod_roknavmenu');
				$cache->setCaching(true);
				$args      = array($this->args);
				$checksum  = md5(implode(',', $this->args));
				$menuitems = $cache->get(array(
				                              $this, 'getFullMenuItems'
				                         ), $args, 'mod_roknavmenu-' . $user->get('aid', 0) . '-' . $checksum);
			} else {*/
				$menuitems = $this->getFullMenuItems($this->args);
			//}

			/* Set the active to the current run since its not saved with the cache */
			$this->setCurrentNode();

			return $menuitems;
		}

		public function getFullMenuItems($args) {

			$context = Context::getContext();

			// Get Menu Items
			$links = BlockEtdTopMenuModel::getLinks(true, true);

			$outputNodes = array();

			if (is_array($links) && count($links) > 0) {

				// Array to Object
				$rows = array();
				foreach($links as $link) {
					$row = new stdClass();
					foreach($link as $k => $v) {
						$row->$k = $v;
					}
					$rows[] = $row;
				}
				unset($links);

				foreach ($rows as $item) {
					//Create the new Node
					$node = new PrestashopRokMenuNode();

					$node->setId($item->id);
					$node->setParent($item->parent_id);
					$node->setTitle(addslashes(htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8')));
					$node->setParams($item->params);
					$node->setColumns($item->columns);
					$node->setDistribution($item->distribution);
					$node->setManualDistribution($item->manual_distribution);
					$node->setWidth($item->width);
					$node->setColumnWidths($item->column_widths);
					$node->setChildrenGroup($item->children_group);
					$node->setChildrenType($item->children_type);
					$node->setModules($item->modules);
					$node->setModuleHooks($item->module_hooks);

					// Get the params.
					$iParams = (is_object($item->params)) ? $item->params : json_decode($item->params);

					switch ($item->type) {
						case 'separator':
							$node->setType('separator');
						break;
						case 'page':
							$node->setType('page');
							$meta = new MetaCore($iParams->id_meta, $context->language->id);
							$node->setLink($context->link->getPageLink($meta->page, false, $context->language->id));
						break;
						case 'pcategory':
							$node->setType('pcategory');
							$cat = new Category($iParams->id_category, $context->language->id);
							$node->setLink($context->link->getCategoryLink($cat->id_category, $cat->link_rewrite));
						break;
						case 'product':
							$node->setType('product');
						break;
						case 'cms':
							$node->setType('cms');
							$cms = new CMS($iParams->id_cms, $context->language->id);
							$node->setLink($context->link->getCMSLink($cms));
						break;
						case 'ccategory':
							$node->setType('ccategory');
							$cat = new CMSCategory($iParams->id_cms_category, $context->language->id);
							$node->setLink($context->link->getCMSCategoryLink($cat));
						break;
						case 'supplier':
							$node->setType('supplier');
						break;
						case 'manufacturer':
							$node->setType('manufacturer');
						break;
						case 'module':
							$node->setType('module');
						break;
						default :
						break;
					}

					if ($node->getLink() != null) {
						// set the target based on menu item options
						switch ($item->browserNav) {
							case 1:
								$node->setTarget('_blank');
								break;
							case 2:
								//$node->setLink(str_replace('index.php', 'index2.php', $node->getLink()));
								//$node->setTarget('newnotool');
								$value = addslashes(htmlspecialchars("window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;", ENT_QUOTES, 'UTF-8'));
								$node->addLinkAttrib('onclick', $value);
								break;
							default:
								//$node->setTarget('current');
								break;
						}

					}

					$node->addListItemClass("item" . $node->getId());
					$node->setAccess($item->access);
					$node->addSpanClass($node->getType());

                    // Add node to output list
                    $outputNodes[$node->getId()] = $node;
				}
			}
			return $outputNodes;
		}

		/**
		 * @param  $nodeList
		 *
		 * @return void
		 */
		protected function populateActiveBranch($nodeList) {

		}


		/**
		 * @return PrestashopRokMenuNodeTree
		 */
		public function getRealMenuTree() {
			$menuitems = $this->getFullMenuItems($this->args);
			$this->setCurrentNode();
			return $this->createPrestashopMenuTree($menuitems, $this->args['maxdepth']);
		}

		/**
		 * Takes the menu item nodes and puts them into a tree structure
		 *
		 * @param  $nodes
		 * @param  $maxdepth
		 *
		 * @return bool|PrestashopRokMenuNodeTree
		 */
		protected function createPrestashopMenuTree(&$nodes, $maxdepth)
		{
			$menu = new PrestashopRokMenuNodeTree(self::ROOT_ID);
			// TODO: move maxdepth to higher processing level?
			if (!empty($nodes)) {
				// Build Menu Tree root down (orphan proof - child might have lower id than parent)
				$ids        = array();
				$ids[0]     = true;
				$unresolved = array();

				// pop the first item until the array is empty if there is any item
				if (is_array($nodes)) {
					while (count($nodes) && !is_null($node = array_shift($nodes))) {
						if (!$menu->addNode($node)) {
							if (!array_key_exists($node->getId(), $unresolved) || $unresolved[$node->getId()] < $maxdepth) {
								array_push($nodes, $node);
								if (!isset($unresolved[$node->getId()])) $unresolved[$node->getId()] = 1; else $unresolved[$node->getId()]++;
							}
						}
					}
				}
			}
			return $menu;
		}

		public function getMenuTree() {
			if (null == $this->menu) {
				//Cache this basd on access level
				/*$conf = JFactory::getConfig();
				if ($conf->get('caching',0) && isset($this->args["module_cache"]) && $this->args["module_cache"]) {
					$user  = JFactory::getUser();
					$cache = JFactory::getCache('mod_roknavmenu');
					$cache->setCaching(true);
					$args       = array($this->args);
					$checksum   = md5(implode(',', $this->args));
					$this->menu = $cache->get(array(
					                               $this, 'getRealMenuTree'
					                          ), $args, 'mod_roknavmenu-' . $user->get('aid', 0) . '-' . $checksum);
				} else {*/
					$this->menu = $this->getRealMenuTree();
				//}


				$this->setCurrentNode();
				$this->active_branch = $this->findActiveBranch($this->menu, $this->current_node);
			}
			return $this->menu;
		}

		/**
		 * Gets the current active based on the current_node
		 *
		 * @param PrestashopRokMenuNodeTree $menu
		 * @param                           $current_node
		 *
		 * @return array
		 */
		protected function findActiveBranch(PrestashopRokMenuNodeTree $menu, $current_node) {
			$active_branch = array();
			/** @var $current PrestashopRokMenuNode */
			$current = $menu->findPrestashopNode($current_node->type, $current_node->params);
			if ($current) {
				do {
					$active_branch[$current->getId()] = $current;
					if ($current->getParent() == self::ROOT_ID) break;
				} while ($current = $current->getParentRef());
				$active_branch = array_reverse($active_branch, true);
			}
			return $active_branch;
		}

		protected function setCurrentNode() {

			if ($this->current_node === null) {

				// Default empty node
				$params = new stdClass();
				$type = '';

				// Get current page name.
				$page_name = Dispatcher::getInstance()->getController();

				// Get context
				$id_lang = Context::getContext()->language->id;

				switch ($page_name) {
					case 'product':
						$type = 'product';
						$params->id_product = Tools::getValue('id_product');
					break;
					case 'cms':
						if (Tools::getValue('id_cms') !== false) { // CMS Page
							$type = 'cms';
							$params->id_cms = Tools::getValue('id_cms');
						} elseif (Tools::getValue('id_cms_category') !== false) { // CMS Category
							$type = 'ccategory';
							$params->id_cms_category = Tools::getValue('id_cms_category');
						}
					break;
					case 'manufacturer':
						$type = 'manufacturer';
						$params->id_manufacturer = Tools::getValue('id_manufacturer');
					break;
					case 'supplier':
						$type = 'supplier';
						$params->id_supplier = Tools::getValue('supplier');
					break;
					case 'category':
						$type = 'pcategory';
						$params->id_category = Tools::getValue('id_category');
					break;
					default: // Page
						$type = 'page';
						$meta = Meta::getMetaByPage($page_name, $id_lang);
						$params->id_meta = $meta['id_meta'];
					break;
				}

				$current_node = new stdClass();
				$current_node->type = $type;
				$current_node->params = $params;
				$this->current_node = $current_node;
			}

		}

	}

}