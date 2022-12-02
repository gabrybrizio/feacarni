<?php

class D {
    static public $version = '2.9.0';
    static public $instance;

    static public function instance($class = null) {
        if(!is_null(static::$instance)) return static::$instance;
        return static::$instance = $class ? new $class : new static;
    }

    static public function version() {
        return static::$version;
    }

    public function __construct() {
        // imposto la root dell'applicazione
        $this->root = dirname(__DIR__, 2);

        // make sure the instance is stored / overwritten
        static::$instance = $this;
    }

    public function init() {
        date_default_timezone_set('Europe/Rome');

        define("GIORNI", "lunedì,martedì,mercoledì,giovedì,venerdì,sabato,domenica");
        define("MESI", "gennaio,febbraio,marzo,aprile,maggio,giugno,luglio,agosto,settembre,ottobre,novembre,dicembre");

        // leggo il file di config
        config::init();

        // se attivo mando email in caso di errore/eccezione
        if (config::email_error_reporting()) {
            ErrorReporting::initEmailErrorReporting(config::email_error_reporting_from(), config::email_error_reporting_to());
        }

        // aggiungo in sessione la lingua corrente
        lang::init();

        // gestisco eventualmente il redirect alla pagina di manutenzione
        if(config::maintenance() && !str::contains(url::path(), '/maintenance/')) {
            redirect::to('/maintenance/');
        }
    }

    public function root(): string {
        return $this->root;
    }
}