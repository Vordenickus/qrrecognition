<?php

namespace Volochaev\Qrcode;

use Zxing\Common\HybridBinarizer;
use Zxing\IMagickLuminanceSource;
use Zxing\BinaryBitmap;
use Zxing\Qrcode\QRCodeReader;

class PlayGround
{
	protected string $source;

	public function __construct(string $source)
	{
		$this->source = $source;
	}


	public function play()
	{
		$im = new \Imagick();
		$im->readImage($this->source);
		$width = $im->getImageWidth();
		$heigth = $im->getImageHeight();
		$source = new IMagickLuminanceSource($im, $width, $heigth);
		$histo = new HybridBinarizer($source);
		$bitmap = new BinaryBitmap($histo);
		$reader = new QRCodeReader();
		$res = $reader->decode($bitmap, null)->toString();
		var_dump($res);
	}


	protected function print(string $str)
	{
		print($str . PHP_EOL);
	}
}
