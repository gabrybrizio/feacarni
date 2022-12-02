<?php

class lang {
    public static function current() {
        return s::get('lang');
    }

    public static function init() {
        $currentLang = lang::current();

        // se passo la lingua in get ed ha un valore valido (due lettere maiuscole o minuscole) vince sempre
        if (!empty(r::get('lang')) && v::match(r::get('lang'), '/^[a-zA-Z]{2}$/')) {
            $langFromRequest = str::lower(r::get('lang'));
        } else {
            // se non la passo in get prende la lingua corrente in sessione
            // se in sessione non c'è niente (es. la prima volta) prende quella di default

            $langFromRequest = (!empty($currentLang) ? $currentLang : config::language_default());
        }

        // aggiorno la sessione solo se la lingua è cambiata o è la prima volta
        if ($currentLang != $langFromRequest) {
            $configPath =  util::configDir() . "lang/$langFromRequest.json";
            $defaultPath = __DIR__ . "/lang/$langFromRequest.json";

            // leggo eventuali valori di default in dorothy/src/lang
            $defaultArr = [];
            if (f::exists($defaultPath)) {
                $langFile = f::read($defaultPath);
                $defaultArr = str::parse($langFile, 'json');
            }

            // leggo eventuali valori specificati nel config
            $configArr = [];
            if (f::exists($configPath)) {
                $langFile = f::read($configPath);
                $configArr = str::parse($langFile, 'json');
            }

            // merge dei valori di default con quelli del config
            $langArr = array_merge($defaultArr, $configArr);

            s::set('lang', $langFromRequest);
            s::set('lang-dictionary', $langArr);
        }
    }

    public static function change($newLang) {
        $path = url::path();

        if (!empty($newLang)) {
            // se il path ha già la lingua la sostituisco altrimenti la aggiungo
            if (substr($path, 2, 1) === '/') {
                $ret = $newLang . substr($path, 2);
            } else {
                $ret = $newLang . '/' . $path;
            }
        }

        return '/' . $ret;
    }

    public static function dictionary() {
        return s::get('lang-dictionary');
    }

    public static function dictionaryJson() {
        return a::json(lang::dictionary());
    }

    /**
     *
     *
     * @param  mixed $key
     * @param  mixed $parameters
     * @return void
     */
    public static function get(string $key, ?array $parameters = null):string {
        $ret = a::get(lang::dictionary(), $key);

        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $ret = str::replace($ret, ':' . $key, $value);
            }
        }

        // se non trova la traduzione restituisco con la chiave
        $ret = ($ret ? $ret : $key);

        return $ret;
    }
}