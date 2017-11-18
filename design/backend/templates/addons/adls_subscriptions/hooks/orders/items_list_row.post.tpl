{assign var="orderItem" value=$oi}
<tr>
    <td colspan="6">
        {if !empty($orderItem.subscription)}
            {assign var="subscription" value=$orderItem.subscription}
            Subscription <a href="{fn_url("subscriptions.view?id=`$subscription->getId()`")}">ID #{$subscription->getId()}</a>
            <br>
            Plan: {$subscription->getPlanId()}
            <br>
            Never Expires: {$subscription->isNeverExpires()}
            <br>
            Availability: {$subscription->getDates()}
            <br>
            Paid Cycles: {$subscription->getPaidCycles()}
            <br>
            Elapsed Cycles: {$subscription->getElapsedCycles()}
            <br>
            Status: {$subscription->getStatusLabel()}
            <br>
            Created at: {$subscription->getCreatedAt()->format('d/m/Y h:i:s')}
            <br>
            Updated at: {$subscription->getCreatedAt()->format('d/m/Y h:i:s')}
        {/if}

    </td>
</tr>