<?php 
require __DIR__ . '/../app/init.php';

$ticket = get('ticket');

if(util::isGUID($ticket)){

		// Ricerco ticket
		$ticket_record = db::table('operatori_password_reset')->where("GUID", "=", $ticket)->first();	
		
		if($ticket_record){

			// Recupero i data richiesta
			$richiesta = $ticket_record->richiesta();
			
			if((time() - $richiesta) > 86400){
				// Richiesta fuori tempo massimo
				$recover_feedback = "error";
			}else{			

				// Recupero dati Operatore
				$username = $ticket_record->username();
				$operatore = db::table('operatori')->where("username", "=", $username)->first();

				// Elimino il ticket (bug gmail doppio invio)
				db::table('operatori_password_reset')->where("GUID", "=", $ticket)->delete();

				if($operatore){

					// Recupero i data operatori
					$data = module::dataOf($operatore);

					// Creo nuova password
					$newPassword = util::generatePassword();

					$data['nome'] = "test";
					$data['password'] = password::hash($newPassword);

					$update_user_password = db::table('operatori')->where('GUID', '=', $operatore->GUID())->update(array('data' => a::json($data)));
					
					if($update_user_password){
						$recover_feedback = "ok";
					}else{
						$recover_feedback = "error";
					}
				
				}else{
					$recover_feedback = "error";
				}
			}				
			
		}else{
			$recover_feedback = "error";
		}
}

?> 

<!DOCTYPE HTML>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=site::get('ragione-sociale') ?> - Reset password</title>
    
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

					<p class="t-center">
						<?php if($recover_feedback == "ok"): ?>
							Ecco la nuova password:<br>
							<b><?= $newPassword ?></b>															
						<?php elseif($recover_feedback == "error"): ?>	
							Si sono verificati dei problemi o la richiesta è scaduta.																	
						<?php endif ?>
					</p>									
					
					<div class="mt-16 t-center">
						<?=  brick::btn("icon:user", "text:Login", "click:location.href='" . util::adminUrl() . "'") ?>
					</div>
					          
			</div>
			<div class="login-footer">
				© <?= date("Y")?> <?= site::get('ragione-sociale');?> <br>
				<a class="important" href="mailto:<?= site::get('email');?>"><?= site::get('email');?></a>
			</div>
		</div>
	</div>
		
</body>
</html>