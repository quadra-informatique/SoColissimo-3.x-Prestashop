<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once _PS_MODULE_DIR_.'socolissimo/classes/SCFields.php';

class SocolissimoRedirectModuleFrontController extends ModuleFrontController
{

	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		//parent::initContent();

		$so = new SCfields('API');

		$fields = $so->getFields();

		/* Build back the fields list for SoColissimo, gift infos are send using the JS */
		$inputs = array();
		foreach ($_GET as $key => $value)
			if (in_array($key, $fields))
				$inputs[$key] = Tools::getValue($key);

		/* for belgium number specific format */
		if (Tools::getIsset(Tools::getValue('cePays')) && Tools::getValue('cePays') == 'BE')
			if (isset($inputs['cePhoneNumber']) && strpos($inputs['cePhoneNumber'], '324') === 0)
				$inputs['cePhoneNumber'] = '+324'.Tools::substr($inputs['cePhoneNumber'], 2);

		$param_plus = array(
			/* Get the data set before */
			Tools::getValue('trParamPlus'),
			Tools::getValue('gift'),
			$so->replaceAccentedChars(Tools::getValue('gift_message'))
		);

		$inputs['trParamPlus'] = implode('|', $param_plus);
		/* Add signature to get the gift and gift message in the trParamPlus */
		$inputs['signature'] = $so->generateKey($inputs);
		// automatic settings api protocol for ssl
		$protocol = 'http://';
		if (Configuration::get('PS_SSL_ENABLED'))
			$protocol = 'https://';
		$socolissimo_url = $protocol.Configuration::get('SOCOLISSIMO_URL');
		
		Context::getContext()->smarty->assign(array(
			'inputs' => $inputs,
			'socolissimo_url' => $socolissimo_url
		));

		$this->setTemplate('redirect.tpl');
	}

}
