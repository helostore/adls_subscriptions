{script src="js/tygh/exceptions.js"}
{script src="js/tygh/checkout.js"}



{$smarty.capture.checkout_error_content nofilter}
{*{include file="views/checkout/components/checkout_steps.tpl"}*}
{include file="views/checkout/components/cart_items.tpl" disable_ids="button_cart" cart_products=$cart.products}
{include file="views/checkout/components/checkout_totals.tpl" location="checkout"}

{*{include file="views/checkout/components/steps/step_four.tpl" step="four" edit=$edit complete=$completed_steps.step_four}*}

<div class="clearfix">
    {include file="views/checkout/components/payments/payment_methods.tpl" payment_id=$cart.payment_id final_section=$smarty.capture.final_section}
</div>



{capture name="mainbox_title"}<span class="ty-checkout__title">{__("secure_checkout")}&nbsp;<i class="ty-checkout__title-icon ty-icon-lock"></i></span>{/capture}



{*
<div class="ty-orders-detail">

    <div class="clearfix">
        {include file="views/checkout/components/payments/payment_methods.tpl" payment_id=$cart.payment_id final_section=$smarty.capture.final_section}
    </div>

    {$cart|aa}
</div>

{capture name="mainbox_title"}
    Renew subscription&nbsp;#{$subscription->getId()}
{/capture}
*}
