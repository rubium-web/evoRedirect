<?php

include_once(MODX_BASE_PATH.'assets/modules/evoRedirect/classes/HelperImport.class.php');

/**
* import class for EvoRedirect
*/
class EvoRedirectImport extends EvoRedirect
{

	/**
	 * Экспорт файла CSV
	 * @param  array $file
	 * @return json  { status: 'server', sname:'$sname'}
	 */
	public function importFile($file)
	{
		$evoRedirect =  new EvoRedirectHelper();
		$fileImport = new HelperImport($file, $this, $evoRedirect);
		if(!$fileImport->resp["error"]){

			$lines = array();
			$fileImport->createRedirects();

			return $fileImport->resp;

		}else{
			return $fileImport;
		}
	}
}