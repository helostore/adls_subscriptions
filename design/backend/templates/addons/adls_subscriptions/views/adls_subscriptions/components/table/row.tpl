{hook name="adls_subscriptions:subscription_row"}
    <tr>
        <td class="left">
            <input type="checkbox" name="ids[]" value="{$subscription->getId()}" class="cm-item cm-item-status-{$subscription->getStatus()|lower}" />
        </td>
        <td>
            <a href='{fn_url("adls_subscriptions.update?id=`$subscription->getId()`")}' target="_blank">
                #{$subscription->getId()}
            </a>
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
            <a href="{"adls_plans.update?id=`$subscription->extra['plan$id']`"|fn_url}" target="_blank">{$subscription->extra['plan$name']}</a>
        </td>
        <td colspan="2" class="center">
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
            <br>{$subscription->getRemainingTime() nofilter}

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