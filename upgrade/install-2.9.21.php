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
function upgrade_module_2_9_21($object, $install = false)
{
    Configuration::updateValue('SOCOLISSIMO_URL', 'ws.colissimo.fr/pudo-fo-frame/storeCall.do');
    Configuration::updateValue('SOCOLISSIMO_URL_MOBILE', 'ws-mobile.colissimo.fr/');
    Configuration::updateValue('SOCOLISSIMO_SUP_URL', 'ws.colissimo.fr/supervision-pudo-frame/supervision.jsp');
    Configuration::updateValue('SOCOLISSIMO_VERSION', '2.9.21');
    return true;
}
