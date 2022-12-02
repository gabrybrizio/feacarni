<?php

require dirname(__DIR__, 2) . '/app/init.php';
ini_set('display_errors', '0');

api::addEndpoint('paypalCreateOrder', 'payPal::createOrder', [], true);
api::addEndpoint('paypalExecutePayment', 'payPal::captureOrder', ['paypalOrderId'], true);

api::addEndpoint('satispayConfirmOrder', 'satispay::confirmOrder', ['satispayOrderId', 'orderId'], true);

api::addEndpoint('sendOrder', 'order::send', ['orderId', 'paymentMethod'], true);

api::addEndpoint('cartCount', 'cart::count', [], true);
api::addEndpoint('cartRemoveProduct', 'cart::removeProduct', ['productId', 'variantId'], true);
api::addEndpoint('cartUpdate', 'cart::update', ['productId', 'quantity', 'sign', 'variantId'], true);
api::addEndpoint('saveDigitalOrderData', 'cart::saveDigitalOrderData', [], true, true);

api::addEndpoint('contactForm', 'site::contactForm', ['nome','email','messaggio','privacy','contact_me','marketing'], true);
api::addEndpoint('userSignup', 'user::signup', [], true, true);
api::addEndpoint('userUpdate', 'user::update', [], false, true);

api::init();