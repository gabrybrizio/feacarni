<?php 
    require __DIR__ . '/../../app/init.php';

$recover_feedback = "";
$username = escape::html(get("username"));

if(r::is('post') && get('resetPassword') && csrf(get('anticsrf'))) {

	// I campi devono essere valorizzati
	if($username != ""){

		// Ricerco lo user
		$user = db::table('utenti')->where("username", "=", $username)->first();	
		
		if($user){

			// Recupero i data 
			$data = module::dataOf($user);
			$failure = (int)$user->login_attempt();
			
			if($failure > config::login_failure_limit()){
				// Utente bannato
				$recover_feedback = "error";
			}else{			

				// Creo nuovo token richiesta su user_password_reset
				$ticket = util::GUID();

				$richiesta_reset = array(
					'GUID' => $ticket,
					'username' => $username,
					'richiesta' => time(),
				);

				$insert = db::table("utenti_password_reset")->insert($richiesta_reset);
				if(v::num($insert)) $insert = true;

				if($insert){
					// Invio email conferma
					$mail = new Mail();
					$mail->setFrom(site::get('email'), site::get('ragione-sociale'));
					$mail->addAddress(a::data('username'),a::data('cognome') . ' ' .a::data('nome'));

					$mail->Subject = site::get('ragione-sociale') . ' - Resetta la tua password';

					$email_azienda = site::get('ragione-sociale');
					$email_title =a::data('cognome') . ' ' .a::data('nome');
					$email_confirm_page = url::base() . '/password-reset-confirm/' . $ticket . '/';
					$email_text = lang::get('password-reset-request') . '<br><br>' . lang::get('password-reset-request-confirm');
					$mail->setBody($email_title, $email_text, $email_confirm_page, lang::get('password-reset'));

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

page::title(lang::get('password-reset'));

require __DIR__ . '/../inc/header.php';

?> 

<div class="container">

	<h1><?=lang::get('password-reset') ?></h1>

	<form method="post">
		<div class="form-login">

			<p>
				<?=lang::get('password-reset-email') ?>
			</p>

			<input name="anticsrf" value="<?= csrf() ?>" type="hidden">

			<div class="form-field is-full">
				<label><?=lang::get('account-email') ?>:</label>
				<input name="username" type="text" autofocus="autofocus" value="<?= $username ?>">
			</div>	

				<?php
					switch ($recover_feedback) {
						case "ok":
							echo '<div id="form_feedback" class="is-visible green">' . lang::get('password-reset-sent') . '</div>';
							break;
						case "incomplete":
							echo '<div id="form_feedback" class="is-visible red">' . lang::get('validation-email') . '</div>';
							break;
						case "no_update":
							echo '<div id="form_feedback" class="is-visible red">' . lang::get('warning-try-later') . '</div>';
							break;
						case "error":
							echo '<div id="form_feedback" class="is-visible red">' . lang::get('account-disabled') . '</div>';
							break;							 
					}
				?>			

			<?php
			  if($recover_feedback != "ok"):
			?>
			<div class="t-center">
				<?= brick::btn("type:submit", "name:resetPassword", "value:resetPassword", "text:" . lang::get('password-reset'), "formnovalidate") ?>
			</div>
			<?php
			  endif;
			?>

		</div>				
	</form>

</div>

<?php 
require __DIR__ . '/../inc/footer.php';
?>