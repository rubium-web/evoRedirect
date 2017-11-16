<?php
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect.trait.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect.class.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirectHelper.class.php');

switch($modx->event->name){
	case 'OnPageNotFound':
		$evoRedirect = new EvoRedirectHelper($modx);
		$evoRedirect->doRedirect();
	break;
	case 'OnDocFormRender':
		$evoRedirect = new EvoRedirectHelper($modx);
		echo $evoRedirect->addAdminButton();
		break;
}