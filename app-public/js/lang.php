<?php
    require '../../app/init.php';

    header("Cache-Control: public, max-age=2592000");
    header("Content-type: application/javascript");
    header_remove ('pragma');

    echo 'const LANG_CONFIG = ' . lang::dictionaryJson();
?>