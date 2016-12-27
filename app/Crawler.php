<?php

namespace App;

use App\Product;

class Crawler
{
    /**
     * @var \simple_html_dom
     */
    private $html;

    /**
     * @var Product
     */
    private $product;


    public function __construct()
    {
        // kontrola POST parametru 'url'
        if(!isset($_POST['url'])) {
            echo "Chybajuci POST parameter 'url'!";
            exit;
        }

        $url = trim($_POST['url']);
        // kontrola validity URL adresy
        if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false) {
            echo "Zadana URL nie je validna";
            exit;
        }

        // vytvorenie DOM-u
        $this->html = new \simple_html_dom();
        $this->html->load_file($url);

        // vytvorenie prazdneho produktu
        $this->product = new Product;
    }

    /**
     * Naparsuje DOM a priradi pozadovane vlastnosti produktu
     * Nepouziva sa retazenie metod find, pretoze nie vzdy existuje rodic hladaneho elementu
     */
    public function parse()
    {
        // nazov
        $name = $this->html->find('.heading-title[itemprop=name]', 0);
        if(!is_null($name)) {
            $this->product->name = $name->plaintext;
        }

        // ulozim si div aby som vzdy nemusel prehladavat cely DOM
        $productDiv = $this->html->find('#product', 0);
        if(!is_null($productDiv)) {
            // model
            $modelRow = $productDiv->find('.row', 0);
            if (!is_null($modelRow)) {
                $modelSpan = $modelRow->find('span', 0);
                if (!is_null($modelSpan)) {
                    $model = $modelSpan->find('b', 0);
                    if (!is_null($model)) {
                        $this->product->model = trim($model->plaintext);
                    } else {
                        $this->product->model = trim($modelSpan->plaintext);

                    }
                }
            }

            $manufacturerDiv = $this->html->find('.manufacturer-product-detail', 0);

            $manufacturerImageDiv = $manufacturerDiv->find('.brand-image', 0);
            if (!is_null($manufacturerImageDiv)) {
                // vyrobca je obrazok
                $img = $manufacturerImageDiv->find('img', 0);
                if (!is_null($img)) {
                    $this->product->manufacturer = trim($img->alt);
                }
            } else {
                // vyrobca je odkaz
                $anchor = $manufacturerDiv->find('a', 0);
                if (!is_null($anchor)) {
                    $this->product->manufacturer = trim($anchor->plaintext);
                }
            }

            // ceny
            $newPrice = $productDiv->find('.price-new', 0);
            if(!is_null($newPrice)) {
                $this->product->actionPrice = trim($newPrice->plaintext);
            }
            $oldPrice = $productDiv->find('.price-old', 0);
            if(!is_null($oldPrice)) {
                $this->product->price = trim($oldPrice->plaintext);
            }

            // velkosti
            foreach($productDiv->find('.options') as $optionsDiv) {
                $label = $optionsDiv->find('.control-label', 0);
                // Tu je potrebne pridat lable z dalsich jazykovych mutacii
                // V HTML som nenasiel ziadny jednoznacny identifikator elementu.
                // Mozno je velkost vzdy posledna z dostupnych moznosti, len tam by bol problem, ze nie vzdy
                // tam ta velkost musi byt
                $labels = ['Veľkosť:', 'Velikost:'];
                if(!is_null($label) && in_array($label->plaintext, $labels)) {
                    foreach ($optionsDiv->find('option') as $option) {
                        if (isset($option->value) && $option->value != '') {
                            $this->product->sizes[$option->value] = trim($option->plaintext);
                        }
                    }
                }
            }

            // popis
            $description = $productDiv->find('.jq-description', 0);
            if (!is_null($description) && $description->style != "display: none;") {
                $this->product->description = trim($description->plaintext);
            }
        }

        // obrazky
        $gallery = $this->html->find('#product-gallery', 0);
        if (!is_null($gallery)) {
            foreach ($gallery->find('a') as $image) {
                $this->product->images[] = trim($image->href);
            }
        }
    }

    public function renderPdf()
    {
        // renderuj iba validny produkt, t.j. mal by mat zadany aspon nazov
        if($this->product->isValid()) {
            $this->product->render();
            $this->product->savePdf();
        } else {
            echo "Nenasiel som pozadovane vlastnosti! PDF subor nebol vygenerovany";
            exit;
        }

    }

    public function getHref():string
    {
       return $this->product->getDownloadLink();
    }


}