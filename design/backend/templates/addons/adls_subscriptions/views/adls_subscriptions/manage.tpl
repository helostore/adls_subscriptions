

{capture name="mainbox"}

    {if $runtime.mode == "new"}
        <p>{__("text_admin_new_subscriptions")}</p>
    {/if}

    {capture name="sidebar"}
        {include file="common/saved_search.tpl" dispatch="adlss_subscriptions.manage" view_type="subscriptions"}
        {include file="addons/adls_subscriptions/views/adls_subscriptions/components/search_form.tpl" dispatch="adlss_subscriptions.manage"}
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
            {include file="addons/adls_subscriptions/views/adls_subscriptions/components/table/table.tpl" subscriptions=$subscriptions}
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
