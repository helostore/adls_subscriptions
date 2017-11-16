<div class="ty-control-group clearfix">
    <label class="ty-product-options__title">Subscription</label>
    <span class="">
        {$subscription->extra['plan$name']}
    </span>
</div>
<div class="ty-control-group clearfix">
    <label class="ty-product-options__title">Availability</label>
    <span class="">
        {if $subscription->hasStartDate()}
            {$subscription->getStartDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
        {else}
            &infin;
        {/if}
        &mdash;
        {if $subscription->hasEndDate()}
            {$subscription->getEndDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
        {else}
            &infin;
        {/if}
    </span>
</div>

<div class="ty-control-group clearfix">
    <label class="ty-product-options__title">Status</label>
    <span class="">
        {if $subscription->isActive()}
            Active
        {/if}
        {if $subscription->isInactive()}
            Inactive &mdash; <a class="ty-btn ty-btn__primary ty-btn" href="{"checkout.checkout?subscription_id={$subscription->getId()}"|fn_url}">Renew</a>
        {/if}
        {if $subscription->isDisabled()}
            Disabled
        {/if}
    </span>
</div>