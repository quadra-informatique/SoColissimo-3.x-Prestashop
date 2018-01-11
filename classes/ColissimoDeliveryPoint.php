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

class ColissimoDeliveryPoint extends ObjectModel
{

    public $id_socolissimo_delivery_point;
    public $id_cart;
    public $id_customer;
    public $accesPersonneMobiliteReduite = '';
    public $adresse1 = '';
    public $adresse2 = '';
    public $adresse3 = '';
    public $codePays = '';
    public $codePostal = '';
    public $congesPartiel = '';
    public $congesTotal = '';
    public $coordGeolocalisationLatitude = '';
    public $coordGeolocalisationLongitude = '';
    public $distanceEnMetre = '';
    public $distributionSort = '';
    public $horairesOuvertureLundi = '';
    public $horairesOuvertureMardi = '';
    public $horairesOuvertureMercredi = '';
    public $horairesOuvertureJeudi = '';
    public $horairesOuvertureVendredi = '';
    public $horairesOuvertureSamedi = '';
    public $horairesOuvertureDimanche = '';
    public $identifiant = '';
    public $libellePays = '';
    public $listeConges = '';
    public $loanOfHandlingTool = '';
    public $localite = '';
    public $lotAcheminement = '';
    public $nom = '';
    public $parking = '';
    public $periodeActiviteHoraireDeb = '';
    public $periodeActiviteHoraireFin = '';
    public $poidsMaxi = '';
    public $reseau = '';
    public $typeDePoint = '';
    public $versionPlanTri = '';
    public static $definition = array(
        'table' => 'socolissimo_delivery_point',
        'primary' => 'id_socolissimo_delivery_point',
        'multilang' => false,
        'fields' => array(
            'id_socolissimo_delivery_point' => array(
                'type' => ObjectModel::TYPE_INT
            ),
            'id_cart' => array(
                'type' => ObjectModel::TYPE_INT,
                'required' => true
            ),
            'id_customer' => array(
                'type' => ObjectModel::TYPE_INT,
                'required' => true
            ),
            'accesPersonneMobiliteReduite' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'required' => false
            ),
            'adresse1' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'adresse2' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'adresse3' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'codePays' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'codePostal' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'congesTotal' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'required' => false
            ),
            'congesPartiel' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'required' => false
            ),
            'coordGeolocalisationLatitude' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'coordGeolocalisationLongitude' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'distanceEnMetre' => array(
                'type' => ObjectModel::TYPE_INT,
                'required' => false
            ),
            'distributionSort' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureLundi' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureMardi' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureMercredi' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureJeudi' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureVendredi' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureSamedi' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'horairesOuvertureDimanche' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'identifiant' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'indiceDeLocalisation' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'langue' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'libellePays' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'listeConges' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'loanOfHandlingTool' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'localite' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'lotAcheminement' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'nom' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'parking' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'required' => false
            ),
            'periodeActiviteHoraireDeb' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'periodeActiviteHoraireFin' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'poidsMaxi' => array(
                'type' => ObjectModel::TYPE_INT,
                'required' => false
            ),
            'reseau' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'typeDePoint' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            ),
            'versionPlanTri' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => false
            )
        )
    );

    public static function alreadyExists($id_cart = 0, $id_customer = 0)
    {
        if (!$id_cart || !$id_customer) {
            return false;
        }
        return Db::getInstance()->getValue('SELECT id_socolissimo_delivery_point FROM '._DB_PREFIX_.'socolissimo_delivery_point WHERE id_cart = '.(int)$id_cart.' AND id_customer ='.(int)$id_customer);
    }
}
