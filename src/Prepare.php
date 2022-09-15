<?php

namespace Volochaev\Qrcode;

use GdImage;

class Prepare
{

	protected array $data;
	protected GdImage $img;
	protected GdImage $imgCropped;

	public function __construct(string $path, string $img)
	{
		$json = $this->parseJson($path);
		$this->data = json_decode($json);
		$this->img = imagecreatefromjpeg($img);
	}


	public function getCropped()
	{
		$coords = $this->getQrCoordinates();
		$imgs = [];
		foreach ($coords as $coord) {
			$imgs[] = imagecrop($this->img, $coord);
		}
		return $imgs;
	}


	protected function parseCoordinates($qrCoordinates)
	{
		$w = imagesx($this->img);
		$h = imagesy($this->img);

		$x1 = $qrCoordinates->x_min * $w;
		$y1 = $qrCoordinates->y_min * $h;
		$x2 = $qrCoordinates->x_max * $w;
		$y2 = $qrCoordinates->y_max * $h;

		return [
			'x' => $x1,
			'y' => $y1,
			'width' => abs($x1 - $x2),
			'height' => abs($y1 - $y2)
		];

	}


	protected function getQrCoordinates($minConf = 0) {
		$filtered = array_filter($this->data, static function($item) use ($minConf) {
			if ($minConf && $item->conf < $minConf) {
				return false;
			}
			return $item->label === 'qr';
		});
		$qrs = [];
		foreach ($filtered as $obj) {
			if ($obj->conf > $minConf) {
				$minConf = $obj->conf;
				$qrs[] = $this->parseCoordinates($obj->normalized_box);
			}
		}
		return $qrs;
	}


	protected function parseJson(string $path)
	{
		$size = filesize($path);
		$stream = fopen($path, 'r');
		$string = '';
		try {
			for ($curStep = 0, $step = 128; $curStep < $size; $curStep += $step) {
				$string .= fread($stream, $step);
			}
		} finally {
			fclose($stream);
		}
		return $string;
	}
}
