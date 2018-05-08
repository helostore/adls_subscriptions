{function tableHeadLink label='' sortBy=''}{strip}
    <a class="cm-ajax"
       href="{"`$c_url`&sort_by=`$sortBy`&sort_order=`$search.sort_order_rev`"|fn_url}"
       data-ca-target-id={$rev}>
        {__($label)}{if $search.sort_by == $sortBy}
            {$c_icon nofilter}
        {else}
            {$c_dummy nofilter}
        {/if}
    </a>
{/strip}{/function}
{strip}
    <table width="100%" class="table table-middle">
        <thead>
        <tr>
            <th class="left">
                {include file="common/check_items.tpl" check_statuses=$order_status_descr}
            </th>
            <th width="5%">{tableHeadLink label="id" sortBy="id"}</th>
            <th width="5%">{tableHeadLink label="order" sortBy="orderId"}</th>
            <th width="17%">{tableHeadLink label="customer" sortBy="customer"}</th>
            <th width="17%">{tableHeadLink label="product" sortBy="product$name"}</th>
            <th width="5%">{tableHeadLink label="price" sortBy="price"}</th>
            <th width="10%">{tableHeadLink label="status" sortBy="status"}</th>
            <th width="17%">{tableHeadLink label="adlss.plan" sortBy='plan$name'}</th>
            <th width="10%">{tableHeadLink label="adlss.startDate" sortBy="startDate"}</th>
            <th width="10%">{tableHeadLink label="adlss.endDate" sortBy="endDate"}</th>
            <th width="17%">{tableHeadLink label="adlss.neverExpires" sortBy="neverExpires"}</th>
            <th width="5%">{tableHeadLink label="adlss.paidCycles" sortBy="paidCycles"}</th>
            <th width="5%">{tableHeadLink label="adlss.elapsedCycles" sortBy="elapsedCycles"}</th>
            <th width="5%">{tableHeadLink label="adlss.updatedAt" sortBy="updatedAt"}</th>
            <th width="5%">{tableHeadLink label="adlss.createdAt" sortBy="createdAt"}</th>

            {hook name="adls_subscriptions:manage_header"}{/hook}

            <th>&nbsp;</th>
        </tr>
        </thead>
        {foreach from=$subscriptions item="subscription"}
            {include file="addons/adls_subscriptions/views/adls_subscriptions/components/table/row.tpl" subscription=$subscription}
        {/foreach}
    </table>
{/strip}