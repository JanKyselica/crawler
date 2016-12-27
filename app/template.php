<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div>Názov: <b><?php echo $product->name; ?></b></div>
<div>Model: <b><?php echo $product->model; ?></b></div>
<div>Cena po zľave: <b><?php echo $product->actionPrice; ?></b></div>
<div>Cena pred zľavou: <b><?php echo $product->price; ?></b></div>
<div>Veľkosti: </div>
<ul>
    <?php foreach($product->sizes as $size) { ?>
    <li><b><?php echo $size; ?></b></li>
    <?php } ?>
</ul>

<div>Obrázky:</div>
<ul>
    <?php foreach($product->images as $image) { ?>
        <li>
            <a href="<?php echo $image; ?>">
                <?php echo $image; ?>
            </a>
        </li>
    <?php } ?>
</ul>
<div>Popis: <b><?php echo $product->description; ?></b></div>
</body>