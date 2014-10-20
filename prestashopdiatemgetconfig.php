<?php

if (!defined('_PS_VERSION_'))
    exit;

class prestashopdiatemgetconfig extends Module {

    public function __construct() {
	$this->name = 'prestashopdiatemgetconfig';
	$this->tab = 'administration';
	$this->version = '0.1.0';
	$this->author = 'Diatem';

	$this->need_instance = 0;
	$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
	$this->bootstrap = true;

	parent::__construct();

	$this->displayName = $this->l('Diatem getConfig REST services');
	$this->description = $this->l('Implémente un service REST sécurisé permettant de récupérer à distance la version de Prestashop ainsi que des modules utilisés.');

	$this->confirmUninstall = $this->l('Etes vous sur de vouloir désinstaller ?');

	if (!Configuration::get('MYMODULE_NAME'))
	    $this->warning = $this->l('No name provided.');
    }

    public function install() {
	if (!parent::install() ||
		!Configuration::updateValue('diatemgetconfig_clepublique', '') ||
		!Configuration::updateValue('diatemgetconfig_cleprivee', '')) {
	    return false;
	}
	return true;
    }

    public function uninstall() {
	if (!parent::uninstall() ||
		!Configuration::deleteByName('diatemgetconfig_clepublique') ||
		!Configuration::deleteByName('diatemgetconfig_cleprivee')) {
	    return false;
	}
	return true;
    }

    public function getContent() {
	$output = null;

	if (Tools::isSubmit('submit' . $this->name)) {
	    $diatemgetconfig_clepublique = strval(Tools::getValue('diatemgetconfig_clepublique'));
	    $diatemgetconfig_cleprivee = strval(Tools::getValue('diatemgetconfig_cleprivee'));
	    if (!$diatemgetconfig_clepublique || empty($diatemgetconfig_clepublique) || !Validate::isGenericName($diatemgetconfig_clepublique) || 
	    !$diatemgetconfig_cleprivee || empty($diatemgetconfig_cleprivee) || !Validate::isGenericName($diatemgetconfig_cleprivee))
		$output .= $this->displayError($this->l('Invalid Configuration value'));
	    else {
		Configuration::updateValue('diatemgetconfig_clepublique', $diatemgetconfig_clepublique);
		Configuration::updateValue('diatemgetconfig_cleprivee', $diatemgetconfig_cleprivee);
		$output .= $this->displayConfirmation($this->l('Settings updated'));
	    }
	}
	return $output . $this->displayForm();
    }

    public function displayForm() {
	// Get default language
	$default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

	// Init Fields form array
	$fields_form[0]['form'] = array(
	    'legend' => array(
		'title' => $this->l('Settings'),
	    ),
	    'input' => array(
		array(
		    'type' => 'text',
		    'label' => $this->l('Clé publique'),
		    'name' => 'diatemgetconfig_clepublique',
		    'size' => 20,
		    'required' => true
		),
		array(
		    'type' => 'text',
		    'label' => $this->l('Clé privée'),
		    'name' => 'diatemgetconfig_cleprivee',
		    'size' => 20,
		    'required' => true
		)
	    ),
	    'submit' => array(
		'title' => $this->l('Save'),
		'class' => 'button'
	    )
	);

	$helper = new HelperForm();

	// Module, token and currentIndex
	$helper->module = $this;
	$helper->name_controller = $this->name;
	$helper->token = Tools::getAdminTokenLite('AdminModules');
	$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

	// Language
	$helper->default_form_language = $default_lang;
	$helper->allow_employee_form_lang = $default_lang;

	// Title and toolbar
	$helper->title = $this->displayName;
	$helper->show_toolbar = true;	// false -> remove toolbar
	$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	$helper->submit_action = 'submit' . $this->name;
	$helper->toolbar_btn = array(
	    'save' =>
	    array(
		'desc' => $this->l('Save'),
		'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
		'&token=' . Tools::getAdminTokenLite('AdminModules'),
	    ),
	    'back' => array(
		'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
		'desc' => $this->l('Back to list')
	    )
	);

	// Load current value
	$helper->fields_value['diatemgetconfig_clepublique'] = Configuration::get('diatemgetconfig_clepublique');
	$helper->fields_value['diatemgetconfig_cleprivee'] = Configuration::get('diatemgetconfig_cleprivee');

	return $helper->generateForm($fields_form);
    }

}
