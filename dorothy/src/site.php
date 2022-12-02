<?php

/**
 * Classe che espone la tabella "azienda" su db
 */
class site {
    public static $data = null;

    public static function get($key = null) {
        if(is_null(static::$data)) {
            static::$data = module::dataOf('azienda');
        }

        if (is_null($key)) {
            // se non ho passato una chiave restituisco tutto l'array
            return static::$data;
        } else {
            return a::get(static::$data, $key, '');
        }
    }

    public static function contactForm($nome, $email, $messaggio, $privacy, $contact_me, $marketing):bool {
        if($nome == "" || $email == "" || $messaggio == ""){
            throw new CustomException(lang::get('validation-all-fields-required'));
        }

        if(!v::email($email)) {
            throw new CustomException(lang::get('validation-email'));
        }

        if($privacy != 'accetto'){
            throw new CustomException(lang::get('validation-privacy'));
        }

        // honeypot
        if($contact_me != ""){
            throw new Exception();
        }

        $mail = new Mail();
        $mail->setFrom('noreply@moloc.net', site::get('ragione-sociale'));
        $mail->addAddress(site::get('email'));
        $mail->Subject = 'Contatti dal sito';

        $email_text = '';
        $email_text .= 'Nome: ' . $nome . '<br>';
        $email_text .= 'Email: ' . $email . '<br>';
        $email_text .= 'Messaggio: ' . $messaggio . '<br>';

        if($marketing == "1"){
            $email_text .= 'Marketing: Sì';
        }else{
            $email_text .= 'Marketing: No';
        }

        $email_title = "Contatto dal sito";
        $mail->setBody($email_title, $email_text);

        if($mail->send()){
            return true;
        }else{
            throw new Exception();
        }
    }
}