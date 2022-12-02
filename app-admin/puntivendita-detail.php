<?php

	require 'inc/menu.php';
	s::set('modulo', "puntivendita");

	if(!user::isAdmin()) util::jumpTo('/');

	require 'inc/detail-data.php';

?>

<h1>Dettaglio punto vendita</h1>

<form method="post" enctype="multipart/form-data">

	<!-- DATI -->
	<div class="box">
		<?php
			brick::field("text", "label:Ragione sociale", "name:denominazione");
			brick::br();
			brick::field("text", "label:Indirizzo");
			brick::br();
			brick::field("text", "label:Città", "class:half");
			brick::field("number", "label:CAP", "class:half");
		?>
	</div>

	<h2>Mappa</h2>
	<div class="box">
		<?php
			brick::field("number", "label:Latitudine");
			brick::field("number", "label:Longitudine");
		?>
	</div>	

	<!-- CONTROLS -->
	<?php require 'inc/detail-controls.php'; ?>

</form>

<?php

	require 'inc/footer.php';

	if(s::get('saving')) {

		// SCRIVO A DB I DATA + I CAMPI CHE SERVONO PER L'ORDINAMENTO
		$new_data = array(
			'GUID' => s::get('el__ID'),
			'denominazione' =>a::data('denominazione'),
			'data' => a::json($data),
		);

	}

	require 'inc/detail-save.php';

 ?>
