<?php

class controller {
	public function __construct() {
		if (isset($_GET['bg'])) {
			$b = new background;
		}
		$this->restrictBrowser();
		if (isset($_GET['send'])) {
			$s = new send;
		}
	}
	
	private function restrictBrowser() {
		$browser = dev::getBrowser();
		if (($browser[0] == 'msie' && $browser[1] < 9)
				|| ($browser[0] == 'gecko' && $browser[1] < 18)) {
			draw::badbrowser();
		}
	}
}