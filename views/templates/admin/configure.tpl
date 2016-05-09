{*
* 1961-2016 BNP Paribas
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Quadra Informatique <modules@quadra-informatique.fr>
*  @copyright 1961-2016 BNP Paribas
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  
*}
<div class="panel">
    <div class="row mercanet-header">
        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/socolissimo.jpg" class="col-xs-6 col-md-4 text-center" id="payment-logo" />
        <div class="col-xs-6 col-md-7">
            <h4>{l s='About Socolissimo Simplicité' mod='socolissimo'} {$colissimo_version}</h4>
            <h5 class="text-branded">{l s='Colissimo Simplicité is a service offered by La Poste, which allows you to offer your customers multiple modes of delivery' mod='socolissimo'} :</h5>
            <ul>
                <li style="font-weight:bold;">{l s='Colissimo at home' mod='socolissimo'} :</li>
                <ul>
                    <li>{l s='With signing' mod='socolissimo'}</li>
                    <li>{l s='Unsigned' mod='socolissimo'}</li>
                </ul>
                <li style="font-weight:bold;">{l s='Colissimo at a withdrawal point' mod='socolissimo'} :</li>
                <ul>
                    <li>{l s='At the post office' mod='socolissimo'}</li>
                    <li>{l s='In a Pickup Station' mod='socolissimo'}</li>
                    <li>{l s='In one of the 18 000 Pickup Relays available in France' mod='socolissimo'}</li>
                </ul>
            </ul>
                    <h5> {l s='To open your Colissimo account, please contact' mod='socolissimo'} <b>{l s='La Poste' mod='socolissimo'}</b> :</h5>
        <ul>
            <li>{l s='By phone : Call' mod='socolissimo'}<b> 3634 </b>{l s='(French phone number)' mod='socolissimo'}</li>
            <li><a href="https://www.colissimo.entreprise.laposte.fr/contact" target="_blank">{l s='By message' mod='socolissimo'}</a></li>
        </ul>
            <em class="text-muted small">
                * {l s='In hac habitasse platea dictumst. Pellentesque dictum, nunc sit amet dapibus tincidunt, nibh dolor efficitur lacus, ut commodo purus urna vel eros.' mod='socolissimo'}
            </em>
        </div>
        <div class="col-xs-12 col-md-2 text-center">
            <h5 class="text-branded">{l s='Contact our merchant support' mod='socolissimo'}</h5>
            <h4 class="text-branded">0123 456 789</h4>
            <em class="text-muted small">
                {l s='Monday to Friday,' mod='socolissimo'}<br/>
                {l s='from 8H to 17h30; 0,15€/min' mod='socolissimo'}
            </em>
            <p><br/>{l s='or' mod='socolissimo'}</p>
            <p><a href="mailto:modules@quadra-informatique.fr">modules@quadra-informatique.fr</a></p>
            <h4><img src="{$module_dir|escape:'htmlall'}/logo.gif" alt="" />{l s='Vendor manual' mod='socolissimo'}</h4>
            {l s='Don\'t hesitate to read the' mod='socolissimo'} 
        <b><a href="{$module_dir|escape:'htmlall'}/readme_fr.pdf" target="_blank">{l s='Vendor manual' mod='socolissimo'} </a></b> 
        {l s='to help you to configure the module' mod='socolissimo'} 
        </div>
    </div>
</div>