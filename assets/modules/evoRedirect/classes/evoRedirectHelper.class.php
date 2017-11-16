<?php

class EvoRedirectHelper extends EvoRedirect
{
	
	public $code;
	public $save_get;
	public $search_get;
	public $active;
	public $table;

	/**
	 * Создание редиректа
	 * @param  array  $arr [Массив с полями для создания записи (old_url, code, new_url, short_uri_crc, save_get, search_get, active)]
	 * @return mixed	[Успешно - вернет входной массив. Не успешно - false]
	 */
	function makeRedirect(array $arr){
		
		switch ($arr['code']) {
			case '1':
				$this->code = 301;
				break;
			case '2':
				$this->code = 302;
				break;
			case 301:
				$this->code = 301;
				break;
			case 302:
				$this->code = 302;
				break;
			default:
				$this->code = 301;
				break;
		}

		$this->save_get = (!empty(intval($arr['save_get']))) ? intval($arr['save_get']) : 0;
		$this->search_get = (!empty(intval($arr['search_get']))) ? intval($arr['search_get']) : 0;
		$this->active = (!empty(intval($arr['active']))) ? intval($arr['active']) : 0;

		if (!empty($arr['short_url'])) {
			if ($this->checkCrc($arr['short_url'])) {
				$uri = $arr['short_url'];
			}
		}
		do{
            if (isset($bNew) || !isset($uri)) {
            	$uri = "~".$this->randString(5);
            }
            $bNew = true;
            $uriCrc32 = $this->makeCrc32($uri);

            $bNew = $this->checkCrc($uriCrc32);
        }
        while (!$bNew);
		
		$result['uri'] = $uri;
		if ($uri != $arr['short_url']) {
			$result['error'] = 'Этот короткая ссылка уже занята';
			return $result;
		}

	    $fields = array(
	    	'old_url'  => $arr['url'],
	    	'new_url'  => $uri,
	    	'code'  => $this->code,
            'short_uri_crc' => $uriCrc32,
            'save_get' => $this->save_get,
            'search_get' => $this->search_get,
            'active' => $this->active
        );

		$id = $this->modx->db->insert( $fields, $this->table);
		
		$result = $fields;

		if (empty($arr['short_url'])) {
			unset($result['error']);
		}

		$oldUrl = $result['old_url'];
		if(is_numeric($result['old_url'])){
			$oldUrl = $this->modx->makeUrl($result['old_url']);
		}

		$result['save_get'] = ($result['save_get'] == 1)? '<span class="checker active"></span>' : '<span class="checker"></span>';
		$result['search_get'] = ($result['search_get'] == 1)? '<span class="checker active"></span>' : '<span class="checker"></span>';
		$result['active'] = ($result['active'] == 1)? '<span class="checker active"></span>' : '<span class="checker"></span>';
		$result['delete'] = '<button type="button" class="delete-item"><i class="fa fa-trash fa-fw"></i></button>';

		$result['new_url'] = "<a href='/".$result['new_url']."' target='_blank'>".$result['new_url']."</a><a class='copy_uri' data-link='/".$result['new_url']."'>";
    	$result['old_url'] = "<a href='".$oldUrl."' target='_blank'>".$result['old_url']."</a>";

		$result['id'] = $id;

		return $result;
	}
	
	/**
	 * Удаление редиректа
	 * @param integer $id [ID в базе]
	 */
	public function deleteItem($id=0)
	{
		$out = array("error" => false, "text" => "");
		if(!empty($id)){
			$id = intval($id);
			$this->modx->db->delete($this->table, "id = $id");
		}else{
			$out["error"] = true;
			$out["text"] = "Не верные параметры!";
		}

		return $out;
	}

	/**
	 * Получение списка редиректов
	 * @param  boolean $getProperties [description]
	 * @return array	[Массив с записями]
	 */
	function getRedirects($getProperties=false)
	{
    	$arr_rows = array();
    	$res = array();
    	$result = $this->modx->db->select("id,old_url,new_url,code,save_get,search_get,active", $this->table,  "1");
    	if( $this->modx->db->getRecordCount( $result ) >= 1 ) {
			$res = $this->modx->db->makeArray( $result );
			$getProperties = true;
    	}

    	if ($getProperties) {
    		foreach ($res as $value) {
    			$oldUrl = $value['old_url'];
    			if(is_numeric($value['old_url'])){
    				$oldUrl = $this->modx->makeUrl($value['old_url']);
    			}

    			$value['save_get'] = ($value['save_get'])? '<span class="checker active"></span>' : '<span class="checker"></span>';
    			$value['search_get'] = ($value['search_get'])? '<span class="checker active"></span>' : '<span class="checker"></span>';
    			$value['active'] = ($value['active'])? '<span class="checker active"></span>' : '<span class="checker"></span>';
    			$value['delete'] = '<button type="button" class="delete-item"><i class="fa fa-trash fa-fw"></i></button>';
    			$value['new_url'] = "<a href='/".$value['new_url']."' target='_blank'>".$value['new_url']."</a><a class='copy_uri' data-link='/".$value['new_url']."'>";
    			$value['old_url'] = "<a href='".$oldUrl."' target='_blank'>".$value['old_url']."</a>";
    			$new_res[] = $value;
    		}
    		$res = $new_res;
    	}
    	return $res;
	}

	/**
	 * Выполнение редиректа. Для плагина
	 * @return void
	 */
	function doRedirect()
	{	

		$uri = $_REQUEST['q'];
		$uriFull = $_SERVER['REQUEST_URI'];
		$params = '';


		if(!empty($uriFull) && $uriFull[0] == '/')
			$uriFull = substr($uriFull, 1, strlen($uriFull));

		$result = $this->modx->db->select( 'old_url, new_url, code, save_get', $this->table, "new_url = '".$this->modx->db->escape($uriFull)."' AND active=1 AND search_get=1");

		if($this->modx->db->getRecordCount($result)==0){
			$result = $this->modx->db->select( 'old_url, new_url, code, save_get', $this->table, "new_url LIKE '%".$this->modx->db->escape($uri)."%' AND active=1 AND search_get=0 ");;
		}

		$save_get = false;

		if( $this->modx->db->getRecordCount( $result ) == 1 ) {
			while( $row = $this->modx->db->getRow( $result ) ) {
				$url = $row['old_url'];
				if($row['save_get'] > 0) $save_get = true;
			}
		}else{
			return false;
		}

		if( $save_get )  $params = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
		    
		if (is_numeric($url)) {
			$url = $this->modx->makeUrl($url, '', $params, 'full');
		}else{
			if(!empty($params)) $url.="?".$params;
		}

		$this->modx->sendRedirect($url,0,'REDIRECT_HEADER','HTTP/1.1 '.$row['code'].' Moved Permanently');
	}

	function addAdminButton()
	{
		include_once(MODX_BASE_PATH.'assets/snippets/DocLister/lib/DLTemplate.class.php');
		global $content;
		$moduleurl = MODX_BASE_URL."assets/modules/evoRedirect/ajax/api.php?";

		$tpl = DLTemplate::getInstance($this->modx);
		$template = '@CODE:'.file_get_contents(dirname(__FILE__).'/../tpl/button.tpl');
		return $tpl->parseChunk($template,array('config'=>json_encode($content, true), "moduleName" => "evoRedirect", "moduleurl"=>$moduleurl  ) );
	}

}