<?php 

	require('../../app/init.php');

	if(s::get('logged') && r::ajax() && r::is('post') && get('_FILE')){
		
		if(f::remove(s::get('el__dir') . "/" . get('_FILE'))){
			
			// Elimino eventuali thumbs dell'immagine cancellata		
			// Folder dove si trovano le thumbs
			$thumbs__dir = s::get('el__dir') . "/thumbs/";
			$thumbs__folder = New folder($thumbs__dir);
			
			$thumbs = $thumbs__folder->files()->filterBy('type','image');
			
			foreach($thumbs as $thumb){
				
				$thumb__name = $thumb->filename();
				
				// Se il nome della thumb inizia col medesimo nome dell'immagine cancellata e
				// l'immagine cancellata ha la medesima estensione della thumb
				// la cancello
				if(str::startsWith($thumb__name, pathinfo(get('_FILE'))['filename'] . "__") && str::endsWith(get('_FILE'), $thumb->extension())){
					f::remove($thumbs__dir . $thumb__name);
				}
			}
			
			echo "OK";
		}else{
			echo "Problemi con la cancellazione. Potresti essere in locale.";
		}
		
	}else{
		echo "Si sono riscontrati dei problemi.";
	}
?>