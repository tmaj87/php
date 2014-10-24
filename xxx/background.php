<?php

class background {
	public function __construct() {
		$this->png();
		exit;
	}

	public function png() {
		$png = imagecreatetruecolor(2000, 1920);
		imagesavealpha($png, true);
		imagefill($png, 0, 0, imagecolorallocatealpha($png, 0, 0, 0, 118));
		$str = '';
		for ($i=0;$i<43008;$i++) {
			$str .= rand(0, 1).' ';
		}
		foreach (str_split($str, 896) as $k => $v) {
			$r = rand(0, 127);
			$r2 = rand(100, 127);
			imagestring($png, 5, 0, (20*$k), $v, imagecolorallocatealpha($png, $r, $r, $r, $r2));
		}
		header("Content-type: image/png");
		imagepng($png);
	}
}
