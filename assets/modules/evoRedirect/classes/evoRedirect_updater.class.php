<?php

/**
* Update class
*/
class EvoRedirectUpdater extends EvoRedirect
{
	/**
	 * check version and update db data
	 */
	function __construct($modx) {
		
		$this->modx = $modx;
		$this->table = $this->modx->db->config['table_prefix'].'evoredirect';
		$is_init = $this->initialized();
		if($is_init === false) $this->init();
	}

	/**
	 * detect initialized module status
	 * @return boolean
	 */
	public function initialized()
	{
		$sql = "SHOW TABLES LIKE '".$this->table."'";
		$query = $this->modx->db->query( $sql );
		$hasTable = $this->modx->db->getRecordCount( $query );

		if($hasTable>0) return true; return false;
	}

	/**
	 * create db module table
	 * @return void
	 */
	public function init()
	{
			
		$sql = "CREATE TABLE IF NOT EXISTS `$this->table` (
					`id` int(11) NOT NULL AUTO_INCREMENT, 
					`old_url` varchar(255),
					`new_url` varchar(255),
					`short_uri_crc` int(20) NOT NULL,
					`code` int(3) NOT NULL,
					`save_get` int(11) NOT NULL,
					`search_get` int(11) NOT NULL,
					`active` int(11) NOT NULL,
					PRIMARY KEY (`id`))
					ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
					
		$this->modx->db->query( $sql );
		echo("Module init finished!");
	}



}