<?php

class EvoRedirectProps extends EvoRedirect
{

	/**
	 * Получение подробностей о редиректе по его ID
	 * @param integer $id [Идентификатор редиректа]
	 */
	public function getProps($id=0)
	{
		header('Content-Type: application/json');
		$arr_rows = array();
		$result = $this->modx->db->select("save_get,active,search_get,id,old_url,new_url,code", $this->table,  " `id`=".$id);

    	if( $this->modx->db->getRecordCount( $result ) >= 1 ) {
			$res = $this->modx->db->makeArray( $result )[0];
    	}else{
    		$res['error'] = '';
    	}

    	$res['old_url_link'] = false;
		if(is_numeric($res['old_url'])){
			$res['old_url_link'] = $this->modx->makeUrl($res['old_url']);
		}

		die(json_encode($res, true));
	}

	/**
	 * Обновление подробностей о редиректе
	 * @param array $fields [Массив с полями редиректа]
	 */
	public function setProps($fields = array())
	{
		$write = array();
		foreach ($fields as $key => $value) {
			if ($key != 'id' && in_array($key, $this->fields)) {
				$write[$key] = $value;
			}else{
				$id = $value;
			}
		}

		$result = $this->modx->db->update( $write, $this->table, 'id = "' . $id . '"' );
		
		$write['old_url_link'] = false;
		if(is_numeric($write['old_url'])){
			$write['old_url_link'] = $this->modx->makeUrl($write['old_url']);
		}
		$write['id'] = $id;

		return $write;	
	}

}