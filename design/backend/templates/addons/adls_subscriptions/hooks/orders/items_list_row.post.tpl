{assign var="orderItem" value=$oi}
{if !empty($orderItem.subscription)}
    <tr>
        <td colspan="6">
            {assign var="subscription" value=$orderItem.subscription}
            <h6>{__('adlss.subscriptions')} {$orderItem.product}</h6>
            {include file="addons/adls_subscriptions/views/adls_subscriptions/components/table/table.tpl" subscriptions=[$subscription]}
{*            <table width="100%">
                {include file="addons/adls_subscriptions/views/adls_subscriptions/components/table/subscription_row.tpl" subscription=$orderItem.subscription}
            </table>*}
        </td>
    </tr>
{/if}
