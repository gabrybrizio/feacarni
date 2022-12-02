<?php

class util {
    const adminFolder = 'app-admin';
    const publicFolder = 'app-public';
    const dataFolder = 'app-data';
    const configFolder = 'config';

    // restituisce un elenco delle costanti di una classe. Utile per le classi tipo OrderStatus e PaymentMethod
    public static function getClassListConst($className, $toJson = false) {
        $refl = new ReflectionClass($className);
        $ret = $refl->getConstants();

        return ($toJson ? a::json($ret) : $ret);
    }

    public static function guid(){
        if (function_exists('com_create_guid') === true){
            return trim(com_create_guid(), '{}');
        }

        return str::lower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
    }

    public static function isGUID($guid){
        if (preg_match('/^\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/', $guid)) {
        return true;
        } else {
        return false;
        }	
    }

    public static function jumpTo($url){
        echo '<script>window.location="' . $url . '"</script>';
        exit();
    }

    public static function scrollTo($y){
        $h = '<script>';
        $h .= 'setTimeout(function(){';
        $h .= 'window.scrollTo({top: ' . $y . '})';
        $h .= '}, 500)';
        $h .= '</script>';	
        
        echo $h;
    }

    public static function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    public static function isLocal(){
        return (url::host() === 'localhost');
    }

    public static function tel($num){
        return preg_replace('/\s+/', '', $num);
    }

    // Formatta un numero in stringa 1.200,00 €
    public static function euro($num){
        if (!v::num($num)) $num = 0;

        $eu = number_format($num, 2, ',', '.');
        return $eu . " <span class=\"euro-symbol\">€</span>";
    }

    public static function toYYYYMMDD($date){
        $d = str_replace('/', '-', $date);
        $d = date('Ymd', strtotime($d));
        
        return $d;
    }

    public static function epoch($date, $hour = "00:00:00"){
        $d = str_replace('/', '-', $date);
        $d .= $hour;
        $d = strtotime($d);
        
        return $d;
    }

    public static function giorno($date){
        $giorno = date('N', static::epoch($date));
        return str::split(GIORNI)[$giorno-1];
    }

    public static function mese($date){
        $mese = date('n', static::epoch($date));
        return str::split(MESI)[$mese-1];	
    }

    public static function giorno_nr($date){
        return str::split($date,"/")[0];	
    }

    public static function mese_nr($date){
        return str::split($date,"/")[1];	
    }

    public static function anno_nr($date){
        return str::split($date,"/")[2];	
    }

    public static function toMinutes($hour){
        if(v::match($hour, '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/')){
            $time = str::split($hour, ':');
            return $time[0] * 60.0 + $time[1] * 1.0;
        }else{
            return 0;
        }
    }

    // Se è scaduto restituisce true;
    public static function scade_entro($data, $giorni){
        if($data != "" && ((static::epoch($data) - time()) < (86400 * $giorni))){
            return true;
        }else{
            return false;
        }
    }

    public static function is_scaduto($data){
        if($data != "" && (static::epoch($data) < time())){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Restituisce array ['it' => 'Italia'] dalla tabella "countries"
     * Se specificata la chiave $key restituisce il relativo valore in stringa
     *
     * @return mixed
     */
    public static function countries($key = null) {
        $query = db::table('countries');
        if (!is_null($key)) {
            $query = $query->where('id', '=', $key);
        }
        $arr = $query->all()->toArray();

        if (count($arr) === 1) {
            $ret = $arr[0]->name;
        } else {
            $ret = [];
            foreach ($arr as $obj) {
                $ret[$obj->id] = $obj->name;
            }
        }

        return $ret;
    }

    public static function adminUrl() {
        return '/' . static::adminFolder . '/';
    }
    public static function dataUrl() {
        return '/' . static::dataFolder . '/';
    }
    public static function dataDir() {
        return d::instance()->root() . DS . static::dataFolder . DS;
    }
    public static function publicDir() {
        return d::instance()->root() . DS . static::publicFolder . DS;
    }
    public static function configDir() {
        return d::instance()->root() . DS . static::configFolder . DS;
    }
    public static function dorothyDir() {
        return d::instance()->root() . DS . 'dorothy' . DS;
    }
}