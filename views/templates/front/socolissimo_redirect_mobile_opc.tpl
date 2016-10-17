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

{if isset($already_select_delivery) && $already_select_delivery}
	<script type="text/javascript">
		var already_select_delivery = true;
	</script>
{else}
	<script type="text/javascript">
		var already_select_delivery = false;
	</script>
{/if}
<form id="socoForm" name="form" action="" method="POST">

				{foreach from=$inputs key=key item=val}
					<input type="hidden" name="{$key|escape:'htmlall':'UTF-8'}" value="{$val|escape:'htmlall':'UTF-8'}"/>
				{/foreach}
				
				
			</form>
<script type="text/javascript">
	var link_socolissimo = "{$link_socolissimo_mobile|escape:'UTF-8'}";
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
		$(document).ready(function ()
		{
			var interval;
			
			
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

		});


		function so_click()
		{
			
				if (!already_select_delivery || !$('#edit_socolissimo').length)
					modifyCarrierLine();
			
		}

		function modifyCarrierLine()
		{
			var carrier = $('input.delivery_option_radio:checked');

			if ((carrier.val() == soCarrierId) || (carrier.val() == soCarrierId + ',')) {
				carrier.parent().parent().parent().parent().find('.order_carrier_logo').after('<div><a class="exclusive_large" id="button_socolissimo" href="#" onclick="redirect();return;" style="text-align:center;" >{/literal}{$select_label|escape:'htmlall':'UTF-8'}{literal}</a></div>');
			} else {
				$('#button_socolissimo').remove();
			}
		}

		function redirect()
		{
			$('#socoForm').attr('action', link_socolissimo + serialiseInput(soInputs));
			$('#socoForm').submit();
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
