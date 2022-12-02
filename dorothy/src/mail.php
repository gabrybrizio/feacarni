<?php

use PHPMailer\PHPMailer\PHPMailer;
class Mail extends PHPMailer {
	public function __construct() {
		parent::__construct(true);

		//$this->SMTPDebug = 2;
		$this->IsSMTP();
		$this->CharSet = 'UTF-8';
		$this->Host = config::mail_host();
		$this->SMTPAuth = true;
		$this->Username = config::mail_username();
		$this->Password =  config::mail_password();
		$this->SMTPSecure = 'ssl';
		$this->Port = 465;
	}

	public function send() {
		if (config::debug()=== true) {
			$this->clearAllRecipients();
			$this->addAddress(config::debug_email());
		}
		return parent::send();
	}

	public function setBody($title, $text, $ctaLink = '{{sito-internet}}', $ctaText = '', $ctaColor='#000000', $extraHtml='', $template = 'email-generic.php', $logo = '/img/logo-email.png') {
		$ragioneSociale = site::get('ragione-sociale');
		$logo = url::base() . $logo;
		$emailServizioClienti = site::get('email');
		$ctaLink = ($ctaLink === '{{sito-internet}}' ? url::base() : $ctaLink);

		$arr = [
			'email_azienda' => $ragioneSociale,
			'email_logo' => $logo,
			'email_title' => $title,
			'email_text' => $text,
			'email_cta_color' => $ctaColor,
			'email_cta_href' => $ctaLink,
			'email_cta_text' => $ctaText,
			'email_last_text' => '',
			'extra_html' => $extraHtml,
			'email_servizio_clienti' => $emailServizioClienti,
			'year' => date("Y")
		];
		$html = template::load($template, $arr);

		$this->Body = $html;
		$this->AltBody = str::unhtml($html);
	}
}