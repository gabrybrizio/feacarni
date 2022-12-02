<?php 
	
	require 'inc/menu.php';
	s::set('modulo', "azienda");
	
	require 'inc/detail-data.php';
	
?>
  
<h1>Dati aziendali</h1>

<form method="post" enctype="multipart/form-data">
	
	<!-- DATI -->
	<div class="box">
		<?php		
			 brick::field("text", "label:Ragione sociale");
			 brick::field("date", "label:Prossima consegna", "class:half");
			 brick::field("text", "label:Descrizione", "class:full");
			brick::br();
			 brick::field("number", "label:Partita IVA", "class:half");
			 brick::field("text", "label:Codice Fiscale", "class:half");			
			 brick::field("text", "label:Capitale Sociale", "class:half");
			 brick::field("text", "label:Codice REA", "class:half");
			brick::br();
			 brick::field("text", "label:IBAN", "class:half");
			 brick::field("text", "label:Intestatario IBAN", "class:half");
		?>			
	</div>
	
	<h2>Sede</h2>
	<div class="box">
		<?php
			 brick::field("text", "label:Indirizzo");
			 brick::field("text", "label:Città", "class:half");
			 brick::field("text", "label:Provincia", "class:half");
			 brick::field("number", "label:CAP", "class:half");
			brick::br();
			 brick::field("telephone", "label:Telefono");
			 brick::field("telephone", "label:Fax");
			brick::br();
			 brick::field("email", "label:Email");
			 brick::field("email", "label:PEC");			
		?>
	</div>	
	
	<h2>Mappa</h2>
	<div class="box">
		<?php
			 brick::field("number", "label:Latitudine");
			 brick::field("number", "label:Longitudine");
		?>
	</div>
	
	<h2>Social</h2>
	<div class="box">
		<?php
			 brick::field("url", "label:Sito internet");
			 brick::field("text", "label:Codice Google Analytics", "placeholder:UA-XXXXXXXX-X");
			brick::br();
			 brick::field("url", "label:Facebook");
			 brick::field("url", "label:Twitter");
			 brick::field("url", "label:Youtube");
		?>
	</div>
	
	<!-- CONTROLS -->
	<?php require 'inc/detail-controls.php'; ?>		

</form>

<?php 

	require 'inc/footer.php'; 

	// SCRIVO A DB I DATA + I CAMPI CHE SERVONO PER L'ORDINAMENTO
	$new_data = array(
		'GUID' => s::get('el__ID'),
		'data' => a::json($data)
	);

	require 'inc/detail-save.php'; 

 ?>
