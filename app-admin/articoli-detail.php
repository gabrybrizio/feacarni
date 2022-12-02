<?php

	require 'inc/menu.php';
	s::set('modulo', "articoli");

	if(!user::isAdmin()) util::jumpTo('/');

	require 'inc/detail-data.php';

?>

<h1>Dettaglio articolo</h1>

<form method="post" enctype="multipart/form-data">

	<!-- DATI -->
	<div class="box">
		<?php
			brick::field("check", "label:Visibile", "class:half nopad");
			brick::br();

			brick::field("text", "label:Nome", "class:half", "required");
			brick::field("choice", "label:Categoria", "class:half", "required", "choices:Bovini,Suini,Formaggi,Biscotti e pasticceria,Avicoli,Vasetti,Salumi,Gastronomia");
			brick::br();
			brick::field("richtext", "label:Descrizione");

			

			// Variante fake (unica)
			$articleId = s::get('el__ID');

			// se non sto salvando prendo i valori dalla tabella variants
			$dbVariants = db::table('variants')->where('articleId', '=', $articleId)->order('sortOrder')->all();

			if(!s::get('saving') && $dbVariants && $dbVariants->count() > 0) {
				$data['variants'] = $dbVariants->toArray(function($item) {
					$ret = a::merge(json_decode($item->data, true), $item->toArray());

					return $ret;
				});
			}
		?>

		<repeaters>
			<?php
				$block = "variants";
				if(a::get($data,$block) == "") $data[$block][0] = null;
				for($i = 0; $i < count(a::get($data, $block, 1)); $i++):
					//if(isset($data[$block][$i])):
			?>
			<repeater>
				<?php

					$campo = "guid";
					$guid = a::get($data[$block][$i], $campo);
					$guid = (!util::isGUID($guid) ? util::guid() : $guid);
					brick::field("hidden", "value:" . $guid, "name:" . $block . "[" . $i . "][" . $campo . "]", "default:UTIL.guid();");

					$campo = "visible";
					brick::field("hidden", "value:1", "name:" . $block . "[" . $i . "][" . $campo . "]");

					$campo = "stock";
					brick::field("hidden",   "value:1", "name:" . $block . "[" . $i . "][" . $campo . "]");					

					brick::br();

					$campo = "weight";
					brick::field("text", "label:Peso", "class:half", "placeholder:Es. 800gr.", "value:" . a::get($data[$block][$i], $campo), "name:" . $block . "[" . $i . "][" . $campo . "]", "required");										

					$campo = "pricePrivate";
					brick::field("euro", "label:Prezzo", "class:half", "placeholder:Es. 32,50", "value:" . a::get($data[$block][$i], $campo), "name:" . $block . "[" . $i . "][" . $campo . "]", "required");

				?>
			</repeater>
			<?php
					//endif;
				endfor;
			?>
		</repeaters>
	</div>
	
	<h2>Immagini</h2>

	<!-- DATI -->
	<div class="box">
		<?php
			brick::field("image", "label:Carne");
			brick::field("image", "label:Infografica mucca");
		?>
	</div>

	<?=brick::upload('detail-images', f::TYPE_IMAGE) ?>	

	<h2>Ricetta invernale</h2>
	<div class="box">
		<?php
			brick::field("text", "label:Titolo", "name:ricetta-invernale-nome");
			brick::field("image", "label:Immagine", "name:ricetta-invernale-immagine");
			brick::br();
			brick::field("richtext","label:Descrizione", "name:ricetta-invernale-descrizione");
			brick::field("richtext","label:Ingredienti", "name:ricetta-invernale-ingredienti");
		?>
	</div>	

	<h2>Ricetta estiva</h2>
	<div class="box">
		<?php
			brick::field("text", "label:Titolo", "name:ricetta-estiva-nome");
			brick::field("image", "label:Immagine", "name:ricetta-estiva-immagine");
			brick::br();
			brick::field("richtext","label:Descrizione", "name:ricetta-estiva-descrizione");
			brick::field("richtext","label:Ingredienti", "name:ricetta-estiva-ingredienti");
		?>
	</div>		

	<!-- DROPZONE -->
	<?php require 'inc/detail-dropzone.php'; ?>

	<!-- CONTROLS -->
	<?php require 'inc/detail-controls.php'; ?>

</form>

<?php

	require 'inc/footer.php';

	if(s::get('saving') && !s::get('errors')) {
		// salvo le varianti in un'altra tabella
		$variants = a::get($data, 'variants');
		unset($data['variants']);


		$i = 0;
		foreach ($variants as $variant) {
			$dbVariant = null;

			// cerco la variante corrente a db
			if ($dbVariants) {
				$variantId = a::get($variant, 'guid');

				$filter = $dbVariants->filter(function($row) use ($variantId) {
					return ($row->guid == $variantId);
				});

				if ($filter && $filter->count() > 0) {
					$dbVariant = $filter->nth(0);
				}
			}

			$variantData = $variant;
			unset($variantData['guid']);
			unset($variantData['visible']);
			unset($variantData['name']);
			unset($variantData['pricePrivate']);
			unset($variantData['stock']);
			unset($variantData['weight']);

			$variantRow = array(
				'guid' => a::get($variant, 'guid'),
				'visible' => a::get($variant, 'visible'),
				'name' => a::data('nome'),
				'articleId' => $articleId,
				'pricePrivate' =>a::get($variant, 'pricePrivate'),
				'stock' => a::get($variant, 'stock'),
				'weight' => a::get($variant, 'weight'),
				'sortOrder' => $i,
				'data' => a::json($variantData),
			);

			// se ho trovato la variante la aggiorno
			if ($dbVariant) {
				$update = module::listOf('variants')->where('guid', '=', $dbVariant->guid)->update($variantRow);
			} else {
				$insert = db::table('variants')->insert($variantRow);
			}

			$i++;
		}

		// elimino eventuali varianti
		if ($dbVariants) {
			$ids = array_map(function($variant) {
				return a::get($variant, 'guid');
			}, $variants);

			$dbIds = $dbVariants->map(function($dbVariant) {
				return $dbVariant->guid;
			});

			foreach ($dbIds as $dbId) {
				if (!in_array($dbId, $ids)) {
					db::table('variants')->where('guid', '=', $dbId)->delete();
				}
			}
		}

		// SCRIVO A DB I DATA + I CAMPI CHE SERVONO PER L'ORDINAMENTO
		$new_data = array(
			'GUID' => s::get('el__ID'),
			'visibile' =>a::data('visibile'),
			'nome' =>a::data('nome'),
			'categoria' => str::slug(a::data('categoria')),
			'data' => a::json($data),
		);
	}

	require 'inc/detail-save.php';

 ?>
