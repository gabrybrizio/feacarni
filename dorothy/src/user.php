<?php

/**
 * Classe statica che interagisce con l'utente in sessione (loggato)
 */
class user {
    private static $user = null;
    private static $userData = null;

    const TYPE_PERSONAL = 'Privato';
    const TYPE_BUSINESS = 'Azienda';

    public static function get($key = null) {
        if(is_null(static::$user)) {
            $userObj = db::table('utenti')->where('GUID','=', s::get('public_id'))->first();

            if($userObj) {
                static::$user = $userObj;
                static::$userData = module::dataOf(static::$user);
            }
        }

        if (is_null($key)) {
            // se non ho passato una chiave restituisco tutto l'oggetto
            return static::$user;
        } else {
            return a::get(static::$userData, $key, '');
        }
    }

    public static function guid() {
        return s::get('public_id');
    }

    public static function role($role){
        if(s::get('role') == $role){
            return true;
        }else{
            return false;
        }
    }

    public static function type() {
        return static::get('tipologia');
    }

    public static function getBillingAddress($user = null, $addContactInfo = true):string {
        if ($user) {
            $userData = json_decode($user->data(), true);
        } else {
            $userData = static::$userData;
        }

        $ret = a::get($userData,'fatturazione-indirizzo');
        $ret .= " - " . a::get($userData,'fatturazione-cap') . " " . a::get($userData,'fatturazione-citta');
        $ret .= " " . a::get($userData,'fatturazione-provincia');
        $ret .= " - " . util::countries(a::get($userData,'fatturazione-paese'));

        if ($addContactInfo) {
            $ret .= '<br><br>' . a::get($userData, 'username');
            $ret .= '<br>' . a::get($userData, 'telefono');
        }

        return $ret;
    }

    public static function getShippingAddress($user = null, $addContactInfo = true){
        if ($user) {
            $userData = json_decode($user->data(), true);
        } else {
            $userData = static::$userData;
        }

        $ret = a::get($userData,'spedizione-indirizzo');
        $ret .= " - " . a::get($userData,'spedizione-cap') . " " . a::get($userData,'spedizione-citta');
        $ret .= " " . a::get($userData,'spedizione-provincia');
        $ret .= " - " . util::countries(a::get($userData,'spedizione-paese'));

        if ($addContactInfo) {
            $ret .= '<br><br>' . a::get($userData, 'username');
            $ret .= '<br>' . a::get($userData, 'telefono');
        }

        return $ret;
    }

    public static function acceptedPolicy(){
        if(a::get($_COOKIE, 'cookie_policy') != ""){
            return true;
        }else{
            return false;
        }
    }

    public static function isLogged() {
        return (s::get('public_logged') && util::isGUID(s::get('public_id')));
    }

    public static function isAdmin() {
        return (user::role('Admin'));
    }

    public static function update(array $data):bool {
        // Dati dell'utente a db
        $utente_data = module::dataOf('utenti', user::guid());

        // Campi obbligatori
        if( a::get($data,'fatturazione-paese') == "" ||
            a::get($data,'fatturazione-indirizzo') == "" ||
            a::get($data,'fatturazione-citta') == "" ||
            a::get($data,'fatturazione-provincia') == "" ||
            a::get($data,'fatturazione-cap') == "" ||
            a::get($data,'telefono') == "" ||
            a::get($data,'spedizione-paese') == "" ||
            a::get($data,'spedizione-indirizzo') == "" ||
            a::get($data,'spedizione-citta') == "" ||
            a::get($data,'spedizione-provincia') == "" ||
            a::get($data,'spedizione-cap') == "") {
            throw new CustomException(lang::get('validation-fields-required'));
        }

        if(!v::match(a::get($data,'telefono'), '/^([0-9 +.])+$/i')){
            throw new CustomException("Il formato del telefono non è corretto.");
        }

        if(a::get($data,'password') != ""){
            if(str::length(a::get($data,'password')) < 6){
                throw new CustomException("Attenzione, la password deve contenere almeno 6 caratteri.");
            }

            if(a::get($data,'password') != a::get($data,'password_copy')){
                throw new CustomException("Attenzione, le password non coincidono.");
            }

            $utente_data['password'] = password::hash($data['password']);
        }

        if(a::get($data,'contact_me') != ""){
            throw new CustomException('');
        }

        // Aggiorno $utente_data
        $utente_data["fatturazione-paese"] = a::get($data,'fatturazione-paese');
        $utente_data["fatturazione-indirizzo"] = a::get($data,'fatturazione-indirizzo');
        $utente_data["fatturazione-citta"] = a::get($data,'fatturazione-citta');
        $utente_data["fatturazione-provincia"] = a::get($data,'fatturazione-provincia');
        $utente_data["fatturazione-cap"] = a::get($data,'fatturazione-cap');
        $utente_data["telefono"] = a::get($data,'telefono');
        $utente_data["spedizione-paese"] = a::get($data,'spedizione-paese');
        $utente_data["spedizione-indirizzo"] = a::get($data,'spedizione-indirizzo');
        $utente_data["spedizione-citta"] = a::get($data,'spedizione-citta');
        $utente_data["spedizione-provincia"] = a::get($data,'spedizione-provincia');
        $utente_data["spedizione-cap"] = a::get($data,'spedizione-cap');
        $utente_data['marketing'] = a::get($data,'marketing') == "1" ? '1' : '0';

        $utente_data["modified"] = date("d/m/Y H:i:s");
        $utente_data["by"] = s::get('public_id');

        // Creo il nuovo utente
        $save_data = array(
            'marketing' => $utente_data['marketing'],
            'data' => a::json($utente_data),
        );

        $update = db::table('utenti')->where("GUID", "=", s::get('public_id'))->update($save_data);

        if($update){
            return true;
        }else{
            throw new Exception('');
        }
    }

    public static function signup(array $data):string {
        $ret = '';

        // Campi obbligatori
        if( a::get($data,'username') == "" ||
            a::get($data,'nome') == "" ||
            a::get($data,'cognome') == "" ||
            a::get($data,'username') == "" ||
            a::get($data,'password') == "" ||
            a::get($data,'password_copy') == "" ||
            a::get($data,'fatturazione-paese') == "" ||
            a::get($data,'fatturazione-indirizzo') == "" ||
            a::get($data,'fatturazione-citta') == "" ||
            a::get($data,'fatturazione-provincia') == "" ||
            a::get($data,'fatturazione-cap') == "" ||
            a::get($data,'telefono') == "" ||
            a::get($data,'spedizione-paese') == "" ||
            a::get($data,'spedizione-indirizzo') == "" ||
            a::get($data,'spedizione-citta') == "" ||
            a::get($data,'spedizione-provincia') == "" ||
            a::get($data,'spedizione-cap') == ""){

            throw new CustomException(lang::get('validation-fields-required'));
        }

        // Tipologia di utente
        $tipo_permessi = [static::TYPE_BUSINESS, static::TYPE_PERSONAL];
        if(!in_array(a::get($data,'tipologia'), $tipo_permessi)){
            $data['tipologia'] = static::TYPE_PERSONAL;
        }

        // Abilitazione cliente
        if($data['tipologia'] == static::TYPE_PERSONAL) $data['abilitato'] = "1";
        if($data['tipologia'] == static::TYPE_BUSINESS) $data['abilitato'] = "0";

        // Campi obbligatori per Aziende e Rivenditori
        if($data['tipologia'] != static::TYPE_PERSONAL){

            if( a::get($data,'azienda') == "" ||
                a::get($data,'partita-iva') == "" ||
                a::get($data,'pec') == "" ||
                a::get($data,'codice-sdi') == ""){
                    throw new CustomException(lang::get('validation-fields-required'));

            }

            if(!v::match(a::get($data,'partita-iva'), '/^([0-9])+$/i') || str::length(a::get($data,'partita-iva')) != 11){
                throw new CustomException(lang::get('validation-vat'));
            }

            if(!v::email(a::get($data,'pec'))) {
                throw new CustomException("Il formato della PEC non è corretto.");
            }
        }

        if(!v::email(a::get($data,'username'))) {
            throw new CustomException(lang::get('validation-email'));

        }else{
            $utenti = module::listOf('utenti')->where("username", "=", a::get($data,'username'))->all();
            if($utenti->count() > 0){
                throw new CustomException(lang::get('validation-email-used'));
            }
        }

        if(!v::match(a::get($data,'telefono'), '/^([0-9 +.])+$/i')){
            throw new CustomException(lang::get('validation-tel'));
        }

        if(str::length(a::get($data,'password')) < 6){
            throw new CustomException(lang::get('validation-password-length'));
        }

        if(a::get($data,'password') != a::get($data,'password_copy')){
            throw new CustomException(lang::get('validation-password-match'));
        }

        if(a::get($data,'privacy') == ""){
            throw new CustomException(lang::get('validation-privacy'));
        }

        if(a::get($data,'contact_me') != ""){
            throw new Exception();
        }

        unset($data['anticsrf']);
        unset($data['password_copy']);
        unset($data['contact_me']);

        $data['data-di-registrazione'] = date("d/m/Y");
        $data['password'] = password::hash($data['password']);

        if(a::get($data,'marketing') == "1"){
            $data['marketing'] = "1";
        }else{
            $data['marketing'] = "0";
        }

        $guid = util::GUID();

        // Creo il nuovo utente
        $save_data = array(
            'GUID' => $guid,
            'abilitato' => a::get($data, 'abilitato'),
            'username' => a::get($data,'username'),
            'nome' => a::get($data,'nome'),
            'cognome' => a::get($data,'cognome'),
            'marketing' => a::get($data,'marketing'),
            'data_registrazione' => time(),
            'data' => a::json($data),
        );

        $insert = db::table('utenti')->insert($save_data);
        if(v::num($insert)){
            $insert = true;
        }else{
            $insert = false;
        }

        if($insert && $data['tipologia'] == user::TYPE_PERSONAL){

            dir::make(util::dataDir() . 'utenti/' . $guid);

            $mail = new Mail();
            $mail->setFrom(site::get('email'), site::get('ragione-sociale'));
            $mail->addAddress(a::get($data, 'username'),a::get($data, 'cognome') . ' ' .a::get($data, 'nome'));

            $mail->Subject = site::get('ragione-sociale') . ' - ' . lang::get('sign-up-confirmation');

            $email_azienda = site::get('ragione-sociale');
            $email_title = a::get($data, 'cognome') . ' ' .a::get($data, 'nome');
            $email_text = lang::get('welcome') . ' ' . $email_azienda . "<br><br>" . lang::get('sign-up-login');
            $ctaText = lang::get('go-to-website');
            $ctaLink = url::base();
            $mail->setBody($email_title, $email_text, $ctaLink, $ctaText);

            if($mail->send()){
                // Loggo l'utente
                s::set('public_logged', true);
                s::set('public_id', $guid);
                s::set('public_username',a::get($data, 'username'));
                s::set('public_name',a::get($data, 'cognome') . ' ' .a::get($data, 'nome'));
                s::set('public_tipologia',a::get($data, 'tipologia'));
                $ret = "OK";
            }else{
                $ret = "KO";
            }
        }else{
            $ret = "AZIENDA";
        }

        return $ret;
    }
}

?>