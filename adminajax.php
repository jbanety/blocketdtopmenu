<?php
/**
 * @package     blocketdtopmenu
 *
 * @version     2.2.0
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../modules/blocketdtopmenu/blocketdtopmenu.php');

// Réponse JSON.
header('Content-type: application/json');

// On instancie le module.
$module = new BlockEtdTopMenu();

$result = array(
	'hasError' => true,
	'msg' => $module->l('Invalid task.')
);

// On contrôle le token.
if (Tools::getValue('token') != sha1('blocketdtopmenu_admin' . _COOKIE_KEY_)) {
	$result = array(
		'hasError' => true,
		'msg' => $module->l('Invalid token')
	);
	die(json_encode($result));
}

if (Tools::isSubmit('loadType')) {
	$result = $module->loadType($_GET['type'], $_GET['id_link']);
}

die(json_encode($result));