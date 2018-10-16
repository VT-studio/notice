<?php
class FileReader
{
    protected $handler = null;
    protected $fbuffer = array();

    /**
     * Конструктор класса, открывающий файл для работы
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        set_time_limit(0);

        if(!($this->handler = fopen($filename, "rb")))
            throw new Exception("Cannot open the file");
    }



    /**
     *
     *
     * @return string
     */
    public function ReadAll()
    {
        if(!$this->handler)
            throw new Exception("Invalid file pointer");

        while(!feof($this->handler))
            $this->fbuffer .= fgets($this->handler);

        return $this->fbuffer;
    }




    /**
     * Построчное чтение $count_line строк файла с учетом сдвига
     *
     * @param int  $count_line
     *
     * @return string
     */
    public function Read($count_line = 1)
    {
        if(isset($fbuffer))
            unset($fbuffer);

        $fbuffer = [];
        
        if(!$this->handler)
            throw new Exception("Invalid file pointer");

        while(!feof($this->handler))
        {
            $fbuffer[] = fgets($this->handler);
            $count_line--;
            if($count_line == 0) break;
        }

        return $fbuffer;
    }

    /**
     * Установить строку, с которой производить чтение файла
     *
     * @param int  $line
     */
    public function SetOffset($line = 0)
    {
        if(!$this->handler)
            throw new Exception("Invalid file pointer");

        while(!feof($this->handler) && $line--) {
            fgets($this->handler);
        }
    }
}
