/**
 * 1969-2018 Relais Colis
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
 *  @copyright 1969-2018 Relais Colis
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

$(document).ready(function () {
    var have_selected_point = 0;
    $('form[name=carrier_area]').submit(function (e) {
        if (isCarrierColissimoSelected())
        {
			have_selected_point = parseInt($('#have_selected_point').val());
            if (!have_selected_point) {
                if (!!$.prototype.fancybox)
                    $.fancybox.open([
                        {
                            type: 'inline',
                            autoScale: true,
                            minHeight: 30,
                            content: '<p class="fancybox-error">' + msg_order_carrier_colissimo + '</p>'
                        }],
                            {
                                padding: 0
                            });
                else
                    alert(msg_order_carrier_colissimo);
                e.preventDefault();
            }
        }
        return true;
    });

    // if no relais point selected, we prevent all form of payment method to be submited
	if($('#order-opc').size() && !(have_selected_point) && idCarrierColissimoSelected()) {
		$("#opc_payment_methods-content form").submit(function(evt) {           
			evt.preventDefault();
		});
        
        if($('#cgv').attr('checked')) {
            $('#HOOK_TOP_PAYMENT').html('<img src="'+baseUrl+'modules/socolissimo/views/img/socolissimo.jpg" alt="Relais Colis"/>');
            $('#opc_payment_methods-content #HOOK_PAYMENT').html('<b>'+ msg_order_carrier_colissimo + '</b>');
        }
	}

    // On One Page Checkout, we need to prevent the selection of a means of payment if there is no selected relais point.
    $('#order-opc').on('click', '.payment_module a', function(e) {
		if(!(have_selected_point) && idCarrierColissimoSelected()) {
			e.preventDefault();

			if (!!$.prototype.fancybox) {
				$.fancybox.open([
				{
					type: 'inline',
					autoScale: true,
					minHeight: 30,
					content: '<p class="fancybox-error">' + msg_order_carrier_colissimo + '</p>'
				}],
				{
					padding: 0
				});
			}
			else {
				alert(msg_order_carrier_colissimo);
			}

			return false;
		}
	});
    idCarrierColissimoSelected();
});

function idCarrierColissimoSelected() {
    if (isCarrierColissimoSelected())
	{
        $('.choice-info').show();
        return true;
    }
    $('.choice-info').hide();
    return false;	
}

function isCarrierColissimoSelected() {
    if(typeof soCarrierId !== 'undefined')
    {
        return (($('#id_carrier' + soCarrierId).is(':checked')) || ($('.delivery_option_radio:checked').val() == soCarrierId + ','));
    }
    
    return false;
}

function updatePaymentMethods(json)
{
    //var have_selected_point = false;
    var have_selected_point = parseInt($('#have_selected_point').val());
    if($('#order-opc').size() && !(have_selected_point) && idCarrierColissimoSelected() && $('#cgv').attr('checked')) {
        $('#HOOK_TOP_PAYMENT').html('<img src="'+baseUrl+'modules/socolissimo/views/img/socolissimo.jpg" alt="Colissimo Simplicite">');
        $('#opc_payment_methods-content #HOOK_PAYMENT').html('<b>'+ msg_order_carrier_colissimo + '</b>');
    }
    else {
        $('#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
        $('#opc_payment_methods-content #HOOK_PAYMENT').html(json.HOOK_PAYMENT);
    }
}

