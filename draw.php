<?php

class draw { // why so static..?
	private static $base = 'tmaj/block/';

	static public function __callStatic($name, $arguments) {
		$f = self::$base.$name.'.php';
		if (file_exists($f)) {
			ob_start();
			include $f;
			echo preg_replace(array('/\s{2,}/', '/[\t\n\r]/'), array(' ', ''), ob_get_clean()).PHP_EOL;
		}
	}
}
