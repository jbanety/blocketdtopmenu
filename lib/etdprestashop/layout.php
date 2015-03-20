<?php
/**
 * @package     blocketdtopmenu
 *
 * @version     1.0.1
 * @copyright   Copyright (C) 2014 Jean-Baptiste Alleaume. Tous droits réservés.
 * @license     http://alleau.me/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

class EtdPrestashopLayout extends AbstractRokMenuLayout {

    protected $theme_path;
    protected $params;
    static $jsLoaded = false;

    protected $context;
    protected $smarty;
    protected $current_subtemplate = null;

    private $activeid;

    public function __construct(&$args) {

        parent::__construct($args);

        $theme_rel_path                = "/modules/blocketdtopmenu/lib/etdprestashop";
        $this->theme_path              = $theme_rel_path;
        $this->args['theme_path']      = $this->theme_path;
        $this->args['theme_rel_path']  = $theme_rel_path;
        $this->args['theme_url']       = $this->args['theme_rel_path'];
        $this->args['responsive-menu'] = $args['responsive-menu'];

        // Load context and smarty
        $this->context = Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty->createData($this->context->smarty);
        }

    }

    public function getArgs($arg = null) {

        if (isset($arg)) {
            return $this->args[$arg];
        }

        return $this->args;
    }

    public function stageHeader() {
    }

    function getModule($id = 0, $name = '') {

        $modules =& RokNavMenu::loadModules();
        $total   = count($modules);
        for ($i = 0; $i < $total; $i++) {
            // Match the name of the module
            if ($modules[$i]->id == $id || $modules[$i]->name == $name) {
                return $modules[$i];
            }
        }

        return null;
    }

    function getModules($position) {

        $modules = JModuleHelper::getModules($position);

        return $modules;
    }

    function array_fill(array $array, $columns, $manual_distro) {

        $new_array = array();

        array_unshift($array, null);

        for ($i = 0; $i < $columns; $i++) {
            if (isset($manual_distro[$i])) {
                $manual_count = $manual_distro[$i];
                for ($c = 0; $c < $manual_count; $c++) {
                    //echo "i:c " . $i . ":". $c;
                    $element = next($array);
                    if ($element) {
                        $new_array[$i][$c] = $element;
                    }
                }
            }

        }

        return $new_array;

    }

    function array_chunkd(array $array, $chunk) {

        if ($chunk === 0) {
            return $array;
        }

        // number of elements in an array
        $size = count($array);

        // average chunk size
        $chunk_size = $size / $chunk;

        // calculate how many not-even elements eg in array [3,2,2] that would be element "3"
        $real_chunk_size = floor($chunk_size);
        $diff            = $chunk_size - $real_chunk_size;
        $not_even        = $diff > 0 ? round($chunk * $diff) : 0;

        // initialise values for return
        $result        = array();
        $current_chunk = 0;

        foreach ($array as $key => $element) {
            $count = isset($result[$current_chunk]) ? count($result[$current_chunk]) : 0;

            // move to a new chunk?
            if ($count == $real_chunk_size && $current_chunk >= $not_even || $count > $real_chunk_size && $current_chunk < $not_even) {
                $current_chunk++;
            }

            // save value
            $result[$current_chunk][$key] = $element;
        }

        return $result;
    }

    public function calculate_sizes(array $array) {

        return implode(', ', array_map('count', $array));
    }

    public function curPageURL($link) {

        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        $replace = str_replace('&', '&amp;', (preg_match("/^http/", $link) ? $pageURL : $_SERVER["REQUEST_URI"]));

        return $replace == $link || $replace == $link . 'index.php';
    }

    public function renderItem(PrestashopRokMenuNode &$item, RokMenuNodeTree &$menu) {

        $wrapper_css = '';
        $ul_css      = '';

        $custom_class = $item->getCSS();

        /*if ($custom_image && $custom_image != -1) $item->addLinkClass('image');
        if ($custom_icon && $custom_icon != -1) $item->addLinkClass('icon');*/
        if ($custom_class != '') {
            $item->addListItemClass($custom_class);
        }

        $dropdown_width = (int)$item->getWidth();

        if ($dropdown_width > 0) {
            $wrapper_css = ' style="width:' . $dropdown_width . 'px;"';
        }

        if ($item->getType() == 'separator') {
            $item->setLink('#');
        }

        if ($item->hasChildren()) {
            $item->addListItemClass('dropdown');
            $item->addLinkClass('dropdown-toggle');
            $item->addLinkAttrib('data-toggle', "dropdown");
            $item->addLinkAttrib('role', 'button');
            $item->addLinkAttrib('aria-expanded',"false");
        }

        $this->smarty->assign(array(
            'wrapper_css'         => $wrapper_css,
            'ul_css'              => $ul_css,
            'custom_class'        => $custom_class,
            'item'                => $item,
            'menu'                => $menu,
            'layout'              => &$this
        ));

        return $this->display('blocketdtopmenu_item.tpl', $this->getCacheId($item->getId()));

    }

    public function renderMenu(&$menu) {

        $this->smarty->assign(array(
            'menu'   => $menu,
            'layout' => &$this
        ));

        return $this->display('blocketdtopmenu_layout.tpl', $this->getCacheId($menu->getId()));

    }

    protected function display($template, $cacheId = null, $compileId = null) {

        $overloaded = $this->_isTemplateOverloaded($template);

        if ($overloaded === null) {
            return 'No template found (' . $template . ')';
        } else {
            if (Tools::getIsset('live_edit') || Tools::getIsset('live_configurator_token')) {
                $cacheId = null;
            }

            $this->smarty->assign(array(
                'module_dir'          => __PS_BASE_URI__ . 'modules/blocketdtopmenu/',
                'module_template_dir' => ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__) . 'modules/blocketdtopmenu/'
            ));

            if ($cacheId !== null) {
                Tools::enableCache();
            }

            $result = $this->getCurrentSubTemplate($template, $cacheId, $compileId)
                           ->fetch();

            if ($cacheId !== null) {
                Tools::restoreCacheSettings();
            }

            $this->resetCurrentSubTemplate($template, $cacheId, $compileId);

            return $result;
        }
    }

    /*
    ** Template management (display, overload, cache)
    */
    protected function _isTemplateOverloaded($template) {

        if (Tools::file_exists_cache(_PS_THEME_DIR_ . 'modules/blocketdtopmenu/' . $template)) {
            return _PS_THEME_DIR_ . 'modules/blocketdtopmenu/' . $template;
        } elseif (Tools::file_exists_cache(_PS_THEME_DIR_ . 'modules/blocketdtopmenu/views/templates/hook/' . $template)) {
            return _PS_THEME_DIR_ . 'modules/blocketdtopmenu/views/templates/hook/' . $template;
        } elseif (Tools::file_exists_cache(_PS_THEME_DIR_ . 'modules/blocketdtopmenu/views/templates/front/' . $template)) {
            return _PS_THEME_DIR_ . 'modules/blocketdtopmenu/views/templates/front/' . $template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . 'blocketdtopmenu/views/templates/hook/' . $template)) {
            return false;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . 'blocketdtopmenu/views/templates/front/' . $template)) {
            return false;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . 'blocketdtopmenu/' . $template)) {
            return false;
        }

        return null;
    }

    protected function getCurrentSubTemplate($template, $cache_id = null, $compile_id = null) {

        if (!isset($this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id])) {
            $this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id] = $this->context->smarty->createTemplate($this->getTemplatePath($template), $cache_id, $compile_id, $this->smarty);
        }

        return $this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id];
    }

    protected function resetCurrentSubTemplate($template, $cache_id, $compile_id) {

        $this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id] = null;
    }

    protected function getTemplatePath($template) {

        $overloaded = $this->_isTemplateOverloaded($template);
        if ($overloaded === null) {
            return null;
        }

        if ($overloaded) {
            return $overloaded;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . 'blocketdtopmenu/views/templates/hook/' . $template)) {
            return _PS_MODULE_DIR_ . 'blocketdtopmenu/views/templates/hook/' . $template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . 'blocketdtopmenu/views/templates/front/' . $template)) {
            return _PS_MODULE_DIR_ . 'blocketdtopmenu/views/templates/front/' . $template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . 'blocketdtopmenu/' . $template)) {
            return _PS_MODULE_DIR_ . 'blocketdtopmenu/' . $template;
        } else {
            return null;
        }
    }

    protected function getCacheId($id=null) {

        $cache_array   = array($id);
        $cache_array[] = 'blocketdtopmenu';
        if (Configuration::get('PS_SSL_ENABLED')) {
            $cache_array[] = (int)Tools::usingSecureMode();
        }
        if (Shop::isFeatureActive()) {
            $cache_array[] = (int)$this->context->shop->id;
        }
        if (Group::isFeatureActive()) {
            $cache_array[] = (int)Group::getCurrent()->id;
        }
        if (Language::isMultiLanguageActivated()) {
            $cache_array[] = (int)$this->context->language->id;
        }
        if (Currency::isMultiCurrencyActivated()) {
            $cache_array[] = (int)$this->context->currency->id;
        }
        $cache_array[] = (int)$this->context->country->id;

        return implode('|', $cache_array);
    }
}
