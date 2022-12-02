<?php 
	
	require 'inc/menu.php';
	s::set('modulo', "operatori");
	
	// Accesso a Admin o a se stesso
	if(!user::isAdmin() && get('e') != s::get("id")){
		util::jumpTo('/');
		die();
	}
		
	require 'inc/detail-data.php';

?>
  
<h1>Dettaglio operatore</h1>

<form method="post" enctype="multipart/form-data">
	
	<!-- DATI -->
	<div class="box">
		<?php 			
			 brick::field("text", "label:Cognome", "class:half");
			 brick::field("text", "label:Nome", "class:half");
			brick::br();
			 brick::field("email", "label:Username", "class:half", "required");
			 brick::field("password", "label:Password", "class:half", "required");
			
			if(user::isAdmin()){
				 brick::field("choice", "label:Ruolo", "class:half", "choices:Admin,User", "required");
			}
		?>
	</div>	

	<!-- CONTROLS -->
	<?php require 'inc/detail-controls.php'; ?>		

</form>

<?php 

	require 'inc/footer.php';

	if(s::get('saving')) {
		// Controllo che lo Username non sia già stata usato
		$usernames = module::listOf('operatori')->where("username", "=", a::get($data,'username'))->andWhere("GUID", "<>", get('e'))->all();

		if($usernames->count() > 0){
			s::set('errors', true);
			?>
			<script>
				document.querySelector("[name=username]").closest("field").classList.add("error");
			</script>
			<?php
			s::set('errors_msg', "Lo username scelto è già utilizzata. Provane un altro.");
		}
		
		// Recupero il ruolo che gli user non vedono non vedono
		if(user::role('User')){
			$data['ruolo'] = a::get($el,"ruolo");
		}			

		// SCRIVO A DB I DATA + I CAMPI CHE SERVONO PER L'ORDINAMENTO
		$new_data = array(
			'GUID' => s::get('el__ID'),
			'username' =>a::data('username'),
			'nome' =>a::data('nome'),
			'cognome' =>a::data('cognome'),
			'data' => a::json($data),
		);
	}

	require 'inc/detail-save.php'; 
 
 ?>
