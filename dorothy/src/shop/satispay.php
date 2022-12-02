<?php

class satispay {

    private static function init() {
        SatispayGBusiness\Api::setSandbox(config::satispay_sandbox());
        SatispayGBusiness\Api::setPublicKey(config::satispay_public_key());
        SatispayGBusiness\Api::setPrivateKey(config::satispay_private_key());
        SatispayGBusiness\Api::setKeyId(config::satispay_key_id());
    }

    public static function createPaymentId(int $amount):string {
        static::init();

        $payment = \SatispayGBusiness\Payment::create([
            "flow" => "MATCH_CODE",
            "amount_unit" => $amount,
            "currency" => "EUR",
            "callback_url" => ""
        ]);

        return $payment->id;
    }

    public static function confirmOrder($satispayOrderId, $orderId) {
        static::init();

        if (!$satispayOrderId) {
            throw new Exception('$satispayOrderId is required.');
        }

        $payment = \SatispayGBusiness\Payment::get($satispayOrderId);

        if ($payment->status = 'ACCEPTED') {
            order::confirmOrder($orderId, paymentMethod::Satispay, [], json_encode($payment));
        } else {
            throw new Exception('$payment->status is not ACCEPTED.');
        }
    }
}