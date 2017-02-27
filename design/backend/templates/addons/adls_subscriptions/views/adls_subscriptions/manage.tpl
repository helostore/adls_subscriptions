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

    {if $runtime.mode == "new"}
        <p>{__("text_admin_new_subscriptions")}</p>
    {/if}

    {capture name="sidebar"}
        {include file="common/saved_search.tpl" dispatch="adls_subscriptions.manage" view_type="subscriptions"}
        {include file="addons/adls_subscriptions/views/adls_subscriptions/components/search_form.tpl" dispatch="adls_subscriptions.manage"}
    {/capture}

    <form action="{""|fn_url}" method="post" target="_self" name="subscriptions_list_form">

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
        {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
        {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}

        {if $incompleted_view}
            {assign var="page_title" value=__("incompleted_orders")}
            {assign var="get_additional_statuses" value=true}
        {else}
            {assign var="page_title" value=__("adlss.subscriptions")}
            {assign var="get_additional_statuses" value=false}
        {/if}
        {assign var="order_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses:$get_additional_statuses:true}
        {assign var="extra_status" value=$config.current_url|escape:"url"}
        {$statuses = []}
        {assign var="order_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:$statuses:$get_additional_statuses:true}

        {if $subscriptions}
            {strip}
            <table width="100%" class="table table-middle">
                <thead>
                <tr>
                    <th class="left">
                        {include file="common/check_items.tpl" check_statuses=$order_status_descr}
                    </th>
                    <th width="5%">{tableHeadLink label="id" sortBy="id"}</th>
                    <th width="17%">{tableHeadLink label="order" sortBy="orderId"}</th>
                    <th width="17%">{tableHeadLink label="customer" sortBy="customer"}</th>
                    <th width="17%">{tableHeadLink label="product" sortBy="product$name"}</th>
                    <th width="17%">{tableHeadLink label="price" sortBy="price"}</th>
                    <th width="10%">{tableHeadLink label="status" sortBy="status"}</th>
                    <th width="17%">{tableHeadLink label="adlss.startDate" sortBy="startDate"}</th>
                    <th width="17%">{tableHeadLink label="adlss.endDate" sortBy="endDate"}</th>
                    <th width="17%">{tableHeadLink label="adlss.neverExpires" sortBy="neverExpires"}</th>
                    <th width="17%">{tableHeadLink label="adlss.paidCycles" sortBy="paidCycles"}</th>
                    <th width="17%">{tableHeadLink label="adlss.elapsedCycles" sortBy="elapsedCycles"}</th>
                    <th width="17%">{tableHeadLink label="adlss.updatedAt" sortBy="updatedAt"}</th>
                    <th width="17%">{tableHeadLink label="adlss.createdAt" sortBy="createdAt"}</th>

                    {hook name="adls_subscriptions:manage_header"}{/hook}

                    <th>&nbsp;</th>
                </tr>
                </thead>
                {foreach from=$subscriptions item="subscription"}
                    {hook name="adls_subscriptions:subscription_row"}
                        <tr>
                            <td class="left">
                                <input type="checkbox" name="ids[]" value="{$subscription->getId()}" class="cm-item cm-item-status-{$subscription->getStatus()|lower}" /></td>
                            <td>
                                <a href="{"orders.details?order_id=`$subscription->getId()`"|fn_url}" class="underlined">#{$subscription->getId()}</a>
                                {include file="views/companies/components/company_name.tpl" object=$subscription}
                            </td>

                            <td>
                                <a href='{fn_url("orders.details?order_id=`$subscription->getOrderId()`")}' target="_blank">
                                    #{$subscription->getOrderId()}
                                </a>
                            </td>
                            <td>
                                <a href='{fn_url("profiles.update?user_id=`$subscription->extra['user$id']`")}' target="_blank">
                                    {$subscription->extra['user$lastName']} {$subscription->extra['user$firstName']} (#{$subscription->extra['user$id']})
                                </a>
                            </td>
                            <td>
                                <a href='{fn_url("products.update?product_id=`$subscription->extra['product$id']`")}' target="_blank">
                                    {$subscription->extra['product$name']} (#{$subscription->extra['product$id']})
                                </a>
                            </td>

                            <td>
                                {include file="common/price.tpl" value=$subscription->extra['orderItem$price']}
                            </td>
                            <td>
                                {if "MULTIVENDOR"|fn_allowed_for}
                                    {assign var="notify_vendor" value=true}
                                {else}
                                    {assign var="notify_vendor" value=false}
                                {/if}
                                {$subscription->getStatusLabel()}

                                {*{include file="common/select_popup.tpl" suffix="o" order_info=$subscription id=$subscription->getId() status=$subscription->getStatus() items_status=$order_status_descr update_controller="orders" notify=true notify_department=true notify_vendor=$notify_vendor status_target_id="orders_total,`$rev`" extra="&return_url=`$extra_status`" statuses=$order_statuses btn_meta="btn btn-info o-status-`$subscription->status` btn-small"|lower}*}
                            </td>
                            <td>
                                {if $subscription->hasStartDate()}
                                    {$subscription->getStartDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                                {else}
                                    &infin;
                                {/if}
                            </td>
                            <td>
                                {if $subscription->hasEndDate()}
                                    {$subscription->getEndDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                                {else}
                                    &infin;
                                {/if}
                            </td>


                            <td>
                                {if $subscription->isNeverExpires()}
                                    {__('yes')}
                                {else}
                                    {__('no')}
                                {/if}
                            </td>

                            <td>
                                {$subscription->getPaidCycles()}
                            </td>
                            <td>
                                {$subscription->getElapsedCycles()}
                            </td>

                            <td>
                                {$subscription->getUpdatedAt()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                            </td>
                            <td>
                                {$subscription->getCreatedAt()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                            </td>
                          {*  <td>
                                {if $subscription->email}<a href="mailto:{$subscription->email|escape:url}">@</a> {/if}
                                {if $subscription->user_id}<a href="{"profiles.update?user_id=`$subscription->user_id`"|fn_url}">{/if}{$subscription->lastname} {$subscription->firstname}{if $subscription->user_id}</a>{/if}
                            </td>
                            <td>{$subscription->phone}</td>

                            {hook name="orders:manage_data"}{/hook}

                            <td width="5%" class="center">
                                {capture name="tools_items"}
                                    <li>{btn type="list" href="orders.details?order_id=`$subscription->order_id`" text={__("view")}}</li>
                                    {hook name="orders:list_extra_links"}
                                        <li>{btn type="list" href="order_management.edit?order_id=`$subscription->order_id`" text={__("edit")}}</li>
                                    {assign var="current_redirect_url" value=$config.current_url|escape:url}
                                        <li>{btn type="list" href="orders.delete?order_id=`$subscription->order_id`&redirect_url=`$current_redirect_url`" class="cm-confirm cm-post" text={__("delete")}}</li>
                                    {/hook}
                                {/capture}
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_items}
                                </div>
                            </td>
                            <td class="right">
                                {include file="common/price.tpl" value=$subscription->total}
                            </td>*}
                        </tr>
                    {/hook}
                {/foreach}
            </table>
            {/strip}
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {if $orders}
            <div class="statistic clearfix" id="orders_total">
                {hook name="orders:statistic_list"}
                    <table class="pull-right ">
                        {if $total_pages > 1 && $search.page != "full_list"}
                            <tr>
                                <td>&nbsp;</td>
                                <td width="100px">{__("for_this_page_orders")}:</td>
                            </tr>
                            <tr>
                                <td>{__("gross_total")}:</td>
                                <td>{include file="common/price.tpl" value=$display_totals.gross_total}</td>
                            </tr>
                            {if !$incompleted_view}
                                <tr>
                                    <td>{__("totally_paid")}:</td>
                                    <td>{include file="common/price.tpl" value=$display_totals.totally_paid}</td>
                                </tr>
                            {/if}
                            <hr />
                            <tr>
                                <td>{__("for_all_found_orders")}:</td>
                            </tr>
                        {/if}
                        <tr>
                            <td class="shift-right">{__("gross_total")}:</td>
                            <td>{include file="common/price.tpl" value=$totals.gross_total}</td>
                        </tr>
                        {hook name="orders:totals_stats"}
                        {if !$incompleted_view}
                            <tr>
                                <td class="shift-right"><h4>{__("totally_paid")}:</h4></td>
                                <td class="price">{include file="common/price.tpl" value=$totals.totally_paid}</td>
                            </tr>
                        {/if}
                        {/hook}
                    </table>
                {/hook}
                <!--orders_total--></div>
        {/if}

        {include file="common/pagination.tpl" div_id=$smarty.request.content_id}


        {capture name="adv_buttons"}
            {hook name="orders:manage_tools"}
                {include file="common/tools.tpl" tool_href="order_management.new" prefix="bottom" hide_tools="true" title=__("add_order") icon="icon-plus"}
            {/hook}
        {/capture}

    </form>
{/capture}

{capture name="incomplete_button"}
    {if $incompleted_view}
        <li>{btn type="list" href="orders.manage" text={__("view_all_orders")}}</li>
    {else}
        <li>{btn type="list" href="orders.manage?skip_view=Y&status=`$smarty.const.STATUS_INCOMPLETED_ORDER`" text={__("incompleted_orders")} form="orders_list_form"}</li>
    {/if}
{/capture}

{capture name="buttons"}
    {capture name="tools_list"}
        {if $orders}
            <li>{btn type="list" text={__("bulk_print_invoice")} dispatch="dispatch[orders.bulk_print]" form="orders_list_form" class="cm-new-window"}</li>
            <li>{btn type="list" text="{__("bulk_print_pdf")}" dispatch="dispatch[orders.bulk_print..pdf]" form="orders_list_form"}</li>
            <li>{btn type="list" text="{__("bulk_print_packing_slip")}" dispatch="dispatch[orders.packing_slip]" form="orders_list_form" class="cm-new-window"}</li>
        <li>{btn type="list" text={__("view_purchased_products")} dispatch="dispatch[orders.products_range]" form="orders_list_form"}</li>

        <li class="divider"></li>
        <li>{btn type="list" text={__("export_selected")} dispatch="dispatch[orders.export_range]" form="orders_list_form"}</li>

            {$smarty.capture.incomplete_button nofilter}

            {if $orders && !$runtime.company_id}
            <li class="divider"></li>
            <li>{btn type="delete_selected" dispatch="dispatch[orders.m_delete]" form="orders_list_form"}</li>
            {/if}
        {else}
            {$smarty.capture.incomplete_button nofilter}
        {/if}
        {hook name="orders:list_tools"}
        {/hook}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{include file="common/mainbox.tpl" title=$page_title sidebar=$smarty.capture.sidebar content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons content_id="manage_orders"}
