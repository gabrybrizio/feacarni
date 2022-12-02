<?php
	require 'inc/menu.php';
	s::set('modulo', "ordini");

	if(!user::isAdmin()) util::jumpTo("/");

	require 'inc/detail-data.php';

	//a::show($el);
	//a::show($data);

	$user = module::listOf("utenti")->where('GUID','=',a::get($el,'utente'))->first();
	$userData = module::dataOf($user);

	$orderId = r::get('e');
	$order = order::getOrder($orderId);

	if ($user) {
		$userEmail = a::get($userData,'username');
		$userName = a::get($userData,'nome');
		$userSurname = a::get($userData,'cognome');
		$userTel = a::get($userData,'telefono');
	} else {
		if (isset($order->userData)) {
			$userEmail = $order->userData->email;
			$userName = $order->userData->name;
			$userSurname = $order->userData->surname;
			$userTel = $order->userData->tel;
			$userEmail = $order->userData->email;
			$userAddress = $order->userData->address;
			$userCap = $order->userData->cap;
			$userCity = $order->userData->city;
			$userProvincia = $order->userData->provincia;
			$userMarketing = $order->userData->marketing;
		}
	}
?>

<style>
	.utente-section{
		border: 1px solid #ccc;
		padding: 12px 12px 12px 12px;
		border-radius: 4px;
		line-height: 20px;
		background: rgb(255 255 255 / .5);
		margin-bottom:12px;
	}
</style>

<h1>Dettaglio ordine <?=($order->isDigital ? 'digitale' : '') ?></h1>

<form method="post" enctype="multipart/form-data">

	<!-- DATI -->
	<div class="box">
		<?php
			$orderStatusList = array_values(util::getClassListConst('OrderStatus'));
			// lo status Carrello non ci deve essere
			$orderStatusList = array_diff($orderStatusList, [OrderStatus::Carrello]);
			$orderStatusList = implode(',', $orderStatusList);

			 brick::field("readonly", "label:Codice dell'ordine", "class:half", "value:" . str::upper(str::short(s::get('el__ID'), 8, "")));
			 brick::field("readonly","label:Data ordine", "name:dataOrdine","class:half");
			 brick::field("choice", "label:Stato", "class:half", "choices:$orderStatusList","required");
			 brick::field("readonly","label:Metodo di pagamento","class:half", "value:" . PaymentMethod::name(a::get($el,'paymentMethod')));
		?>
	</div>

	<h2>Acquirente</h2>
	<div class="box">

		<!-- DATI UTENTE -->
		<field>
			<div class="utente-section">
				<h5><?=$userSurname . " " . $userName ?></h5>
				<break></break>
				E-mail: <a href="mailto:<?=$userEmail ?>" class="t-underline"><?=$userEmail ?></a>
				<br>
				Telefono: <a href="tel:<?=$userTel ?>" class="t-underline"><?=$userTel ?></a>
				<br>
				Indirizzo: <?=$userAddress ?> - <?=$userCap ?> <?=$userCity ?> (<?=$userProvincia ?>)
				<br><br>
				<?php
				  if($userMarketing):
				?>
					Ha dato consenso per invio materiale promozionale e news
				<?php
				  endif;
				?>
			</div>

		<!-- AZIENDA -->
		<?php
		  	if($user && a::get($userData,'azienda') != ""):
		?>
			<div class="utente-section">
				<h5>Azienda</h5>
				<break></break>
				<?php
					echo "<b>" . a::get($userData,'azienda') . "</b><br>";
					echo "Partita iva: " . a::get($userData,'partita-iva') . "<br>";
					echo "PEC: " . a::get($userData,'pec') . "<br>";
					echo "Codice SDI: " . a::get($userData,'codice-sdi') . "<br>";
				?>
			</div>

		<?php
		  	endif;
		?>

		</field>
		<?php if ($user): ?>
			<field>
				<!-- INDIRIZZO FATTURAZIONE -->
				<?php
					$indirizzo_fatturazione = a::get($userData,'fatturazione-indirizzo');
					$indirizzo_fatturazione .= "<br>" . a::get($userData,'fatturazione-cap') . " " . a::get($userData,'fatturazione-citta');
					$indirizzo_fatturazione .= " " . a::get($userData,'fatturazione-provincia');
					$indirizzo_fatturazione .= " - " . a::get($userData,'fatturazione-paese');
				?>

				<div class="utente-section">
					<h5>Indirizzo di fatturazione</h5>
					<break></break>
					<?= $indirizzo_fatturazione ?>
				</div>

				<!-- INDIRIZZO SPEDIZIONE -->
				<?php
						$indirizzo_spedizione = a::get($userData,'spedizione-indirizzo');
						$indirizzo_spedizione .= "<br>" . a::get($userData,'spedizione-cap') . " " . a::get($userData,'spedizione-citta');
						$indirizzo_spedizione .= " " . a::get($userData,'spedizione-provincia');
						$indirizzo_spedizione .= " - " . a::get($userData,'spedizione-paese');
				?>

				<div class="utente-section">
					<h5>Indirizzo di spedizione</h5>
					<break></break>
					<?= $indirizzo_spedizione ?>
				</div>
			</field>
		<?php endif; ?>
	</div>

	<h2>Carrello</h2>
        <div class="ordine-carrello box">
            <table>
                <thead>
                    <tr>
                        <th class="t-center">Articolo</th>
                        <th class="t-right">Prezzo unitario</th>
                        <th class="t-center">Quantità</th>
                        <th class="t-right">Subtotale</th>
                    </tr>
                </thead>
                <tbody>
            <?php

			//a::show($order->rows);

				// Ciclo gli articoli
				if (count($order->rows) > 0):
					foreach($order->rows as $row):
            ?>
                <tr class="art">
                    <td class="art-nome">
                        <span><a href="<?=$row->url ?>"><?=$row->name ?></a></span><br>
                    </td>
                    <td class="art-prezzo">
						<?=util::euro($row->price); ?>
                    </td>
                    <td class="art-qt">
						<?=$row->quantity; ?>
                    </td>
                    <td class="art-subtotale">
						<?=util::euro($row->totalPrice); ?>
                    </td>
                </tr>
            <?php
                        endforeach;

                    endif;
            ?>
				</tobdy>
			</table>

			<div class="ordine-subtotali">
				<div class="subtotali-label">
					Subtotale:<br>
					Spese di spedizione:
				</div>

				<div class="subtotali-prezzi">
					<?= util::euro($order->subtotal) ?><br>
					<?=util::euro($order->shippingCost) ?>
				</div>
			</div>

			<div class="ordine-totale">

				<div class="totale-label">
					Totale ordine:
				</div>

				<div class="totale-prezzo">
					<?=util::euro($order->total); ?>
				</div>
			</div>

		</div>

	<!-- CONTROLS -->
	<?php require 'inc/detail-controls.php'; ?>

</form>

<?php

	require 'inc/footer.php';

	if(s::get('saving')) {

		// Prendo i dati a db
		// Aggiorno solamente lo stato
		$el['stato'] = a::data('stato');
		$el['modified'] = a::data('modified');
		$el['by'] = a::data('by');

		// SCRIVO A DB I DATA + I CAMPI CHE SERVONO PER L'ORDINAMENTO
		$new_data = array(
			'stato' => a::data('stato'),
			'data' => a::json($el),
		);

		//a::show($new_data);

	}

	require 'inc/detail-save.php';

 ?>
