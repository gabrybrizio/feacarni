<?php 

require('../app/init.php');

if(r::is('post') && get('login') && csrf(get('anticsrf'))) {
   
    $error = true;
	
	// CERCO NELLA TABELLA OPERATORI
	$user = db::table('operatori')->where("GUID", "=", get("guid"))->first();		
    
    if($user){
		$data = module::dataOf($user);
		
		s::set('logged', true);
		s::set('role',a::data('ruolo'));
		s::set('id', $user->GUID);
		s::set('name',a::data('cognome') . ' ' .a::data('nome'));
		go(util::adminUrl() . config::landing());
		die();
	}	
    
}else{
    $error = false;
}

?> 

<!DOCTYPE HTML>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= site::get('ragione-sociale');?> - Impersonate</title>
     
	<?php include 'inc/assets.php' ?>

</head>
<body>
	
	<div class="login-wrapper">
		<div class="login">
			<div class="t-center">
				<img src="assets/img/logo.png" alt=""/>
			</div>

			<div class="login-box">
			
				<h2>Impersonate</h2>
			
				<form method="post">
					<?php
						 brick::field("hidden", "name:anticsrf", "value:" . csrf());
						
						 brick::field("text", "label:GUID", "class:full", "autofocus");						
					?>
					
					<?php if($error): ?>
						<field class="full">
							<div class="alert">GUID non valido. Riprova, grazie.</div>
						</field>
					<?php endif ?>                

					<div class="login-actions">
						<?=  brick::btn("icon:log-in", "type:submit", "name:login", "value:login", "text:Accedi", "formnovalidate") ?>
					</div>				
				</form>            
			</div>
			<div class="login-footer">
				© <?= date("Y") . " " .  site::get('ragione-sociale');?><br>
				<a class="important" href="mailto:<?= site::get('email');?>"><?= site::get('email');?></a>
			</div>
		</div>
	</div>
		
</body>
 	
</html>
