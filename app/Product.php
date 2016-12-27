<?php

namespace App;

use Dompdf\Dompdf;

class Product
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $model;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $actionPrice;

    /**
     * @var array
     */
    public $sizes = [];

    /**
     * @var array
     */
    public $images = [];

    /**
     * @var string
     */
    public $manufacturer;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Dompdf
     */
    private $pdf;

    /**
     * @var string
     */
    private $filename;

    public function isValid():bool
    {
        return (isset($this->name) && !empty($this->name));
    }

    public function render()
    {
        $this->pdf = new Dompdf();
        $this->pdf->loadHtml($this->getHtmlTemplate());
        $this->pdf->render();
    }

    // uloz PDF subor
    public function savePdf()
    {
        $output = $this->pdf->output();
        $this->filename = uniqid() . '.pdf';
        $path = __DIR__ . '/../public/files/'.$this->filename;
        file_put_contents($path, $output);
    }

    // vrat link pre stiahnutie PDF suboru
    public function getDownloadLink():string
    {
        return 'http://'.$_SERVER['HTTP_HOST'] .'/'. $this->filename;
    }

    private function getHtmlTemplate():string
    {
        ob_start();
        $product = $this; // pouzite v sablone
        include 'template.php';
        $html = ob_get_clean();
        return $html;

    }

}