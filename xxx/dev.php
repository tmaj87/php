<?php
//require_once '../bb/db.php'; -> $dbh

class dev {
	private function wol() {
		$url = 'http://www.depicus.com/wake-on-lan/woli.aspx?m=D43D7E334913&i=91.142.192.203&s=255.255.255.255&p=2839';
		$h = fopen($url, 'r');
	}
	
	static public function getOsVersion() {
		$str = $_SERVER['HTTP_USER_AGENT'];
//		$tmpArr = split(" ", $str);
		$os = 'unknown';
		if (stristr($str, "windows")) {
			$os = 'Windows';
		} elseif (stristr($str, "linux")) {
			$os = 'Linux';
		} elseif (stristr($str, "mac os")) {
			$os = 'Mac';
		}
		return $os;
	}
	
	static public function getBrowser() {
//		$browser = $_SERVER['HTTP_USER_AGENT'];
//		$tmpArr = split(" ", $browser);
//		d($tmpArr);
//		return $tmpArr[count($tmpArr)-1];
		include_once 'tmaj/browser_detection.php';
		$a_browser_data = browser_detection('full');
		$dict = array(
//			'gecko' => 'Firefox',
//			'msie' => 'Internet Explorer',
//			'chrome' => 'Chrome'
		);
		return array(strtr($a_browser_data[7], $dict), $a_browser_data[9]);
	}
	
	static public function passGen($length = 8, $count = 5, $charset = 2) {
		// $charset = 0  small letters
		// $charset = 1  + big letters
		// $charset = 2  + numbers
		// $charset = 3  + special chars
		if(!is_int($length) || !is_int($count) || !is_int($charset))
			return 0;
		if($length < 6 || $length > 32 || $count < 1 || $count > 32 || $charset < 0 || $charset > 3)
			return 0;

		for($i = 0; $i < $count; $i++) {
			$pass[$i] = '';
			for($j = 0; $j < $length; $j++) {
				switch($charset) {
					case 0 : // 3/3
						$rnd = rand(0, 2);
						break;

					case 1 : // 2/5
						$rnd = rand(0, 4);
						break;

					case 2 : // 1/6
						$rnd = rand(0, 5);
						break;

					case 3 : // 1/7
						$rnd = rand(0, 6);
						break;
				}
				if($rnd < 3) {
					// small letters
					$pass[$i] .= chr(rand(97, 122));
				} elseif($rnd < 5) {
					// big letters
					$pass[$i] .= chr(rand(65, 90));
				} elseif($rnd < 6) {
					// numbers
					$pass[$i] .= rand(0, 9);
				} else {
					// special chars
					$dictionary = '!?().,;:-_^';
					$pass[$i] .= substr($dictionary, rand(0, strlen($dictionary)-1), 1);
				}
			}
		}
		return $pass;
	}
	
	static public function counter() {
		$site = $_SERVER['SCRIPT_NAME'];
//		$debugmode = 0;

//		if($debugmode) {
//			if($_GET['page'] == 'erase')
//				$_SESSION['erase'] = 1;
//		}
		$host = 'mysql.cba.pl';
		$user = 'skUU7admiN';
		$pass = 'str33tWe4r';

		$dbname = 'k4_cba_pl';
		$dbtable = 'counter';
		$logtable = 'counter_log';

		$dblink = mysql_connect($host, $user, $pass)
			or die(mysql_error());
		mysql_select_db($dbname);

		$dbquery = "CREATE TABLE IF NOT EXISTS `$dbtable` (`id` INT NOT NULL AUTO_INCREMENT, `site` CHAR(255), `count` INT UNSIGNED, PRIMARY KEY(`id`))";
		$dbresult = mysql_query($dbquery);
		$dbquery = "CREATE TABLE IF NOT EXISTS `$logtable` (`id` INT NOT NULL AUTO_INCREMENT, `ip` CHAR(15), `site` CHAR(255), `timestamp` INT UNSIGNED, PRIMARY KEY(`id`))";
		$dbresult = mysql_query($dbquery);

		if($_SERVER['HTTP_X_FORWARDED_FOR']) {
			$logip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
			$logip = $_SERVER['REMOTE_ADDR'];

		$dbquery = "SELECT `timestamp` FROM `$logtable` WHERE `ip`='$logip', `site`='$site' ORDER BY `id` DESC LIMIT 1";
		$dbresult = mysql_query($dbquery);
		if($dbresult) {
			$row = mysql_fetch_row($dbresult);
		} else
			$row[0] = 0;
		if(empty($_SESSION['added']) && $row[0]+(15*60) < time()) {
			$dbquery = "SELECT `count` FROM `$dbtable` WHERE `site`='$site'";
			$dbresult = mysql_query($dbquery);
			if(mysql_num_rows($dbresult) == 1) {
				$row = mysql_fetch_row($dbresult);
				$dbquery = "UPDATE `$dbtable` SET `count`='".($row[0]+1)."' WHERE `site`='$site'";
			} elseif(mysql_num_rows($dbresult)) {
				$int = 0;
				while($row = mysql_fetch_row($dbresult)) {
					$int += $row[0];
				}
				$dbquery = "DELETE FROM `$dbtable` WHERE `site`='$site'";
				$dbresult = mysql_query($dbquery);
				$dbquery = "INSERT INTO `$dbtable` (`id`, `site`, `count`) VALUES ('', '$site', '$int')";
			} else {
				$dbquery = "INSERT INTO `$dbtable` (`id`, `site`, `count`) VALUES ('', '$site', '1')";
			}
			$dbresult = mysql_query($dbquery);
			// log part
			$dbquery = "INSERT INTO `$logtable` (`id`, `ip`, `site`, `timestamp`) VALUES ('', '$logip', '$site', '".time()."')";
			$dbresult = mysql_query($dbquery);
			// end of log part
			$_SESSION['added'] = 1;
		}

//		if($debugmode) {
//			if(isset($_SESSION['erase'])) {
//				$dbquery = "DROP TABLE IF EXISTS `$dbtable`";
//				$dbresult = mysql_query($dbquery);
//				$dbquery = "DROP TABLE IF EXISTS `$logtable`";
//				$dbresult = mysql_query($dbquery);
//				unset($_SESSION['erase']);
//				unset($_SESSION['added']);
//			}
//		}

		$dbquery = "SELECT `count` FROM `$dbtable` WHERE `site`='$site'";
		$dbresult = mysql_query($dbquery);
		$row = mysql_fetch_row($dbresult);
		echo $row[0];

		mysql_close($dblink);

//		if($debugmode)
//			echo '<p><a href="?page=erase">erase</a> <a href="?">refresh</a></p>';
	}
}
