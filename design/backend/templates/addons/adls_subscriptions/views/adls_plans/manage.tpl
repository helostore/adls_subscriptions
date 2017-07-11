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

{capture name="mainbox"}

    {capture name="sidebar"}
        {include file="common/saved_search.tpl" dispatch="adlss_plans.manage" view_type="plans"}
        {include file="addons/adls_subscriptions/views/adls_plans/components/search_form.tpl" dispatch="adls_plans.manage"}
    {/capture}

    <form action="{""|fn_url}" method="post" target="_self" name="plans_list_form">

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
        {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}

        {if $incompleted_view}
            {assign var="page_title" value=__("incompleted_orders")}
            {assign var="get_additional_statuses" value=true}
        {else}
            {assign var="page_title" value=__("adlss.plans")}
            {assign var="get_additional_statuses" value=false}
        {/if}
        {assign var="order_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses:$get_additional_statuses:true}
        {assign var="extra_status" value=$config.current_url|escape:"url"}
        {$statuses = []}
        {assign var="order_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:$statuses:$get_additional_statuses:true}

        {if $plans}
            {strip}
            <table width="100%" class="table table-middle">
                <thead>
                <tr>
                    <th width="1%" class="left">
                        {include file="common/check_items.tpl" check_statuses=$order_status_descr}
                    </th>
                    <th width="5%">{tableHeadLink label="id" sortBy="id"}</th>
                    <th width="17%">{tableHeadLink label="adlss.plan.name" sortBy="name"}</th>
                    <th width="17%">{tableHeadLink label="adlss.plan.cycle" sortBy="cycle"}</th>
                    <th width="17%">{tableHeadLink label="adlss.updatedAt" sortBy="updatedAt"}</th>
                    <th width="17%">{tableHeadLink label="adlss.createdAt" sortBy="createdAt"}</th>

                    {hook name="adls_plans:manage_header"}{/hook}

                    <th>&nbsp;</th>
                </tr>
                </thead>
                {foreach from=$plans item="plan"}
                    {hook name="adls_plans:plan_row"}
                        <tr>
                            <td class="left">
                                <input type="checkbox" name="ids[]" value="{$plan->getId()}" class="cm-item cm-item-status-{$plan->getStatus()|lower}" /></td>
                            <td>
                                <a href="{"adls_plans.update?id=`$plan->getId()`"|fn_url}" class="underlined">#{$plan->getId()}</a>
                            </td>

                            <td>
                                {$plan->getName()}
                            </td>
                            <td>
                                {$plan->getCycle()}
                            </td>

                            <td>
                                {if $plan->getCreatedAt()}
                                    {$plan->getCreatedAt()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                                {else}
                                    &infin;
                                {/if}
                            </td>
                            <td>
                                {if $plan->getCreatedAt()}
                                    {$plan->getCreatedAt()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                                {else}
                                    &infin;
                                {/if}
                            </td>
                            <td>
                                {$plan->getStatus()}
                            </td>

                            <td width="5%" class="center">
                                {capture name="tools_items"}
                                    <li>{btn type="list" href="adls_plans.update?id=`$plan->getId()`" text={__("edit")}}</li>
                                    {assign var="current_redirect_url" value=$config.current_url|escape:url}
                                    <li>{btn type="list" href="adls_plans.delete?id=`$plan->getId()`&redirect_url=`$current_redirect_url`" class="cm-confirm cm-post" text={__("delete")}}</li>
                                {/capture}
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_items}
                                </div>
                            </td>
                        </tr>
                    {/hook}
                {/foreach}
            </table>
            {/strip}
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {include file="common/pagination.tpl" div_id=$smarty.request.content_id}


        {capture name="adv_buttons"}
            {hook name="orders:manage_tools"}
                {include file="common/tools.tpl" tool_href="adls_plans.add" prefix="bottom" hide_tools="true" title=__("adlss.plans.new") icon="icon-plus"}
            {/hook}
        {/capture}

    </form>
{/capture}

{capture name="buttons"}

{/capture}

{include file="common/mainbox.tpl" title=$page_title sidebar=$smarty.capture.sidebar content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons content_id="manage_orders"}
