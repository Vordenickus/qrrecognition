<?php

require(__DIR__ . '/vendor/autoload.php');

use Volochaev\Qrcode\PlayGround;
use Volochaev\Qrcode\Prepare;
use Zxing\QrReader;

//$old = ini_set('memory_limit', '1024M');

setConfig();

$jsonPath = __DIR__ . '/input/test9.json';
$imgPath = __DIR__ . '/images/test9.jpg';
$time = microtime(true);

$pr = new Prepare($jsonPath, $imgPath);

$imgs = $pr->getCropped();

$count = 0;

foreach ($imgs as $img) {
	$count++;
	imagepng($img, "output/$count.png");
	$pg = new QrReader($img, QrReader::SOURCE_TYPE_RESOURCE, false);
	$res = $pg->text();
	var_dump($res);
	var_dump(microtime(true) - $time);
	imagedestroy($img);
	$time = microtime(true);
}

//ini_set('memory_limit', $old);

die();


function setConfig()
{
	error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
}
