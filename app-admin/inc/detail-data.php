<?php
	// CONTROLLO VALIDITA E
	if(!util::isGUID(get('e'))) util::jumpTo(util::adminUrl() . s::get('modulo') . "-list.php");
	
	// ELEMENTO 
	s::set('el__ID', get('e'));
	s::set('el__dir', util::dataDir() . s::get('modulo') . '/' . s::get('el__ID')); // Utilizzato in delete, upload and save
	
	// CONTROLLO L'ESISTENZA DELL'ELEMENTO A DB
	// SE ESISTE RECUPERO I DATA
	$el = module::dataOf(s::get('modulo'), s::get('el__ID'));	
	
	s::set('saving', false);
	s::set('errors', false);
	s::set('errors_msg', "");
  
	// SALVATAGGIO, CONTROLLO DATI
	if(r::is('post') && get('btn_save') && csrf(get('anticsrf'))) {	
		
		// Abilito la validazione
		s::set('saving', true);
    
		// Array dei dati passati in post
		$data = r::postData();
		
		// Riposiziono la pagina allo stesso scroll
		util::scrollTo($data['scroll_position']);
		
		// Tolgo i dati non utili
		unset($data['anticsrf']);
		unset($data['scroll_position']);
		unset($data['btn_save']);

		// Se esiste il campo password nel form che sto salvando
		if(array_key_exists("password",$data)){
			// Se è valorizzato e non è criptato, lo cripto
			if(a::data('password') != '' && !password::isHash(a::data('password'))){
				$data['password'] = password::hash($data['password']);
			}else{
			// Altrimenti lo recupero da db, perchè il field password non è mai valorizzato in visualizzazione
				$data['password'] = a::get($el, 'password');
			}
		}		
    		
	}else{
		// ARRIVO DALL'ELENCO
		// Recupero i DATA del dettaglio 
		// O nuovo elemento

		$data = ($el ? $el : null);
	}
?>	