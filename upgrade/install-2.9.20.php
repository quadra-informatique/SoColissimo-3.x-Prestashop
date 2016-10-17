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
function upgrade_module_2_9_20($object, $install = false)
{
    // add column lotacheminement, versionplantri, distributionsort in table socolissimo_delivery_info, checking exitence first (2.9.19 update)
    $query = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS
			  WHERE COLUMN_NAME= "lotacheminement"
			  AND TABLE_NAME=  "'._DB_PREFIX_.'socolissimo_delivery_info"
			  AND TABLE_SCHEMA = "'._DB_NAME_.'"';

    $result = Db::getInstance()->ExecuteS($query);

    // adding column lotacheminement
    if (!$result) {
        $query = 'ALTER TABLE '._DB_PREFIX_.'socolissimo_delivery_info add  `lotacheminement` varchar(64) NOT NULL';
        Db::getInstance()->Execute($query);
    }
    $query = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS
			  WHERE COLUMN_NAME= "distributionsort"
			  AND TABLE_NAME=  "'._DB_PREFIX_.'socolissimo_delivery_info"
			  AND TABLE_SCHEMA = "'._DB_NAME_.'"';

    $result = Db::getInstance()->ExecuteS($query);

    // adding column distributionsort
    if (!$result) {
        $query = 'ALTER TABLE '._DB_PREFIX_.'socolissimo_delivery_info add  `distributionsort` varchar(64) NOT NULL';
        Db::getInstance()->Execute($query);
    }
    $query = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS
			  WHERE COLUMN_NAME= "versionplantri"
			  AND TABLE_NAME=  "'._DB_PREFIX_.'socolissimo_delivery_info"
			  AND TABLE_SCHEMA = "'._DB_NAME_.'"';

    $result = Db::getInstance()->ExecuteS($query);

    // adding column versionplantri
    if (!$result) {
        $query = 'ALTER TABLE '._DB_PREFIX_.'socolissimo_delivery_info add  `versionplantri` varchar(10) NOT NULL';
        Db::getInstance()->Execute($query);
    }
    Configuration::updateValue('SOCOLISSIMO_VERSION', '2.9.20');
    return true;
}
