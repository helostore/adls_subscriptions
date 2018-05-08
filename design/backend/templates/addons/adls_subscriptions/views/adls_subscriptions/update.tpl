{if $subscription->getId()}
    {assign var="id" value=$subscription->getId()}
{else}
    {assign var="id" value=0}
{/if}

{assign var="allow_save" value=true}
{if "ULTIMATE"|fn_allowed_for}
    {*{assign var="allow_save" value=$subscription|fn_allow_save_object:"adls_subscriptions"}*}
    {assign var="allow_save" value=true}
{/if}
{$show_save_btn = $allow_save scope = root}
{capture name="mainbox"}

    {capture name="tabsbox"}

        <form action="{""|fn_url}" method="post" name="subscription_update_form" class="form-horizontal form-edit  {if !$allow_save}cm-hide-inputs{/if}" enctype="multipart/form-data">

            <div id="update_subscription_form_{$subscription->getId()}">
                <input type="hidden" class="cm-no-hide-input" id="id" name="id" value="{$id}" />

                <div id="content_basic">

                    {include file="common/subheader.tpl" title=__("information") target="#subscriptions_information_setting"}
                    <div id="subscriptions_information_setting" class="in collapse">
                        <fieldset>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("order")}:</label>
                                <div class="controls">
                                    <a href='{fn_url("orders.details?order_id=`$subscription->getOrderId()`")}' target="_blank">
                                        #{$subscription->getOrderId()}
                                    </a>
                                    {*<input type="text" name="subscription[name]" id="" size="55" value="{$subscription->getName()}" class="input-large" />*}
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("customer")}:</label>
                                <div class="controls">
                                    <a href='{fn_url("profiles.update?user_id=`$subscription->extra['user$id']`")}' target="_blank">
                                        {$subscription->extra['user$lastName']} {$subscription->extra['user$firstName']} (#{$subscription->extra['user$id']})
                                    </a>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("product")}:</label>
                                <div class="controls">
                                    <a href='{fn_url("products.update?product_id=`$subscription->extra['product$id']`")}' target="_blank">
                                        {$subscription->extra['product$name']} (#{$subscription->extra['product$id']})
                                    </a>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("price")}:</label>
                                <div class="controls">
                                    {include file="common/price.tpl" value=$subscription->extra['orderItem$price']}
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("status")}:</label>
                                <div class="controls">
                                    {$subscription->getStatusLabel()}
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("adlss.plan")}:</label>
                                <div class="controls">

                                    {if !empty($plans)}
                                        <select name="subscription[planId]">
                                            {foreach from=$plans item="plan"}
                                                <option value="{$plan->getId()}" {if $plan->getId() == $subscription->extra['plan$id']}selected="selected"{/if}>{$plan->getName()}</option>
                                            {/foreach}
                                        </select>
                                        &nbsp;&nbsp;New initial paid period:
                                        <input type="text" name="subscription[initialPaidPeriod]" size="55" value="" class="input-small" />

                                        <p><small><em>Changing the plan will automatically update the start/end dates.</em></small></p>
                                    {else}
                                        <a href="{"adls_plans.update?id=`$subscription->extra['plan$id']`"|fn_url}" target="_blank">{$subscription->extra['plan$name']}</a>
                                    {/if}
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("adlss.start_date")} &mdash; {__("adlss.end_date")}:</label>
                                <div class="controls">
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
                                    <small>(remaining: {$subscription->getRemainingTime()} nofilter)</small>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="" class="control-label cm-required">{__("adlss.neverExpires")}:</label>
                                <div class="controls">
                                    {if $subscription->isNeverExpires()}
                                        {__('yes')}
                                    {else}
                                        {__('no')}
                                    {/if}
                                </div>
                            </div>



                            <div class="control-group">
                                <label for="elm_subscription_cycle" class="control-label cm-required">{__("adlss.subscription.cycle")}:</label>
                                <div class="controls">
                                    {*<input type="text" name="subscription[cycle]" id="elm_subscription_cycle" size="55" value="{$subscription->getCycle()}" class="input-large" />*}
                                </div>
                            </div>

                            {*{include file="common/select_status.tpl" input_name="subscription[status]" id="elm_subscription_status" obj=$subscription hidden=true}*}

                        </fieldset>
                    </div>


                </div>

                {if !$id}
                    {assign var="_title" value=__('adlss.subscription.new')}
                {else}
                    {assign var="_title" value="{__('adlss.subscription.edit')}: #`$subscription->getId()`"}
                {/if}

                {capture name="buttons"}
                    {if $id}
                        {capture name="tools_list"}
                            {hook name="adls_subscriptions:tools_list"}
                            {/hook}
                            <li class="divider"></li>
                            {$smarty.capture.preview nofilter}
                            {if $allow_save}
                                {*<li>{btn type="list" text=__("adlss.subscription.delete") class="cm-confirm cm-post" href="adls_subscriptions.delete?id=$id"}</li>*}
                            {/if}
                        {/capture}
                    {/if}
                    {dropdown content=$smarty.capture.tools_list}

                    {if !$show_save_btn}
                        {assign var="hide_first_button" value=true}
                        {assign var="hide_second_button" value=true}
                    {/if}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[adls_subscriptions.update]" hide_first_button=$hide_first_button hide_second_button=$hide_second_button but_target_form="subscription_update_form" save=$id}
                {/capture}

                <!--update_subscription_form_{$subscription->getId()}--></div>
        </form>

        {hook name="adls_subscriptions:tabs_extra"}
        {/hook}

    {/capture}
    {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox track=true}

{/capture}

{capture name="sidebar"}

{/capture}

{include file="common/mainbox.tpl" title=$_title sidebar=$smarty.capture.sidebar sidebar_position="left" content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}
