<?php

/**
 *
 * @name evoRedirect module for MODx Evo
 * @version 1.0
 * @author Rubium_Team <rubium@webu>
 *
 */

if (IN_MANAGER_MODE != "true" || empty($modx) || !($modx instanceof DocumentParser)) {
	die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODX Content Manager instead of accessing this file directly.");
}
if (!$modx->hasPermission('exec_module')) {
	header("location: " . $modx->getManagerPath() . "?a=106");
}
if(!is_array($modx->event->params)){
    $modx->event->params = array();
}

$confgiLang = "en";
if(stripos($modx->config['manager_language'], "russian") >= 0) $confgiLang = "ru";

//Подключаем обработку шаблонов через DocLister
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/lang/'.$confgiLang.'.php');
include_once(MODX_BASE_PATH.'assets/snippets/DocLister/lib/DLTemplate.class.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect.trait.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect.class.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect_import.class.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect_props.class.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirectHelper.class.php');
include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/evoRedirect_updater.class.php');

$tpl = DLTemplate::getInstance($modx);

$moduleurl = 'index.php?a=112&id='.$_GET['id'].'&';
$action = isset($_GET['action']) ? $_GET['action'] : 'home';


$data = array ('moduleurl'      => $moduleurl, 
                'manager_theme' => $modx->config['manager_theme'], 
                'session'       => $_SESSION, 
                'get'           => $_GET, 
                'action'        => $action , 
                'selected'      => array($action => 'selected'),
                'lang'          => json_encode($lang, true),
                'language'      => $lang
            );
$evoRedirectHelper = new EvoRedirectHelper($modx);
$evoRedirectProps = new EvoRedirectProps($modx);
$evoRedirectImport = new EvoRedirectImport($modx);
$evoRedirectUpdater = new EvoRedirectUpdater($modx);

switch ($action) {
    case 'home':
	    $template = '@CODE:'.file_get_contents(dirname(__FILE__).'/tpl/home.tpl');
        $data['short_url'] = '~'.$evoRedirectHelper->randString(5);
	    $outTpl = $tpl->parseChunk($template,$data);
    break;
    case 'updateRandom':
        $outData['short_url'] = '~'.$evoRedirectHelper->randString(5);
    break;
    case 'makeRedirect':
    	$outData = $evoRedirectHelper->makeRedirect($_POST);
    break;
    case 'getData':
        $outData = $evoRedirectHelper->getRedirects();
    break;
    case 'deleteItem':
        $outData = $evoRedirectHelper->deleteItem($_POST['element_id']);
    break;
    case 'getProps':
        $outData = $evoRedirectProps->getProps($_REQUEST['selected_id']);
    break;
    case 'setProps':
        $outData = $evoRedirectProps->setProps($_POST);
    break;
    case 'importFile':
        $outData = $evoRedirectImport->importFile($_FILES);
    break;
}

// Вывод результата или шаблон или Ajax 
if(!is_null($outTpl)){

    $headerTpl = '@CODE:'.file_get_contents(dirname(__FILE__).'/tpl/header.tpl');
    $footerTpl = '@CODE:'.file_get_contents(dirname(__FILE__).'/tpl/footer.tpl');
    $output = $tpl->parseChunk($headerTpl,$data) . $outTpl . $tpl->parseChunk($footerTpl,$data);

}else{ 

    header('Content-type: application/json');
    $output=preg_replace_callback('/\\\u([0-9а-яА-Яa-fA-F]{4})/',create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($outData));

}
echo $output;
