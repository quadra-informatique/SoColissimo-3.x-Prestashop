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
<div class="panel">
    <div class="row colissimo-header">
        <div class="col-md-1 text-center logo">
            <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/colissimo.png" id="colissimo-logo" />
        </div>
        <div class="col-md-6 about">
            <h4>{l s='About Socolissimo Simplicité' mod='socolissimo'} {$colissimo_version|escape:'htmlall':'UTF-8'}</h4>
            Une solution gratuite sans développement, facile à implémenter depuis votre back-office Prestashop.
            <ul>
                <li>Une page de livraison « iframe » qui reste dans la continuité du site garantissant ainsi une continuité dans le processus d’achat.</li>
                <li>Un large choix de modes de livraison pour satisfaire vos e-acheteurs.</li>
                <li>Un chiffre d’affaires additionnel grâce à l’offre Colissimo vers l'Europe</li>
                <li>Un accès offert à un espace client dédié sur la « Colissimo Box » et à un outil de suivi de vos expéditions : ColiView.</li>
                <li>Une version mobile du module pour assurer une complémentarité et un relai d’achat tout au long de la journée. Vous captez ainsi des ventes supplémentaires grâce à votre présence multicanale.</li>
            </ul>
            <em class="text-muted small">
                NB : Ce module s’adresse aux marchands disposant d’un numéro SIRET.
            </em>
        </div>

        <div class="col-md-2 text-center subscribe">
            <h5 class="text-branded">{l s='Subcribe to the Colissimo Simplicity offer' mod='socolissimo'}</h5>
            {l s='By phone, Call' mod='socolissimo'}
            <h4 class="text-branded">3634</h4>
            {l s='By using our' mod='socolissimo'}<br/>
            <h6><a href="https://www.colissimo.entreprise.laposte.fr/fr/contact" target="_blank">{l s='Contact formula' mod='socolissimo'}</a></h6>
        </div>
        <div class="col-md-3 text-center support">
            <h4>{l s='Need support ?' mod='socolissimo'}</h4>
            {l s='Don\'t hesitate to read the' mod='socolissimo'} 
            <h5><a href="{$module_dir|escape:'htmlall':'UTF-8'}/readme_fr.pdf" target="_blank"><b>{l s='Vendor manual' mod='socolissimo'}</b></a></h5> 
            {l s='to help you to configure the module' mod='socolissimo'}<br/>
            <p>{l s='You can also call the Hotline at' mod='socolissimo'}<br/>
                <strong class="text-branded">0825 086 005</strong><br/>
                <em class="text-muted small">
                    {l s='Monday to Friday, from 8am to 6pm.' mod='socolissimo'}. <br/>
                    {l s='Say "Incident", and "Web Solutions" in the voice menu' mod='socolissimo'}
                </em>
            </p>
        </div>

    </div>
</div>