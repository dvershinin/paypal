<?php

namespace OpenBuildings\PayPal;

/**
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 * @copyright 2013 OpenBuildings, Inc.
 * @license http://spdx.org/licenses/BSD-3-Clause
 */
class Payment_Recurring extends Payment_ExpressCheckout {

	protected function _set_params(array $params = array())
	{
		$result = array_replace(parent::_set_params($params), array(
			'L_BILLINGTYPE0'                   => 'RecurringPayments',
			'L_BILLINGAGREEMENTDESCRIPTION0'   => $this->config('description'),
			'L_PAYMENTREQUEST_0_ITEMCATEGORY0' => 'Digital',
		));
		
		return $result;
	}

	public function transaction_amount()
	{
		$amount = $this->config('amount_per_month');

		if ($this->config('charged_yearly'))
		{
			$amount *= 12;
		}

		return $amount;
	}

	public function create_recurring_payments_profile($token, array $params)
	{
		
		$params+= [
			'TOKEN'             => $token,
			'PROFILESTARTDATE'  => gmdate('Y-m-d\TH:i:s.00\Z', strtotime('+1 hour')),
			'DESC'              => $this->config('description'),
			'MAXFAILEDATTEMPTS' => $this->config('max_failed_attempts'),
			'AUTOBILLOUTAMT'    => 'AddToNextBilling',
			'AMT'               => $this->config('amount'),
			'CURRENCYCODE'      => $this->config('paypal.currency'),
		];
		
		return $this->_request('CreateRecurringPaymentsProfile', $params);
	}

	public function manage_recurring_payments_profile_status(array $params)
	{
		return $this->_request('ManageRecurringPaymentsProfileStatus', $params);
	}

	public function get_recurring_payments_profile_details($profile_id)
	{
		return $this->_request('GetRecurringPaymentsProfileDetails', array(
			'PROFILEID' => $profile_id
		));
	}

	public function bill_outstanding_amount(array $params)
	{
		return $this->_request('BillOutstandingAmount', $params);
	}
}