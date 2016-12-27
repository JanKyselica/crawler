<?php
require __DIR__ . '/../vendor/autoload.php';

// ak je zadana URI zobraz PDF subor
$uri = $_SERVER['REQUEST_URI'];
if(isset($uri) && $uri != '/') {
    $path = "files/".$uri;

    if(!file_exists($path)) {
        echo "Subor neexistuje!";
        exit;
    }

    $filename = "file.pdf";
    header('Content-Length: ' . filesize($path));
    header('Content-Encoding: none');
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename=' . $filename);
    readfile($path);
}


// prehladaj stranku a pokus sa najst pozadovane vlastnosti
use App\Crawler;
$crawler = new Crawler();
$crawler->parse();
$crawler->renderPdf();
echo $crawler->getHref(); // zobraz URL link na stiahnutie PDF suboru