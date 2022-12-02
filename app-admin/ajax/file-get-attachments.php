<?php 

	require('../../app/init.php');

	if(s::get('logged') && r::ajax() && r::is('post') && get('_FILELIST') && get('_ATTACHMENTTYPE')){
		
		$file_list = get('_FILELIST');

		if($file_list != ""){

			// Restituisco anche l'elenco dei file in modo
			// da poterli inserire nell'attachment corretto
			// Nel caso uscissero fuori problemi di sync
			// $h = $file_list . "###";
			// 
			$h = "";

			// Recupero la cartella in cui si trovano i file
			$dir = util::dataDir() . s::get('modulo') . '/' . s::get('el__ID') . "/";
			$folder = New folder($dir);

			$file_list_array = str::split(get('_FILELIST'), "|");

			foreach($file_list_array as $filename){

				$file = $folder->files()->filterBy('filename',$filename)->first();		

				if($file->type() == 'image'){
					$h .='<a class="card" onclick="window.open(\'' . thumb::src($file) . '\',\'_blank\')">';
					$h .='<card-img style="background-image:url(\'' . thumb::src($file, 275) . '\')"></card-img>';				
					$h .='<card-hat>' . $file->filename() . '</card-hat>';

					// Nei readonly-attachments non è possibile eliminare i file
					if (get('_ATTACHMENTTYPE') == "attachments"){
						$h .='<card-footer>';
						$h .= brick::btn("text:ELIMINA", "class:ghost small", "click:FILE.deleteConfirm(event,'" . $file->filename() . "')");
						$h .='</card-footer>';
					}

					$h .='</a>';
				}elseif($file->type() == 'document'){
					$h .='<a class="card" onclick="window.open(\'' . $file->url() . '\',\'_blank\')">';
					$h .='<card-img class="no-cover" style="background-image:url(\'assets/img/ico-file.png\')">';	
					$h .='<card-tag>' . str::upper($file->extension()) . '</card-tag>';			
					$h .='</card-img>';				
					$h .='<card-hat>' . $file->filename() . '</card-hat>';

					// Nei readonly-attachments non è possibile eliminare i file
					if (get('_ATTACHMENTTYPE') == "attachments"){
						$h .='<card-footer>';
						$h .= brick::btn("text:ELIMINA", "class:ghost small", "click:FILE.deleteConfirm(event,'" . $file->filename() . "')");
						$h .='</card-footer>';
					}

					$h .='</a>';				
				}
				
			}
		}

		echo $h;
		
	}else{
		echo "Si sono riscontrati dei problemi.";
	}
?>