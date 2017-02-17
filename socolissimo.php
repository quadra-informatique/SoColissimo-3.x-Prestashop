<?php
/**
 * 2010-2016 La Poste SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules-prestashop@laposte.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Quadra Informatique <modules@quadra-informatique.fr>
 *  @copyright 2010-2016 La Poste SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of La Poste SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Socolissimo extends CarrierModule
{

    protected $config_form = false;
    protected $config_single_values_keys = false;
    protected $config_single_values_keys_exception = false;
    private $_html = '';
    private $post_errors = array();
    private $api_num_version = '4.0';
    private $config = array(
        'name' => 'La Poste - Colissimo Simplicité',
        'id_tax_rules_group' => 0,
        'url' => 'http://www.colissimo.fr/portail_colissimo/suivreResultat.do?parcelnumber=@',
        'active' => true,
        'deleted' => 0,
        'shipping_handling' => false,
        'range_behavior' => 0,
        'is_module' => true,
        'delay' => array(
            'fr' => 'Avec La Poste, Faites-vous livrer là ou vous le souhaitez en France Métropolitaine.',
            'en' => 'Do you deliver wherever you want in France.'),
        'delay_seller' => array(
            'fr' => 'Vous pouvez ici paramétrer votre tarif pour une livraison en commerce de proximité.',
            'en' => 'Price management for Pick-up shipping points.'),
        'id_zone' => 1,
        'shipping_external' => true,
        'external_module_name' => 'socolissimo',
        'need_range' => true
    );
    public $personal_data_phone_error = false;
    public $personal_data_zip_code_error = false;
    public $siret_error = false;
    public $info_partner_error = false;
    public $url = '';
    public $errors = array();
    public $initial_cost = 0;
    public $seller_cost = 0;

    public function __construct()
    {
        $this->name = 'socolissimo';
        $this->tab = 'shipping_logistics';
        $this->version = '3.0.4';
        $this->author = 'Quadra Informatique';
        $this->module_key = '8b991db851bdf7c64ca441f1a4481964';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Colissimo Simplicité');
        $this->description = $this->l('Offer your customer 5 different delivery methods with LaPoste.');
        $this->confirmUninstall = $this->l('Removing the module will also delete the associated carriers');
        $this->ps_versions_compliancy = array(
            'min' => '1.5.0.0',
            'max' => '1.6.99.99');

        $protocol = function_exists('Tools::getProtocol') ? Tools::getProtocol() : 'http://';
        if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $protocol = 'https://';
        }
        $this->url = $protocol.Tools::getShopDomainSsl().__PS_BASE_URI__.'modules/'.$this->name.'/validation.php';

        if (self::isInstalled($this->name)) {
            $warning = array();
            $so_carrier = new Carrier(Configuration::get('SOCOLISSIMO_CARRIER_ID'));
            if (Validate::isLoadedObject($so_carrier)) {
                if (!$this->checkZone((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier Zone(s)\'').' ';
                }
                if (!$this->checkGroup((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier Group\'').' ';
                }
                if (!$this->checkRange((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier Range(s)\'').' ';
                }
                if (!$this->checkDelivery((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier price delivery\'').' ';
                }
            }
            $so_carrier = new Carrier(Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER'));
            if (Validate::isLoadedObject($so_carrier)) {
                if (!$this->checkZone((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier Zone(s)\'').' ';
                }
                if (!$this->checkGroup((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier Group\'').' ';
                }
                if (!$this->checkRange((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier Range(s)\'').' ';
                }
                if (!$this->checkDelivery((int)$so_carrier->id)) {
                    $warning[] .= $this->l('\'Carrier price delivery\'').' ';
                }
            }

            //Check config and display warning
            if (!Configuration::get('SOCOLISSIMO_ID')) {
                $warning[] .= $this->l('\'Id FO\'').' ';
            }
            if (!Configuration::get('SOCOLISSIMO_KEY')) {
                $warning[] .= $this->l('\'Key\'').' ';
            }
            if (!Configuration::get('SOCOLISSIMO_URL')) {
                $warning[] .= $this->l('\'Url So\'').' ';
            }

            if (count($warning)) {
                $this->warning .= implode(' , ', $warning).$this->l('must be configured to use this module correctly').' ';
            }
        }
        $this->config_single_values_keys = array(
            'SOCOLISSIMO_CARRIER_ID',
            'SOCOLISSIMO_CARRIER_ID_SELLER',
            'SOCOLISSIMO_ID',
            'SOCOLISSIMO_USE_FANCYBOX',
            'SOCOLISSIMO_USE_IFRAME',
            'SOCOLISSIMO_KEY',
            'SOCOLISSIMO_URL',
            'SOCOLISSIMO_URL_MOBILE',
            'SOCOLISSIMO_OVERCOST',
            'SOCOLISSIMO_COST_SELLER',
            'SOCOLISSIMO_UPG_COUNTRY',
            'SOCOLISSIMO_PREPARATION_TIME',
            'SOCOLISSIMO_CARRIER_ID',
            'SOCOLISSIMO_CARRIER_ID_SELLER',
            'SOCOLISSIMO_SUP',
            'SOCOLISSIMO_SUP_URL',
            'SOCOLISSIMO_OVERCOST_TAX',
            'SOCOLISSIMO_PERSONAL_PHONE',
            'SOCOLISSIMO_PERSONAL_ZIP_CODE',
            'SOCOLISSIMO_PERSONAL_QUANTITIES',
            'SOCOLISSIMO_PERSONAL_SIRET',
            'SOCOLISSIMO_PERSONAL_DATA',
        );
        $this->config_single_values_keys_exception = array(
            'SOCOLISSIMO_PERSONAL_PHONE',
            'SOCOLISSIMO_PERSONAL_ZIP_CODE',
            'SOCOLISSIMO_PERSONAL_QUANTITIES',
            'SOCOLISSIMO_PERSONAL_SIRET',
            'SOCOLISSIMO_PERSONAL_DATA',
            'SOCOLISSIMO_PERSONAL_ACCEPT'
        );
    }

    public function install()
    {
        if (!parent::install() ||
            !Configuration::updateValue('SOCOLISSIMO_ID', null) ||
            !Configuration::updateValue('SOCOLISSIMO_KEY', null) ||
            !Configuration::updateValue('SOCOLISSIMO_URL', 'ws.colissimo.fr/pudo-fo-frame/storeCall.do') ||
            !Configuration::updateValue('SOCOLISSIMO_URL_MOBILE', 'ws-mobile.colissimo.fr/') ||
            !Configuration::updateValue('SOCOLISSIMO_PREPARATION_TIME', 1) ||
            !Configuration::updateValue('SOCOLISSIMO_COST_SELLER', false) ||
            !Configuration::updateValue('SOCOLISSIMO_OVERCOST', 3.6) ||
            !Configuration::updateValue('SOCOLISSIMO_SUP_URL', 'ws.colissimo.fr/supervision-pudo-frame/supervision.jsp') ||
            !Configuration::updateValue('SOCOLISSIMO_SUP', true) ||
            !Configuration::updateValue('SOCOLISSIMO_USE_FANCYBOX', 0) ||
            !Configuration::updateValue('SOCOLISSIMO_USE_IFRAME', true) ||
            !$this->registerHook('extraCarrier') ||
            !$this->registerHook('AdminOrder') ||
            !$this->registerHook('updateCarrier') ||
            !$this->registerHook('newOrder') ||
            !$this->registerHook('paymentTop') ||
            !$this->registerHook('backOfficeHeader')) {
            return false;
        }

        //creat config table in database
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'socolissimo_delivery_info` (
				  `id_cart` int(10) NOT NULL,
				  `id_customer` int(10) NOT NULL,
				  `delivery_mode` varchar(3) NOT NULL,
				  `prid` text(10) NOT NULL,
				  `prname` varchar(64) NOT NULL,
				  `prfirstname` varchar(64) NOT NULL,
				  `prcompladress` text NOT NULL,
				  `pradress1` text NOT NULL,
				  `pradress2` text NOT NULL,
				  `pradress3` text NOT NULL,
				  `pradress4` text NOT NULL,
				  `przipcode` text(10) NOT NULL,
				  `prtown` varchar(64) NOT NULL,
				  `cecountry` varchar(10) NOT NULL,
				  `cephonenumber` varchar(32) NOT NULL,
				  `ceemail` varchar(64) NOT NULL,
				  `cecompanyname` varchar(64) NOT NULL,
				  `cedeliveryinformation` text NOT NULL,
				  `cedoorcode1` varchar(10) NOT NULL,
				  `cedoorcode2` varchar(10) NOT NULL,
                  `codereseau` varchar(3) NOT NULL,
                  `cename` varchar(64) NOT NULL,
				  `cefirstname` varchar(64) NOT NULL,
				  `lotacheminement` varchar(64) NOT NULL,
				  `distributionsort` varchar(64) NOT NULL,
				  `versionplantri` text(10) NOT NULL,
				  `dyforwardingcharges` decimal(20,6) NOT NULL,
				  PRIMARY KEY  (`id_cart`,`id_customer`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }


        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        // Add carrier in back office
        if (!$this->createSoColissimoCarrier($this->config)) {
            return false;
        }
        // add carrier for cost seller
        if (!$this->createSoColissimoCarrierSeller($this->config)) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $so_id = (int)Configuration::get('SOCOLISSIMO_CARRIER_ID');
        $so_id_seller = (int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER');
        Configuration::deleteByName('SOCOLISSIMO_ID');
        Configuration::deleteByName('SOCOLISSIMO_USE_FANCYBOX');
        Configuration::deleteByName('SOCOLISSIMO_USE_IFRAME');
        Configuration::deleteByName('SOCOLISSIMO_KEY');
        Configuration::deleteByName('SOCOLISSIMO_URL');
        Configuration::deleteByName('SOCOLISSIMO_URL_MOBILE');
        Configuration::deleteByName('SOCOLISSIMO_OVERCOST');
        Configuration::deleteByName('SOCOLISSIMO_COST_SELLER');
        Configuration::deleteByName('SOCOLISSIMO_UPG_COUNTRY');
        Configuration::deleteByName('SOCOLISSIMO_PREPARATION_TIME');
        Configuration::deleteByName('SOCOLISSIMO_CARRIER_ID');
        Configuration::deleteByName('SOCOLISSIMO_CARRIER_ID_SELLER');
        Configuration::deleteByName('SOCOLISSIMO_SUP');
        Configuration::deleteByName('SOCOLISSIMO_SUP_URL');
        Configuration::deleteByName('SOCOLISSIMO_OVERCOST_TAX');

        if (!parent::uninstall() ||
            !Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'socolissimo_delivery_info`') ||
            !$this->unregisterHook('extraCarrier') ||
            !$this->unregisterHook('payment') ||
            !$this->unregisterHook('AdminOrder') ||
            !$this->unregisterHook('newOrder') ||
            !$this->unregisterHook('updateCarrier') ||
            !$this->unregisterHook('paymentTop') ||
            !$this->unregisterHook('backOfficeHeader')) {
            return false;
        }

        // Delete So Carrier
        $so_carrier = new Carrier($so_id);

        // If socolissimo carrier is default set other one as default
        if (Configuration::get('PS_CARRIER_DEFAULT') == (int)$so_carrier->id) {
            $carriers_d = Carrier::getCarriers($this->context->language->id);
            foreach ($carriers_d as $carrier_d) {
                if ($carrier_d['active'] && !$carrier_d['deleted'] && ($carrier_d['name'] != $this->config['name'])) {
                    Configuration::updateValue('PS_CARRIER_DEFAULT', $carrier_d['id_carrier']);
                }
            }
        }

        // Save old carrier id
        Configuration::updateValue('SOCOLISSIMO_CARRIER_ID_HIST', Configuration::get('SOCOLISSIMO_CARRIER_ID_HIST').'|'.(int)$so_carrier->id);
        $so_carrier->deleted = 1;
        if (!$so_carrier->update()) {
            return false;
        }

        // Delete So Carrier Seller
        $so_carrier = new Carrier($so_id_seller);

        // If socolissimo carrier is default set other one as default
        if (Configuration::get('PS_CARRIER_DEFAULT') == (int)$so_carrier->id) {
            $carriers_d = Carrier::getCarriers($this->context->language->id);
            foreach ($carriers_d as $carrier_d) {
                if ($carrier_d['active'] && !$carrier_d['deleted'] && ($carrier_d['name'] != $this->config['name'])) {
                    Configuration::updateValue('PS_CARRIER_DEFAULT', $carrier_d['id_carrier']);
                }
            }
        }

        // Save old carrier id
        Configuration::updateValue('SOCOLISSIMO_CARRIER_ID_HIST', Configuration::get('SOCOLISSIMO_CARRIER_ID_HIST').'|'.(int)$so_carrier->id);
        $so_carrier->deleted = 1;
        if (!$so_carrier->update()) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitColissimoModule')) == true) {
            $this->postProcess();
        }
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('colissimo_version', $this->version);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        if (!Configuration::get('SOCOLISSIMO_PERSONAL_DATA')) {
            $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure_warning.tpl');
        }

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitColissimoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array(
                array(
                    'form' => $this->getConfigForm()
                )
        ));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {

        $form = array(
            'legend' => array(
                'title' => $this->l('Colissimo Simplicity').' V'.$this->version,
                'icon' => 'icon-cogs',
            )
        );
        //======================================================================
        // INFO TAB
        if (version_compare(_PS_VERSION_, '1.6.0.6', '>')) {
            $form['tabs']['about'] = $this->l('About Colissimo Informations');

            // Check current language
            $language = new Language($this->context->language->id);
            $defaut_tpl = $this->local_path.'views/templates/admin/about.tpl';
            if ($language->iso_code != 'fr') {
                $defaut_tpl = $this->local_path.'views/templates/admin/about_en.tpl';
            }
            $form['input'][] = array(
                'tab' => 'about',
                'type' => 'html',
                'name' => 'about',
                'html_content' => $this->context->smarty->fetch($defaut_tpl),
            );
        }

        //======================================================================
        // CREDENTIALS TAB
        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            $form['input'][] = array(
                'type' => 'free',
                'desc' => '<h3>'.$this->l('Merchant Informations').'</h3>'
            );
        } else {
            $form['tabs']['credentials'] = $this->l('Merchant Informations');
        }

        $form['input'][] = array(
            'tab' => 'credentials',
            'type' => 'text',
            'col' => 2,
            'required' => true,
            'label' => $this->l('Phone number'),
            'name' => 'SOCOLISSIMO_PERSONAL_PHONE',
            'desc' => $this->l('Example  0144183004'),
        );
        $form['input'][] = array(
            'tab' => 'credentials',
            'type' => 'text',
            'col' => 2,
            'required' => true,
            'label' => $this->l('Zip code'),
            'name' => 'SOCOLISSIMO_PERSONAL_ZIP_CODE',
            'desc' => $this->l('Example  92300'),
        );
        $form['input'][] = array(
            'tab' => 'credentials',
            'type' => 'select',
            'required' => true,
            'label' => $this->l('Mean number of parcels'),
            'name' => 'SOCOLISSIMO_PERSONAL_QUANTITIES',
            'options' => array(
                'query' => array(
                    array(
                        'id' => '< 250 colis / mois',
                        'name' => $this->l('< 250 colis / mois')
                    ),
                    array(
                        'id' => '> 250 colis / mois',
                        'name' => $this->l('> 250 colis / mois')
                    ),
                ),
                'id' => 'id',
                'name' => 'name'
            ),
        );
        $form['input'][] = array(
            'tab' => 'credentials',
            'type' => 'text',
            'col' => 2,
            'required' => true,
            'label' => $this->l('Siret'),
            'name' => 'SOCOLISSIMO_PERSONAL_SIRET',
            'desc' => $this->l('Siret is 14 number'),
        );

        $form['input'][] = array(
            'tab' => 'credentials',
            'type' => 'checkbox',
            'required' => true,
            'label' => $this->l('Terms & conditions'),
            'name' => 'SOCOLISSIMO_PERSONAL',
            'desc' => $this->l('In case of refusal, you can sent an email at the following address').
            ' : <a style="color: #268ccd;" href="mailto: modules-prestashop@laposte.fr">modules-prestashop@laposte.fr</a>',
            'values' => array(
                'query' => array(
                    array(
                        'id' => 'ACCEPT',
                        'name' => $this->l('I accept that informations concerning the number of parcels are sent to our partner La poste - Colissimo'),
                        'val' => 1
                    ),
                ),
                'id' => 'id',
                'name' => 'name',
            )
        );

        //======================================================================
        // GENERAL TAB
        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            $form['input'][] = array(
                'type' => 'free',
                'desc' => '<h3>'.$this->l('Your Colissimo Box').'</h3>'
            );
        } else {
            $form['tabs']['general'] = $this->l('Your Colissimo Box');
        }

        $form['input'][] = array(
            'tab' => 'general',
            'col' => 3,
            'type' => 'text',
            'required' => true,
            'label' => $this->l('Encryption key'),
            'name' => 'SOCOLISSIMO_KEY',
            'desc' => $this->l('Available in your ').' <a href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >Colissimo Box </a>'.'<br/>'.
            $this->l('by using the menu "Applications > Delivery > Choice of delivery methods" ')
        );
        $form['input'][] = array(
            'tab' => 'general',
            'col' => 3,
            'type' => 'text',
            'required' => true,
            'label' => $this->l('Front Office Identifier'),
            'name' => 'SOCOLISSIMO_ID',
            'desc' => $this->l('Available in your ').' <a href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >Colissimo Box </a>'.'<br/>'.
            $this->l('by using the menu "Applications > Delivery > Choice of delivery methods" ')
        );
        $form['input'][] = array(
            'tab' => 'general',
            'col' => 3,
            'type' => 'text',
            'required' => true,
            'label' => $this->l('Order Preparation time'),
            'suffix' => $this->l('Day(s)'),
            'name' => 'SOCOLISSIMO_PREPARATION_TIME',
            'desc' => $this->l('Business days from Monday to Friday').
            '<br/>'.$this->l('Must be the same parameter as in your ').
            ' <a href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >Colissimo Box </a>'
        );
        $form['input'][] = array(
            'tab' => 'general',
            'type' => 'free',
            'name' => 'url_note',
            'desc' => '<hr/><strong>'.$this->l('Please fill in these two addresses in your').
            ' <a href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >Colissimo Box </a></strong><ul>'.
            '<li>'.$this->l('In the "Delivery options selection page"').'</li>'.
            '<li>'.$this->l('In the "Delivery options selection page (mobile version)"').'</li></ul>',
        );

        $form['input'][] = array(
            'tab' => 'general',
            'type' => 'free',
            'label' => $this->l('When the customer has successfully selected the delivery method (Validation)'),
            'name' => 'VALIDATION_URL',
        );
        $form['input'][] = array(
            'tab' => 'general',
            'type' => 'free',
            'label' => $this->l('When the client could not select the delivery method (Failed)'),
            'name' => 'RETURN_URL',
        );

        //======================================================================
        // SYSTEM TAB
        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            $form['input'][] = array(
                'type' => 'free',
                'desc' => '<h3>'.$this->l('Colissimo simplicity system parameters').'</h3>'
            );
        } else {
            $form['tabs']['system'] = $this->l('Colissimo simplicity system parameters');
        }

        $form['input'][] = array(
            'tab' => 'system',
            'col' => 3,
            'type' => 'text',
            'required' => true,
            'label' => $this->l('Url of back office Colissimo.'),
            'name' => 'SOCOLISSIMO_URL',
            'desc' => $this->l('Url of back office Colissimo.')
        );
        $form['input'][] = array(
            'tab' => 'system',
            'col' => 3,
            'type' => 'text',
            'required' => true,
            'label' => $this->l('Url So Mobile'),
            'name' => 'SOCOLISSIMO_URL_MOBILE',
            'desc' => $this->l('Url of back office Colissimo Mobile. Customers with smartphones or ipad will be redirect there. Warning, this url do not allow delivery in belgium ')
        );
        $form['input'][] = array(
            'tab' => 'system',
            'type' => (version_compare(_PS_VERSION_, '1.6.0.7', '<') ? 'radio' : 'switch'),
            'label' => $this->l('Supervision'),
            'name' => 'SOCOLISSIMO_SUP',
            'is_bool' => true,
            'required' => true,
            'desc' => $this->l('Enable or disable the check availability  of Colissimo service.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->l('Disabled')
                )
            ),
        );
        $form['input'][] = array(
            'tab' => 'system',
            'col' => 3,
            'type' => 'text',
            'required' => true,
            'label' => $this->l('Url Supervision'),
            'name' => 'SOCOLISSIMO_SUP_URL',
            'desc' => $this->l('The monitor URL is to ensure the availability of the socolissimo service. We strongly recommend that you do not disable it')
        );

        //======================================================================
        // PRESTASHOP TAB
        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            $form['input'][] = array(
                'type' => 'free',
                'desc' => '<h3>'.$this->l('Colissimo simplicity prestashop parameters').'</h3>'
            );
        } else {
            $form['tabs']['prestashop'] = $this->l('Colissimo simplicity prestashop parameters');
        }

        $form['input'][] = array(
            'tab' => 'prestashop',
            'col' => 3,
            'type' => 'radio',
            'label' => $this->l('Display Mode'),
            'name' => 'DISPLAY_TYPE',
            'required' => false,
            'desc' => $this->l('Choose your display mode for Simplicity formula'),
            'values' => array(
                array
                    (
                    'id' => 'classic_on',
                    'value' => 0,
                    'label' => $this->l('Classic')
                ),
                array(
                    'id' => 'fancybox_on',
                    'value' => 1,
                    'label' => $this->l('Fancybox')
                ),
                array(
                    'id' => 'iframe_on',
                    'value' => 2,
                    'label' => $this->l('Iframe')
                )
            )
        );
        $form['input'][] = array(
            'tab' => 'prestashop',
            'type' => 'select',
            'required' => true,
            'label' => $this->l('Home carrier'),
            'name' => 'SOCOLISSIMO_CARRIER_ID',
            'options' => array(
                'query' => Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                'id' => 'id_carrier',
                'name' => 'name'
            ),
            'desc' => $this->l('Carrier used to get "Colissimo at home" cost')
        );
        $form['input'][] = array(
            'tab' => 'prestashop',
            'type' => (version_compare(_PS_VERSION_, '1.6.0.7', '<') ? 'radio' : 'switch'),
            'label' => $this->l('Withdrawal point cost'),
            'name' => 'SOCOLISSIMO_COST_SELLER',
            'is_bool' => true,
            'required' => true,
            'desc' => $this->l('This cost override the normal cost for seller delivery.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->l('Disabled')
                )
            ),
        );
        $form['input'][] = array(
            'tab' => 'prestashop',
            'type' => 'select',
            'required' => true,
            'label' => $this->l('Withdrawal point carrier'),
            'name' => 'SOCOLISSIMO_CARRIER_ID_SELLER',
            'options' => array(
                'query' => Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                'id' => 'id_carrier',
                'name' => 'name'
            ),
            'desc' => $this->l('Carrier used to get "Colissimo at a withdrawal point" cost')
        );

        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            $form['input'][] = array(
                'type' => 'free',
                'desc' => '<h3>'.$this->l('Save').'</h3>'
            );
        }

        $form['submit'] = array(
            'title' => $this->l('Save')
        );

        return $form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $return = array();
        foreach ($this->config_single_values_keys as $key) {
            $return[$key] = Configuration::get($key);
        }

        if (!Configuration::get('SOCOLISSIMO_USE_FANCYBOX') && !Configuration::get('SOCOLISSIMO_USE_IFRAME')) {
            $display_type = 0;
        } elseif (Configuration::get('SOCOLISSIMO_USE_FANCYBOX')) {
            $display_type = 1;
        } elseif (Configuration::get('SOCOLISSIMO_USE_IFRAME')) {
            $display_type = 2;
        }
        $return['DISPLAY_TYPE'] = $display_type;

        $return['VALIDATION_URL'] = '<p class="form-control-static">'.htmlentities($this->url, ENT_NOQUOTES, 'UTF-8').'</p>';
        $return['RETURN_URL'] = '<p class="form-control-static">'.htmlentities($this->url, ENT_NOQUOTES, 'UTF-8').'</p>';
        return $return;
    }

    protected function savePreactivationRequest()
    {
        $employee = new Employee((int)Context::getContext()->cookie->id_employee);

        $data = array(
            'iso_lang' => Tools::strtolower($this->context->language->iso_code),
            'iso_country' => Tools::strtoupper($this->context->country->iso_code),
            'host' => $_SERVER['HTTP_HOST'],
            'ps_version' => _PS_VERSION_,
            'ps_creation' => _PS_CREATION_DATE_,
            'partner' => $this->name,
            'firstname' => $employee->firstname,
            'lastname' => $employee->lastname,
            'email' => $employee->email,
            'shop' => Configuration::get('PS_SHOP_NAME'),
            'type' => 'home',
            'phone' => Configuration::get('SOCOLISSIMO_PERSONAL_PHONE'),
            'zipcode' => Configuration::get('SOCOLISSIMO_PERSONAL_ZIP_CODE'),
            'fields' => serialize(
                array(
                    'quantities' => Configuration::get('SOCOLISSIMO_PERSONAL_QUANTITIES'),
                    'siret' => Configuration::get('SOCOLISSIMO_PERSONAL_SIRET'),
                )
            ),
        );

        $query = http_build_query($data);

        return @Tools::file_get_contents('http://api.prestashop.com/partner/premium/set_request.php?'.$query);
    }

    private function postProcess()
    {

        if (Tools::getValue('SOCOLISSIMO_ID') == null) {
            $this->context->controller->errors[] = $this->l('ID SO not specified');
        }

        if (Tools::getValue('SOCOLISSIMO_KEY') == null) {
            $this->context->controller->errors[] = $this->l('Key SO not specified');
        }

        if (Tools::getValue('SOCOLISSIMO_PREPARATION_TIME') == null) {
            $this->context->controller->errors[] = $this->l('Preparation time not specified');
        } elseif (!Validate::isInt(Tools::getValue('SOCOLISSIMO_PREPARATION_TIME'))) {
            $this->context->controller->errors[] = $this->l('Invalid preparation time');
        }

        if (Tools::getValue('SOCOLISSIMO_URL') == null) {
            $this->context->controller->errors[] = $this->l('Front URL is not specified');
        }
        if (Tools::getValue('SOCOLISSIMO_URL_MOBILE') == null) {
            $this->context->controller->errors[] = $this->l('Front mobile URL is not specified');
        }
        if (Tools::getValue('SOCOLISSIMO_SUP_URL') == null) {
            $this->context->controller->errors[] = $this->l('Supervision URL is not specified');
        }

        if ((int)Tools::getValue('SOCOLISSIMO_CARRIER_ID') == (int)Tools::getValue('SOCOLISSIMO_CARRIER_ID_SELLER')) {
            $this->context->controller->errors[] = $this->l('Socolissimo carrier cannot be the same as socolissimo CC');
        }

        if (!count($this->context->controller->errors)) {
            // re allocation id socolissimo if needed
            if ((int)Tools::getValue('SOCOLISSIMO_CARRIER_ID') != (int)Configuration::get('SOCOLISSIMO_CARRIER_ID')) {
                Configuration::updateValue(
                    'SOCOLISSIMO_CARRIER_ID_HIST',
                    Configuration::get(
                        'SOCOLISSIMO_CARRIER_ID_HIST'
                    ).'|'.(int)Tools::getValue('SOCOLISSIMO_CARRIER_ID')
                );
                Configuration::updateValue(
                    'SOCOLISSIMO_CARRIER_ID',
                    (int)Tools::getValue('SOCOLISSIMO_CARRIER_ID')
                );
                $this->reallocationCarrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'));
            }
            // re allocation id socolissimo CC  if needed
            if ((int)Tools::getValue('SOCOLISSIMO_CARRIER_ID_SELLER') != (int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER')) {
                Configuration::updateValue(
                    'SOCOLISSIMO_CARRIER_ID_HIST',
                    Configuration::get(
                        'SOCOLISSIMO_CARRIER_ID_HIST'
                    ).'|'.(int)Tools::getValue('SOCOLISSIMO_CARRIER_ID_SELLER')
                );
                Configuration::updateValue(
                    'SOCOLISSIMO_CARRIER_ID_SELLER',
                    (int)Tools::getValue('SOCOLISSIMO_CARRIER_ID_SELLER')
                );
                $this->reallocationCarrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER'));
            }
            foreach ($this->config_single_values_keys as $key) {
                if (!array_search($key, $this->config_single_values_keys_exception)) {
                    Configuration::updateValue($key, Tools::getValue($key));
                }
            }
            if (Tools::getValue('DISPLAY_TYPE') == 1) {
                Configuration::updateValue('SOCOLISSIMO_USE_FANCYBOX', true);
                Configuration::updateValue('SOCOLISSIMO_USE_IFRAME', false);
            }
            if (Tools::getValue('DISPLAY_TYPE') == 2) {
                Configuration::updateValue('SOCOLISSIMO_USE_IFRAME', true);
                Configuration::updateValue('SOCOLISSIMO_USE_FANCYBOX', false);
            }
            if (Tools::getValue('DISPLAY_TYPE') == 0) {
                Configuration::updateValue('SOCOLISSIMO_USE_IFRAME', false);
                Configuration::updateValue('SOCOLISSIMO_USE_FANCYBOX', false);
            }
        }
        $reload_credit = false;

        if (Configuration::get('SOCOLISSIMO_PERSONAL_DATA')) {
            if (Tools::getValue('SOCOLISSIMO_PERSONAL_PHONE') && (Tools::getValue('SOCOLISSIMO_PERSONAL_PHONE') != Configuration::get('SOCOLISSIMO_PERSONAL_PHONE'))) {
                $reload_credit = true;
            }
            if (Tools::getValue('SOCOLISSIMO_PERSONAL_ZIP_CODE') && (Tools::getValue('SOCOLISSIMO_PERSONAL_ZIP_CODE') != Configuration::get('SOCOLISSIMO_PERSONAL_ZIP_CODE'))) {
                $reload_credit = true;
            }
            if (Tools::getValue('SOCOLISSIMO_PERSONAL_QUANTITIES') && (Tools::getValue('SOCOLISSIMO_PERSONAL_QUANTITIES') != Configuration::get('SOCOLISSIMO_PERSONAL_QUANTITIES'))) {
                $reload_credit = true;
            }
            if (Tools::getValue('SOCOLISSIMO_PERSONAL_SIRET') && (Tools::getValue('SOCOLISSIMO_PERSONAL_SIRET') != Configuration::get('SOCOLISSIMO_PERSONAL_SIRET'))) {
                $reload_credit = true;
            }
        }

        if (!Configuration::get('SOCOLISSIMO_PERSONAL_DATA') || $reload_credit) {
            if (!(bool)preg_match('#^(([\d]{2})([\s]){0,1}){5}$#', Tools::getValue('SOCOLISSIMO_PERSONAL_PHONE'))) {
                $this->context->controller->errors[] = $this->l('Phone number is incorrect');
            }
            if (!(bool)preg_match('#^(([0-8][0-9])|(9[0-5]))[0-9]{3}$#', Tools::getValue('SOCOLISSIMO_PERSONAL_ZIP_CODE'))) {
                $this->context->controller->errors[] = $this->l('Zip code is incorrect');
            }
            if (!Tools::getValue('SOCOLISSIMO_PERSONAL_QUANTITIES')) {
                $this->context->controller->errors[] = $this->l('Mean number is incorrect');
            }
            if (!$this->isSiret(Tools::getValue('SOCOLISSIMO_PERSONAL_SIRET'))) {
                $this->context->controller->errors[] = $this->l('Siret is incorrect');
            }
            if (!Tools::getValue('SOCOLISSIMO_PERSONAL_ACCEPT')) {
                $this->context->controller->errors[] = $this->l('You must accept terms and conditions');
            }
            if (!count($this->context->controller->errors)) {
                Configuration::updateValue('SOCOLISSIMO_PERSONAL_PHONE', Tools::getValue('SOCOLISSIMO_PERSONAL_PHONE'));
                Configuration::updateValue('SOCOLISSIMO_PERSONAL_ZIP_CODE', Tools::getValue('SOCOLISSIMO_PERSONAL_ZIP_CODE'));
                Configuration::updateValue('SOCOLISSIMO_PERSONAL_QUANTITIES', Tools::getValue('SOCOLISSIMO_PERSONAL_QUANTITIES'));
                Configuration::updateValue('SOCOLISSIMO_PERSONAL_SIRET', Tools::getValue('SOCOLISSIMO_PERSONAL_SIRET'));
                Configuration::updateValue('SOCOLISSIMO_PERSONAL_ACCEPT', Tools::getValue('SOCOLISSIMO_PERSONAL_ACCEPT'));
                if ($this->savePreactivationRequest()) {
                    Configuration::updateValue('SOCOLISSIMO_PERSONAL_DATA', 1);
                }
            }
        }
    }

    /**
     * Add the CSS & JavaScript files in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookExtraCarrier($params)
    {
        $carrier_so = new Carrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'));

        if (!isset($carrier_so) || !$carrier_so->active) {
            return '';
        }

        $country = new Country((int)$params['address']->id_country);
        $carriers = Carrier::getCarriers(
            $this->context->language->id,
            true,
            false,
            false,
            null,
            (defined('ALL_CARRIERS') ? ALL_CARRIERS : Carrier::ALL_CARRIERS)
        );

        $id_carrier = $carrier_so->id;

        // bug fix for cart rule with restriction
        CartRule::autoAddToCart($this->context);

        // For now works only with single shipping !
        if (method_exists($params['cart'], 'carrierIsSelected')) {
            if ($params['cart']->carrierIsSelected((int)$carrier_so->id, $params['address']->id)) {
                $id_carrier = (int)$carrier_so->id;
            }
        }
        $customer = new Customer($params['address']->id_customer);

        $gender = array(
            '1' => 'MR',
            '2' => 'MME',
            '3' => 'MLE');

        if (in_array((int)$customer->id_gender, array(
                1,
                2))) {
            $cecivility = $gender[(int)$customer->id_gender];
        } else {
            $cecivility = 'MR';
        }

        $tax_rate = Tax::getCarrierTaxRate($id_carrier, isset($params['cart']->id_address_delivery) ? $params['cart']->id_address_delivery : null);
        $tax_rate_seller = Tax::getCarrierTaxRate(
            Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER'),
            isset($params['cart']->id_address_delivery) ? $params['cart']->id_address_delivery : null
        );
        if ($tax_rate) {
            $std_cost_with_taxes = number_format((float)$this->initial_cost * (1 + ($tax_rate / 100)), 2, ',', ' ');
        } else {
            $std_cost_with_taxes = number_format((float)$this->initial_cost, 2, ',', ' ');
        }
        $seller_cost_with_taxes = 0;
        if ($this->seller_cost) {
            if ($tax_rate_seller) {
                $seller_cost_with_taxes = number_format((float)$this->seller_cost * (1 + ($tax_rate_seller / 100)), 2, ',', ' ');
            } else {
                $seller_cost_with_taxes = number_format((float)$this->seller_cost, 2, ',', ' ');
            }
        }
        $free_shipping = false;

        $rules = $params['cart']->getCartRules();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                    $free_shipping = true;
                    break;
                }
            }
            if (!$free_shipping) {
                $key_search = $id_carrier.',';
                $deliveries_list = $params['cart']->getDeliveryOptionList();
                foreach ($deliveries_list as $deliveries) {
                    foreach ($deliveries as $key => $elt) {
                        if ($key == $key_search) {
                            $free_shipping = $elt['is_free'];
                        }
                    }
                }
            }
        } else {
            // for cart rule with restriction
            $key_search = $id_carrier.',';
            $deliveries_list = $params['cart']->getDeliveryOptionList();
            foreach ($deliveries_list as $deliveries) {
                foreach ($deliveries as $key => $elt) {
                    if ($key == $key_search) {
                        $free_shipping = $elt['is_free'];
                    }
                }
            }
        }
        //weight must be sent in grams
        $weight_unit = Configuration::get('PS_WEIGHT_UNIT');
        if (Tools::strtoupper($weight_unit) == 'G') {
            $weight = (float)$params['cart']->getTotalWeight();
        } else if (Tools::strtoupper($weight_unit) == 'LB' || Tools::strtoupper($weight_unit) == 'LBS') {
            $weight = (float)$params['cart']->getTotalWeight() * 453.6;
        } else {
            $weight = (float)$params['cart']->getTotalWeight() * 1000;
        }
        // Keep this fields order (see doc.)
        $inputs = array(
            'pudoFOId' => Configuration::get('SOCOLISSIMO_ID'),
            'ceName' => $this->replaceAccentedChars(Tools::substr($params['address']->lastname, 0, 34)),
            'dyPreparationTime' => (int)Configuration::Get('SOCOLISSIMO_PREPARATION_TIME'),
            'dyForwardingCharges' => $std_cost_with_taxes,
            'dyForwardingChargesCMT' => $seller_cost_with_taxes,
            'trClientNumber' => (int)$params['address']->id_customer,
            'orderId' => $this->formatOrderId((int)$params['address']->id),
            'numVersion' => $this->getNumVersion(),
            'ceCivility' => $cecivility,
            'ceFirstName' => $this->replaceAccentedChars(Tools::substr($params['address']->firstname, 0, 29)),
            'ceCompanyName' => $this->replaceAccentedChars(Tools::substr($params['address']->company, 0, 38)),
            'ceAdress3' => $this->replaceAccentedChars(Tools::substr($params['address']->address1, 0, 38)),
            'ceAdress4' => $this->replaceAccentedChars(Tools::substr($params['address']->address2, 0, 38)),
            'ceZipCode' => $this->replaceAccentedChars($params['address']->postcode),
            'ceTown' => $this->replaceAccentedChars(Tools::substr($params['address']->city, 0, 32)),
            'ceEmail' => $this->replaceAccentedChars($params['cookie']->email),
            'cePhoneNumber' => $this->replaceAccentedChars(
                str_replace(array(
                ' ',
                '.',
                '-',
                ',',
                ';',
                '/',
                '\\',
                '(',
                ')'), '', $params['address']->phone_mobile)
            ),
            'dyWeight' => $weight,
            'trParamPlus' => $carrier_so->id,
            'trReturnUrlKo' => htmlentities($this->url, ENT_NOQUOTES, 'UTF-8'),
            'trReturnUrlOk' => htmlentities($this->url, ENT_NOQUOTES, 'UTF-8'),
            'CHARSET' => 'UTF-8',
            'cePays' => $country->iso_code,
            'trInter' => 1,
            'ceLang' => 'FR'
        );
        if (!$inputs['dyForwardingChargesCMT'] && !Configuration::get('SOCOLISSIMO_COST_SELLER')) {
            unset($inputs['dyForwardingChargesCMT']);
        }

        // set params for Api 3.0 if needed
        $inputs = $this->setInputParams($inputs);

        // generate key for API
        $inputs['signature'] = $this->generateKey($inputs);

        // calculate lowest cost
        $from_cost = $std_cost_with_taxes;
        if ($seller_cost_with_taxes || (Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER') && Configuration::get('SOCOLISSIMO_COST_SELLER'))) {
            if ((float)str_replace(',', '.', $seller_cost_with_taxes) < (float)str_replace(',', '.', $std_cost_with_taxes)) {
                $from_cost = $seller_cost_with_taxes;
            }
        }
        $rewrite_active = true;
        if (!Configuration::get('PS_REWRITING_SETTINGS')) {
            $rewrite_active = false;
        }

        $link = new Link();
        $module_link = $link->getModuleLink('socolissimo', 'redirect', array(), true);
        $module_link_mobile = $link->getModuleLink('socolissimo', 'redirectmobile', array(), true);

        // automatic settings api protocol for ssl
        $protocol = 'http://';
        if (Configuration::get('PS_SSL_ENABLED')) {
            $protocol = 'https://';
        }
        $from_mention = $this->l('From');
        $initial_cost = $from_cost.$this->l(' €');
        $tax_mention = $this->l(' TTC');
        if ($free_shipping) {
            $from_mention = '';
            $initial_cost = $this->l('Free (Will be apply after address selection)');
            $tax_mention = '';
        }
        $this->context->smarty->assign(array(
            'select_label' => $this->l('Select delivery mode'),
            'edit_label' => $this->l('Edit delivery mode'),
            'token' => sha1('socolissimo'._COOKIE_KEY_.Context::getContext()->cookie->id_cart),
            'urlSo' => $protocol.Configuration::get('SOCOLISSIMO_URL').'?trReturnUrlKo='.htmlentities($this->url, ENT_NOQUOTES, 'UTF-8'),
            'urlSoMobile' => $protocol.Configuration::get('SOCOLISSIMO_URL_MOBILE').'?trReturnUrlKo='.htmlentities($this->url, ENT_NOQUOTES, 'UTF-8'),
            'id_carrier' => $id_carrier,
            'id_carrier_seller' => Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER'),
            'inputs' => $inputs,
            'initialCost_label' => $from_mention,
            'initialCost' => $initial_cost, // to change label for price in tpl
            'taxMention' => $tax_mention, // to change label for price in tpl
            'finishProcess' => $this->l('To choose SoColissimo, click on a delivery method'),
            'rewrite_active' => $rewrite_active,
            'link_socolissimo' => $module_link,
            'link_socolissimo_mobile' => $module_link_mobile
        ));

        $ids = array();
        foreach ($carriers as $carrier) {
            $ids[] = $carrier['id_carrier'];
        }

        if ($params['cart']->id_carrier == Configuration::Get(
            'SOCOLISSIMO_CARRIER_ID'
        ) && $this->getDeliveryInfos($this->context->cart->id, $this->context->customer->id)) {
            $this->context->smarty->assign('already_select_delivery', true);
        } else {
            $this->context->smarty->assign('already_select_delivery', false);
        }

        if ((Configuration::Get('SOCOLISSIMO_ID') != null) && (Configuration::get('SOCOLISSIMO_KEY') != null) && (Configuration::Get('SOCOLISSIMO_PERSONAL_DATA')) && $this->checkAvailibility() && $this->checkSoCarrierAvailable((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'))
            && in_array((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'), $ids)) {

            if (Context::getContext()->getMobileDevice() || $this->isIpad() || $this->isMobile()) {
                if (Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                    return $this->fetchTemplate('socolissimo_redirect_mobile_opc.tpl');
                } else {
                    return $this->fetchTemplate('socolissimo_redirect_mobile.tpl');
                }
            }
            // route display mode
            if (Configuration::get('PS_ORDER_PROCESS_TYPE') || Configuration::get('SOCOLISSIMO_USE_FANCYBOX')) {
                return $this->fetchTemplate('socolissimo_fancybox.tpl');
            }
            if (Configuration::get('SOCOLISSIMO_USE_IFRAME')) {
                return $this->fetchTemplate('socolissimo_iframe.tpl');
            }
            return $this->fetchTemplate('socolissimo_redirect.tpl');
        } else {
            $tab_id_soco = explode('|', Configuration::get('SOCOLISSIMO_CARRIER_ID_HIST'));
            $tab_id_soco[] = $id_carrier;
            $this->context->smarty->assign('ids', $tab_id_soco);
            return $this->fetchTemplate('socolissimo_error.tpl');
        }
    }

    public function hookNewOrder($params)
    {
        if ($params['order']->id_carrier != Configuration::get('SOCOLISSIMO_CARRIER_ID')) {
            return;
        }

        $order = $params['order'];
        $order->id_address_delivery = $this->isSameAddress((int)$order->id_address_delivery, (int)$order->id_cart, (int)$order->id_customer);
        $order->update();
        Configuration::updateValue('SOCOLISSIMO_CONFIGURATION_OK', true);
    }

    public function hookAdminOrder($params)
    {
        require_once _PS_MODULE_DIR_.'socolissimo/classes/SCFields.php';

        $delivery_mode = array(
            'DOM' => 'Livraison à domicile',
            'BPR' => 'Livraison en Bureau de Poste',
            'A2P' => 'Livraison Commerce de proximité',
            'MRL' => 'Livraison Commerce de proximité',
            'CMT' => 'Livraison Commerce',
            'CIT' => 'Livraison en Cityssimo',
            'ACP' => 'Agence ColiPoste',
            'CDI' => 'Centre de distribution',
            'BDP' => 'Bureau de poste Belge',
            'RDV' => 'Livraison sur Rendez-vous');

        $order = new Order($params['id_order']);
        $address_delivery = new Address((int)$order->id_address_delivery, (int)$params['cookie']->id_lang);

        $so_carrier = new Carrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'));
        $delivery_infos = $this->getDeliveryInfos((int)$order->id_cart, (int)$order->id_customer);

        // in 2.8.0 country is mandatory
        $sql = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'country c
										  LEFT JOIN '._DB_PREFIX_.'country_lang cl ON cl.id_lang = '.(int)$params['cookie']->id_lang.'
										  AND cl.id_country = c.id_country WHERE iso_code = "'.pSQL($delivery_infos['cecountry']).'"');
        $name_country = $sql['name'];

        if (((int)$order->id_carrier == (int)$so_carrier->id || in_array((int)$order->id_carrier, explode('|', Configuration::get('SOCOLISSIMO_CARRIER_ID_HIST')))) && !empty($delivery_infos)) {
            $html = '<br><div class="panel"><fieldset style="width:400px;"><legend><img src="'.$this->_path.'logo.gif" alt="" /> ';
            $html .= $this->l('Colissimo Simplicité').'</legend><b>'.$this->l('Delivery mode').' : </b>';

            $sc_fields = new SCFields($delivery_infos['delivery_mode']);

            switch ($sc_fields->delivery_mode) {
                case SCFields::HOME_DELIVERY:
                    $html .= $delivery_mode[$delivery_infos['delivery_mode']].'<br /><br />';
                    $html .= '<b>'.$this->l('Customer').' : </b>'.
                        Tools::htmlentitiesUTF8($address_delivery->firstname).' '.Tools::htmlentitiesUTF8($address_delivery->lastname).'<br />'.
                        (!empty($delivery_infos['cecompanyname']) ? '<b>'
                            .$this->l('Company').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['cecompanyname']).'<br/>' : '' ).
                        (!empty($delivery_infos['ceemail']) ? '<b>'
                            .$this->l('E-mail address').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['ceemail']).'<br/>' : '' ).
                        (!empty($delivery_infos['cephonenumber']) ? '<b>'
                            .$this->l('Phone').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['cephonenumber']).'<br/><br/>' : '' ).
                        '<b>'.$this->l('Customer address').' : </b><br/>'
                        .(Tools::htmlentitiesUTF8($address_delivery->address1) ? Tools::htmlentitiesUTF8($address_delivery->address1).'<br />' : '')
                        .(!empty($address_delivery->address2) ? Tools::htmlentitiesUTF8($address_delivery->address2).'<br />' : '')
                        .(!empty($address_delivery->postcode) ? Tools::htmlentitiesUTF8($address_delivery->postcode).'<br />' : '')
                        .(!empty($address_delivery->city) ? Tools::htmlentitiesUTF8($address_delivery->city).'<br />' : '')
                        .(!empty($address_delivery->country) ? Tools::htmlentitiesUTF8($address_delivery->country).'<br />' : '')
                        .(!empty($address_delivery->other) ? '<hr><b>'
                            .$this->l('Other').' : </b>'.Tools::htmlentitiesUTF8($address_delivery->other).'<br /><br />' : '')
                        .(!empty($delivery_infos['cedoorcode1']) ? '<b>'
                            .$this->l('Door code').' 1 : </b>'.Tools::htmlentitiesUTF8($delivery_infos['cedoorcode1']).'<br/>' : '' )
                        .(!empty($delivery_infos['cedoorcode2']) ? '<b>'
                            .$this->l('Door code').' 2 : </b>'.Tools::htmlentitiesUTF8($delivery_infos['cedoorcode2']).'<br/>' : '' )
                        .(!empty($delivery_infos['cedeliveryinformation']) ? '<b>'.$this->l('Delivery information').' : </b>'.
                            Tools::htmlentitiesUTF8($delivery_infos['cedeliveryinformation']).'<br/><br/>' : '' );
                    break;
                case SCFields::RELAY_POINT:
                    $html .= str_replace('+', ' ', $delivery_mode[$delivery_infos['delivery_mode']]).'<br/>'
                        .(!empty($delivery_infos['prid']) ? '<b>'.
                            $this->l('Pick up point ID').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['prid']).'<br/>' : '' )
                        .(!empty($delivery_infos['prname']) ? '<b>'.
                            $this->l('Pick up point').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['prname']).'<br/>' : '' )
                        .'<b>'.$this->l('Pick up point address').' : </b><br/>'
                        .(!empty($delivery_infos['pradress1']) ? Tools::htmlentitiesUTF8($delivery_infos['pradress1']).'<br/>' : '' )
                        .(!empty($delivery_infos['pradress2']) ? Tools::htmlentitiesUTF8($delivery_infos['pradress2']).'<br/>' : '' )
                        .(!empty($delivery_infos['pradress3']) ? Tools::htmlentitiesUTF8($delivery_infos['pradress3']).'<br/>' : '' )
                        .(!empty($delivery_infos['pradress4']) ? Tools::htmlentitiesUTF8($delivery_infos['pradress4']).'<br/>' : '' )
                        .(!empty($delivery_infos['przipcode']) ? Tools::htmlentitiesUTF8($delivery_infos['przipcode']).'<br/>' : '' )
                        .(!empty($delivery_infos['prtown']) ? Tools::htmlentitiesUTF8($delivery_infos['prtown']).'<br/>' : '' )
                        .(!empty($name_country) ? Tools::htmlentitiesUTF8($name_country).'<br/>' : '' )
                        .(!empty($delivery_infos['ceemail']) ? '<b>'.
                            $this->l('Email').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['ceemail']).'<br/>' : '' )
                        .(!empty($delivery_infos['cephonenumber']) ? '<b>'.
                            $this->l('Phone').' : </b>'.Tools::htmlentitiesUTF8($delivery_infos['cephonenumber']).'<br/><br/>' : '' );

                    break;
            }
            $html .= '</fieldset></div>';
            return $html;
        }
    }

    public function hookUpdateCarrier($params)
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if ((int)$params['id_carrier'] == (int)Configuration::get('SOCOLISSIMO_CARRIER_ID')) {
            Configuration::updateValue('SOCOLISSIMO_CARRIER_ID', (int)$params['carrier']->id);
            Configuration::updateValue(
                'SOCOLISSIMO_CARRIER_ID_HIST',
                Configuration::get('SOCOLISSIMO_CARRIER_ID_HIST').'|'.(int)$params['carrier']->id
            );
        }
        if ((int)$params['id_carrier'] == (int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER')) {
            Configuration::updateValue('SOCOLISSIMO_CARRIER_ID_SELLER', (int)$params['carrier']->id);
            Configuration::updateValue(
                'SOCOLISSIMO_CARRIER_ID_HIST',
                Configuration::get('SOCOLISSIMO_CARRIER_ID_HIST').'|'.(int)$params['carrier']->id
            );
            // force this carrier inactive
            $carrier = new Carrier((int)$params['carrier']->id);
            if ($carrier->active) {
                $carrier->active = 0;
                $carrier->update();
            }
        }
    }

    public function hookPaymentTop($params)
    {
        if ($params['cart']->id_carrier == Configuration::get('SOCOLISSIMO_CARRIER_ID') && !$this->getDeliveryInfos((int)$params['cookie']->id_cart, (int)$params['cookie']->id_customer)) {
            $params['cart']->id_carrier = 0;
        }
    }

    /**
     * Generate the signed key
     *
     * @static
     * @param $params
     * @return string
     */
    public function generateKey($params)
    {
        $str = '';

        foreach ($params as $key => $value) {
            if (!in_array(Tools::strtoupper($key), array(
                    'SIGNATURE'))) {
                $str .= utf8_decode($value);
            }
        }

        return sha1($str.Tools::strtolower(Configuration::get('SOCOLISSIMO_KEY')));
    }

    public static function createSoColissimoCarrier($config)
    {
        $carrier = new Carrier();
        $carrier->name = $config['name'];
        $carrier->id_tax_rules_group = $config['id_tax_rules_group'];
        $carrier->id_zone = $config['id_zone'];
        $carrier->url = $config['url'];
        $carrier->active = $config['active'];
        $carrier->deleted = $config['deleted'];
        $carrier->delay = $config['delay'];
        $carrier->shipping_handling = $config['shipping_handling'];
        $carrier->range_behavior = $config['range_behavior'];
        $carrier->is_module = $config['is_module'];
        $carrier->shipping_external = $config['shipping_external'];
        $carrier->external_module_name = $config['external_module_name'];
        $carrier->need_range = $config['need_range'];

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            if ($language['iso_code'] == 'fr') {
                $carrier->delay[$language['id_lang']] = $config['delay'][$language['iso_code']];
            }
            if ($language['iso_code'] == 'en') {
                $carrier->delay[$language['id_lang']] = $config['delay'][$language['iso_code']];
            }
        }

        if ($carrier->add()) {

            if (Shop::isFeatureActive()) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }

            Configuration::updateValue('SOCOLISSIMO_CARRIER_ID', (int)$carrier->id);
            $groups = Group::getgroups(true);

            foreach ($groups as $group) {
                Db::getInstance()->execute(
                    'INSERT INTO '._DB_PREFIX_.'carrier_group VALUE (\''.(int)$carrier->id.'\',\''.(int)$group['id_group'].'\')'
                );
            }

            $range_price = new RangePrice();
            $range_price->id_carrier = $carrier->id;
            $range_price->delimiter1 = '0';
            $range_price->delimiter2 = '10000';
            $range_price->add();

            $range_weight = new RangeWeight();
            $range_weight->id_carrier = $carrier->id;
            $range_weight->delimiter1 = '0';
            $range_weight->delimiter2 = '10000';
            $range_weight->add();

            //copy logo
            if (!copy(dirname(__FILE__).'/views/img/socolissimo.jpg', _PS_SHIP_IMG_DIR_.'/'.$carrier->id.'.jpg')) {
                return false;
            }
            return true;
        }
        return false;
    }

    public static function createSoColissimoCarrierSeller($config)
    {
        $carrier = new Carrier();
        $carrier->name = $config['name'].' - Commerce de proximité';
        $carrier->id_tax_rules_group = $config['id_tax_rules_group'];
        $carrier->id_zone = $config['id_zone'];
        $carrier->url = $config['url'];
        $carrier->active = 0;
        $carrier->deleted = $config['deleted'];
        $carrier->delay = $config['delay'];
        $carrier->shipping_handling = $config['shipping_handling'];
        $carrier->range_behavior = $config['range_behavior'];
        $carrier->is_module = $config['is_module'];
        $carrier->shipping_external = $config['shipping_external'];
        $carrier->external_module_name = $config['external_module_name'];
        $carrier->need_range = $config['need_range'];

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            if ($language['iso_code'] == 'fr') {
                $carrier->delay[$language['id_lang']] = $config['delay_seller'][$language['iso_code']];
            }
            if ($language['iso_code'] == 'en') {
                $carrier->delay[$language['id_lang']] = $config['delay_seller'][$language['iso_code']];
            }
        }

        if ($carrier->add()) {
            if (Shop::isFeatureActive()) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
            Configuration::updateValue('SOCOLISSIMO_CARRIER_ID_SELLER', (int)$carrier->id);
            $groups = Group::getgroups(true);

            foreach ($groups as $group) {
                Db::getInstance()->execute(
                    'INSERT INTO '._DB_PREFIX_.'carrier_group VALUE (\''.(int)$carrier->id.'\',\''.(int)$group['id_group'].'\')'
                );
            }

            $range_price = new RangePrice();
            $range_price->id_carrier = $carrier->id;
            $range_price->delimiter1 = '0';
            $range_price->delimiter2 = '10000';
            $range_price->add();

            $range_weight = new RangeWeight();
            $range_weight->id_carrier = $carrier->id;
            $range_weight->delimiter1 = '0';
            $range_weight->delimiter2 = '10000';
            $range_weight->add();

            //copy logo
            if (!copy(dirname(__FILE__).'/views/img/socolissimo.jpg', _PS_SHIP_IMG_DIR_.'/'.$carrier->id.'.jpg')) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function getDeliveryInfos($id_cart, $id_customer)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'socolissimo_delivery_info
			WHERE id_cart = '.(int)$id_cart.' AND id_customer = '.(int)$id_customer
        );
    }

    public function isSameAddress($id_address, $id_cart, $id_customer)
    {
        $return = Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'socolissimo_delivery_info
			WHERE id_cart =\''.(int)$id_cart.'\' AND id_customer =\''.(int)$id_customer.'\''
        );

        if (!$return) {
            return $id_address;
        }

        $ps_address = new Address((int)$id_address);
        $new_address = new Address();
        $sql = Db::getInstance()->getRow('SELECT c.id_country, cl.name FROM '._DB_PREFIX_.'country c
										  LEFT JOIN '._DB_PREFIX_.'country_lang cl ON cl.id_lang = '.(int)$this->context->language->id.'
										  AND cl.id_country = c.id_country WHERE iso_code = "'.pSQL($return['cecountry']).'"');

        $iso_code = $sql['id_country'];

        if ($this->upper($ps_address->lastname) != $this->upper($return['prname']) || $ps_address->id_country != $iso_code || $this->upper($ps_address->firstname) != $this->upper($return['prfirstname'])
            || $this->upper($ps_address->address1) != $this->upper($return['pradress3']) || $this->upper($ps_address->address2) != $this->upper($return['pradress2']) || $this->upper($ps_address->postcode)
            != $this->upper($return['przipcode']) || $this->upper($ps_address->city) != $this->upper($return['prtown']) || str_replace(array(
                ' ',
                '.',
                '-',
                ',',
                ';',
                '+',
                '/',
                '\\',
                '+',
                '(',
                ')'), '', $ps_address->phone_mobile) != $return['cephonenumber']) {
            $new_address->id_customer = (int)$id_customer;
            $firstname_company = preg_replace('/\d/', '', Tools::substr($return['prfirstname'], 0, 31));
            $lastname_company = preg_replace('/\d/', '', Tools::substr($return['prname'], 0, 32));
            $firstname = preg_replace('/\d/', '', Tools::substr($return['cefirstname'], 0, 32));
            $lastname = preg_replace('/\d/', '', Tools::substr($return['cename'], 0, 32));
            $firstname_company_formatted = trim($this->formatName($firstname_company));
            $lastname_company_formatted = trim($this->formatName($lastname_company));
            $new_address->lastname = trim($this->formatName($lastname));
            $new_address->firstname = trim($this->formatName($firstname));
            $new_address->postcode = $return['przipcode'];
            $new_address->city = $return['prtown'];
            $new_address->id_country = $iso_code;
            $new_address->alias = 'Colissimo - '.date('d-m-Y');
            $new_address->phone_mobile = $return['cephonenumber'];

            if (!in_array($return['delivery_mode'], array(
                    'DOM',
                    'RDV'))) {
				$new_address->company = $firstname_company_formatted.' '.$lastname_company_formatted;
                $new_address->active = 1;
                $new_address->deleted = 1;
                $new_address->address1 = $return['pradress1'];
                $new_address->address2 = $return['pradress2'];
                $new_address->add();
            } else {
                $new_address->address1 = $return['pradress3'];
                ((isset($return['pradress2'])) ? $new_address->address2 = $return['pradress2'] : $new_address->address2 = '');
                ((isset($return['pradress1'])) ? $new_address->other .= $return['pradress1'] : $new_address->other = '');
                ((isset($return['pradress4'])) ? $new_address->other .= ' | '.$return['pradress4'] : $new_address->other = '');
                $new_address->postcode = $return['przipcode'];
                $new_address->city = $return['prtown'];
                $new_address->id_country = $iso_code;
                $new_address->alias = 'Colissimo - '.date('d-m-Y');
                $new_address->add();
            }
            return (int)$new_address->id;
        }
        return (int)$ps_address->id;
    }

    public function checkZone($id_carrier)
    {
        return (bool)Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'carrier_zone WHERE id_carrier = '.(int)$id_carrier);
    }

    public function checkGroup($id_carrier)
    {
        return (bool)Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'carrier_group WHERE id_carrier = '.(int)$id_carrier);
    }

    public function checkRange($id_carrier)
    {
        $sql = '';
        $carrier = new Carrier($id_carrier);
        if ($carrier->shipping_method) {
            switch ($carrier->shipping_method) {
                case '2':
                    $sql = 'SELECT * FROM '._DB_PREFIX_.'range_price WHERE id_carrier = '.(int)$id_carrier;
                    break;
                case '1':
                    $sql = 'SELECT * FROM '._DB_PREFIX_.'range_weight WHERE id_carrier = '.(int)$id_carrier;
                    break;
            }
        }
        if (!$sql) {
            switch (Configuration::get('PS_SHIPPING_METHOD')) {
                case '0':
                    $sql = 'SELECT * FROM '._DB_PREFIX_.'range_price WHERE id_carrier = '.(int)$id_carrier;
                    break;
                case '1':
                    $sql = 'SELECT * FROM '._DB_PREFIX_.'range_weight WHERE id_carrier = '.(int)$id_carrier;
                    break;
            }
        }
        return (bool)Db::getInstance()->getRow($sql);
    }

    public function checkDelivery($id_carrier)
    {
        return (bool)Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'delivery WHERE id_carrier = '.(int)$id_carrier);
    }

    public function upper($str_in)
    {
        return Tools::strtoupper(str_replace('-', ' ', Tools::link_rewrite($str_in)));
    }

    public function lower($str_in)
    {
        return Tools::strtolower(str_replace('-', ' ', Tools::link_rewrite($str_in)));
    }

    /**
     * Generate good order id format.
     *
     * @param $id
     * @return string
     */
    public function formatOrderId($id)
    {
        $str_len = Tools::strlen($id);
        while ($str_len < 5) {
            $id = '0'.$id;
            $str_len = Tools::strlen($id);
        }
        return $id;
    }

    public function checkAvailibility()
    {
        if (Configuration::get('SOCOLISSIMO_SUP')) {
            $protocol = 'http://';
            if (Configuration::get('PS_SSL_ENABLED')) {
                $protocol = 'https://';
            }
            $ctx = @stream_context_create(array(
                    'http' => array(
                        'timeout' => 1)));
            $return = @Tools::file_get_contents($protocol.Configuration::get('SOCOLISSIMO_SUP_URL'), 0, $ctx);

            if (ini_get('allow_url_fopen') == 0) {
                return true;
            } else {
                if (!empty($return)) {
                    preg_match('[OK]', $return, $matches);
                    if ($matches[0] == 'OK') {
                        return true;
                    }
                    return false;
                }
            }
        }
        return true;
    }

    private function checkSoCarrierAvailable($id_carrier)
    {
        $carrier = new Carrier((int)$id_carrier);
        $address = new Address((int)$this->context->cart->id_address_delivery);
        $id_zone = Address::getZoneById((int)$address->id);


        if ($carrier->shipping_method) {
            if (($carrier->shipping_method == 1 && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false && !$carrier->is_free) || ($carrier->shipping_method == 2 && $carrier->getMaxDeliveryPriceByPrice($id_zone) === false && !$carrier->is_free)) {
                return false;
            }
        } else {
            if ((Configuration::get('PS_SHIPPING_METHOD') && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false && !$carrier->is_free) || (!Configuration::get('PS_SHIPPING_METHOD') && $carrier->getMaxDeliveryPriceByPrice($id_zone)
                === false && !$carrier->is_free)) {
                return false;
            }
        }

        // If out-of-range behavior carrier is set on "Desactivate carrier"
        if ($carrier->range_behavior) {
            // Get id zone
            $id_zone = (int)$this->context->country->id_zone;
            if (isset($this->context->cart->id_address_delivery) && $this->context->cart->id_address_delivery) {
                $id_zone = Address::getZoneById((int)$this->context->cart->id_address_delivery);
            }

            if ($carrier->shipping_method) {
                if (($carrier->shipping_method == 1 && !Carrier::checkDeliveryPriceByWeight((int)$carrier->id, $this->context->cart->getTotalWeight(), $id_zone) ) || ($carrier->shipping_method == 2 && !Carrier::checkDeliveryPriceByPrice(
                    (int)$carrier->id,
                    $this->context->cart->getOrderTotal(
                        true,
                        Cart::BOTH_WITHOUT_SHIPPING
                    ),
                    $id_zone,
                    $this->context->cart->id_currency
                )
                )
                ) {
                    return false;
                }
            } else {
                if ((Configuration::get('PS_SHIPPING_METHOD') && !Carrier::checkDeliveryPriceByWeight((int)$carrier->id, $this->context->cart->getTotalWeight(), $id_zone) ) || (!Configuration::get('PS_SHIPPING_METHOD')
                    && !Carrier::checkDeliveryPriceByPrice(
                        (int)$carrier->id,
                        $this->context->cart->getOrderTotal(
                            true,
                            Cart::BOTH_WITHOUT_SHIPPING
                        ),
                        $id_zone,
                        $this->context->cart->id_currency
                    )
                    )
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
		// for order in BO
		if (!$this->context->cart instanceof Cart || !$this->context->cart->id) {
			$this->context->cart = new Cart($params->id);
		}
        // for label in tpl
        if (!$this->initial_cost) {
            $this->initial_cost = $this->getStandardCost();
        }
        if (!$this->seller_cost) {
            $this->seller_cost = $this->getSellerCost();
        }
        $delivery_info = $this->getDeliveryInfos($this->context->cart->id, $this->context->cart->id_customer);

        // apply overcost if needed
        if (!empty($delivery_info)) {
            // check api already return a shipping cost ?
            $api_price = $this->getApiPrice((int)$this->context->cart->id);
            if ($api_price) {
                $carrier_colissimo = new Carrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'));
                $address = new Address((int)$this->context->cart->id_address_delivery);
                $tax = $carrier_colissimo->getTaxesRate($address);
                // must retrieve the price without tax if needed
                if ($tax) {
                    (float)$tax_rate = ((float)$tax / 100) + 1;
                    $api_price = (float)$api_price / (float)$tax_rate;
                }
                return (float)$api_price;
            }
            if ($delivery_info['delivery_mode'] == 'A2P' && Configuration::get('SOCOLISSIMO_COST_SELLER') && $delivery_info['cecountry'] == 'FR') {
                if (Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER')) {
                    $carrier = new Carrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER'));
                    $address = new Address((int)$this->context->cart->id_address_delivery);
                    $id_zone = Address::getZoneById((int)$address->id);
                    $products = $this->context->cart->getProducts();
                    $additional_shipping_cost = 0;
                    //Additional shipping cost on product
                    foreach ($products as $product) {
                        if (!$product['is_virtual']) {
                            $additional_shipping_cost += (float)$product['additional_shipping_cost'] * $product['quantity'];
                        }
                    }
                    if ($carrier->shipping_handling) {
                        return $this->getCostByShippingMethod($carrier, $id_zone) + (float)$additional_shipping_cost + (float)Configuration::get('PS_SHIPPING_HANDLING');
                    } else {
                        return $this->getCostByShippingMethod($carrier, $id_zone) + (float)$additional_shipping_cost;
                    }
                }
            }
            return $shipping_cost;
        }
        return $shipping_cost;
    }

    public function getSellerCost()
    {
        if (Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER') && Configuration::get('SOCOLISSIMO_COST_SELLER')) {
            $carrier = new Carrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER'));
            $address = new Address((int)$this->context->cart->id_address_delivery);
            $id_zone = Address::getZoneById((int)$address->id);
            $products = $this->context->cart->getProducts();
            $additional_shipping_cost = 0;
            //Additional shipping cost on product
            foreach ($products as $product) {
                if (!$product['is_virtual']) {
                    $additional_shipping_cost += (float)$product['additional_shipping_cost'] * $product['quantity'];
                }
            }
            if ($carrier->shipping_handling) {
                return $this->getCostByShippingMethod($carrier, $id_zone) + (float)$additional_shipping_cost + (float)Configuration::get('PS_SHIPPING_HANDLING');
            } else {
                return $this->getCostByShippingMethod($carrier, $id_zone) + (float)$additional_shipping_cost;
            }
        }
        return false;
    }

    public function getCostByShippingMethod($carrier, $id_zone)
    {

        if ($carrier->shipping_method) {
            if ($carrier->shipping_method == 1) {
                if ($carrier->getDeliveryPriceByWeight($this->context->cart->getTotalWeight(), $id_zone)) {
                    return $carrier->getDeliveryPriceByWeight($this->context->cart->getTotalWeight(), $id_zone);
                }
            }
            if ($carrier->shipping_method == 2) {
                if ($carrier->getDeliveryPriceByPrice(
                    $this->context->cart->getOrderTotal(
                        true,
                        Cart::BOTH_WITHOUT_SHIPPING
                    ),
                    $id_zone,
                    $this->context->cart->id_currency
                )
                ) {
                    return $carrier->getDeliveryPriceByPrice(
                        $this->context->cart->getOrderTotal(
                            true,
                            Cart::BOTH_WITHOUT_SHIPPING
                        ),
                        $id_zone,
                        $this->context->cart->id_currency
                    );
                }
            }
        } else {
            if (Configuration::get('PS_SHIPPING_METHOD')) {
                if ($carrier->getDeliveryPriceByWeight($this->context->cart->getTotalWeight(), $id_zone)) {
                    return $carrier->getDeliveryPriceByWeight($this->context->cart->getTotalWeight(), $id_zone);
                }
            }
            if (!Configuration::get('PS_SHIPPING_METHOD')) {
                if ($carrier->getDeliveryPriceByPrice(
                    $this->context->cart->getOrderTotal(
                        true,
                        Cart::BOTH_WITHOUT_SHIPPING
                    ),
                    $id_zone,
                    $this->context->cart->id_currency
                )
                ) {
                    return $carrier->getDeliveryPriceByPrice(
                        $this->context->cart->getOrderTotal(
                            true,
                            Cart::BOTH_WITHOUT_SHIPPING
                        ),
                        $id_zone,
                        $this->context->cart->id_currency
                    );
                }
            }
        }
        return false;
    }

    public function getStandardCost()
    {
        if (Configuration::get('SOCOLISSIMO_CARRIER_ID')) {
            $carrier = new Carrier((int)Configuration::get('SOCOLISSIMO_CARRIER_ID'));
            $address = new Address((int)$this->context->cart->id_address_delivery);
            $id_zone = Address::getZoneById((int)$address->id);
            $products = $this->context->cart->getProducts();
            $additional_shipping_cost = 0;
            //Additional shipping cost on product
            foreach ($products as $product) {
                if (!$product['is_virtual']) {
                    $additional_shipping_cost += (float)$product['additional_shipping_cost'] * $product['quantity'];
                }
            }

            if ($carrier->shipping_handling) {
                return $this->getCostByShippingMethod($carrier, $id_zone) + (float)$additional_shipping_cost + (float)Configuration::get('PS_SHIPPING_HANDLING');
            } else {
                return $this->getCostByShippingMethod($carrier, $id_zone) + (float)$additional_shipping_cost;
            }
        }
        return false;
    }

    public function getOrderShippingCostExternal($params)
    {
        return false;
    }

    public function getNumVersion()
    {
        return $this->api_num_version;
    }

    /**
     * Return the cecivility customer
     *
     * @return string
     */
    public function getTitle(Customer $customer)
    {
        $gender = new Gender($customer->id_gender, $this->context->language->id);
        return $gender->name;
    }

    /**
     * @param $str
     * @return mixed
     */
    public function replaceAccentedChars($str)
    {
        $str = preg_replace(
            array(
            /* Lowercase */
            '/[\x{0105}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u',
            '/[\x{00E7}\x{010D}\x{0107}]/u',
            '/[\x{010F}]/u',
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}]/u',
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u',
            '/[\x{0142}\x{013E}\x{013A}]/u',
            '/[\x{00F1}\x{0148}]/u',
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u',
            '/[\x{0159}\x{0155}]/u',
            '/[\x{015B}\x{0161}]/u',
            '/[\x{00DF}]/u',
            '/[\x{0165}]/u',
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u',
            '/[\x{00FD}\x{00FF}]/u',
            '/[\x{017C}\x{017A}\x{017E}]/u',
            '/[\x{00E6}]/u',
            '/[\x{0153}]/u',
            /* Uppercase */
            '/[\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u',
            '/[\x{00C7}\x{010C}\x{0106}]/u',
            '/[\x{010E}]/u',
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{011A}\x{0118}]/u',
            '/[\x{0141}\x{013D}\x{0139}]/u',
            '/[\x{00D1}\x{0147}]/u',
            '/[\x{00D3}]/u',
            '/[\x{0158}\x{0154}]/u',
            '/[\x{015A}\x{0160}]/u',
            '/[\x{0164}]/u',
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}]/u',
            '/[\x{017B}\x{0179}\x{017D}]/u',
            '/[\x{00C6}]/u',
            '/[\x{0152}]/u',
            ),
            array(
            'a',
            'c',
            'd',
            'e',
            'i',
            'l',
            'n',
            'o',
            'r',
            's',
            'ss',
            't',
            'u',
            'y',
            'z',
            'ae',
            'oe',
            'A',
            'C',
            'D',
            'E',
            'L',
            'N',
            'O',
            'R',
            'S',
            'T',
            'U',
            'Z',
            'AE',
            'OE'
            ),
            $str
        );
        $array_unauthorised_api = array(
            ';',
            '€',
            '~',
            '#',
            '{',
            '(',
            '[',
            '|',
            '\\',
            '^',
            ')',
            ']',
            '=',
            '}',
            '$',
            '¤',
            '£',
            '%',
            'μ',
            '*',
            '§',
            '!',
            '°',
            '²',
            '"');
        foreach ($array_unauthorised_api as $key => $value) {
            $str = str_replace($value, '', $str);
        }
        $str = preg_replace('/\s+/', ' ', $str);
        return $str;
    }

    /**
     * @param array
     * @return array
     */
    public function setInputParams($inputs)
    {
        $get_mobile_device = Context::getContext()->getMobileDevice();

        // set api params for 4.0 and mobile
        if ($get_mobile_device || $this->isIpad() || $this->isMobile()) {
            unset($inputs['CHARSET']);
            $inputs['numVersion'] = '4.0';
        }
        return $inputs;
    }

    /**
     * Check if agent user is iPad(for so_mobile)
     * @return bool
     */
    public function isIpad()
    {
        return (bool)strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
    }

    public function isMobile()
    {
        if (method_exists(Context::getContext()->mobile_detect, 'isMobile')) {
            return (bool)Context::getContext()->mobile_detect->isMobile();
        } else {
            return false;
        }
    }

    public function fetchTemplate($name)
    {
        $views = 'views/templates/';
        if (@filemtime(dirname(__FILE__).'/'.$views.'hook/'.$name)) {
            return $this->display(__FILE__, $views.'hook/'.$name);
        } elseif (@filemtime(dirname(__FILE__).'/'.$views.'front/'.$name)) {
            return $this->display(__FILE__, $views.'front/'.$name);
        } elseif (@filemtime(dirname(__FILE__).'/'.$views.'admin/'.$name)) {
            return $this->display(__FILE__, $views.'admin/'.$name);
        }
    }

    public function getCarrierShop($id_shop, $id_socolissimo)
    {

        return Db::getInstance()->ExecuteS('SELECT c.name, c.id_carrier
        FROM '._DB_PREFIX_.'carrier c
        LEFT JOIN '._DB_PREFIX_.'carrier_shop sh ON sh.id_shop = '.(int)$id_shop.' AND sh.id_carrier = c.id_carrier
        WHERE c.deleted = 0 AND c.id_carrier <> '.(int)$id_socolissimo);
    }

    public function formatName($name)
    {
        return preg_replace('/[0-9!<>,;?=+()@#"°{}_$%:]/', '', Tools::stripslashes($name));
    }

    public function reallocationCarrier($id_socolissimo)
    {
        // carrier must be module carrier
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'carrier SET
            shipping_handling = 0,
            is_module = 1,
            shipping_external = 1,
            need_range = 1,
            external_module_name = "socolissimo"
            WHERE  id_carrier = '.(int)$id_socolissimo);

        // old carrier no longer linked with socolissimo
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'carrier SET
            is_module = 0,
            external_module_name = ""
            WHERE  id_carrier NOT IN ( '.(int)Configuration::get('SOCOLISSIMO_CARRIER_ID_SELLER').','.(int)Configuration::get('SOCOLISSIMO_CARRIER_ID').')');
    }

    /**
     * Validate SIRET Code Taken from prestashop core for compatibility 1.4 reason
     * @static
     * @param $siret SIRET Code
     * @return boolean Return true if is valid
     */
    public function isSiret($siret)
    {
        if (Tools::strlen($siret) != 14) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i != 14; $i++) {
            $tmp = ((($i + 1) % 2) + 1) * (int)$siret[$i];
            if ($tmp >= 10) {
                $tmp -= 9;
            }
            $sum += $tmp;
        }
        return ($sum % 10 === 0);
    }

    public function getApiPrice($id_cart)
    {
        if ((int)$id_cart) {
            return Db::getInstance()->getValue('SELECT dyforwardingcharges
            FROM '._DB_PREFIX_.'socolissimo_delivery_info
            WHERE id_cart = '.(int)$id_cart);
        }
        return false;
    }

    /**
     * Return colissimo availables langs.
     */
    public function getAvailableLanguages()
    {

        $langs = Language::getLanguages();

        foreach ($langs as $key => $lang) {
            switch ($lang['iso_code']) {
                case 'fr':
                    break;
                default:
                    unset($langs[$key]);
            }
        }
        return $langs;
    }
}
