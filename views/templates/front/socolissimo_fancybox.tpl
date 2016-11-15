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

<a href="#" style="display:none" class="fancybox fancybox.iframe" id="soLink"></a>
{if isset($opc) && $opc}
	<script type="text/javascript">
		var opc = true;
	</script>
{else}
	<script type="text/javascript">
		var opc = false;
	</script>
{/if}
{if isset($already_select_delivery) && $already_select_delivery}
	<script type="text/javascript">
		var already_select_delivery = true;
	</script>
{else}
	<script type="text/javascript">
		var already_select_delivery = false;
	</script>
{/if}

<script type="text/javascript">
	var link_socolissimo = "{$link_socolissimo|escape:'UTF-8'}";
	var soInputs = new Object();
	var soCarrierId = "{$id_carrier|escape:'htmlall':'UTF-8'}";
	var soSellerId = "{$id_carrier_seller|escape:'htmlall':'UTF-8'}";
	var soToken = "{$token|escape:'htmlall':'UTF-8'}";
	var initialCost_label = "{$initialCost_label|escape:'htmlall':'UTF-8'}";
	var initialCost = "{$initialCost|escape:'htmlall':'UTF-8'}";
	var taxMention = "{$taxMention|escape:'htmlall':'UTF-8'}";
	var baseDir = '{$content_dir|escape:'htmlall':'UTF-8'}';
	var rewriteActive = '{$rewrite_active|escape:'htmlall':'UTF-8'}';

	{foreach from=$inputs item=input key=name name=myLoop}
	soInputs.{$name|escape:'htmlall':'UTF-8'} = "{$input|strip_tags|addslashes}";
	{/foreach}

	{literal}
		$('#soLink').fancybox({
			'width': 590,
			'height': 810,
			'autoScale': true,
			'centerOnScroll': true,
			'autoDimensions': false,
			'transitionIn': 'none',
			'transitionOut': 'none',
			'hideOnOverlayClick': false,
			'hideOnContentClick': false,
			'showCloseButton': true,
			'showIframeLoading': true,
			'enableEscapeButton': true,
			'type': 'iframe',
			onStart: function () {
				$('#soLink').attr('href', link_socolissimo + serialiseInput(soInputs));
				
			},
			onClosed: function () {
				$.ajax({
					type: 'GET',
					url: baseDir + '/modules/socolissimo/ajax.php',
					async: false,
					cache: false,
					dataType: "json",
					data: "token=" + soToken,
					success: function (jsonData) {
						if (jsonData && jsonData.answer && typeof jsonData.answer != undefined && !opc) {
							if (jsonData.answer)
								$('#form').submit();
							else if (jsonData.msg.length)
								alert(jsonData.msg);
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
					}
				});
			}
		});

		$(document).ready(function ()
		{
			var interval;
			$('#soLink').attr('href', baseDir + 'modules/socolissimo/redirect.php' + serialiseInput(soInputs));
			
			
				$('input.delivery_option_radio').each(function ()
				{
					if ($(this).val() == soCarrierId + ',') {
						$(this).next().children().children().find('div.delivery_option_price').html(initialCost_label + '<br/>' + initialCost + taxMention);
						// 1.6 themes
						if ($(this).next().children('div.delivery_option_price').length == 0)
							$(this).parents('tr').children('td.delivery_option_price').find('div.delivery_option_price').html(initialCost_label + '<br/>' + initialCost + taxMention);
					}
				});
				if (soCarrierId)
					so_click();
			
			$('.delivery_option').each(function ( ) {
				if ($(this).children('.delivery_option_radio').val() == '{/literal}{$id_carrier_seller|escape:'htmlall':'UTF-8'}{literal},') {
					$(this).remove();
				}
			});
			$('#id_carrier{/literal}{$id_carrier_seller|escape:'htmlall':'UTF-8'}{literal}').parent().parent().remove();

		});


		function so_click()
		{
			if (opc) {
				if (!already_select_delivery || !$('#edit_socolissimo').length)
					modifyCarrierLine();
			}
			else if (soCarrierId == 0) {
				$('[name=processCarrier]').unbind('click').live('click', function () {
					return true;
				});
			} else {
				$('[name=processCarrier]').unbind('click').live('click', function () {
					if (($('#id_carrier' + soCarrierId).is(':checked')) || ($('.delivery_option_radio:checked').val() == soCarrierId + ','))
					{
						if (acceptCGV()) {
							$('#soLink').attr('href', link_socolissimo + serialiseInput(soInputs));
							$("#soLink").trigger("click");
						}
						return false;
					}
					return true;
				});
			}
		}

		function modifyCarrierLine()
		{
			var carrier = $('input.delivery_option_radio:checked');
			var container = '#id_carrier' + soCarrierId;
			if ((carrier.val() == soCarrierId) || (carrier.val() == soCarrierId + ',')) {
				carrier.next().children().children().find('div.delivery_option_delay').append('<div><a class="exclusive_large" id="button_socolissimo" href="#" onclick="redirect();return;" >{/literal}{$select_label|escape:'htmlall':'UTF-8'}{literal}</a></div>');
				// 1.6 theme
				carrier.parent().parent().parent().parent().find('td.delivery_option_price').before('<td><div><a class="exclusive_large" id="button_socolissimo" href="#" onclick="redirect();return;" style="text-align:center;" >{/literal}{$select_label|escape:'htmlall':'UTF-8'}{literal}</a></div></td>');
			} else {
				$('#button_socolissimo').remove();
			}
			if (already_select_delivery)
			{
				$(container).css('display', 'block');
				$(container).css('margin', 'auto');
				$(container).css('margin-top', '5px');
			} else
				$(container).css('display', 'none');
		}

		function redirect()
		{
			$('#soLink').attr('href', link_socolissimo + serialiseInput(soInputs));
			$("#soLink").trigger("click");
			return false;
		}

		function serialiseInput(inputs)
		{
			if (!rewriteActive)
				var str = '&first_call=1&';
			else
				var str = '?first_call=1&';
		
			for (var cle in inputs)
				str += cle + '=' + inputs[cle] + '&';
			return (str + 'gift=' + $('#gift').attr('checked') + '&gift_message=' + $('#gift_message').attr('value'));
		}

	{/literal}
</script>
