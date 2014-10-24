<?php

class send {
	public function __construct() {
		require_once 'tmaj/phpmailer/PHPMailerAutoload.php';
		$this->send();
	}
	
	private function send() {
		$resp = recaptcha_check_answer(
				'6LcN0vASAAAAACM1_H4QZKjxd0UegfqMLuCstP7z',
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
			$this->ret('invalid recaptcha'); //, $resp->error);
		}
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->ret('invalid data', 'invalid email');
		}
		$msg = filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_STRING);
		if (empty($msg) || strlen($msg) < 11) {
			$this->ret('invalid data', 'too short message');
		}
		$mail = $this->prepare();
		$mail->Body = $email."\n\n".$msg;
		if(!$mail->send()) {
			$this->ret('couldn\'t send message'); //, $mail->ErrorInfo);
		}
		$this->ret('success');
	}
	
	private function prepare() {
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		$mail->Mailer = 'mail';
		$mail->From = 'noreplay@k4.cba.pl';
		$mail->FromName = '';
		$mail->addAddress('tmaj.pl@gmail.com', 'Tomasz Maj');
		$mail->WordWrap = 50;
		$mail->isHTML(false);
		$mail->Subject = 'Nowa wiadomość z tmaj.pl';
		return $mail;
	}
	
	private function ret($msg, $error = '') {
		echo json_encode(array('msg' => $msg, 'error' => $error));
		exit;
	}
}
