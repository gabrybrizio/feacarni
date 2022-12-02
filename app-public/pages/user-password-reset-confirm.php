<?php 
    require __DIR__ . '/../../app/init.php';

$ticket = get('ticket');

if(util::isGUID($ticket)){

		// Ricerco ticket
		$ticket_record = db::table('utenti_password_reset')->where("GUID", "=", $ticket)->first();	
		
		if($ticket_record){

			// Recupero i data richiesta
			$richiesta = $ticket_record->richiesta();
			
			if((time() - $richiesta) > 86400){
				// Richiesta fuori tempo massimo
				$recover_feedback = "error";
			}else{			

				// Recupero dati Utente
				$username = $ticket_record->username();
				$user = db::table('utenti')->where("username", "=", $username)->first();

				// Elimino il ticket (bug gmail doppio invio)
				db::table('utenti_password_reset')->where("GUID", "=", $ticket)->delete();

				if($user){

					// Recupero i data utente
					$data = module::dataOf($user);

					// Creo nuova password
					$newPassword = util::generatePassword();
					$data['password'] = password::hash($newPassword);

					$update_user_password = db::table('utenti')->where('GUID', '=', $user->GUID())->update(array('data' => a::json($data)));
					
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

page::title(lang::get('password-reset'));

require __DIR__ . '/../inc/header.php';

?> 

<div class="container">

	<h1><?=lang::get('password-reset') ?></h1>

	<form method="post">
		<div class="form-login">

			<p class="t-center">
				<?php if($recover_feedback == "ok"): ?>
					Ecco la nuova password:<br>
					<b><?= $newPassword ?></b>																
				<?php elseif($recover_feedback == "error"): ?>	
					Si sono verificati dei problemi o la richiesta è scaduta.																	
				<?php endif ?>
			</p>			

			<div class="form-actions">
				<a class="btn" href="/accedi">LOGIN</a>
			</div>

		</div>				
	</form>

</div>

<?php 
require __DIR__ . '/../inc/footer.php';
?>