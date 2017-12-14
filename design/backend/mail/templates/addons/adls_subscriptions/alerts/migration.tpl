<!-- CONTENT TABLE // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top" class="textContent">
            <!--
                The "mc:edit" is a feature for MailChimp which allows
                you to edit certain row. It makes it easy for you to quickly edit row sections.
                http://kb.mailchimp.com/templates/code/create-editable-content-areas-with-mailchimps-template-language
            -->
            <h3 mc:edit="header" style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Your order has been migrated to a subscription tier.</h3>
            <div mc:edit="body" style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">

                <p>HELOstore is moving from one-time payments to subscription-based payments.<br>
                For you, this means that in the future you will have to renew your software upgrade subscription for some of our products, in order to benefit for future upgrades.</p>
                <p>However, because we appreciate our existing customers, and because we don't want to take you by surprise, <b>we are offering you a complimentary 12 months subscription</b> to our future upgrades to all of the products in this order.<br>
                    These products are:
                </p>

                {if !empty($subscriptions)}
                    <ul>
                    {foreach from=$subscriptions item='subscription'}
                        <li>
                            <b>{$subscription->extra['product$name']}</b> has been migrated to <b>{$subscription->extra['plan$name']}</b>, valid from
                            <b>
                            {if $subscription->hasStartDate()}
                                {$subscription->getStartDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                            {else}
                                &infin;
                            {/if}
                            </b>
                            to
                            <b>
                            {if $subscription->hasEndDate()}
                                {$subscription->getEndDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
                            {else}
                                &infin;
                            {/if}
                            </b>
                        </li>
                    {/foreach}
                    </ul>
                {/if}



                <!-- CONTENT TABLE // -->
                <table border="0" cellpadding="0" cellspacing="0" width="50%" class="emailButton" style="background-color: #3498DB;">
                    <tr>
                        <td align="center" valign="middle" class="buttonContent" style="padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;">
                            <a style="color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:135%;" href="{fn_url("orders.details?order_id=`$order.order_id`", 'C', 'https')}" target="_blank">View Your Order</a>
                        </td>
                    </tr>
                </table>
                <!-- // CONTENT TABLE -->


            </div>
        </td>
    </tr>
</table>
<!-- // CONTENT TABLE -->