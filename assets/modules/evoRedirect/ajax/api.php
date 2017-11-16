<?php
error_reporting(E_ALL);
ini_set('display_errors',1);


include_once(dirname(__FILE__)."/../../../cache/siteManager.php");
require_once(dirname(__FILE__).'/../../../../'.MGR_DIR.'/includes/protect.inc.php');
define('MODX_MANAGER_PATH', "../../../../".MGR_DIR."/");
require_once(MODX_MANAGER_PATH . 'includes/config.inc.php');
require_once(MODX_MANAGER_PATH . '/includes/protect.inc.php');
require_once(MODX_MANAGER_PATH.'/includes/document.parser.class.inc.php');

session_name($site_sessionname);
session_id($_COOKIE[session_name()]);
session_start();
$modx = new DocumentParser;
$modx->db->connect();
$modx->getSettings();
$modx->config['site_url'] = isset($request['site_url']) ? $request['site_url'] : '';

if($_SESSION['mgrValidated']){
	define('IN_MANAGER_MODE', true);
	define('MODX_API_MODE', true);
}

include_once("../evoRedirect.inc.php");