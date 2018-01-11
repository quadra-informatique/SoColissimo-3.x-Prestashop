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

function upgrade_module_3_0_10($object, $install = false)
{
    Configuration::updateValue('SOCOLISSIMO_VERSION', '3.0.10');
    Configuration::updateValue('SOCOLISSIMO_USE_POINTDERETRAIT', false);
    Configuration::updateValue('SOCOLISSIMO_WS_URL', 'ws.colissimo.fr/');
    Configuration::updateValue('SOCOLISSIMO_URL_POINTDERETRAIT', 'ws.colissimo.fr/widget-point-retrait/rest/authenticate.rest');
    Configuration::updateValue('SOCOLISSIMO_PDR_TOKEN_HOUR', '');
    Configuration::updateValue('SOCOLISSIMO_TOKEN_POINTDERETRAIT', '');
    Configuration::updateValue('SOCOLISSIMO_LOGIN', '');
    Configuration::updateValue('SOCOLISSIMO_PASSWORD', '');

    $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'socolissimo_delivery_point` (
                  `id_socolissimo_delivery_point` INT NOT NULL AUTO_INCREMENT,
                  `id_cart` INT NOT NULL,
                  `id_customer` INT NOT NULL,
                  `accesPersonneMobiliteReduite` BOOLEAN NOT NULL,
                  `adresse1` text(100) NOT NULL,
                  `adresse2` text(100) NOT NULL,
                  `adresse3` text (60) NOT NULL,
                  `codePays` text(10) NOT NULL,
                  `codePostal` text(10) NOT NULL,
                  `congesPartiel` text(200) NOT NULL,
                  `congesTotal` text(200) NOT NULL,
                  `coordGeolocalisationLatitude` varchar(10) NOT NULL,
                  `coordGeolocalisationLongitude` varchar(10) NOT NULL,
                  `distanceEnMetre` INT NOT NULL,
                  `distributionSort` text(10) NOT NULL,
                  `horairesOuvertureLundi` varchar(70) NOT NULL,
                  `horairesOuvertureMardi` varchar(70) NOT NULL,
                  `horairesOuvertureMercredi` varchar(70) NOT NULL,
                  `horairesOuvertureJeudi` varchar(70) NOT NULL,
                  `horairesOuvertureVendredi` varchar(70) NOT NULL,
                  `horairesOuvertureSamedi` varchar(70) NOT NULL,
                  `horairesOuvertureDimanche` varchar(70) NOT NULL,
                  `identifiant` varchar(10) NOT NULL,
                  `indiceDeLocalisation` varchar(3) NOT NULL,
                  `langue` varchar(64) NOT NULL,
                  `libellePays` varchar(64) NOT NULL,
                  `listeConges` varchar(64) NOT NULL,
                  `loanOfHandlingTool` varchar(64) NOT NULL,
                  `localite` text(10) NOT NULL,
                  `lotAcheminement` varchar(70) NOT NULL,
                  `nom` varchar(64) NOT NULL,
                  `parking` boolean NOT NULL,
                  `periodeActiviteHoraireDeb` varchar(6150) NOT NULL,
                  `periodeActiviteHoraireFin` varchar(150) NOT NULL,
                  `poidsMaxi` INT(15) NOT NULL,
                  `reseau` varchar(5) NOT NULL,
                  `typeDePoint` varchar(6) NOT NULL,
                  `versionPlanTri` varchar(5) NOT NULL,
                  PRIMARY KEY  (`id_socolissimo_delivery_point`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

    Db::getInstance()->Execute($query);

    return true;
}
