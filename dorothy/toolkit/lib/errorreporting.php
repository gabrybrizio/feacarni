<?php

/**
 * Error Reporting
 *
 * Changes values of the PHP error reporting
 *
 * @package   Kirby Toolkit
 * @author    Lukas Bestle <lukas@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Lukas Bestle
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ErrorReporting {

  /**
   * Returns the current raw value
   *
   * @return int     The current value
   */
  public static function get() {
    return error_reporting();
  }

  /**
   * Sets a new raw error reporting value
   *
   * @param  int     $level The new level to set
   * @return int     The new value
   */
  public static function set($level) {
    if(static::get() !== error_reporting($level)) {
      throw new Exception('Internal error: error_reporting() did not return the old value.');
    }
    return static::get();
  }

  /**
   * Check if the current error reporting includes an error level
   *
   * @param  mixed   $level The level to check for
   * @param  int     $current A custom current level
   * @return boolean
   */
  public static function includes($level, $current = null) {
    // also allow strings
    if(is_string($level)) {
      if(defined($level)) {
        $level = constant($level);
      } else if(defined('E_' . strtoupper($level))) {
        $level = constant('E_' . strtoupper($level));
      } else {
        throw new Exception('The level "' . $level . '" does not exist.');
      }
    }

    $value = ($current)? $current : static::get();
    return bitmask::includes($level, $value);
  }

  /**
   * Adds a level to the current error reporting
   *
   * @param  int     $level The level to add
   * @return boolean
   */
  public static function add($level) {
    // check if it is already added
    if(static::includes($level)) return false;

    $old = static::get();
    $newExpected = bitmask::add($level, $old);
    $newActual = static::set($newExpected);

    return $newActual === $newExpected;
  }

  /**
   * Removes a level from the current error reporting
   *
   * @param  int     $level The level to remove
   * @return boolean
   */
  public static function remove($level) {
    // check if it is already removed
    if(!static::includes($level)) return false;

    $old = static::get();
    $newExpected = bitmask::remove($level, $old);
    $newActual = static::set($newExpected);

    return $newActual === $newExpected;
  }

  /**
   * Dato il numero di un errore php restituisce il tipo di errore in stringa
   *
   * @param  mixed $number
   * @return void
   */
  public static function getErrorLabel($number) {
    $ret = '';

    switch($number) {
        case E_ERROR: // 1
          $ret = 'E_ERROR';
          break;
        case E_WARNING: // 2
          $ret = 'E_WARNING';
          break;
        case E_PARSE: // 4
          $ret = 'E_PARSE';
          break;
        case E_NOTICE: // 8
          $ret = 'E_NOTICE';
          break;
        case E_CORE_ERROR: // 16
          $ret = 'E_CORE_ERROR';
          break;
        case E_CORE_WARNING: // 32
          $ret = 'E_CORE_WARNING';
          break;
        case E_COMPILE_ERROR: // 64
          $ret = 'E_COMPILE_ERROR';
          break;
        case E_COMPILE_WARNING: // 128
          $ret = 'E_COMPILE_WARNING';
          break;
        case E_USER_ERROR: // 256
          $ret = 'E_USER_ERROR';
          break;
        case E_USER_WARNING: // 512
          $ret = 'E_USER_WARNING';
          break;
        case E_USER_NOTICE: // 1024
          $ret = 'E_USER_NOTICE';
          break;
        case E_STRICT: // 2048
          $ret = 'E_STRICT';
          break;
        case E_RECOVERABLE_ERROR: // 4096
          $ret = 'E_RECOVERABLE_ERROR';
          break;
        case E_DEPRECATED: // 8192
          $ret = 'E_DEPRECATED';
          break;
        case E_USER_DEPRECATED: // 16384
          $ret = 'E_USER_DEPRECATED';
          break;
        case E_ALL: // 32767
          $ret = 'E_ALL';
          break;
        default:
          $ret = $number;
          break;
    }

    return $ret;
  }

  public static function errorHandler($number, $desc, $file, $line) {
    try {
      // non mando l'email nel caso in cui l'eccezione sia stata generata
      // dal @ error-control operator.
      if (error_reporting() == 0) {
        return false;
      }

      // se è un'eccezione custom quindi gestita non invio l'email
      if ($number === CustomException::CUSTOM_EXCEPTION_CODE) {
        return false;
      }

      $userAgent = null;
      if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        // se è un bot non riporto l'errore (per es. il csrf non è mai valido)
        if (str::contains($userAgent, 'Googlebot')) {
          return false;
        }
      }

      if ($number <> 0) {
        $errorLabel = static::getErrorLabel($number);
      } else {
        $errorLabel = str::substr($desc, 0, 50);
      }

      $subject = $errorLabel . ' - ' . Url::base();
      $date = date("Y-m-d H:i:s", time());

      $body = "$desc<br><br>";
      $body .= "Error number: $number - $errorLabel<br>";
      $body .= "$file:$line<br>";
      $body .= "$date<br>";
      $body .= "PHP version: " . PHP_VERSION ." (" . PHP_OS . ")<br>";

      $body .= Url::current() . '<br>';

      if ($userAgent) {
        $body .= $userAgent;
      }

      $body .= static::formatBacktrace();

      $mail = new Mail();
      $mail->setFrom(config::email_error_reporting_from());
      $mail->addAddress(config::email_error_reporting_to());
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->isHTML(true);
      $mail->send();
    } catch (Throwable $th) {
    }

    // permette di continuare con la gestione normale dell'errore (es. display in locale)
    return false;
  }

  private static function formatBacktrace() {
    $ret = '<br><br>';

    try {
      foreach (debug_backtrace() as $trace) {
        // evito che riporti il backtrace di questa stessa funzione
        if ($trace['function'] == 'errorHandler' || $trace['function'] == 'formatBacktrace') {
          continue;
        }

        $parameters = is_array($trace['args']) ? implode(', ',$trace['args']) : '';
        $parameters = str::replace($parameters, '#', '<br>#');
        $class = array_key_exists('class', $trace) ? $trace['class'] . '::' : '';

        $file = (array_key_exists('file', $trace) ? $trace['file'] : '');
        $line = (array_key_exists('line', $trace) ? ':' . $trace['line'] : '');
        $function = (array_key_exists('function', $trace) ? $trace['function'] : '');

        $ret .= "<b>$file$line</b><br>";
        $ret .= sprintf("%s%s<br>", $class, $function);
        $ret .= sprintf("%s<br>", $parameters);
      }
    } catch (Throwable $th) {
    }

    return $ret;
  }

  public static function exceptionHandler($ex) {
    static::errorHandler($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
  }

  /**
   * Permette di agganciare l'handler che invia un'email ogni volta che viene generata un'eccezione o un errore
   *
   * @return void
   */
  public static function initEmailErrorReporting() {
    set_error_handler('ErrorReporting::errorHandler');
    set_exception_handler('ErrorReporting::exceptionHandler');
  }
}
