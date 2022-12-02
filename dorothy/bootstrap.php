<?php

if(!defined('DS'))  define('DS', DIRECTORY_SEPARATOR);
if(!defined('MB'))  define('MB', (int)function_exists('mb_get_info'));
if(!defined('BOM')) define('BOM', "\xEF\xBB\xBF");

// polyfill for new sort flag
if(!defined('SORT_NATURAL')) define('SORT_NATURAL', 'SORT_NATURAL');

// scrive un unico file di log nella root del sito
ini_set('error_log', __DIR__ . '/../error_log');

// a super simple autoloader
function load($classmap, $base = null) {
  spl_autoload_register(function($class) use ($classmap, $base) {
    $class = strtolower($class);
    if(!isset($classmap[$class])) return false;
    if($base) {
      include($base . DS . $classmap[$class]);
    } else {
      include($classmap[$class]);
    }
  });
}

// LOAD COMPOSER'S AUTOLOADER
require(__DIR__ . '/../vendor/autoload.php');

// auto-load all toolkit classes
load(array(
  'a'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'a.php',
  'bitmask'                     => __DIR__ . DS . 'toolkit/lib' . DS . 'bitmask.php',
  'brick'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'brick.php',
  'c'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'c.php',
  'cookie'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'cookie.php',
  'cache'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'cache.php',
  'cache\\driver'               => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'driver.php',
  'cache\\driver\\apc'          => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'driver' . DS . 'apc.php',
  'cache\\driver\\file'         => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'driver' . DS . 'file.php',
  'cache\\driver\\memcached'    => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'driver' . DS . 'memcached.php',
  'cache\\driver\\mock'         => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'driver' . DS . 'mock.php',
  'cache\\driver\\session'      => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'driver' . DS . 'session.php',
  'cache\\value'                => __DIR__ . DS . 'toolkit/lib' . DS . 'cache' . DS . 'value.php',
  'collection'                  => __DIR__ . DS . 'toolkit/lib' . DS . 'collection.php',
  'crypt'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'crypt.php',
  'data'                        => __DIR__ . DS . 'toolkit/lib' . DS . 'data.php',
  'database'                    => __DIR__ . DS . 'toolkit/lib' . DS . 'database.php',
  'database\\query'             => __DIR__ . DS . 'toolkit/lib' . DS . 'database' . DS . 'query.php',
  'db'                          => __DIR__ . DS . 'toolkit/lib' . DS . 'db.php',
  'detect'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'detect.php',
  'dimensions'                  => __DIR__ . DS . 'toolkit/lib' . DS . 'dimensions.php',
  'dir'                         => __DIR__ . DS . 'toolkit/lib' . DS . 'dir.php',
  'email'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'email.php',
  'embed'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'embed.php',
  'error'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'error.php',
  'customexception'             => __DIR__ . DS . 'toolkit/lib' . DS . 'customexception.php',
  'errorreporting'              => __DIR__ . DS . 'toolkit/lib' . DS . 'errorreporting.php',
  'escape'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'escape.php',
  'exif'                        => __DIR__ . DS . 'toolkit/lib' . DS . 'exif.php',
  'exif\\camera'                => __DIR__ . DS . 'toolkit/lib' . DS . 'exif' . DS . 'camera.php',
  'exif\\location'              => __DIR__ . DS . 'toolkit/lib' . DS . 'exif' . DS . 'location.php',
  'f'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'f.php',
  'folder'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'folder.php',
  'header'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'header.php',
  'html'                        => __DIR__ . DS . 'toolkit/lib' . DS . 'html.php',
  'i'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'i.php',
  'l'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'l.php',
  'media'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'media.php',
  'obj'                         => __DIR__ . DS . 'toolkit/lib' . DS . 'obj.php',
  'pagination'                  => __DIR__ . DS . 'toolkit/lib' . DS . 'pagination.php',
  'password'                    => __DIR__ . DS . 'toolkit/lib' . DS . 'password.php',
  'r'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'r.php',
  'redirect'                    => __DIR__ . DS . 'toolkit/lib' . DS . 'redirect.php',
  'remote'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'remote.php',
  'response'                    => __DIR__ . DS . 'toolkit/lib' . DS . 'response.php',
  'router'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'router.php',
  's'                           => __DIR__ . DS . 'toolkit/lib' . DS . 's.php',
  'server'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'server.php',
  'silo'                        => __DIR__ . DS . 'toolkit/lib' . DS . 'silo.php',
  'sql'                         => __DIR__ . DS . 'toolkit/lib' . DS . 'sql.php',
  'str'                         => __DIR__ . DS . 'toolkit/lib' . DS . 'str.php',
  'system'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'system.php',
  'thumb'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'thumb.php',
  'timer'                       => __DIR__ . DS . 'toolkit/lib' . DS . 'timer.php',
  'toolkit'                     => __DIR__ . DS . 'toolkit/lib' . DS . 'toolkit.php',
  'template'                    => __DIR__ . DS . 'toolkit/lib' . DS . 'template.php',
  'upload'                      => __DIR__ . DS . 'toolkit/lib' . DS . 'upload.php',
  'url'                         => __DIR__ . DS . 'toolkit/lib' . DS . 'url.php',
  'v'                           => __DIR__ . DS . 'toolkit/lib' . DS . 'v.php',
  'visitor'                     => __DIR__ . DS . 'toolkit/lib' . DS . 'visitor.php',
  'xml'                         => __DIR__ . DS . 'toolkit/lib' . DS . 'xml.php',
  'yaml'                        => __DIR__ . DS . 'toolkit/lib' . DS . 'yaml.php',

  // vendors
  'spyc'                        => __DIR__ . DS . 'toolkit/vendors' . DS . 'yaml' . DS . 'yaml.php',
  'abeautifulsite\\simpleimage' => __DIR__ . DS . 'toolkit/vendors' . DS . 'abeautifulsite' . DS . 'SimpleImage.php',
  'mimereader'                  => __DIR__ . DS . 'toolkit/vendors' . DS . 'mimereader' . DS . 'mimereader.php',
  'truebv\\punycode'            => __DIR__ . DS . 'toolkit/vendors' . DS . 'truebv' . DS . 'punycode.php',

  // auto-load all dorothy classes
  'api'     => __DIR__ . '/src/api.php',
  'config'  => __DIR__ . '/src/config.php',
  'd'       => __DIR__ . '/src/d.php',
  'lang'    => __DIR__ . '/src/lang.php',
  'icon'    => __DIR__ . '/src/icon.php',
  'mail'    => __DIR__ . '/src/mail.php',
  'module'  => __DIR__ . '/src/module.php',
  'page'    => __DIR__ . '/src/page.php',
  'site'    => __DIR__ . '/src/site.php',
  'user'    => __DIR__ . '/src/user.php',
  'util'    => __DIR__ . '/src/util.php',

  'article'           => __DIR__ . '/src/shop/article.php',
  'variant'           => __DIR__ . '/src/shop/variant.php',
  'cart'              => __DIR__ . '/src/shop/cart.php',
  'order'             => __DIR__ . '/src/shop/order.php',
  'product'           => __DIR__ . '/src/shop/product.php',
  'shop'              => __DIR__ . '/src/shop/shop.php',
  'orderstatus'       => __DIR__ . '/src/shop/orderStatus.php',
  'paymentmethod'     => __DIR__ . '/src/shop/paymentMethod.php',
  'paypal'            => __DIR__ . '/src/shop/payPal.php',
  'satispay'          => __DIR__ . '/src/shop/satispay.php',
));

// load all helpers
include(__DIR__ . DS . 'helpers.php');

s::start();

$d = new D();
$d->init();