<?php

class config extends Silo {
    public static $data = array();

    public static function defaults() {
        $ret = [
            'version' => '0.0.0',
            'debug' => true,
            'debug_email' => '',
            'email_error_reporting' => true,
            'email_error_reporting_from' => 'log@eiconlab.com',
            'email_error_reporting_to' => 'log@eiconlab.com',
            'maintenance' => false,
            'login_failure_limit' => 5,
            'landing' => 'articoli-list.php',
            'paypal' => false,
            'paypal_sandbox' => true,
            'paypal_client_id' => '',
            'paypal_client_secret' => '',
            'satispay' => false,
            'satispay_sandbox' => true,
            'satispay_public_key' => '',
            'satispay_private_key' => '',
            'satispay_key_id' => '',
            'bank_transfer' => false,
            'cash_on_delivery' => false,
            'stock' => false,
            'language_default' => 'it',
            'language_show_in_url' => false,
            'mail_host' => '',
            'mail_username' => '',
            'mail_password' => '',
            'db_type' => '',
            'db_host' => '',
            'db_name' => '',
            'db_user' => '',
            'db_password' => '',
            'shop_digital_products' => false,
        ];

      return $ret;
    }

    public static function init() {
        // leggo il file di config
        $configFile = util::configDir() . 'config.php';
        $confArr = [];
        if (f::exists($configFile)) {
            $confArr = include $configFile;
        }

        // merge dei valori di default con quelli del config
        config::$data = array_merge(static::defaults(), $confArr);
    }

    /* metodi statici comodi per intellisense */

    public static function version() {
        return config::get('version');
    }

    public static function debug() {
        return config::get('debug');
    }
    public static function debug_email() {
        return config::get('debug_email');
    }

    /**
     * Attiva l'invio di email in caso di errore/eccezione
     *
     * @return void
     */
    public static function email_error_reporting() {
        return config::get('email_error_reporting');
    }

    /**
     * Indirizzo email dal quale inviare l'email
     *
     * @return void
     */
    public static function email_error_reporting_from() {
        return config::get('email_error_reporting_from');
    }

    /**
     * Indirizzo email al quale inviare l'email
     *
     * @return void
     */
    public static function email_error_reporting_to() {
        return config::get('email_error_reporting_to');
    }

    public static function maintenance() {
        return config::get('maintenance');
    }
    public static function login_failure_limit() {
        return config::get('login_failure_limit');
    }

	public static function landing() {
        return config::get('landing');
    }

    /**
     * Attiva paypal come metodo di pagamento
     *
     * @return void
     */
    public static function paypal() {
        return config::get('paypal');
    }
    public static function paypal_sandbox() {
        return config::get('paypal_sandbox');
    }
    public static function paypal_client_id() {
        return config::get('paypal_client_id');
    }
    public static function paypal_client_secret() {
        return config::get('paypal_client_secret');
    }

    /**
     * Attiva satispay come metodo di pagamento
     *
     * @return void
     */
    public static function satispay() {
        return config::get('satispay');
    }
    public static function satispay_sandbox() {
        return config::get('satispay_sandbox');
    }
    public static function satispay_public_key() {
        return config::get('satispay_public_key');
    }
    public static function satispay_private_key() {
        return config::get('satispay_private_key');
    }
    public static function satispay_key_id() {
        return config::get('satispay_key_id');
    }

    /**
     * Attiva o meno il bonifico bancario come metodo di pagamento
     *
     * @return void
     */
    public static function bank_transfer() {
        return config::get('bank_transfer');
    }

    /**
     * Attiva o meno il contrassegno/pagamento alla consegna come metodo di pagamento
     *
     * @return void
     */
    public static function cash_on_delivery() {
        return config::get('cash_on_delivery');
    }

    public static function stock() {
        return config::get('stock');
    }

    public static function language_default() {
        return config::get('language_default');
    }
    public static function language_show_in_url() {
        return config::get('language_show_in_url');
    }

    public static function mail_host() {
        return config::get('mail_host');
    }
    public static function mail_username() {
        return config::get('mail_username');
    }
    public static function mail_password() {
        return config::get('mail_password');
    }

    public static function db_type() {
        return config::get('db_type');
    }
    public static function db_host() {
        return config::get('db_host');
    }
    public static function db_name() {
        return config::get('db_name');
    }
    public static function db_user() {
        return config::get('db_user');
    }
    public static function db_password() {
        return config::get('db_password');
    }
    public static function db_prefix() {
        return config::get('db_prefix');
    }

    public static function shop_digital_products() {
        return config::get('shop_digital_products');
    }
}