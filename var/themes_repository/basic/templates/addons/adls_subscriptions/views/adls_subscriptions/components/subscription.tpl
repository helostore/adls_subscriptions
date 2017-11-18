<div class="ty-control-group clearfix">
    <label class="ty-product-options__title">Subscription</label>
    <span class="">
        {$subscription->extra['plan$name']}
    </span>
</div>
<div class="ty-control-group clearfix">
    <label class="ty-product-options__title">{__('adlss.availability')}</label>
    <span class="">
        {include file="addons/adls_subscriptions/views/adls_subscriptions/components/availability.tpl" subscription=$subscription}
    </span>
</div>

<div class="ty-control-group clearfix">
    <label class="ty-product-options__title">{__('adlss.status')}</label>
    <span class="">
        {include file="addons/adls_subscriptions/views/adls_subscriptions/components/status.tpl" subscription=$subscription}
        {include file="addons/adls_subscriptions/views/adls_subscriptions/components/buttons.tpl" subscription=$subscription}
    </span>
</div>