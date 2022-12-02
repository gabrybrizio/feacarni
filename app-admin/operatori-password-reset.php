<?php 
require __DIR__ . '/../app/init.php';

$recover_feedback = "";
$username = Escape::html(get("username"));

if(r::is('post') && get('resetPassword') && csrf(get('anticsrf'))) {

	// I campi devono essere valorizzati
	if($username != ""){

		// Ricerco l'operatore
		$operatore = db::table('operatori')->where("username", "=", $username)->first();	
		
		if($operatore){

			// Recupero i data 
			$data = module::dataOf($operatore);
			$failure = (int)$operatore->login_attempt();
			
			if($failure > 4){
				// Operatore bannato
				$recover_feedback = "error";
			}else{			

				// Creo nuovo token richiesta su operatori_password_reset
				$ticket = util::GUID();

				$richiesta_reset = array(
					'GUID' => $ticket,
					'username' => $username,
					'richiesta' => time(),
				);

				$insert = db::table("operatori_password_reset")->insert($richiesta_reset);
				if(v::num($insert)) $insert = true;

				if($insert){
					// Invio email conferma
					$mail = new Mail();
					$mail->setFrom(site::get('email'), site::get('ragione-sociale'));
					$mail->addAddress(a::data('username'),a::data('cognome') . ' ' .a::data('nome'));
					$mail->Subject = site::get('ragione-sociale') . ' - Resetta la tua password';

					$email_ctaLink = url::to(util::adminUrl() . "operatori-password-reset/$ticket/");
					$email_ctaTitle = 'Resetta password';
					$email_azienda = site::get('ragione-sociale');

					$email_title =a::data('cognome') . ' ' .a::data('nome');
					$email_text = "Hai richiesto il reset della tua password. Se non hai fatto tu questa richiesta, verifica di poter accedere al sito <b>$email_azienda</b>.<br><br>Se hai richiesto la modifica della password, confermalo qui entro 24 ore.";
					$mail->setBody($email_title, $email_text, $email_ctaLink, $email_ctaTitle);

					if(!$mail->send()){
						// Problemi con invio email
						$recover_feedback = "email_problem";
						//echo 'Mailer Error: ' . $mail->ErrorInfo;
					} else {
						$recover_feedback = "ok";
					}

				}

			}				
			
		}else{
			$recover_feedback = "error";
		}
	
	}else{
		$recover_feedback = "incomplete";
	}
    
}

?> 

<!DOCTYPE HTML>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= site::get('ragione-sociale');?> - Reset password</title>
    
	<?php include 'inc/assets.php' ?>
    
</head>
<body>

	<div class="login-wrapper">
		<div class="login">   
			<div class="t-center">
				<img src="assets/img/logo.png" alt=""/>
			</div>

			<div class="login-box">
			
				<h2>Reset password</h2>
			
				<form method="post">
					<p>
						Inserisci la tua Username, riceverai un'email di conferma.
					</p>
					
					<?php
						 brick::field("hidden", "name:anticsrf", "value:" . csrf());
						 brick::field("text", "label:Username", "value:" . $username, "class:full", "autofocus");
					?>

					<field class="full">
						<?php if($recover_feedback == "ok"): ?>
							<div class="alert success">Ti è stata inviata un'email di conferma. Controlla la tua casella di posta.</div>						
						<?php elseif($recover_feedback == "incomplete"): ?>
							<div class="alert">Inserire la username.</div>
						<?php elseif($recover_feedback == "no_update"): ?>	
							<div class="alert">Si sono verificati dei problemi. Riprovare più tardi.</div>										
						<?php elseif($recover_feedback == "error"): ?>	
							<div class="alert">L'operatore non esiste o è stato disabilitato. Contattare gli amministratori.</div>																	
						<?php endif ?>
					</field> 									
					
					<div class="mt-16 t-center">
						<?=  brick::btn("icon:refresh-ccw", "type:submit", "name:resetPassword", "value:resetPassword", "text:Reset password", "formnovalidate") ?>
					</div>
					
					<div class="mt-16 t-center">
						<small>
					  		<a href="index.php" class="t-underline">Indietro</a>
					  	</small>
					</div>				
									
				</form>            
			</div>
			<div class="login-footer">
				© <?= date("Y")?> <?= site::get('ragione-sociale');?> <br>
				<a class="important" href="mailto:<?= site::get('email');?>"><?= site::get('email');?></a>
			</div>
		</div>
	</div>
		
</body>
</html>