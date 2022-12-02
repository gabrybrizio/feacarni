<?php

/**
 * Classe che rappresenta una pagina pubblica
 */
class page {
    static $title = '';
    static $description = '';
    static $image = '';

    /**
     * Titolo della pagina. Aggiunge sempre al fondo " | Ragione Sociale".
     * Usato nel tag <title> e nell'og:title.
     *
     * @param  string $title
     * @return void
     */
    public static function title(string $title = ''):string {
        $ret = '';

        try {
            if (!empty($title)) {
                static::$title = $title;
            }

            if(empty(static::$title)){
                $ret = site::get('ragione-sociale');
            } else {
                $ret = static::$title . ' | ' . site::get('ragione-sociale');
            }
        } catch (Throwable $th) {
            $ret = '';
        }

        return $ret;
    }

    /**
     * Descrizione della pagina.
     * Usata nel meta tag description e og:description.
     *
     * @param  mixed $description
     * @return void
     */
    public static function description(string $description = '') {
        $ret = '';

        try {
            if (!empty($description)) {
                static::$description = $description;
            }

            if(empty(static::$description)){
                $ret = site::get('ragione-sociale');
            } else {
                $ret = static::$description;
            }

            $ret = str::unhtml($ret);
            $ret = str::short($ret, 200);
        } catch (Throwable $th) {
            $ret = '';
        }

        return $ret;
    }

    /**
     * Immagine della pagina. Se non spceficato restituisce "/img/logo.jpg".
     * Usata nel tag og:image.
     *
     * @param  mixed $image
     * @return void
     */
    public static function image(string $image = '') {
        if (!empty($image)) {
            static::$image = $image;
        }

        if(empty(static::$image)){
            $ret = '/img/logo.jpg';
        } else {
            $ret = static::$image;
        }

        return $ret;
    }
}