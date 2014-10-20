<?php

require('prestashopconfig_class.php');
include 'restservice_class.php';

class DiatemPrestashopGetConfig extends RestService{
    public function __construct() {
	parent::__construct();
    }
    
    public function _get(){
	if($this->get_request_method() != 'GET'){
	    $this->response('', 405);
	}
	
	$retStr = PrestashopConfig::getJSon();
	$this->response($retStr, 200);
    }
}

$api = new DiatemPrestashopGetConfig();
$api->setSecured(PrestashopConfig::getSecuredKeys());
$api->processApi();
