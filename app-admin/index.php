<?php
require __DIR__ . '/../app/init.php';

if(config::maintenance()) go("/maintenance/"); 

$login_fedback = "";
$username = Escape::html(get("username"));
$password = get("password");
$anticsrf = get("anticsrf");

// se sono già loggato
if(user::isAdmin()) {
	go(util::adminUrl() . config::landing());
	die();
}

if(r::is('post') && get('login') && csrf($anticsrf)) {

	// I campi devono essere valorizzati
	if($username != "" && $password != ""){
	
		// Ricerco l'Operatore
		$user = db::table('operatori')->where("username", "=", $username)->first();	
		
		if($user){
	
			// Recupero i data 
			$data = module::dataOf($user);
			$failure = (int)$user->login_attempt();

			if($failure > 4){
				// Operatore bannato
				$login_fedback = "banned";
			}else{
				// Operatore non bannato, controllo la password
				if(password::match($password,a::data('password'))){

					// Login OK
					s::set('logged', true);
					s::set('role',a::data('ruolo'));
					s::set('id', $user->GUID());
					s::set('username', $user->username());
					s::set('name',a::data('cognome') . ' ' .a::data('nome'));

					// Azzero login_attempt
					$update_login_attempt = db::table('operatori')->where('GUID', '=', $user->GUID())->update(array('login_attempt' => 0));

					if($update_login_attempt){
						go(util::adminUrl() . config::landing());
						die();
					}

				}else{
					// Login KO
					$login_fedback = "error";
					$failure ++;
					$update_login_attempt = db::table('operatori')->where('GUID', '=', $user->GUID())->update(array('login_attempt' => $failure));
				}
			}

		}else{
			$login_fedback = "error";
		}
	
	}else{
		$login_fedback = "incomplete";
	}
    
}

?> 
<!DOCTYPE HTML>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= site::get('ragione-sociale');?> - Accesso</title>
     
	<?php include 'inc/assets.php' ?>

</head>
<body>
	<div class="login-wrapper">
		<div class="login">
			<div class="t-center">
				<img src="assets/img/logo.png" alt=""/>
			</div>

			<div class="login-box">
			
				<h2>Login</h2>
			
				<form method="post">
					<?php
						 brick::field("hidden", "name:anticsrf", "value:" . csrf());
						
						 brick::field("text", "label:Username", "value:" . $username, "class:full", "autofocus");
						 brick::field("password", "label:Password", "class:full");
					?>

					<field class="full">
						<?php
							switch ($login_fedback) {
								case "incomplete":
									echo '<div class="alert">Tutti i campi sono obbligatori.</div>';
								 break;
								case "error":
									echo '<div class="alert">Username o Password non valide. Riprovare, grazie.</div>';
								 break;
								case "banned":
									echo '<div class="alert">L\'utenza è stata disabilitata. Contattare gli amministratori.</div>';
								 break;
						  	}
						?>
					</field>                

					<div class="login-actions">
						<?=  brick::btn("icon:log-in", "type:submit", "name:login", "value:login", "text:Accedi", "formnovalidate") ?>
					</div>

					<div class="login-actions">
						<small>
					  		<a href="operatori-password-reset.php" class="t-underline">Password dimenticata?</a>
					  	</small>
					</div>				
				</form>            
			</div>
			<div class="login-footer">
				© <?= date("Y") . " " .  site::get('ragione-sociale');?><br>
				<a class="important" href="mailto:<?= site::get('email');?>"><?= site::get('email');?></a>
			</div>
		</div>
	</div>

	<script src="<?= util::adminUrl() ?>assets/js/fn.min.js?<?=config::version()?>"></script>
</body>
 	
</html>