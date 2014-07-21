<?php
/**
 * @version   $Id: JoomlaRokMenuNode.php 4585 2012-10-27 01:44:54Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
if (!class_exists('PrestashopRokMenuNode')) {
    class PrestashopRokMenuNode extends RokMenuNode {
        protected $access = 0;
        protected $params = '';
        protected $type = 'menuitem';
        //protected $id_link = null;
		protected $columns = 1;
		protected $distribution = 'even';
		protected $manual_distribution = '';
		protected $width = '';
		protected $column_widths = '';
		protected $children_group = 1;
		protected $children_type = 'menuitems';
		protected $modules = 0;
		protected $module_hooks = 0;
		protected $css = '';

        /**
         * Gets the access
         * @access public
         * @return string
         */
        public function getAccess() {
            return $this->access;
        }

        /**
         * Sets the access
         * @access public
         * @param string $access
         */
        public function setAccess($access) {
            $this->access = $access;
        }

        /**
         * Gets the params
         * @access public
         * @return string
         */
        public function getParams() {
            return $this->params;
        }

        /**
         * Sets the params
         * @access public
         * @param string $params
         */
        public function setParams($params) {
            $this->params = $params;
        }

        /**
         * Gets the type
         * @access public
         * @return string
         */
        public function getType() {
            return $this->type;
        }

        /**
         * Sets the type
         * @access public
         * @param string $type
         */
        public function setType($type) {
            $this->type = $type;
        }

        /**
         * @param $id_link
         */
       /* public function setLinkId($id_link){
            $this->id_link = $id_link;
        }*/

        /**
         * @return null
         */
        /*public function getLinkId(){
            return $this->id_link;
        }*/

		/**
		 * @param $columns
		 */
		public function setColumns($columns){
			$this->columns = (int) $columns;
		}

		/**
		 * @return null
		 */
		public function getColumns(){
			return $this->columns;
		}

		/**
		 * @param $distribution
		 */
		public function setDistribution($distribution){
			$this->distribution = $distribution;
		}

		/**
		 * @return null
		 */
		public function getDistribution(){
			return $this->distribution;
		}

		/**
		 * @param $manual_distribution
		 */
		public function setManualDistribution($manual_distribution){
			$this->manual_distribution = $manual_distribution;
		}

		/**
		 * @return null
		 */
		public function getManualDistribution(){
			return $this->manual_distribution;
		}

		/**
		 * @param $width
		 */
		public function setWidth($width){
			$this->width = (int) $width;
		}

		/**
		 * @return null
		 */
		public function getWidth(){
			return $this->width;
		}

		/**
		 * @param $column_widths
		 */
		public function setColumnWidths($column_widths){
			$this->column_widths = $column_widths;
		}

		/**
		 * @return null
		 */
		public function getColumnWidths(){
			return $this->column_widths;
		}

		/**
		 * @param $children_group
		 */
		public function setChildrenGroup($children_group){
			$this->children_group = (int) $children_group;
		}

		/**
		 * @return null
		 */
		public function getChildrenGroup(){
			return $this->children_group;
		}

		/**
		 * @param $children_type
		 */
		public function setChildrenType($children_type){
			$this->children_type = $children_type;
		}

		/**
		 * @return null
		 */
		public function getChildrenType(){
			return $this->children_type;
		}

		/**
		 * @param $modules
		 */
		public function setModules($modules){
			$this->modules = (int) $modules;
		}

		/**
		 * @return null
		 */
		public function getModules(){
			return $this->modules;
		}

		/**
		 * @param $module_hooks
		 */
		public function setModuleHooks($module_hooks){
			$this->module_hooks = (int) $module_hooks;
		}

		/**
		 * @return null
		 */
		public function getModuleHooks(){
			return $this->module_hooks;
		}

		/**
		 * @param $css
		 */
		public function setCSS($css){
			$this->css = $css;
		}

		/**
		 * @return null
		 */
		public function getCSS(){
			return $this->css;
		}

    }
}