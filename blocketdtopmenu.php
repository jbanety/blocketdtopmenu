<?php
/**
 * @package     blocketdtopmenu
 *
 * @version     2.2.0
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

include_once(dirname(__FILE__) . '/BlockEtdTopMenuModel.php');

class BlockEtdTopMenu extends Module {

	public function __construct() {

		$this->name = 'blocketdtopmenu';
		$this->tab = 'front_office_features';
		$this->version = '2.2.0';
		$this->author = 'ETD Solutions';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('ETD Top Menu');
		$this->description = $this->l('Add an enhanced horizontal menu to the top of your e-commerce website.');
		$this->secure_key = Tools::encrypt($this->name);
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

	}

	public function install() {

		return
			parent::install() &&
			$this->registerHook('displayTop') &&
			$this->installDB();

	}

	protected function installDB() {
		$db = Db::getInstance();
		$sql = file_get_contents(dirname(__FILE__).'/sql/install.sql');
		if ($sql === false)
			return false;
		$sql = str_replace('#__', _DB_PREFIX_, $sql);
		return $db->execute($sql);
	}

	public function uninstall() {
		return
			parent::uninstall() &&
			$this->uninstallDB();
	}

	protected function uninstallDB() {
		$db = Db::getInstance();
		$sql = file_get_contents(dirname(__FILE__).'/sql/uninstall.sql');
		if ($sql === false)
			return false;
		$sql = str_replace('#__', _DB_PREFIX_, $sql);
		return $db->execute($sql);
	}

	public function initToolbar() {

		$current_index = AdminController::$currentIndex;
		$token = Tools::getAdminTokenLite('AdminModules');

		$back = Tools::safeOutput(Tools::getValue('back', ''));

		if (!isset($back) || empty($back))
			$back = $current_index.'&amp;configure='.$this->name.'&token='.$token;

		switch ($this->_display) {
			case 'add':
				$this->toolbar_btn['cancel'] = array(
					'href' => $back,
					'desc' => $this->l('Cancel')
				);
				break;
			case 'edit':
				$this->toolbar_btn['cancel'] = array(
					'href' => $back,
					'desc' => $this->l('Cancel')
				);
				break;
			case 'index':
				$this->toolbar_btn['new'] = array(
					'href' => $current_index.'&amp;configure='.$this->name.'&amp;token='.$token.'&amp;addLink',
					'desc' => $this->l('Add new')
				);
				$this->toolbar_btn['rebuild'] = array(
					'href' => $current_index.'&amp;configure='.$this->name.'&amp;token='.$token.'&amp;rebuildMenu',
					'desc' => $this->l('Rebuild menu'),
					'class' => 'icon-bolt'
				);
                $this->toolbar_btn['refresh-cache'] = array(
                    'href' => $current_index.'&amp;configure='.$this->name.'&amp;token='.$token.'&amp;clearCache',
                    'desc' => $this->l('Clear cache'),
                    'class' => 'icon-eraser'
                );
			break;
			default:
				break;
		}

		return $this->toolbar_btn;
	}

	protected function displayForm() {

		$this->context->controller->addJqueryPlugin('tablednd');
		$this->context->controller->addJS(_PS_JS_DIR_.'admin-dnd.js');

		$current_index = AdminController::$currentIndex;
		$token = Tools::getAdminTokenLite('AdminModules');

		$this->_display = 'index';

		$links = BlockEtdTopMenuModel::getLinks();

		$ordering = array();
		foreach ($links as $item) {
			$ordering[$item['parent_id']][] = $item['id'];
		}

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Menu links'),
				'icon' => 'icon-list-alt'
			),
			'input' => array(
				array(
					'type' => 'links',
					'label' => $this->l('Links:'),
					'name' => 'links',
					'values' => $links,
					'desc' => $this->l(''),
					'ordering' => $ordering
				)
			),
			'buttons' => array(
				'newLink' => array(
					'title' => $this->l('Add new'),
					'href' => $current_index.'&amp;configure='.$this->name.'&amp;token='.$token.'&amp;addLink',
					'class' => 'pull-right',
					'icon' => 'process-icon-new'
				)
			)
		);

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Tag Class'),
                    'name' => 'ETDTOPMENU_TAGCLASS',
                    'desc' => $this->l('')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Tag ID'),
                    'name' => 'ETDTOPMENU_TAGID',
                    'desc' => $this->l('')
                )
            ),
            'submit' => array(
                'name' => 'submitSettings',
                'title' => $this->l('Save'),
            )
        );

		$helper = $this->initForm();
		$helper->submit_action = '';
		$helper->title = $this->l('Top Menu Links');
        $helper->fields_value = $this->getConfigValues();

		if (isset($this->fields_value))
			$helper->fields_value = $this->fields_value;
		$this->_html .= $helper->generateForm($this->fields_form);

		return;
	}

	protected function displayAddForm() {

		$this->_display = 'add';
		$link = null;
		$id_link = 0;

		if (Tools::isSubmit('editLink') && Tools::getValue('id_link')) {
			$this->_display = 'edit';
			$id_link = (int)Tools::getValue('id_link');
			$link = BlockEtdTopMenuModel::getLink($id_link);
		}

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Details'),
				'icon' => isset($custom) ? 'icon-edit' : 'icon-plus-square'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_link'
				),
				array(
					'type' => 'hidden',
					'name' => 'old_parent_id'
				),
				array(
					'type' => 'select',
					'label' => $this->l('Type:'),
					'name' => 'type',
					'options' => array(
						'id' => 'id',
						'name' => 'name',
						'query' => array(
							array(
								'id' => '',
								'name' => '--'
							),
							array(
								'id' => 'separator',
								'name' => $this->l('Separator')
							),
							array(
								'id' => 'heading',
								'name' => $this->l('Heading')
							),
							array(
								'id' => 'page',
								'name' => $this->l('Page')
							),
							array(
								'id' => 'pcategory',
								'name' => $this->l('Product Category')
							),
							array(
								'id' => 'product',
								'name' => $this->l('Product')
							),
							array(
								'id' => 'cms',
								'name' => $this->l('CMS Page')
							),
							array(
								'id' => 'ccategory',
								'name' => $this->l('CMS Category')
							),
							array(
								'id' => 'supplier',
								'name' => $this->l('Supplier')
							),
							array(
								'id' => 'manufacturer',
								'name' => $this->l('Manufacturer')
							),
							array(
								'id' => 'hook',
								'name' => $this->l('Hook')
							),
                            array(
                                'id' => 'url',
                                'name' => $this->l('URL')
                            )
						)
					),
					'required' => true,
					'desc' => $this->l('')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Title:'),
					'name' => 'title',
					'lang' => true,
					'desc' => $this->l(''),
					'size' => 40,
					'maxlength' => 255,
					'required' => true
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Published:'),
					'name' => 'published',
					'desc' => $this->l(''),
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'published_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'published_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Access:'),
					'name' => 'access',
					'default_value' => 0,
					'options' => array(
						'id' => 'id',
						'name' => 'name',
						'query' => array(
							array(
								'id' => 0,
								'name' => $this->l('Public')
							),
							array(
								'id' => 1,
								'name' => $this->l('Guests')
							),
							array(
								'id' => 2,
								'name' => $this->l('Customers')
							)
						)
					),
					'desc' => $this->l('')
				),
				array(
					'type' => 'select_parent',
					'label' => $this->l('Parent link:'),
					'name' => 'parent_id',
					'values' => BlockEtdTopMenuModel::getLinks(),
					'required' => true,
					'current' => $id_link,
					'desc' => $this->l('')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Target:'),
					'name' => 'browserNav',
					'default_value' => 0,
					'options' => array(
						'id' => 'id',
						'name' => 'name',
						'query' => array(
							array(
								'id' => 0,
								'name' => $this->l('Parent')
							),
							array(
								'id' => 1,
								'name' => $this->l('New window with nav bar')
							),
							array(
								'id' => 2,
								'name' => $this->l('New window without nav bar')
							)
						)
					),
					'desc' => $this->l('')
				),
                array(
                    'type' => 'text',
                    'label' => $this->l('Menu image:'),
                    'name' => 'image',
                    'desc' => $this->l(''),
                    'maxlength' => 255
                ),
				array(
					'type' => 'text',
					'label' => $this->l('CSS Class:'),
					'name' => 'css',
					'desc' => $this->l('A Custom CSS class that can be assigned to this item'),
					'size' => 40,
					'maxlength' => 255
				),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Group Child Items:'),
                    'name' => 'params[group_child_items]',
                    'desc' => $this->l('Select this to group children under menu item rather than treating as a submenu'),
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'group_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'group_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show title:'),
                    'name' => 'params[show_title]',
                    'desc' => $this->l('Select this to show title on top of the grouped children list'),
                    'is_bool' => true,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'show_title_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'show_title_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Columns of Child Items:'),
                    'name' => 'params[columns]',
                    'desc' => $this->l('How many columns should display for children of this menu item'),
                    'options' => array(
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => '--'
                        ),
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => 1
                            ),
                            array(
                                'id' => 2,
                                'name' => 2
                            ),
                            array(
                                'id' => 3,
                                'name' => 3
                            ),
                            array(
                                'id' => 4,
                                'name' => 4
                            ),
                            array(
                                'id' => 5,
                                'name' => 5
                            ),
                            array(
                                'id' => 6,
                                'name' => 6
                            ),
                            array(
                                'id' => 7,
                                'name' => 7
                            ),
                            array(
                                'id' => 8,
                                'name' => 8
                            ),
                            array(
                                'id' => 9,
                                'name' => 9
                            ),
                            array(
                                'id' => 10,
                                'name' => 10
                            ),
                            array(
                                'id' => 11,
                                'name' => 11
                            ),
                            array(
                                'id' => 12,
                                'name' => 12
                            )
                        )
                    )
                ),
                array(
                    'type' => 'columns_widths',
                    'label' => $this->l('Columns CSS Classes:'),
                    'name' => 'params[columns_widths]',
                    'desc' => $this->l('CSS Classes to apply to columns to size them.')
                )
			),
			'submit' => array(
				'name' => 'submitLink',
				'title' => $this->l('Save'),
			)
		);

		$this->context->controller->getLanguages();

		$this->fields_value['id_link'] = 0;
		$this->fields_value['old_parent_id'] = 0;

		if (Tools::getValue('type'))
			$this->fields_value['type'] = Tools::getValue('type');
		else if (isset($link))
			$this->fields_value['type'] = $link['type'];
		else
			$this->fields_value['type'] = '';

		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language) {
			if (Tools::getValue('title_'.$language['id_lang']))
				$this->fields_value['title'][$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
			else if (isset($link) && isset($link['title'][$language['id_lang']]))
				$this->fields_value['title'][$language['id_lang']] = $link['title'][$language['id_lang']];
			else
				$this->fields_value['title'][$language['id_lang']] = '';
		}

		if (Tools::getValue('published'))
			$this->fields_value['published'] = Tools::getValue('published');
		else if (isset($link))
			$this->fields_value['published'] = $link['published'];
		else
			$this->fields_value['published'] = 0;

		if (Tools::getValue('access'))
			$this->fields_value['access'] = Tools::getValue('access');
		else if (isset($link))
			$this->fields_value['access'] = $link['access'];
		else
			$this->fields_value['access'] = 0;

		if (Tools::getValue('parent_id'))
			$this->fields_value['parent_id'] = Tools::getValue('parent_id');
		else if (isset($link))
			$this->fields_value['parent_id'] = $link['parent_id'];
		else
			$this->fields_value['parent_id'] = 1;

		if (Tools::getValue('browserNav'))
			$this->fields_value['browserNav'] = Tools::getValue('browserNav');
		else if (isset($link))
			$this->fields_value['browserNav'] = $link['browserNav'];
		else
			$this->fields_value['browserNav'] = 0;

		if (Tools::getValue('css'))
			$this->fields_value['css'] = Tools::getValue('css');
		else if (isset($link))
			$this->fields_value['css'] = $link['css'];
		else
			$this->fields_value['css'] = '';

        if (Tools::getValue('image'))
            $this->fields_value['image'] = Tools::getValue('image');
        else if (isset($link))
            $this->fields_value['image'] = $link['image'];
        else
            $this->fields_value['image'] = '';

        if (Tools::getValue('params')) {
            $params = Tools::getValue('params');
            foreach ($params as $k => $v) {
                $this->fields_value['params[' . $k . ']'] = $v;
            }
        } else if (isset($link) && isset($link['params'])) {
            foreach (get_object_vars($link['params']) as $k => $v) {
                $this->fields_value['params[' . $k . ']'] = $v;
            }
        } else {
            $this->fields_value['params[item_column]'] = '';
            $this->fields_value['params[group_child_items]'] = '0';
            $this->fields_value['params[columns]'] = '';
            $this->fields_value['params[columns_widths]'] = '';
            $this->fields_value['params[show_title]'] = '0';
        }


		$helper = $this->initForm();
		$helper->submit_action = '';
		$helper->title = ($this->_display == 'add') ? $this->l('Add new link') : $this->l('Edit link');

		if (isset($id_link)) {
			$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_link='.$id_link;
			$helper->submit_action = 'editLink';
			$this->fields_value['id_link'] = $id_link;
			$this->fields_value['old_parent_id'] = $link['parent_id'];
		}
		else
			$helper->submit_action = 'addLink';

		$helper->fields_value = $this->fields_value;
		$this->_html .= $helper->generateForm($this->fields_form);

		return;
	}

	private function initForm() {

		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = 'blocketdtopmenu';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $this->context->controller->_languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $this->context->controller->default_form_language;
		$helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();

		return $helper;
	}

	protected function _postValidation() {

		$this->_errors = array();

		if (Tools::isSubmit('submitLink')) {

			$type = Tools::getValue('type');
			if (!in_array($type, array('separator','heading','page','pcategory','product','cms','ccategory','supplier','manufacturer','module','url','hook'))) {
				$this->_errors[] = $this->l('Please choose a valid type.');
			}

			$languages = LanguageCore::getLanguages(true);
			foreach ($languages as $language) {

				if (!Tools::getValue('title_'.$language['id_lang']))
					$this->_errors[] = $this->l('You must type a title.');

				if (strlen(Tools::getValue('title_'.$language['id_lang'])) > 255)
					$this->_errors[] = $this->l('The link title is too long.');
			}

			$params = Tools::getValue('params');
			switch ($type) {
				case 'page':
					if (!$params || !array_key_exists('id_meta', $params) || empty($params['id_meta']))
						$this->_errors[] = $this->l('You must choose a page.');
				break;
                case 'product':
                    if (!$params || !array_key_exists('id_product', $params) || empty($params['id_product']))
                        $this->_errors[] = $this->l('You must choose a product.');
                break;
                case 'supplier':
                    if (!$params || !array_key_exists('id_supplier', $params) || empty($params['id_supplier']))
                        $this->_errors[] = $this->l('You must choose a supplier.');
                break;
                case 'manufacturer':
                    if (!$params || !array_key_exists('id_manufacturer', $params) || empty($params['id_manufacturer']))
                        $this->_errors[] = $this->l('You must choose a manufacturer.');
                break;
                case 'hook':
                    if (!$params || !array_key_exists('hook', $params) || empty($params['hook']))
                        $this->_errors[] = $this->l('You must type a hook name.');
                break;
                case 'url':
                    if (!$params || !array_key_exists('url', $params) || empty($params['url']))
                        $this->_errors[] = $this->l('You must type an url.');
                break;
				case 'pcategory':
					if (!$params || !array_key_exists('id_category', $params) || empty($params['id_category']))
						$this->_errors[] = $this->l('You must choose a category.');
					break;
				case 'ccategory':
					if (!$params || !array_key_exists('id_cms_category', $params) || empty($params['id_cms_category']))
						$this->_errors[] = $this->l('You must choose a category.');
					break;
			}

		}

		if (count($this->_errors)) {
			foreach ($this->_errors as $err)
				$this->_html .= '<div class="alert alert-danger">'.$err.'</div>';

			return false;
		}
		return true;
	}

	private function _postProcess() {

		if ($this->_postValidation() == false)
			return false;

		$this->_errors = array();
		if (Tools::isSubmit('submitLink')) {

			$title = array();
			foreach ($this->context->controller->getLanguages() as $lang) {
				$title[$lang['id_lang']] = Tools::getValue('title_'.$lang['id_lang']);
			}

			$link = array();
			foreach ($_POST as $k => $v) {
				$link[$k] = $v;
			}

			$link['title'] = $title;
			$link['shops'] = Shop::getContextListShopID();
			$link['params'] = json_encode($link['params']);

			if (Tools::isSubmit('addLink')) {

				BlockEtdTopMenuModel::storeLink($link);
				$redirect = 'addLinkConfirmation';

			} else if (Tools::isSubmit('editLink')) {

				$link['id'] = (int) Tools::getValue('id_link');
				BlockEtdTopMenuModel::storeLink($link, true);
				$redirect = 'editLinkConfirmation';

			}

			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&'.$redirect);

		} elseif (Tools::isSubmit('deleteLink') && Tools::getValue('id_link')) {
			$id_link = (int) Tools::getvalue('id_link');

			if ($id_link) {
				BlockEtdTopMenuModel::deleteLink($id_link);
				Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteLinkConfirmation');
			} else
				$this->_html .= $this->displayError($this->l('You are trying to delete a non-existing link. '));

		} elseif (Tools::isSubmit('orderUp') && Tools::getValue('id_link')) {
			$id_link = (int) Tools::getvalue('id_link');

			if ($id_link) {
				if (!BlockEtdTopMenuModel::orderUp($id_link)) {
					$this->_html .= $this->displayError($this->l('An error occured when trying order a link. '));
				}
			} else
				$this->_html .= $this->displayError($this->l('You are trying to order a non-existing link. '));

		} elseif (Tools::isSubmit('orderDown') && Tools::getValue('id_link')) {
			$id_link = (int) Tools::getvalue('id_link');

			if ($id_link) {
				if (!BlockEtdTopMenuModel::orderDown($id_link)) {
					$this->_html .= $this->displayError($this->l('An error occured when trying order a link. '));
				}
			} else
				$this->_html .= $this->displayError($this->l('You are trying to order a non-existing link. '));

		} elseif (Tools::isSubmit('rebuildMenu')) {

			$redirect = 'rebuildMenuConfirmation';

			if (!BlockEtdTopMenuModel::rebuild()) {
				$redirect = '';
				$this->_html .= $this->displayError($this->l('An error occured when rebuilding menu. '));
			}

			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&'.$redirect);

		} elseif (Tools::isSubmit('clearCache')) {

            BlockEtdTopMenuModel::cleanCache();
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&clearCacheConfirmation');

        } elseif (Tools::isSubmit('submitSettings'))  {

            Configuration::updateGlobalValue('ETDTOPMENU_TAGCLASS', Tools::getValue('ETDTOPMENU_TAGCLASS'));
            Configuration::updateGlobalValue('ETDTOPMENU_TAGID', Tools::getValue('ETDTOPMENU_TAGID'));

            $this->_html .= $this->displayConfirmation($this->l('Settings updated.'));

        } elseif (Tools::isSubmit('addLinkConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Menu link added.'));
		 elseif (Tools::isSubmit('clearCacheConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Cache cleared.'));
		elseif (Tools::isSubmit('editLinkConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Menu link edited.'));
		elseif (Tools::isSubmit('rebuildMenuConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Menu rebuilt.'));

	}

	public function getContent() {

		$this->_html = '';
		$this->_postProcess();

		if (Tools::isSubmit('addLink') || Tools::isSubmit('editLink'))
			$this->displayAddForm();
		else
			$this->displayForm();

		$this->_html .= '<script src="'._MODULE_DIR_.'blocketdtopmenu/js/admin.js"></script>';
		$this->_html .= "<script>
			jQuery(document).ready(function() {
				blockEtdTopMenuAdmin.init({
					'baseURI': '"._MODULE_DIR_."blocketdtopmenu/',
					'token': '".sha1('blocketdtopmenu_admin' . _COOKIE_KEY_)."'
				});
			});
		</script>";

		return $this->_html;
	}

	public function loadType($type, $id_link=null) {

		$result = array(
			'hasError' => true,
			'msg' => $this->l('Invalid type.'),
			'html' => ''
		);

		$context = Context::getContext();
		$link = false;

		if (!empty($id_link) && Validate::isInt($id_link)) {
			$link = BlockEtdTopMenuModel::getLink($id_link);
		}

		$html = '<div class="form-group">';

		$label = '';
		$input = '';

		switch($type) {

			case 'separator':
			case 'heading':
				return array(
					'hasError' => false,
					'msg' => '',
					'html' => ''
				);
				break;

            case 'product':
                $label = $this->l('Product:');
                $input = '<select name="params[id_product]" id="id_product"><option value="">--</option>';

                $products = Product::getProducts($context->language->id, false, false, 'name', 'ASC');

                foreach ($products as $product) {
                    $selected = false;
                    if ($link && $link['type'] == 'product' && $link['params']->id_product == $product['id_product']) {
                        $selected = true;
                    }
                    $input .= '<option value="'.$product['id_product'].'"' . ( $selected ? 'selected="selected"' : '') . '>'.$product['name'].'</option>';
                }
                $input .= '</select>';
                break;

            case 'supplier':
                $label = $this->l('Supplier:');
                $input = '<select name="params[id_supplier]" id="id_supplier"><option value="">--</option>';

                $suppliers = Supplier::getSuppliers();

                foreach ($suppliers as $supplier) {
                    $selected = false;
                    if ($link && $link['type'] == 'supplier' && $link['params']->id_supplier == $supplier['id_supplier']) {
                        $selected = true;
                    }
                    $input .= '<option value="'.$supplier['id_supplier'].'"' . ( $selected ? 'selected="selected"' : '') . '>'.$supplier['name'].'</option>';
                }
                $input .= '</select>';
                break;

            case 'manufacturer':
                $label = $this->l('Manufacturer:');
                $input = '<select name="params[id_manufacturer]" id="id_manufacturer"><option value="">--</option>';

                $manufacturers = Manufacturer::getManufacturers();

                foreach ($manufacturers as $manufacturer) {
                    $selected = false;
                    if ($link && $link['type'] == 'manufacturer' && $link['params']->id_manufacturer == $manufacturer['id_manufacturer']) {
                        $selected = true;
                    }
                    $input .= '<option value="'.$manufacturer['id_manufacturer'].'"' . ( $selected ? 'selected="selected"' : '') . '>'.$manufacturer['name'].'</option>';
                }
                $input .= '</select>';
                break;

            case 'url':
                $label = $this->l('URL:');
                $input = '<input type="text" name="params[url]" id="url" value="' . (isset($link['params']->url) ? $link['params']->url : '') . '">';

                break;

            case 'hook':
                $label = $this->l('Hook:');
                $input = '<input type="text" name="params[hook]" id="hook" value="' . (isset($link['params']->hook) ? $link['params']->hook : '') . '">';

                break;

            case 'page':
				$label = $this->l('Page:');
				$input = '<select name="params[id_meta]" id="id_meta"><option value="">--</option>';
				$metas = Meta::getMetas();
				foreach ($metas as $meta) {
					$selected = false;
					if ($link && $link['type'] == 'page' && $link['params']->id_meta == $meta['id_meta']) {
						$selected = true;
					}
					$input .= '<option value="'.$meta['id_meta'].'"' . ( $selected ? 'selected="selected"' : '') . '>'.$meta['page'].'</option>';
				}
				$input .= '</select>';
			break;

			case 'pcategory':
				$label = $this->l('Category:');
				$input = '<select name="params[id_category]" id="id_category"><option value="">--</option>';

				$cats = Category::getCategories(false, false, false, 'AND level_depth > 0', 'ORDER BY c.nleft ASC');

				foreach ($cats as $cat) {
					$selected = false;
					if ($link && $link['type'] == 'pcategory' && $link['params']->id_category == $cat['id_category']) {
						$selected = true;
					}
					$input .= '<option value="'.$cat['id_category'].'"' . ( $selected ? 'selected="selected"' : '') . '>'. str_repeat('-&nbsp;', $cat['level_depth']-1) . $cat['name'].'</option>';
				}

				$input .= '</select>';
			break;

			case 'ccategory':
				$label = $this->l('Category:');
				$input = '<select name="params[id_cms_category]" id="id_cms_category"><option value="">--</option>';

				$id_cms_category = isset($link['params']->id_cms_category) ? $link['params']->id_cms_category : 0;
				$categories = CMSCategory::getCategories($context->language->id, false);
				$html_categories = CMSCategoryCore::recurseCMSCategory($categories, $categories[0][1], 1, $id_cms_category, 1);
				$input .= str_replace('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '-&nbsp;', $html_categories);

				$input .= '</select>';
				break;

			case 'cms':

				$label = $this->l('Page:');
				$input = '<select name="params[id_cms]" id="id_cms"><option value="">--</option>';

				$pages = CMS::getCMSPages();
				$ordered = array();

				if (count($pages)) {
					foreach($pages as $page) {
						$ordered[$page['id_cms_category']][$page['position']] = $page;
					}
				}

				foreach($ordered as $id_cms_category => $pages) {
					$cat = new CMSCategory($id_cms_category, $this->context->language->id);
					$input .= '<optgroup label="' . $cat->name . '">';
					foreach($pages as $page) {
                        $selected = false;
                        if ($link && $link['type'] == 'cms' && $link['params']->id_cms == $page['id_cms']) {
                            $selected = true;
                        }
						$cms = new CMSCore($page['id_cms'], $this->context->language->id);
						$input .= '<option value="' . $cms->id . '"' . ( $selected ? ' selected="selected"' : '') . '>' . $cms->meta_title . '</option>';
					}
					$input .= '</optgroup>';
				}

				$input .= '</select>';
				break;

			default:
				return $result;
			break;
		}

		if (!empty($label)) {
			$html .= '<label class="control-label col-lg-3 required">' . $label . '</label>';
		}

		if (!empty($input)) {
			if (!empty($label)) {
				$html .= '<div class="col-lg-9">';
			}
			$html .= $input;
			if (!empty($label)) {
				$html .= '</div>';
			}
		}

		$html .= '</div>';

		$result = array(
			'hasError' => false,
			'msg' => 'OK',
			'html' => $html
		);

		return $result;

	}

	public function hookDisplayTop() {

		$this->smarty->assign('position', 'top');

        return $this->renderMenu();

	}

	public function hookDisplayLeftColumn() {

		$this->smarty->assign('position', 'leftcol');

		return $this->renderMenu();
	}

	public function hookDisplayRightColumn() {

		$this->smarty->assign('position', 'rightcol');

		return $this->renderMenu();
	}

	protected function renderMenu() {

		if (!$this->isCached('blocketdtopmenu.tpl', $this->getCacheId())) {

            $conf = $this->getConfigValues();

			$this->smarty->assign('tagId', $conf['ETDTOPMENU_TAGID']);
			$this->smarty->assign('tagClass', $conf['ETDTOPMENU_TAGCLASS']);
			$this->smarty->assign('list', BlockEtdTopMenuModel::getList());
			$this->smarty->assign('item_tpl', $this->getTemplatePath('blocketdtopmenu_item.tpl'));

		}

		return $this->display(__FILE__, 'blocketdtopmenu.tpl', $this->getCacheId());

	}

    protected function getConfigValues() {

        $ret = array();

        foreach ($this->getConfigFields() as $field) {
            $ret[$field] = Tools::getValue($field, Configuration::getGlobalValue($field));
        }

        return $ret;
    }

    protected function getConfigFields() {

        return array(
            'ETDTOPMENU_TAGCLASS',
            'ETDTOPMENU_TAGID'
        );

    }

    /**
     * Get realpath of a template of current module (check if template is overriden too)
     *
     * @since 1.5.0
     * @param string $template
     * @return string
     */
    public function getTemplatePath($template)
    {
        $overloaded = $this->_isTemplateOverloaded($template);
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

    public function _isTemplateOverloaded($template)
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