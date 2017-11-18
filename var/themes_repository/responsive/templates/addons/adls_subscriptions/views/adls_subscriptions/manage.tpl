{function tableRowHeader label="" key="" search="" sort_sign=""}
    <th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=`$key`&sort_order=`$search.sort_order_rev`"|fn_url}"
           data-ca-target-id="pagination_contents">{__($label)}</a>{if $search.sort_by == $key}{$sort_sign nofilter}{/if}
    </th>
{/function}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $search.sort_order == "asc"}
    {assign var="sort_sign" value="<i class=\"ty-icon-down-dir\"></i>"}
{else}
    {assign var="sort_sign" value="<i class=\"ty-icon-up-dir\"></i>"}
{/if}
{if !$config.tweaks.disable_dhtml}
    {assign var="ajax_class" value="cm-ajax"}

{/if}

{include file="common/pagination.tpl"}

<table class="ty-table ty-subscriptions-search">
    <thead>
    <tr>
        {*{tableRowHeader key="id" label="id" sort_sign=$sort_sign search=$search}*}
        {tableRowHeader key="productId" label="product" sort_sign=$sort_sign search=$search}
        {tableRowHeader key="date" label="adlss.availability" sort_sign=$sort_sign search=$search}
        {tableRowHeader key="plan" label="adlss.plan" sort_sign=$sort_sign search=$search}
        {hook name="subscriptions:manage_header"}{/hook}
        {tableRowHeader key="status" label="status" sort_sign=$sort_sign search=$search}
        {tableRowHeader key="order_id" label="order" sort_sign=$sort_sign search=$search}
        {tableRowHeader key="createdAt" label="adlss.createdAt" sort_sign=$sort_sign search=$search}
        {*{tableRowHeader key="updatedAt" label="adlss.updatedAt" sort_sign=$sort_sign search=$search}*}
        {*<th></th>*}
    </tr>
    </thead>
    {foreach from=$subscriptions item="subscription"}
        <tr>
            {*<td class="ty-subscriptions-search__item"><strong>#{$subscription->getId()}</strong></td>*}
            <td class="ty-subscriptions-search__item"><strong>{$subscription->getExtra('product$name')}</strong></td>

            <td class="ty-subscriptions-search__item">
                {include file="addons/adls_subscriptions/views/adls_subscriptions/components/availability.tpl" subscription=$subscription}
                {include file="addons/adls_subscriptions/views/adls_subscriptions/components/buttons.tpl" subscription=$subscription}
            </td>
            <td class="ty-subscriptions-search__item">
                {include file="common/price.tpl" value=$subscription->getAmount()} / {$subscription->getExtra('plan$cycle')} months
            </td>

            {hook name="subscriptions:manage_data"}{/hook}
            <td class="ty-subscriptions-search__item">
                {include file="addons/adls_subscriptions/views/adls_subscriptions/components/status.tpl" subscription=$subscription}
            </td>
            <td class="ty-subscriptions-search__item">
                <a href="{"orders.details?order_id=`$subscription->getOrderId()`"|fn_url}">#{$subscription->getOrderId()}</a></td>
            <td class="ty-subscriptions-search__item">{$subscription->getCreatedAt()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}</td>
            {*<td class="ty-subscriptions-search__item">{$subscription->getUpdatedAt()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}</td>*}
            {*<td></td>*}
        </tr>
    {foreachelse}
        <tr class="ty-table__no-items">
            <td colspan="5"><p class="ty-no-items">{__("text_no_items")}</p></td>
        </tr>
    {/foreach}
</table>

{include file="common/pagination.tpl"}

{capture name="mainbox_title"}{__("adlss.subscriptions")}{/capture}