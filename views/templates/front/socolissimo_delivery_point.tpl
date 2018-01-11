{*
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
*}
<input type="hidden" value="{$is_15|escape:'htmlall':'UTF-8'}" id="colissimo-version"/>
<input type="hidden" id="widget-conf-message" value="{l s='Your delivery point has been registered.' mod='socolissimo'}" />
<input type="hidden" id="widget-error-message" value="{l s='Error : the web service is inaccessible, please try again later and check your configurations.' mod='socolissimo'}" />
<script type="text/javascript">
    var soInputs = new Object();
    var soCarrierId = "{$id_carrier|escape:'htmlall':'UTF-8'}";
    var initialCost_label = "{$initialCost_label|escape:'htmlall':'UTF-8'}";
    var initialCost = "{$initialCost|escape:'htmlall':'UTF-8'}";
    var taxMention = "{$taxMention|escape:'htmlall':'UTF-8'}";
    var moduleLink = "{$module_link|escape:'UTF-8'}";
    var colissimoModuleUrl = "{$baseUrl|escape:'UTF-8'}modules/socolissimo/"; 
    var colissimoModuleCss = "{$baseUrl|escape:'UTF-8'}modules/socolissimo/views/css/";
    var colissimoModuleJs =  "{$baseUrl|escape:'UTF-8'}modules/socolissimo/views/js/";
    var wsUrl = "{$wsUrl|escape:'UTF-8'}";
    var baseUrl = "{$baseUrl|escape:'UTF-8'}";
    {foreach from=$inputs item=input key=name name=myLoop}
    soInputs.{$name|escape:'htmlall':'UTF-8'} = "{$input|strip_tags|addslashes}";
    {/foreach}
  $(document).ready(function () {
      
            /* iframe génération */
            /* hidding iframe if carrier Colissimo Simplicité is not selected */
            
             if ($('#widget-container').length) {
            $('#widget-container').hide();
        }
            var id_hook = $('#colissimo-version').parent().attr('id');
            if ($('.delivery_option_radio:checked').val() == soCarrierId + ',') {
                if (!$('#widget-container').length) {
                $('#footer').append('<div id="widget-container" class="col-xs-12"></div>');
                generateMap();
            }
                $('#'+id_hook).append($('#widget-container'));
                $('#widget-container').show();
            }
            $('.delivery_option_radio').change(function () {
                $('#footer').append($('#widget-container'));
            });
        });

        var generateMap = function () {
            $(function () {
                $('#widget-container').frameColiposteOpen({
                    "ceLang": soInputs.ceLang,
                    "callBackFrame":  encodeURI(callBackFrame),
                    "ceCountryList": "FR,BE,DE,NL,LU,ES,GB,PT,AT,EE,LV,LT",
                    "dyPreparationTime": soInputs.dyPreparationTime,
                    "ceAddress": soInputs.ceAddress,
                    "ceZipCode": soInputs.ceZipCode,
                    "ceTown": soInputs.ceTown,
                    "ceCountry": soInputs.cePays,
                    "token": soInputs.token
                });
                 
                function callBackFrame(point) {
                  saveDeliveryPoint(point, moduleLink);
                }
            });
        }
</script>


<input type="hidden" id="pudoWidgetErrorCode">
<input type="hidden" id="pudoWidgetErrorCodeMessage">
<input type="hidden" id="pudoWidgetCompanyName">
<input type="hidden" id="pudoWidgetAddress1">
<input type="hidden" id="pudoWidgetAddress2">
<input type="hidden" id="pudoWidgetAddress3">
<input type="hidden" id ="pudoWidgetCity">
<input type="hidden" id="pudoWidgetZipCode">
<input type="hidden" id ="pudoWidgetCountry">
<input type="hidden" id="pudoWidgetType">

