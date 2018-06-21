<?php

namespace Services;

use \mPDF;

Class PDF extends BaseService
{

    private $mpdf;
    private $tmp;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->tmp =  __DIR__ . '/../../var/tmp/';


    }

    public function render($views, $filename="file.pdf", $toBrowser=true)
    {
        $this->setInit();
        //$this->renderToBrowser(, $filename);
        $this->mpdf->WriteHTML(\Template::instance()->render($views));
        $this->mpdf->Output($this->tmp . $filename);
        if ($toBrowser) {
            header("Content-type:application/pdf");
            header("Content-Disposition:attachment;filename='$filename'");
            echo file_get_contents($this->tmp . $filename);
        }
    }

    protected function setInit()
    {
        $this->mpdf  = new mPDF();
        //$stylesheet = file_get_contents( __DIR__ . '/../public/assets/css/style.css');
        //$this->mpdf->WriteHTML($stylesheet,1);
    }

}