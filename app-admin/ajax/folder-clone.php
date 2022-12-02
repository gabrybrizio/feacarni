<?php 

	require('../../app/init.php');

	if(s::get('logged') && r::ajax() && r::is('post') && get('_MODULO') && get('_FOLDER')){

		$modulo = get('_MODULO');
		$folder = get('_FOLDER');

		// Recupero la riga da duplicare
		$master = db::table($modulo)->where('GUID','=',$folder)->first();

		$new_GUID = util::GUID();

		// Recupero i data
		$data = module::dataOf($master);

		// Campi da modificare
		$data['nome'] = $data['nome'] . ' - copia';		

		$clone = db::table($modulo)->insert(array(
			'GUID' => $new_GUID,
			'data' => a::json($data)
		));

		if (v::num($clone)){
			// Duplicare la cartella immagini
			dir::copy(util::dataDir() . $modulo . '/' . $folder, util::dataDir() . $modulo . '/' . $new_GUID);			
			echo "OK";
		}else{
			echo "Problemi con la duplicazione, riprovare più tardi.";
		}			

	}else{
		echo "Si sono riscontrati dei problemi.";
	}
  
?>