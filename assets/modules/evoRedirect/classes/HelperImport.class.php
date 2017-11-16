<?php
/**
 * Класс для работы с файлами экспорта
 */
class HelperImport
{

    public $resp = array("error" => false, "text" => "", "status" => "server");
    public $file = array();
    public $lines = array();
    public $maxLines = 1000;
    public $lineSize = 10000;
    public $data = array();
    public $evoRedirect = [];
    public $fields = ["url", "short_url", "code", "save_get", "search_get", "active"];
    public $lost = array();

	function __construct($file, EvoRedirect $EvoRedirect, EvoRedirectHelper $EvoRedirectHelper)
	{
       
		if(empty($file) || empty($file["upload"] || empty($file["upload"]["type"]))){
            $this->resp["error"] = true;
            $this->resp["text"] = "Файл не найден";
            return  0;
        }
        if($file["upload"]["type"] != "text/csv"){
            $this->resp["error"] = true;
            $this->resp["text"] = "Не верный формат файла";
            return  0;
        }
        $this->evoRedirect = $EvoRedirect;
        $this->EvoRedirectHelper  = $EvoRedirectHelper;
        $this->file = $file["upload"];

	}

    /**
     * Создание редиректов из файла экспорта
     * @return void
     */
	public function createRedirects(){
        
        $parse = $this->csvToArray($this->file["tmp_name"], ";");
        if(!$this->resp["error"]){
            foreach ($this->lines as $line) {
              $this->data[] = $this->EvoRedirectHelper->makeRedirect($line);
            }
            $this->resp["data"] = $this->data;
            $this->resp["text"] = "Импорт завершен успеншо. Добавлено ".count($this->data)." записей";
            if(!empty($this->lost)){
                $this->resp["text"] .= ". Были пропущены записи ".implode($this->lost, ",");
            }
        }

    }

    /**
     * Подготавливаем массив для экспорта
     * @param  string $filename  имя файла csv
     * @param  string $delimiter разделитель
     * @return array
     */
    public function csvToArray($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $err = false;
        $first = true;
     
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if($first){
                    $first = false;
                }else{
                  $col = array_combine($this->fields, $row);
                  $err = $this->validateLine($col);
                  if(!$err){
                   $this->lines[] = $col;
                  }                  
                }
            }
            fclose($handle);
        }
        return $err;
    }

    /**
     * Проверка строки
     * @param  array  $line строка с редиректом
     * @return boolean прошла ли строка проверку
     */
    public function validateLine($line = array())
    {
       $error = false;

       if(count($line) != count($this->fields)){
        $error = true;
        $this->resp["error"] = true;
        $this->resp["text"] = "Количество столбцов должно быть равно ".count($this->fields).", а в файле ".count($line)." столбцов!";
        return $error;
       } 

        if (!empty($line['short_url'])) {
            if ($this->evoRedirect->checkCrc($line['short_url'])) {
                $uri = $line['short_url'];
            }
        }

        do{
            if (isset($bNew) || !isset($uri))
                $uri = "~".$this->evoRedirect->randString(5);

            $bNew = true;
            $uriCrc32 = $this->evoRedirect->makeCrc32($uri);

            $bNew = $this->evoRedirect->checkCrc($uriCrc32);
        }
        while (!$bNew);

        if ($uri != $line['short_url'] || empty($line['url']) || empty($line['short_url'])) {
            $this->lost[] = $line['short_url'];
            $error = true;
            return $error;
        }

    }
}