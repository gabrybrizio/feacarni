<?php

class module {

    //listOf($modulo)
    public static function listOf($modulo) {
        return db::table($modulo);
    }

    /**
    * dataOf($modulo-standalone)
    * dataOf($modulo, $ID = null)
    * dataOf($el)
    */
    public static function dataOf($modulo, $ID = null){
        if (!$modulo) {
            return false;
        }

        if (!is_object($modulo)) {
            $elements = db::table($modulo);

            if($ID === null){
                // Single
                $el = $elements->first();
            }else{
                // Controllo esistenza elemento a db
                $el = $elements->where('GUID', '=', $ID)->first();
            }

            if($el){
                return json_decode($el->data(), true);
            }else{
                // Non esiste, nuovo elemento
                return false;
            }

        }else{
            // Sto ciclando gli element di un modulo
            return json_decode($modulo->data(), true);
        }
    }

    public static function imagesOf($modulo, $ID = null, $filename = null){
        if($ID === null){
            // Si tratta di un modulo single, restituisco le immagini della prima cartella
            $dir = util::dataDir() . $modulo;
            $folder = New folder($dir);
            $images =  $folder->children()->first()->files()->filterBy('type','image');
        }else{
            // Restituisco tutte le immagini di quell'elemento
            $dir = util::dataDir() . $modulo . '/' . $ID . "/";
            $folder = New folder($dir);
            $images = $folder->files()->filterBy('type','image');
        }

        if($filename !== null){
            $images = $images->filterBy('filename', '==', $filename)->first();
        }

        return $images;
    }

    public static function documentsOf($modulo, $ID = null, $filename = null){
        if($ID == null){
            // Si tratta di un modulo single, restituisco i documenti della prima cartella
            $dir = util::dataDir() . $modulo;
            $folder = New folder($dir);
            $documents = $folder->children()->first()->files()->filterBy('type','document');
        }else{
            // Restituisco tutte i documenti di quell'elemento
            $dir = util::dataDir() . $modulo . '/' . $ID . "/";
            $folder = New folder($dir);
            $documents = $folder->files()->filterBy('type','document');
        }

        if($filename !== null){
            $documents = $documents->filterBy('filename', '==', $filename)->first();
        }

        return $documents;
    }
}