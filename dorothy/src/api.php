<?php

class api {

    private static $endpoints = [];

    /**
     * Restituisce l'url pubblico delle API.
     * Se il parametro $endpoint è specificato aggiunge il parametro GET "endpoint"
     * api.aspx[?endpoint=:endpoint]
     *
     * @param  mixed $endpointName
     * @return string
     */
    public static function getUrl(string $endpoint = ''):string {
        return url::to('/api.php') . ($endpoint ? '?endpoint=' . $endpoint: '');
    }

    public static function getEndpoint(string $name):?object {
        $ret = null;

        if(array_key_exists($name, static::$endpoints)) {
            $ret = static::$endpoints[$name];
        }

        return $ret;
    }

    /**
     * Aggiunge un endpoint richiamabile lato client tramite API($endpointName, {})
     *
     * @param  mixed $endpointName Nome
     * @param  mixed $callback Funzione callable alla quale passare i parametri.
     * @param  mixed $parameters Array di parametri da passare al metodo.
     * @param  mixed $isPublic Se true non viene controllata la login.
     * @param  mixed $allRequestParameters Se true non viene letto l'array di parametri ma vengono passati tutti da r::get().
     * Utile quando ho tanti parametri e non voglio passarli uno ad uno.
     * @return void
     */
    public static function addEndpoint(string $endpointName, string $callback, array $parameters = [], bool $isPublic = false, bool $allRequestParameters = false):void {
        $obj = new stdClass;
        $obj->name = $endpointName;
        $obj->callback = $callback;
        $obj->isPublic = $isPublic;
        $obj->parameters = $parameters;
        $obj->allRequestParameters = $allRequestParameters;

        static::$endpoints[$endpointName] = $obj;
    }

    public static function readEndpoint($endpoint) {
        if(!$endpoint) {
            throw new Exception('$endpoint is required.');
        }

        $callback = $endpoint->callback;

        if ($endpoint->allRequestParameters) {
            $parameters[] = r::get();
        } else {
            $parameters = $endpoint->parameters;
            $parameters = array_map(function($param) {
                return r::get($param);
            }, $parameters);
        }

        if(is_callable($callback)) {
            return call_user_func_array($callback, $parameters);
        } else {
            throw new Exception("$callback is not callable");
        }
    }

    /**
     * Elabora la richiesta. Parametri post/get minimi richiesti: endpoint, csrf (gestiti in automatico).
     *
     * @return void
     */
    public static function init() {
        $ret = '';
        $error = '';

        try {
            $endpointName = r::get('endpoint', '');
            $csrf = r::get('csrf', '');

            if (!$endpointName) {
                throw new Exception("The endpoint parameter is required.");
            }

            if (!csrf($csrf)) {
                throw new Exception('CSRF token check failed.');
            }

            $endpoint = static::getEndpoint($endpointName);
            if (!$endpoint) {
                throw new Exception("The endpoint '$endpointName' does not exist.");
            }

            // controllo sempre la login tranne che per i metodi pubblici
            if (!user::isLogged() && !$endpoint->isPublic) {
                throw new Exception('User not authorized.');
            }

            $ret = static::readEndpoint($endpoint);
        } catch (Throwable $th) {
            $error = [
                'code' => $th->getCode(),
                'detail' => $th->getMessage(),
            ];

            if($th instanceof CustomException) {
                $error['message'] = $th->getPublicMessage();
                ErrorReporting::exceptionHandler($th);
            } else {
                $error['message'] = lang::get('warning-try-later');
                // Invio la email di errore se non è una CustomException
                // ovvero un'eccezione prevista
                ErrorReporting::exceptionHandler($th);
            }
        }

        if ($error) {
            echo Response::json($error, 500);
        } else {
            $ret = json_encode(['content' => $ret]);
            echo Response::json($ret);
        }
    }
}