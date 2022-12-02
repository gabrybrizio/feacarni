<?php
	require 'inc/menu.php';
	s::set('modulo', "ordini");

	if(!user::isAdmin()) util::jumpTo("/");
?>

<h1>Ordini</h1>

<?php require 'inc/list-search.php'; ?>

<style>
	.a-carrello{background-color:#1180ae}
	.pagato{background-color:#e60077}
	.spedito{background-color:#1180ae}
	.chiuso{background-color:#83B52C}
	.anomalia{background-color:#e30000}

	.short-column{width:150px !important}
</style>

<div id="list" class="box box--table">

	<?php

	// CARICO LA TABELLA TRAMITE QUERYSTRING
	$queryString = "SELECT ord.GUID as ordine_GUID, ";
	$queryString .= "ord.data_ordine as ordine_date, ";
	$queryString .= "ord.stato as ordine_stato, ";
	$queryString .= "ord.totale as ordine_totale, ";
	$queryString .= "ord.data as ordine_data, ";
	$queryString .= "ute.GUID as utente_GUID, ";
	$queryString .= "ute.username as utente_username, ";
	$queryString .= "ute.nome as utente_nome, ";
	$queryString .= "ute.cognome as utente_cognome ";
	$queryString .= "FROM ordini ord ";
	$queryString .= "LEFT JOIN utenti ute ";
	$queryString .= "ON ord.utente = ute.GUID ";
	$queryString .= "WHERE ord.stato <> '" . OrderStatus::Carrello . "' ";
	$queryString .= "ORDER BY ord.data_ordine DESC";

	$elements = db::query($queryString);

	//a::show($elements);

	// Se ci sono elementi
	if ($elements->count() > 0):

		foreach ($elements as $el):
			$orderData = json_decode($el->ordine_data(), true);
			$isDigitalOrder = (bool)a::get($orderData, 'isDigital', false);
			$userEmail = $userName = $userSurname = null;

			if ($el->utente_GUID()) {
				$userEmail = $el->utente_username();
				$userName = $el->utente_nome();
				$userSurname = $el->utente_cognome();
			} else {
				$userData = a::get($orderData, 'utenteData');
				if ($userData) {
					$userEmail = a::get($userData, 'email');
					$userName = a::get($userData, 'name');
					$userSurname = a::get($userData, 'surname');
				}
			}
		?>

			<div class="card">

				<card-title class="short-column">
					<a href="<?= s::get('modulo') ?>-detail.php?e=<?= $el->ordine_GUID() ?>" class="t-underline">
						<b>ORDINE <?= str::upper(str::short($el->ordine_GUID(), 8, "")) ?></b>
					</a>
				</card-title>

				<div class="short-column">
					<card-tag class="<?= str::slug($el->ordine_stato()) ?>" ><?= str::upper($el->ordine_stato()) ?></card-tag>
				</div>

				<card-hat class="short-column">
					<?= date("d/m/Y, H:i", $el->ordine_date()) ?>
				</card-hat>

				<card-hat>
					<a href="utenti-detail.php?e=<?= $el->utente_GUID() ?>" class="t-underline"><?= $userSurname . " " . $userName; ?></a>
					<br>
					<small>(<?=$userEmail ?>)</small>
				</card-hat>

				<card-hat style="text-align:right">
					<b><?= util::euro($el->ordine_totale()); ?></b>
				</card-hat>

				<card-footer class="short-column">
					<?=  brick::btn("text:DETTAGLIO", "class:ghost small", "click:location.href='ordini-detail.php?e=" . $el->ordine_GUID() . "'"); ?>
				</card-footer>
			</div>

		<?php

		endforeach;
	else:
		echo '<h3>Nessun ordine presente.</h3>';
	endif;

	?>

</div>

<controls>
	<?php
		//echo  brick::btn("icon:plus", "text:Nuovo ordine", "click:location.href='" . s::get('modulo') . "-detail.php?e=" . util::GUID() . "'");
		echo  brick::btn("icon:chevron-up", "class:ghost", "click:SCROLL.toTop()");
	?>
</controls>

<?php require 'inc/footer.php'; ?>
