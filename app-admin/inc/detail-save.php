<?php
	// POSTBACK SALVATAGGIO DATI
	$update = false;
	$insert = false;
	
	if(s::get('saving')) {
		
		if (s::get('errors')){

			$errors_msg = 'Sono presenti dei campi obbligatori non compilati o con una sintassi errata.';

			if(s::get('errors_msg') != ""){
				$errors_msg = s::get('errors_msg');
			}
			
		?>
			<script>
				$content = "<?= $errors_msg ?>";
				MODAL.open($content,'','error');
			</script>
		<?php			
			
		}else{
			
			if($el){
				$update = module::listOf(s::get('modulo'))->where('GUID', '=', s::get('el__ID'))->update($new_data);
			}else{
				// Non uso module::listOf perchè ha il where e l'insert non funziona
				// $insert restituisce l'id creato
				$insert = db::table(s::get('modulo'))->insert($new_data);
				// Creo la cartella dei file di default
				dir::make(s::get('el__dir'));

				if(v::num($insert)) $insert = true;
			}			
			
			if($update or $insert){
				?>
					<script>
						$content = 'Salvataggio avvenuto con successo.';

						MODAL.open($content,'','success');
					</script>
				<?php					
			}
		
		}
	}
?>