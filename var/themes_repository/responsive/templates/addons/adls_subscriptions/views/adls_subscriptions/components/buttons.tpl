{if $subscription->isInactive()}
    <a class="ty-btn ty-btn__primary ty-btn cm-post cm-ajax cm-ajax-full-render" href="{"adls_subscriptions.add?subscription_id={$subscription->getId()}"|fn_url}" data-ca-target-id="cart_status*">Renew</a>
{/if}