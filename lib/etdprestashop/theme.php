<?php
/**
 * @package     blocketdtopmenu
 *
 * @version     1.0.1
 * @copyright   Copyright (C) 2014 Jean-Baptiste Alleaume. Tous droits réservés.
 * @license     http://alleau.me/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

class EtdPrestashopTheme extends AbstractRokMenuTheme {

	protected $defaults = array(
		'enable_js' => 1,
		'opacity' => 1,
		'effect' => 'slidefade',
		'hidedelay' => 500,
		'menu-animation' => 'Quad.easeOut',
		'menu-duration' => 400,
		'centered-offset' => 0,
		'tweak-initial-x' => -3,
		'tweak-initial-y' => 0,
		'tweak-subsequent-x' => 0,
		'tweak-subsequent-y' => 1,
		'tweak-width' => 0,
		'tweak-height' => 0,
		'enable_current_id' => 0,
		'responsive-menu' => 'panel'
	);

	public function getFormatter($args){
		require_once(dirname(__FILE__) . '/formatter.php');
		return new EtdPrestashopFormatter($args);
	}

	public function getLayout($args){
		require_once(dirname(__FILE__) . '/layout.php');
		return new EtdPrestashopLayout($args);
	}
}
