/*
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
 *  @copyright 2010-2017 La Poste SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of La Poste SA
 */
function saveDeliveryPoint(point, moduleLink) {
    $.ajax({
        url: moduleLink,
        method: 'POST',
        data: point,
        success: function (data) {
            var msg = $('#widget-conf-message').val();
            displayConfDelivery(msg);
            $('#have_selected_point').val('1');
            if (typeof(updatePaymentMethodsDisplay) !== 'undefined')
						updatePaymentMethodsDisplay();
        },
        error: function (data) {

        }
    });
}
jQuery.extend(jQuery.fn, {
    frameColiposteOpen: function (params)
    {
        //jquery.frameColiposte
        var colissimo = $;
        var url = colissimo('script[src*="jquery.frameColiposte"]').attr("src");
        //var indexpath = url.indexOf('widget-point-retrait', 0);
        var urlColiposte = wsUrl;
        var lang = params.ceLang;
        var is15 = colissimo('#colissimo-version').val();
        var callBackFrame = params.callBackFrame;
        // params controls
        var codeRetour = 0;
        if (params.ceCountryList == null || params.ceCountryList == '')
        {
            codeRetour = 10;
        }
        if (params.ceCountry == null || params.ceCountry == '')
        {
            codeRetour = 20;
        }
        if (params.ceLang == null || params.ceLang == '')
        {
            codeRetour = 30;
        }
        if (params.dyPreparationTime == null || params.dyPreparationTime == '')
        {
            codeRetour = 40;
        }

        var css_link = colissimo("<link>", {
            rel: "stylesheet",
            type: "text/css",
            href: colissimoModuleCss + "mystyle.css",
        });
        css_link.appendTo('head');
        colissimo("head").append('\n');

        if (is15 != '0') {
            var css_link15 = colissimo("<link>", {
                rel: "stylesheet",
                type: "text/css",
                href: colissimoModuleCss + "mystyle15.css",
            });
            css_link15.appendTo('head');
            colissimo("head").append('\n');
        }
        var ui_css_link = colissimo("<link>", {
            rel: "stylesheet",
            type: "text/css",
            href: urlColiposte + "/widget-point-retrait/resources/css/jquery-ui.min-1.11.4.css"
        });
        ui_css_link.appendTo('head');
        colissimo("head").append('\n');

        var s = document.createElement("script");
        s.type = "text/javascript";
        s.src = urlColiposte + "/widget-point-retrait/resources/js/bootstrap.min.js";
        s.defer = true;
        colissimo("head").append(s);
        colissimo("head").append('\n');

        var sUI = document.createElement("script");
        sUI.type = "text/javascript";
        sUI.src = urlColiposte + "/widget-point-retrait/resources/js/jquery-ui.min-1.11.4.js";
        colissimo("head").append(sUI);
        colissimo("head").append('\n');

        var mapbox = document.createElement("script");
        mapbox.type = "text/javascript";
        mapbox.src = "https://api.mapbox.com/mapbox.js/v2.2.1/mapbox.js";
        mapbox.defer = true;
        colissimo("head").append(mapbox);
        colissimo("head").append('\n');

        var mapbox_css_link = colissimo("<link>", {
            rel: "stylesheet",
            type: "text/css",
            href: "https://api.mapbox.com/mapbox.js/v2.2.1/mapbox.css"
        });
        mapbox_css_link.appendTo('head');
        colissimo("head").append('\n');

        // meta
        colissimo("head").append('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        colissimo("head").append('\n');
        colissimo("head").append('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />');
        colissimo("head").append('\n');
        colissimo("head").append('<meta name="apple-mobile-web-app-capable" content="yes">');
        colissimo("head").append('\n');


        var scroll = document.createElement("script");
        scroll.type = "text/javascript";
        scroll.src = urlColiposte + "/widget-point-retrait/resources/js/jquery.jscrollpane.min.js";
        scroll.defer = true;
        colissimo("head").append(scroll);
        colissimo("head").append('\n');

        var mouse = document.createElement("script");
        mouse.type = "text/javascript";
        mouse.src = urlColiposte + "/widget-point-retrait/resources/js/jquery.mousewheel.js";
        mouse.defer = true;
        colissimo("head").append(mouse);
        colissimo("head").append('\n');

        var scrollbar = document.createElement("script");
        scrollbar.type = "text/javascript";
        scrollbar.src = urlColiposte + "/widget-point-retrait/resources/js/jquery.scrollbar.js";
        scrollbar.defer = true;

        var widget_url = wsUrl + "widget-point-retrait/index.htm";

        colissimo.ajax({
            method: "POST",
            url: widget_url,
            data: 'h1=' + lang + '&callBackFrame=' + callBackFrame + '&domain=' + urlColiposte + '&ceCountryList=' + params.ceCountryList + '&codeRetour=' + codeRetour + '&dyPreparationTime=' + params.dyPreparationTime + '&ceCountry=' + params.ceCountry + '&ceZipCode=' + params.ceZipCode + '&token=' + params.token,
            success: function (data) {
                colissimo('#widget-container').html(data);

					if (params.ceCountry != null && params.ceCountry != '')
                    { 
				setTimeout(function () {
                     colissimo("#listePays").val(params.ceCountry);
                        }, 1000)
                       
                    }

                setTimeout(function () {
                    if (params.ceAddress != null && params.ceAddress != '')
                    {
                        colissimo("#Adresse1").val(params.ceAddress);
                    }
                    if (params.ceZipCode != null && params.ceZipCode != '')
                    {
                        colissimo("#CodePostal").val(params.ceZipCode);
                    }
                    if (params.ceTown != null && params.ceTown != '')
                    {
                        colissimo("#Ville").val(params.ceTown);
                        setTimeout(function () {
                            getPointsRetrait();
                        }, 1000)
                    }

                }, 500)

            },
            error: function (resultat, statut, erreur) {
			var msg = $('#widget-error-message').val();
            displayConfDelivery(msg);
            }
        });
        return this;
    },
    frameColiposteClose: function ()
    {
        return this;
    }

});
var $ = jQuery.noConflict();

function displayConfDelivery(msg)
{
		if (!!$.prototype.fancybox)
		    $.fancybox.open([
	        {
	            type: 'inline',
	            autoScale: true,
	            minHeight: 30,
	            content: '<p class="fancybox-error">' + msg + '</p>'
	        }],
			{
		        padding: 0
		    });
		else
		    alert(msg);
	return false;
}

        