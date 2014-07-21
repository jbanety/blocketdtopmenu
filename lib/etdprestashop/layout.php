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

class EtdPrestashopLayout extends AbstractRokMenuLayout
{
	protected $theme_path;
	protected $params;
	static $jsLoaded = false;

	private $activeid;

	public function __construct(&$args)
	{
		parent::__construct($args);

		$theme_rel_path = "/modules/blocketdtopmenu/lib/etdprestashop";
		$this->theme_path = $theme_rel_path;
		$this->args['theme_path'] = $this->theme_path;
		$this->args['theme_rel_path'] = $theme_rel_path;
		$this->args['theme_url'] = $this->args['theme_rel_path'];
		$this->args['responsive-menu'] = $args['responsive-menu'];
	}

	public function stageHeader()
	{

		/*JHtml::_('behavior.framework', true);
		if (!self::$jsLoaded && $gantry->get('layout-mode', 'responsive') == 'responsive'){
			if (!($gantry->browser->name == 'ie' && $gantry->browser->shortver < 9)){
				$gantry->addScript($gantry->baseUrl . 'modules/mod_roknavmenu/themes/default/js/rokmediaqueries.js');
				$gantry->addScript($gantry->baseUrl . 'modules/mod_roknavmenu/themes/default/js/responsive.js');
				if ($this->args['responsive-menu'] == 'selectbox') $gantry->addScript($gantry->baseUrl . 'modules/mod_roknavmenu/themes/default/js/responsive-selectbox.js');
			}
			self::$jsLoaded = true;
		}
		$gantry->addLess('menu.less', 'menu.css', 1, array('headerstyle'=>$gantry->get('headerstyle','dark'), 'menuHoverColor'=>$gantry->get('linkcolor')));

		// no media queries for IE8 so we compile and load the hovers
		if ($gantry->browser->name == 'ie' && $gantry->browser->shortver < 9){
			$gantry->addLess('menu-hovers.less', 'menu-hovers.css', 1, array('headerstyle'=>$gantry->get('headerstyle','dark'), 'menuHoverColor'=>$gantry->get('linkcolor')));
		}*/
	}

	protected function renderItem(PrestashopRokMenuNode &$item, RokMenuNodeTree &$menu)
	{

		$wrapper_css = '';
		$ul_css = '';
		$group_css = '';

		$item_params = $item->getParams();

		//get columns count for children
		$columns = $item->getColumns();
		//get custom image
		//$custom_image = $item_params->get('dropdown_customimage');
		//get the custom icon
		//$custom_icon = $item_params->get('dropdown_customicon');
		//get the custom class
		$custom_class = $item->getCSS();

		//add default link class
		$item->addLinkClass('item');

		/*if ($custom_image && $custom_image != -1) $item->addLinkClass('image');
		if ($custom_icon && $custom_icon != -1) $item->addLinkClass('icon');*/
		if ($custom_class != '') $item->addListItemClass($custom_class);

		$dropdown_width = $item->getWidth();
		$column_widths = explode(",",$item->getColumnWidths());

		if (trim($columns)=='') $columns = 1;
		if ($dropdown_width == 0) $dropdown_width = 180;

		$wrapper_css = ' style="width:'.$dropdown_width.'px;"';

		$col_total = 0;$cols_left=$columns;
		if (trim($column_widths[0] != '')) {
			for ($i=0; $i < $columns; $i++) {
				if (isset($column_widths[$i])) {
					$ul_css[] = ' style="width:'.trim(intval($column_widths[$i])).'px;"';
					$col_total += intval($column_widths[$i]);
					$cols_left--;
				} else {
					$col_width = floor(intval((intval($dropdown_width) - $col_total) / $cols_left));
					$ul_css[] = ' style="width:'.$col_width.'px;"';
				}
			}
		} else {
			for ($i=0; $i < $columns; $i++) {
				$col_width = floor(intval($dropdown_width)/$columns);
				$ul_css[] = ' style="width:'.$col_width.'px;"';
			}
		}

		$grouping = $item->getChildrenGroup();
		if ($grouping == 1) $item->addListItemClass('grouped');

		$child_type = $item->getChildrenType();
		$child_type = $child_type == '' ? 'menuitems' : $child_type;
		$distribution = $item->getDistribution();
		$manual_distribution = explode(",",$item->getManualDistribution());

		$modules = array();
		if ($child_type == 'modules') {
			$modules_id = $item->getModules();

			$ids = is_array($modules_id) ? $modules_id : array($modules_id);
			foreach ($ids as $id) {
				if ($module = $this->getModule ($id)) $modules[] = $module;
			}
			$group_css = ' type-module';

		} elseif ($child_type == 'modulehooks') {
			$modules_pos = $item->getModuleHooks();

			$positions = is_array($modules_pos) ? $modules_pos : array($modules_pos);
			foreach ($positions as $pos) {
				$mod = $this->getModules ($pos);
				$modules = array_merge ($modules, $mod);
			}
			$group_css = ' type-module';
		}

		//not so elegant solution to add subtext
		/*$item_subtext = $item_params->get('dropdown_item_subtext','');
		if ($item_subtext=='') $item_subtext = false;
		else $item->addLinkClass('subtext');*/
		$item_subtext = false;

		//sort out module children:
		if ($child_type!="menuitems") {
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$params		= array('style'=>'dropdown');

			$mod_contents = array();
			foreach ($modules as $mod)  {

				$mod_contents[] = $renderer->render($mod, $params);
			}
			$item->setChildren($mod_contents);

			$link_classes = explode(' ', $item->getLinkClasses());
			$item->setLinkClasses($link_classes);
		}

		if ($item->getType() == 'separator') {
			$item->addListItemClass('separator');
			$item->setLink('javascript:void(0);');
		}

		?>
		<li <?php if($item->hasListItemClasses()) : ?>class="<?php echo $item->getListItemClasses()?>"<?php endif;?> <?php if($item->hasCssId() && $this->activeid):?>id="<?php echo $item->getCssId();?>"<?php endif;?>>

			<a <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?>"<?php endif;?> <?php if($item->hasLink()):?>href="<?php echo $item->getLink();?>"<?php endif;?> <?php if($item->hasTarget()):?>target="<?php echo $item->getTarget();?>"<?php endif;?> <?php if ($item->hasAttribute('onclick')): ?>onclick="<?php echo $item->getAttribute('onclick'); ?>"<?php endif; ?><?php if ($item->hasLinkAttribs()): ?> <?php echo $item->getLinkAttribs(); ?><?php endif; ?>>


				<?php	echo $item->getTitle(); ?>

				<?php
				// Comment this out if you don't need a 1px bottom border fix
				if ($item->hasChildren()): ?>
					<span class="border-fixer"><span class="border-fixer1"><span class="border-fixer2"></span><span class="border-fixer3"></span></span></span>
				<?php endif; ?>
			</a>


			<?php if ($item->hasChildren()): ?>

				<?php if ($grouping == 0 or $item->getLevel() == 0) :

					if ($distribution=='inorder') {
						$count = sizeof($item->getChildren());
						$items_per_col = intval(ceil($count / $columns));
						$children_cols = array_chunk($item->getChildren(),$items_per_col);
					} elseif ($distribution=='manual') {
						$children_cols = $this->array_fill($item->getChildren(), $columns, $manual_distribution);
					} else {
						$children_cols = $this->array_chunkd($item->getChildren(),$columns);
					}
					$col_counter = 0;
					?>
					<div class="dropdown collapse <?php if ($item->getLevel() > 0) echo 'flyout '; ?><?php echo 'columns-'.$columns.' '; ?>"<?php echo $wrapper_css; ?>>
						<?php foreach($children_cols as $col) : ?>
							<div class="column col<?php echo intval($col_counter)+1; ?>" <?php echo $ul_css[$col_counter++]; ?>>
								<ul class="l<?php echo $item->getLevel() + 2; ?>">
									<?php foreach ($col as $child) : ?>
										<?php if ($child_type=='menuitems'): ?>
											<?php $this->renderItem($child, $menu); ?>
										<?php else: ?>
											<li class="modules">
												<?php if (isset($module) and ($module->showtitle != 0)) : ?>
													<div class="module-title">
														<h2 class="title"><?php echo $module->title; ?></h2>
													</div>
												<?php endif; ?>
												<div class="module-content">
													<?php echo ($child); ?>
												</div>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach;?>
					</div>

				<?php else : ?>

					<ol class="<?php echo $group_css; ?>">
						<?php foreach ($item->getChildren() as $child) : ?>
							<?php if ($child_type=='menuitems'): ?>
								<?php $this->renderItem($child, $menu); ?>
							<?php else: ?>
								<li class="modules">
									<?php if (isset($module) and ($module->showtitle != 0)) : ?>
										<div class="module-title">
											<h2 class="title"><?php echo $module->title; ?></h2>
										</div>
									<?php endif; ?>
									<div class="module-content">
										<?php echo ($child); ?>
									</div>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ol>

				<?php endif; ?>
			<?php endif; ?>
		</li>
	<?php
	}

	function getModule ($id=0, $name='')
	{

		$modules	=& RokNavMenu::loadModules();
		$total		= count($modules);
		for ($i = 0; $i < $total; $i++)
		{
			// Match the name of the module
			if ($modules[$i]->id == $id || $modules[$i]->name == $name)
			{
				return $modules[$i];
			}
		}
		return null;
	}

	function getModules ($position)
	{
		$modules = JModuleHelper::getModules ($position);
		return $modules;
	}

	function array_fill(array $array, $columns, $manual_distro) {

		$new_array = array();

		array_unshift($array, null);

		for ($i=0;$i<$columns;$i++) {
			if (isset($manual_distro[$i])) {
				$manual_count = $manual_distro[$i];
				for ($c=0;$c<$manual_count;$c++) {
					//echo "i:c " . $i . ":". $c;
					$element = next($array);
					if ($element) $new_array[$i][$c] = $element;
				}
			}

		}

		return $new_array;

	}

	function array_chunkd(array $array, $chunk)
	{
		if ($chunk === 0)
			return $array;

		// number of elements in an array
		$size = count($array);

		// average chunk size
		$chunk_size = $size / $chunk;

		// calculate how many not-even elements eg in array [3,2,2] that would be element "3"
		$real_chunk_size = floor($chunk_size);
		$diff = $chunk_size - $real_chunk_size;
		$not_even = $diff > 0 ? round($chunk * $diff) : 0;

		// initialise values for return
		$result = array();
		$current_chunk = 0;

		foreach ($array as $key => $element)
		{
			$count = isset($result[$current_chunk]) ? count($result[$current_chunk]) : 0;

			// move to a new chunk?
			if ($count == $real_chunk_size && $current_chunk >= $not_even || $count > $real_chunk_size && $current_chunk < $not_even)
				$current_chunk++;

			// save value
			$result[$current_chunk][$key] = $element;
		}

		return $result;
	}

	public function calculate_sizes (array $array)
	{
		return implode(', ', array_map('count', $array));
	}

	public function curPageURL($link) {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		$replace = str_replace('&', '&amp;', (preg_match("/^http/", $link) ? $pageURL : $_SERVER["REQUEST_URI"]));

		return $replace == $link || $replace == $link . 'index.php';
	}

	public function renderMenu(&$menu) {
		ob_start();
		?>
<div id="etd-menu-container">
    <ul class="etd-menu l1 " <?php if (array_key_exists('tag_id',$this->args)): ?>id="<?php echo $this->args['tag_id'];?>"<?php endif;?>>
        <?php foreach ($menu->getChildren() as $item) : ?>
            <?php $this->renderItem($item, $menu); ?>
        <?php endforeach; ?>
    </ul>
</div>
		<?php
		return ob_get_clean();
	}
}
