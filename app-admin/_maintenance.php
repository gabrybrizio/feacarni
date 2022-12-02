<?php 
require __DIR__ . '/../app/init.php';

?> 

<!DOCTYPE HTML>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= site::get('ragione-sociale');?> - Manutenzione in corso</title>
     
	<?php include 'inc/assets.php' ?>

</head>
<body>
	
	<div class="login-wrapper">
		<div class="login">
			<div class="t-center">
				<img src="<?= util::adminUrl() ?>assets/img/logo.png" alt=""/>
			</div>

			<div class="login-box t-center">
			
				<h2>Lavori in corso</h2>
			
				<p>
					Siamo momentaneamente offline per operazioni di mantenimento o aggiornamento del sistema.
				</p>  
				<p>
					Ci scusiamo per l'inconveniente, torneremo online a breve.<br><br>
				</p>
			</div>
			<div class="login-footer">
				© <?= date("Y") . " " .  site::get('ragione-sociale');?><br>
				<a class="important" href="mailto:<?= site::get('email');?>"><?= site::get('email');?></a>
			</div>
		</div>
	</div>
		
</body>
 	
</html>
