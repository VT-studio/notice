<?php
require_once($dir . 'PHPExcel.php');
require_once($dir . 'PHPExcel/Writer/Excel5.php');



class modelExcel
{
    private $header;

    private $data;

    private $activeSheet = 0;

    private $dir = dir . 'xls/';

    private $filename = 'export';

    private $expansion = '.xlsx';

    private $writeType = 'Excel2007';

    private $sheetTitle = 'ExportTemplate';

    private $startRow = 2;

    private $rowName = [];
    /*
        $rowName = [
            'a' => 'Item Number',
            'b' => 'Description',
            'c' => 'Price'
        ]
        Array where Key it is a position cell, and Value it is a title cell
     */

    /*setting amazon*/

    /**
     * Amazon_custom_system_Model_Excel constructor.
     * @param array $header
     * @param array $data
     * @param array $setting
     * @param array $rowName
     * @var $setting['activeSheet'] string
     * @var $setting['dir'] string
     * @var $setting['filename'] string
     * @var $setting['expansion'] string
     * @var $setting['writeType'] string
     * @var $setting['sheetTitle'] string
     * @var $setting['startRow'] int
     */
    public function __construct($header = null, $data = null, $setting = null, $rowName = null)
    {
        $this->header = $header;

        $this->data = $data;

       if (isset($setting['activeSheet'])) $this->setActiveSheet($setting['activeSheet']);
       if (isset($setting['dir'])) $this->setDir($setting['dir']);
       if (isset($setting['filename'])) $this->setFileName($setting['filename']);
       if (isset($setting['expansion'])) $this->setExpansion($setting['expansion']);
       if (isset($setting['writeType'])) $this->setWriteType($setting['writeType']);
       if (isset($setting['sheetTitle'])) $this->setSheetTitle($setting['sheetTitle']);
       if (isset($setting['startRow'])) $this->setStartRow($setting['startRow']);
       if (isset($rowName)) $this->setRowName($rowName);
    }

    public function export()
    {
        $xls = new PHPExcel();
        $xls->setActiveSheetIndex($this->activeSheet);
        $sheet = $xls->getActiveSheet();
        $sheet->setTitle($this->sheetTitle);
        /*заглавные ячейки*/
        $i_h = 'A';
        foreach ($this->header as $header)
        {
            $sheet->setCellValue("{$i_h}1", $header);
            $i_h++;
        }

        $i = $this->startRow;
        foreach ($this->data as $data){
            $i_b = 'A';
            foreach ($data as $result)
            {
                $sheet->setCellValue("{$i_b}{$i}", $result);
                $i_b++;
            }
            $i++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($xls, $this->writeType);
        if ($objWriter->save($this->getFileName()))
            return $this->getFileName();

        return false;

    }

    public function import($wey)
    {
        if (empty($this->rowName)) return false;

        $Excel = PHPExcel_IOFactory::load($wey);

        $sheet = $Excel->getActiveSheet($this->activeSheet);
        $count = $sheet->getHighestRow();
        $i = $this->startRow;

        $result = [];
        $str = [];
        for ($i; $i <= $count; $i++){
            foreach ($this->rowName as $key => $value ){
                $str[$value] = $sheet->getCell($key.$i)->getValue();
            }
            $result[] = $str;
        }

        return $result;
    }


    /**
     * @return string
     */
    private function getFileName()
    {
        return $this->dir.$this->filename.$this->expansion;
    }


    private function setActiveSheet($value)
    {
        $this->activeSheet = $value;
    }

    private function setDir($value)
    {
        $this->dir = $value;
    }

    private function setFileName($value)
    {
        $this->filename = $value;
    }

    private function setExpansion($value)
    {
        $this->expansion = $value;
    }

    private function setWriteType($value)
    {
        $this->writeType = $value;
    }

    private function setSheetTitle($value)
    {
        $this->sheetTitle = $value;
    }

    private function setStartRow($value)
    {
        $this->startRow = $value;
    }

    private function setRowName($value)
    {
        if (isset($value[0])) return false;
        $this->rowName = $value;
    }
}
