<?php

abstract class PaymentMethod {
	public const PayPal = 'paypal';
	public const BankTransfer = 'bank-transfer';
	public const Satispay = 'satispay';
	public const CashOnDelivery = 'cash-on-delivery';

	public static function get():array {
		return [static::PayPal, static::BankTransfer, static::Satispay, static::CashOnDelivery];
	}

	public static function name($key):string {
		$ret = lang::get('payment-method-' . $key);

		return $ret;
	}
}