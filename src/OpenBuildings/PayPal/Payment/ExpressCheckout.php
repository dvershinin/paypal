<?php

namespace OpenBuildings\PayPal;

/**
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 * @copyright 2013 OpenBuildings, Inc.
 * @license http://spdx.org/licenses/BSD-3-Clause
 */
class Payment_ExpressCheckout extends Payment {

	const API_VERSION = '98.0';

	public function get_express_checkout_details($token, array $params = [])
	{

		$params += ['TOKEN' => $token];
		
		return $this->_request('GetExpressCheckoutDetails', $params);
	}

	/**
	 * Make an SetExpressCheckout call.
	 */
	public function set_express_checkout(array $params = array())
	{
		return $this->_request('SetExpressCheckout', $this->_set_params($params));
	}

	public function do_express_checkout_payment($token, $payer_id)
	{
		$order = $this->order();

		return $this->_request('DoExpressCheckoutPayment', $this->_set_params(array(
			'TOKEN'                          => $token,
			'PAYERID'                        => $payer_id,

			// Total amount of the order
			'PAYMENTREQUEST_0_AMT'           => number_format($order['total_price'], 2, '.', ''),

			// Price of the items being sold
			'PAYMENTREQUEST_0_ITEMAMT'       => number_format($order['items_price'], 2, '.', ''),

			// Shipping costs for the whole transaction
			'PAYMENTREQUEST_0_SHIPPINGAMT'   => number_format($order['shipping_price'], 2, '.', ''),

			'PAYMENTREQUEST_0_CURRENCYCODE'  => $this->config('currency'),

			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale'
		)));
	}

	protected function _set_params(array $params = array())
	{
		$order = $this->order();

		$defaultParams = array(
			// Total amount for the transaction
			'PAYMENTREQUEST_0_AMT' => number_format($order['total_price'], 2, '.', ''),

			// Price of the items being sold
			'PAYMENTREQUEST_0_ITEMAMT' => number_format($order['items_price'], 2, '.', ''),

			// Shipping costs for the whole transaction
			'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format($order['shipping_price'], 2, '.', ''),

			'PAYMENTREQUEST_0_CURRENCYCODE' => $this->config('currency'),

			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			
			'RETURNURL' => $this->return_url(),

			'CANCELURL' => $this->cancel_url(),

			'useraction' => 'commit',

			// PayPal won't display shipping fields to the customer
			// For digital goods this field is required and it must be set to 1.
			'NOSHIPPING' => 1,

			'ADDROVERRIDE' => 0,
		);
		$params = array_merge($defaultParams, $params);

		if ($this->notify_url())
		{
			$params['PAYMENTREQUEST_0_NOTIFYURL'] = $this->notify_url();
		}

		return $params;
	}

	protected function _request($method, array $params = array())
	{
		
		$params = array(
			'METHOD'    => $method,
			'VERSION'   => Payment_ExpressCheckout::API_VERSION,
			'USER'      => $this->config('username'),
			'PWD'       => $this->config('password'),
			'SIGNATURE' => $this->config('signature'),
		) + $params;
		
		return $this->request(static::merchant_endpoint_url(), $params);
	}
	
	/**
	 * Get redirect URL to Paypal page
	 * @param boolean $commit Whether payment button is to be displayed at Paypal
	 * @param string $token Token from SetExpressCheckout
	 */
	public static function getRedirectUrl($token, $commit = true)
	{
		$params = array(
			'token' => $token
		);
		
		if ($commit) {
			$params['useraction'] = 'commit';
		}
		
		// Redirect to Paypal for payment
		return Payment::webscr_url('_express-checkout', $params);
	}
	
}
