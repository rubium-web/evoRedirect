<?php
trait EvoRedirectTrait{

	/**
	 * Генерация рандомной строки
	 * @param  integer $pass_len   [Длина строки]
	 * @param  boolean $pass_chars [Используемые символы]
	 * @return string            [Сгенерированная строка]
	 */
	public function randString($char_len=10, $char_chars=false){
		static $allchars = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
		$string = "";
		if(is_array($char_chars))
		{
			while(strlen($string) < $char_len)
			{
				if(function_exists('shuffle'))
					shuffle($char_chars);
				foreach($char_chars as $chars)
				{
					$n = strlen($chars) - 1;
					$string .= $chars[mt_rand(0, $n)];
				}
			}
			if(strlen($string) > count($char_chars))
				$string = substr($string, 0, $char_len);
		}
		else
		{
			if($char_chars !== false)
			{
				$chars = $char_chars;
				$n = strlen($char_chars) - 1;
			}
			else
			{
				$chars = $allchars;
	            $n = 61; //strlen($allchars)-1;
	        }
	        for ($i = 0; $i < $char_len; $i++)
	        	$string .= $chars[mt_rand(0, $n)];
	    }
	    return $string;
	}

	/**
	 * Генерация makeCrc32
	 * @param int [makeCrc32]
	 */
	public static function makeCrc32($str){
		$c = crc32($str);
		if ($c > 0x7FFFFFFF)
			$c = -(0xFFFFFFFF - $c + 1);
		return $c;
	}

	/**
	 * Проверка на дубли короткого URI в базе
	 * @param  string $uriCrc32 [короткая ссылка]
	 * @return boolean
	 */
	public function checkCrc($uriCrc32){
		$result = $this->modx->db->select("short_uri_crc", $this->table,  "short_uri_crc=".$uriCrc32);
		if( $this->modx->db->getRecordCount( $result ) >= 1 ) {
			return false;
		}else{
			return true;
		}
	}
}