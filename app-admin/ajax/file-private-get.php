<?php

    require('../../app/init.php');

    if(s::get('logged') && util::isGUID(get('e'))){
        
        $dir = util::dataDir() . get('m') . '/' . get('e') . "/";
		$folder = New folder($dir);
        $f = $folder->files()->filterBy('filename', '==', get('f'))->first();

        //a::show($f);
        //die;

        // Se è un Excel lo scarico altrimenti lo apro nel browser
        if($f->extension() == 'xlsx' || $f->extension() == 'xls'){
            f::download($f);
            die;
        }else{
            $fp = fopen($f->root(), 'rb');

            header($f->header());
            header("Content-Length: " . $f->size());  
    
            fpassthru($fp);
            die;
        }

    }else{

        util::jumpTo('/');
        die;

    }
?>