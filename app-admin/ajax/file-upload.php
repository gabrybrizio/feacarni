<?php

ini_set('memory_limit', '-1');
ini_set('display_errors', '0');

require('../../app/init.php');

try {
	if(!(s::get('logged') && r::ajax() && r::is('post') && csrf(r::get('anticsrf')) && !empty($_FILES['file']))) {
		throw new Exception();
	}

	// I tipi di file permessi sono image e document
	// In f::$types sono elencate le estensioni permesse per ogni tipo
	$typeOk = [f::TYPE_IMAGE, f::TYPE_DOCUMENT];
	$type = r::get('type');
	if (!in_array($type, $typeOk, true)) {
		throw new Exception();
	}

	$error = "";
	$total = 0;
	$uploaded = 0;

	$filelinked = get('files__linked');
	$filelinked = str::split($filelinked, '|');

	foreach($_FILES['file']['name'] as $key=>$filename){
		if($filename != '') {
			$total ++;

			// Creo la cartella dell'elemento nel caso non esistesse
			dir::make(s::get('el__dir'));

			$safeFilename = str::slug(f::name($filename)) . "-" . date("d-m-Y") . "." . str::lower(f::extension($filename));

			try {
				$upload = new Upload(s::get('el__dir') . '/' . $safeFilename, ['index' => $key, 'acceptType' => $type, 'maxSize' => 4194304]);

				if($file = $upload->file()) {
					$uploaded ++;
					$filelinked[] = $safeFilename;

					echo "OK#" . implode("|", $filelinked);
				} else {
					throw new Exception();
				}
			} catch (Throwable $ex) {
				// eccezioni provenienti dalla classe Upload
				throw new Exception("Errore durante il caricamento di \"$filename\". {$ex->getMessage()}.<br/>");
			}
		}
	}
} catch (Throwable $th) {
	$message = $th->getMessage();
	$message = ($message === '' ? 'Errore durante il caricamento del file.' : $message);

	echo "KO#$message";
}