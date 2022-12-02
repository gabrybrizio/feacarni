<?php 

	require('../../app/init.php');

	if(s::get('logged') && r::ajax() && r::is('post') && get('_MODULO') && get('_FOLDER')){

		$modulo = get('_MODULO');
		$folder = get('_FOLDER');
		
		// Cancellazione fisica
		if(db::table($modulo)->where('GUID', '=', $folder)->delete()) {

			$folder = New folder(util::dataDir() . $modulo . '/' . $folder);

			if ($folder->exists()){
				$folder->delete();
			}

			echo "OK";
		}else{
			echo "Problemi con la cancellazione, riprovare più tardi.";
		}			

	}else{
	  
		echo "Si sono riscontrati dei problemi.";

	}
  
?>