__PayPal SDK for ExpressCheckout and Adaptive Payments.__

[![Build Status](https://travis-ci.org/OpenBuildings/paypal.png?branch=master)](https://travis-ci.org/OpenBuildings/paypal)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/OpenBuildings/paypal/badges/quality-score.png?s=53b5b5f60d66af528e241107cd7466af31b5df7d)](https://scrutinizer-ci.com/g/OpenBuildings/paypal/)
[![Code Coverage](https://scrutinizer-ci.com/g/OpenBuildings/paypal/badges/coverage.png?s=a95e1eef67c247cd5114cce36c33fe9cbea5e604)](https://scrutinizer-ci.com/g/OpenBuildings/paypal/)
[![Latest Stable Version](https://poser.pugx.org/openbuildings/paypal/v/stable.png)](https://packagist.org/packages/openbuildings/paypal)

Features:
 - recurring payments
 - simple payments
 - parallel payments
 - chained payments

Installation
------------

You could use this library in your project by, adjust your composer.json to include

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/dvershinin/paypal"
        }
    ],
    
and

    "require": {
        "openbuildings/paypal": "dev-master"

then on the command line, run:

    php composer.phar update

[Learn more about Composer](http://getcomposer.org).

Usage
-----

Here is a simple usage example performing a payment with ExpressCheckout:

``` php

// Get a Payment instance using the ExpressCheckout driver
$payment = OpenBuildings\PayPal\Payment::instance('ExpressCheckout');

// Set the order
$payment->order(array(
    'items_price' => 10,
    'shipping_price' => 3,
    'total_price' => 13
));

// Set additional params needed for ExpressCheckout
$payment->return_url('example.com/success');
$payment->cancel_url('example.com/success');

// Send a SetExpressCheckout API call
$response = $payment->set_express_checkout();

// Redirect to Paypal for payment
$paypalUrl = Payment::webscr_url('_express-checkout', [
    'useraction' => 'commit',
    'token' => $response['TOKEN'],
]);
return Redirect::to($paypalUrl); // example redirect call for Laravel

// On subsequent page, finish the payment with the token and the payer id received.
$response = $payment->do_express_checkout_payment(
    Input::get('token'), 
			 Input::get('PayerID')
);

```

Documentation
-------------

 * [Getting started](docs/getting-started.md)
 * [Configuration](docs/configuration.md)
 * [ExpressCheckout](docs/ExpressCheckout.md)
 * [Recurring Payments](docs/recurring.md) ([API docs](https://developer.paypal.com/docs/classic/express-checkout/ht_ec-recurringPaymentProfile-curl-etc/))
 * [Adaptive Payments](docs/adaptive-payments.md)

Contributing
------------

Read the [Contribution guidelines](CONTRIBUTING.md).

License
-------

Licensed under BSD-3-Clause open-source license.

[License file](LICENSE)
