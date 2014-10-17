<?php


class PrestashopConfig {

    static $loaded = false;

    public static function getJSon() {
	if (!self::$loaded) {
	    self::initPrestashop();
	}
	return json_encode(self::getDataArray());
    }

    public static function getSecuredKeys() {
	if (!self::$loaded) {
	    self::initPrestashop();
	}

	return array(Configuration::get('diatemgetconfig_clepublique') => Configuration::get('diatemgetconfig_cleprivee'));
    }

    private static function initPrestashop() {
	require('../../config/config.inc.php');
	require('../../modules/prestashopdiatemgetconfig/prestashopdiatemgetconfig.php');

	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	$modules = ModuleCore::getNonNativeModuleList();
	$test = new prestashopdiatemgetconfig();

	foreach ($modules AS $module) {
	    if ($module['name'] != 'prestashopdiatemgetconfig') {
		require('../../modules/' . $module['name'] . '/' . $module['name'] . '.php');
	    }
	}

	self::$loaded = true;
    }

    private static function getDataArray() {
	$output = array();

	$output['cms'] = self::getCms();
	$output['plugins'] = self::getPlugins();

	return $output;
    }

    private static function getCms() {
	$output = array();

	$output['name'] = 'prestashop';
	$output['version'] = _PS_VERSION_;
	return $output;
    }

    private static function getPlugins() {
	$output = array();

	$modules = ModuleCore::getNonNativeModuleList();

	foreach ($modules AS $module) {
	    $instModule = new $module['name']();

	    $line = array();
	    $line['type'] = $instModule->tab;
	    $line['name'] = $instModule->name;
	    $line['version'] = $instModule->version;
	    $line['editeur'] = $instModule->author;
	    $line['pluginUrl'] = '';
	    $line['info'] = $instModule->description;
	    $line['enabled'] = ($instModule->active == 1) ? true : false;
	    $output[] = $line;
	}

	return $output;
    }

}
