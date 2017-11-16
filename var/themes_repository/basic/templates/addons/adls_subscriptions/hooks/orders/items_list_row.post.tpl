{if !$product.extra.parent && !empty($product.subscription)}
    {$colSpan = 4}
    {if $order_info.use_discount}
        {$colSpan = $colSpan + 1}
    {/if}
    {if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
        {$colSpan = $colSpan + 1}
    {/if}
    <tr class="ty-valign-top adls-order-item-subscription">
        <td colspan="{$colSpan}">
            {include file="addons/adls_subscriptions/views/adls_subscriptions/components/subscription.tpl" subscription=$product.subscription}
        </td>
    </tr>
{/if}