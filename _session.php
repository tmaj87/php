<?php
session_start();

function d() {
	echo '<pre>';
	foreach (func_get_args() as $key => $val) {
		var_dump($val);
	}
	echo '</pre>';
}

class timer {
	private $host = 'mysql:dbname=database;host=127.0.0.1', $user = 'admin', $pass = 'pass';
	private static $seed = 'tiNRHu;M60QcHNoGjrMFLkSVKPTDRONu53zSb:2IYe2fkYVS.3v2c4J2runnyeTh';
	private static $table = 'tmaj_';
	private static $dbh = null;
	
	private static function connect() {
		try {
			//self::$dbh = new PDO($host, $user, $pass);
			self::$dbh = 1;
		} catch (PDOException $e) {
			self::error($e->getMessage());
		}
	}
	
	static function prepare($uid, $cid, $url) {
		self::is_connected();
		$hash = hash('sha256', $uid.self::$seed.$cid.time());
		
		//$sql = "INSERT INTO ".self::$table." (hash, _in, uid, cid, url) VALUES ($hash, ".time().", $uid, $cid, '$url')";
		//$result = $this->dbh->exec($sql);
		
		return $hash;
	}
	
	static function finish($hash) {
		self::is_connected();
		
		//$sql = "UPDATE ".self::$table." SET _out = ".time()." WHERE hash = $hash";
		//return $this->dbh->exec($sql);
		return 1;
	}
	
	private static function is_connected() {
		if (self::$dbh == null) {
			self::connect();
		}
	}
	
	private static function error($message) {
		if (empty($message)) {
			$message = 'error';
		}
		die($message);
	}
}

class packet {
	//private $id;
	private $time;
	private $data = array();
	//private static $current_id = 1;
	
	public function __construct() {
		//$this->id = self::$current_id++;
		//$this->id = 1;
		$this->time = time();
	}
	
	public function __get($name) {
		return !isset($this->data[$name])?:$this->data[$name];
	}
	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public function get_meta_tags() {
		//return array($this->id, $this->time);
		return array($this->time);
	}
}



isset($_SESSION['the_array'])?:$_SESSION['the_array']=array();

if (isset($_GET['1'])) {
	$count = count($_SESSION['the_array']);
	
	$packet = new packet();
	$packet->hash = timer::prepare(1, 1, 'http');
	
	$_SESSION['the_array'][$count] = $packet;
} elseif (isset($_GET['2'])) {
	// jeżeli hash istnieje w bazie, dopisz time() zamknięcia strony
	
	d($_SESSION['the_array']);
} elseif (isset($_GET['3'])) {
	unset($_SESSION['the_array']);
}