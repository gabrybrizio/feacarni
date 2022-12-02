<?php 

	require('../../app/init.php');

	if(s::get('logged') && r::ajax() && r::is('post')){
		
		$h = "";
		$type = get("_TYPE");
		
		// Elenco immagini generiche nel dettaglio
		if($type=="images"){
			
			$el__images = module::imagesOf(s::get('modulo'), s::get('el__ID'));

			if($el__images->count() > 0){
				foreach($el__images as $img){
					$h .='<a class="card" onclick="window.open(\'' . thumb::src($img) . '\',\'_blank\')">';
					$h .='<card-img style="background-image:url(\'' . thumb::src($img, 275) . '\')"></card-img>';				
					$h .='<card-hat>' . $img->filename() . '</card-hat>';
					$h .='<card-footer>';
					$h .= brick::btn("text:ELIMINA", "class:ghost small", "click:FILE.deleteConfirm(event,'" . $img->filename() . "')");
					$h .='</card-footer>';
					$h .='</a>';
				}
			}else{
				$h .='<h3>';
				$h .='Nessuna immagine caricata.<br>';
				$h .='Clicca su ' . icon::get("paperclip") . ' o trascina qui i file.<br>';
				$h .='(Massimo 4 Mb)'; 
				$h .='</h3>';	
			}
		
		// Elenco documenti generici nel dettaglio
		}else if($type=="documents"){
			
			$el__documents = module::documentsOf(s::get('modulo'), s::get('el__ID'));

			if($el__documents->count() > 0){
				foreach($el__documents as $doc){
					$h .='<a class="card" onclick="window.open(\'' . $doc->url() . '\',\'_blank\')">';
					$h .='<card-img class="no-cover" style="background-image:url(\'assets/img/ico-file.png\')">';	
					$h .='<card-tag>' . str::upper($doc->extension()) . '</card-tag>';			
					$h .='</card-img>';				
					$h .='<card-hat>' . $doc->filename() . '</card-hat>';
					$h .='<card-footer>';
					$h .= brick::btn("text:ELIMINA", "class:ghost small", "click:FILE.deleteConfirm(event,'" . $doc->filename() . "')");
					$h .='</card-footer>';
					$h .='</a>';
				}
			}else{
				$h .='<h3>';
				$h .='Nessun documento caricato.<br>';
				$h .='Clicca su ' . icon::get("paperclip") . ' o trascina qui i file.<br>';
				$h .='(Massimo 4 Mb)';
				$h .='</h3>';	
			}			
			
		// Elenco immagini nel field choices
		}else if($type=="choice_images"){
					
			$el__images = module::imagesOf(s::get('modulo'), s::get('el__ID'));
			
			if($el__images->count() > 0){
				foreach($el__images as $img){
					$h .='<choice onclick="CHOICE.set(this)">';
					$h .='<choice-img style="background-image:url(\'' . thumb::src($img, 75) . '\')"></choice-img>';
					$h .='<choice-value>' . $img->filename() . '</choice-value>';
					$h .='</choice>';
				}
			}else{
				$h .='<choice class="no-choice">';
				$h .='<choice-value>Nessuna immagine ancora caricata</choice-value>';
				$h .='</choice>';
			}			
		}else if($type=="choice_documents"){
				
			$el__documents = module::documentsOf(s::get('modulo'), s::get('el__ID'));
			
			if($el__documents->count() > 0){
				foreach($el__documents as $doc){
					$h .='<choice onclick="CHOICE.set(this)">';
					$h .='<choice-ext>' . $doc->extension() . '</choice-ext>';
					$h .='<choice-value>' . $doc->filename() . '</choice-value>';
					$h .='</choice>';
				}
			}else{
				$h .='<choice class="no-choice">';
				$h .='<choice-value>Nessun documento ancora caricato</choice-value>';
				$h .='</choice>';
			}							
		}
		
		echo $h;
		
	}else{
		echo "Si sono riscontrati dei problemi.";
	}
?>