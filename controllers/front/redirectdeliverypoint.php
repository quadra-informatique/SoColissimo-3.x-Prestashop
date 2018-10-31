<?php
/**
 * La Poste
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@relaiscolis.com so we can send you a copy immediately.
 *
 *  @author    Quadra Informatique <modules@quadra-informatique.fr>
 *  @copyright La Poste
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'socolissimo/classes/ColissimoDeliveryPoint.php';

class SocolissimoredirectdeliverypointModuleFrontController extends ModuleFrontController
{

    public $ssl = true;
    public $display_header = false;
    public $display_footer = false;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

        $socolissimo_pointderetrait_info_exist = ColissimoDeliveryPoint::alreadyExists($this->context->cart->id, $this->context->customer->id);

        if ((int)$socolissimo_pointderetrait_info_exist) {
            $socolissimo_pointderetrait_info = new ColissimoDeliveryPoint((int)$socolissimo_pointderetrait_info_exist);
        } else {
            $socolissimo_pointderetrait_info = new ColissimoDeliveryPoint();
        }

        $socolissimo_pointderetrait_info->id_cart = $this->context->cart->id;
        $socolissimo_pointderetrait_info->id_customer = $this->context->customer->id;

        if (Tools::getValue('accesPersonneMobiliteReduite')) {
            $socolissimo_pointderetrait_info->accesPersonneMobiliteReduite = (bool)Tools::getValue('accesPersonneMobiliteReduite');
        }
        if (Tools::getValue('nom')) {
            $socolissimo_pointderetrait_info->nom = trim(utf8_encode(Tools::getValue('nom')));
        }
        if (Tools::getValue('adresse1')) {
            $socolissimo_pointderetrait_info->adresse1 = trim(utf8_encode(Tools::getValue('adresse1')));
        }
        if (Tools::getValue('adresse2')) {
            $socolissimo_pointderetrait_info->adresse2 = trim(utf8_encode(Tools::getValue('adresse2')));
        }
        if (Tools::getValue('adresse3')) {
            $socolissimo_pointderetrait_info->adresse3 = trim(utf8_encode(Tools::getValue('adresse3')));
        }
        if (Tools::getValue('codePays')) {
            $socolissimo_pointderetrait_info->codePays = trim(Tools::getValue('codePays'));
        }
        if (Tools::getValue('codePostal')) {
            $socolissimo_pointderetrait_info->codePostal = trim(Tools::getValue('codePostal'));
        }
        if (Tools::getValue('congesTotal')) {
            $socolissimo_pointderetrait_info->congesTotal = trim(Tools::getValue('congesTotal'));
        }
        if (Tools::getValue('congesPartiel')) {
            $socolissimo_pointderetrait_info->congesPartiel = trim(utf8_encode(Tools::getValue('congesPartiel')));
        }
        if (Tools::getValue('coordGeolocalisationLatitude')) {
            $socolissimo_pointderetrait_info->coordGeolocalisationLatitude = trim(Tools::getValue('coordGeolocalisationLatitude'));
        }
        if (Tools::getValue('coordGeolocalisationLongitude')) {
            $socolissimo_pointderetrait_info->coordGeolocalisationLongitude = trim(Tools::getValue('coordGeolocalisationLongitude'));
        }
        if (Tools::getValue('distanceEnMetre')) {
            $socolissimo_pointderetrait_info->distanceEnMetre = trim(Tools::getValue('distanceEnMetre'));
        }
        if (Tools::getValue('distributionSort')) {
            $socolissimo_pointderetrait_info->distributionSort = trim(Tools::getValue('distributionSort'));
        }
        if (Tools::getValue('horairesOuvertureLundi')) {
            $socolissimo_pointderetrait_info->horairesOuvertureLundi = $this->sanitize(Tools::getValue('horairesOuvertureLundi'));
        }
        if (Tools::getValue('horairesOuvertureMardi')) {
            $socolissimo_pointderetrait_info->horairesOuvertureMardi = $this->sanitize(Tools::getValue('horairesOuvertureMardi'));
        }
        if (Tools::getValue('horairesOuvertureMercredi')) {
            $socolissimo_pointderetrait_info->horairesOuvertureMercredi = $this->sanitize(Tools::getValue('horairesOuvertureMercredi'));
        }
        if (Tools::getValue('horairesOuvertureJeudi')) {
            $socolissimo_pointderetrait_info->horairesOuvertureJeudi = $this->sanitize(Tools::getValue('horairesOuvertureJeudi'));
        }
        if (Tools::getValue('horairesOuvertureVendredi')) {
            $socolissimo_pointderetrait_info->horairesOuvertureVendredi = $this->sanitize(Tools::getValue('horairesOuvertureVendredi'));
        }
        if (Tools::getValue('horairesOuvertureSamedi')) {
            $socolissimo_pointderetrait_info->horairesOuvertureSamedi = $this->sanitize(Tools::getValue('horairesOuvertureSamedi'));
        }
        if (Tools::getValue('horairesOuvertureDimanche')) {
            $socolissimo_pointderetrait_info->horairesOuvertureDimanche = $this->sanitize(Tools::getValue('horairesOuvertureDimanche'));
        }
        if (Tools::getValue('identifiant')) {
            $socolissimo_pointderetrait_info->identifiant = trim(Tools::getValue('identifiant'));
        }
        if (Tools::getValue('langue')) {
            $socolissimo_pointderetrait_info->langue = trim(Tools::getValue('langue'));
        }
        if (Tools::getValue('libellePays')) {
            $socolissimo_pointderetrait_info->libellePays = trim(Tools::getValue('libellePays'));
        }
        if (Tools::getValue('listeConges')) {
            $socolissimo_pointderetrait_info->listeConges = trim(Tools::getValue('listeConges'));
        }
        if (Tools::getValue('loanOfHandlingTool')) {
            $socolissimo_pointderetrait_info->loanOfHandlingTool = trim(Tools::getValue('loanOfHandlingTool'));
        }
        if (Tools::getValue('localite')) {
            $socolissimo_pointderetrait_info->localite = trim(Tools::getValue('localite'));
        }
        if (Tools::getValue('parking')) {
            $socolissimo_pointderetrait_info->parking = trim(Tools::getValue('parking'));
        }
        if (Tools::getValue('periodeActiviteHoraireDeb')) {
            $socolissimo_pointderetrait_info->periodeActiviteHoraireDeb = trim(Tools::getValue('periodeActiviteHoraireDeb'));
        }
        if (Tools::getValue('periodeActiviteHoraireFin')) {
            $socolissimo_pointderetrait_info->periodeActiviteHoraireFin = trim(Tools::getValue('periodeActiviteHoraireFin'));
        }
        if (Tools::getValue('poidsMaxi')) {
            $socolissimo_pointderetrait_info->poidsMaxi = trim(Tools::getValue('poidsMaxi'));
        }
        if (Tools::getValue('reseau')) {
            $socolissimo_pointderetrait_info->reseau = trim(Tools::getValue('reseau'));
        }
        if (Tools::getValue('typeDePoint')) {
            $socolissimo_pointderetrait_info->typeDePoint = trim(Tools::getValue('typeDePoint'));
        }
        if (Tools::getValue('versionPlanTri')) {
            $socolissimo_pointderetrait_info->versionPlanTri = trim(Tools::getValue('versionPlanTri'));
        }
        $this->context->cart->id_carrier = (int)Configuration::getGlobalValue('SOCOLISSIMO_CARRIER_ID');
        $socolissimo_pointderetrait_info->save();
    }
    
    public function sanitize($value)
    {
        if ($value) {
            $value = str_replace('&nbsp;', '', $value);
            return trim($value);
        }
        return false;
    }
}
