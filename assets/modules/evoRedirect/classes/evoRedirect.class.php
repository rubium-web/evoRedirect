<?php

class EvoRedirect
{
	use EvoRedirectTrait;
	
	public $modx;
	public $table;
	public $fields;

	function __construct(){
		global $modx;
		$this->modx = $modx;
		$this->table = $this->modx->getFullTableName('evoredirect');
		$this->fields = array('id', 'old_url', 'new_url', 'short_uri_crc', 'code', 'save_get', 'search_get', 'active');
	}
}