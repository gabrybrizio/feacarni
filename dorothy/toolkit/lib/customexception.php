<?php

/**
 * Eccezione custom che permette di specificare un messaggio di errore da usare direttamente lato client.
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class CustomException extends Exception {

  const CUSTOM_EXCEPTION_CODE = -666;

  protected $publicMessage = '';

  public function __construct($publicMessage, $message = '',  $code = 0, Throwable $previous = null) {
    $this->publicMessage = $publicMessage;

    if (!$message) {
      $message = $publicMessage;
    }

    // Codice d'errore che mi permette di identificare in errorreporting.php
    // le eccezioni custom in modo da non inviare l'email
    $code = static::CUSTOM_EXCEPTION_CODE;

    parent::__construct($message, $code, $previous);
  }

  public function getPublicMessage() {
    return $this->publicMessage;
  }
}