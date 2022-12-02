<?php

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PayPal {

  /**
   * Returns PayPal HTTP client instance with environment that has access
   * credentials context. Use this instance to invoke PayPal APIs, provided the
   * credentials have access.
   *
   * @return void
   */
  public static function client():PayPalHttpClient {
    return new PayPalHttpClient(self::environment());
  }

  /**
   * Set up and return PayPal PHP SDK environment with PayPal access credentials.
   * This sample uses SandboxEnvironment. In production, use ProductionEnvironment.
   */
  public static function environment() {
    $clientId = config::paypal_client_id();
    $clientSecret = config::paypal_client_secret();

    if (config::paypal_sandbox()) {
      return new SandboxEnvironment($clientId, $clientSecret);
    } else {
      return new ProductionEnvironment($clientId, $clientSecret);
    }
  }

  /**
   *This is the sample function to create an order. It uses the
   *JSON body returned by buildRequestBody() to create an order.
   */
  public static function createOrder() {
    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');

    // Leggo il totale del carrello
    $cart = cart::getCartByUser(user::guid());
    $amount = $cart->total;
    $guid = $cart->guid;

    $request->body = self::buildRequestBody($guid, $amount);

    // Call PayPal to set up a transaction
    $client = PayPal::client();
    $response = $client->execute($request);

    // Return the response to the client.
    return $response->result;
  }

  /**
   * Setting up the JSON request body for creating the order with minimum request body. The intent in the
   * request body should be "AUTHORIZE" for authorize intent flow.
   *
   */
  private static function buildRequestBody($referenceId, $amount) {
    $ret = [
      'intent' => 'CAPTURE',
      'application_context' => [
        //'return_url' => 'https://example.com/return',
        //'cancel_url' => 'https://example.com/cancel',
        'shipping_preference' => 'NO_SHIPPING', // nasconde la scelta dell'indirizzo su paypal
      ],
      'purchase_units' =>
        [ 0 => [ 'amount' =>
                  ['currency_code' => 'EUR',
                        'value' => number_format($amount, 2, '.', '')
                  ],
                  'reference_id' => $referenceId
                  ]
          ]
    ];

      return $ret;
    }

  /**
   *This function can be used to capture an order payment by passing the approved
   *order ID as argument.
   *
   *@param orderId
   *@param debug
   *@returns
   */
  public static function captureOrder($paypalOrderId):bool {
    $ret = false;

    try {
      $request = new OrdersCaptureRequest($paypalOrderId);

      // Call PayPal to capture an authorization
      $client = PayPal::client();
      $response = $client->execute($request);
      $response->statusCode;

      if ($response->result->status === 'COMPLETED') {
        $paypalOrderId = $response->result->id;
        $paypalCapture =  $response->result->purchase_units[0]->payments->captures[0];
        $orderId = $response->result->purchase_units[0]->reference_id;
        //$amount = $paypalCapture->amount->value;

        $extraParams = [
          'paypalOrderId' => $paypalOrderId,
          'paypalCaptureId' => $paypalCapture->id
        ];

        order::confirmOrder($orderId, paymentMethod::PayPal, $extraParams, json_encode($response));
        $ret = true;
      }
    } catch (Throwable $th) {
      throw $th;
    }

    return $ret;
  }
}