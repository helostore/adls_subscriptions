<!-- CONTENT TABLE // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top" class="textContent">
            <!--
                The "mc:edit" is a feature for MailChimp which allows
                you to edit certain row. It makes it easy for you to quickly edit row sections.
                http://kb.mailchimp.com/templates/code/create-editable-content-areas-with-mailchimps-template-language
            -->
            <p style="text-align:left;font-family:Montserrat, Helvetica, sans-serif;font:18px/26px sans-serif;margin-bottom:25px;color:#000000;">Hello{if !empty($order.firstname)}<strong> {$order.firstname}</strong>{/if},</p>

            <h3 mc:edit="header" style="color:#000000;line-height:125%;font-family:Montserrat, Helvetica, sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Your order has been migrated to a subscription tier.</h3>
            <div mc:edit="body" style="text-align:left;font-family:Montserrat,sans-serif;font:16px/26px sans-serif;margin-bottom:0;color:#000000;">

                <p style="margin-bottom: 35px;">The time has come to make a change. <strong><br />We are migrating from one-time payments to a subscription tier</strong> on all of our current and future paid products for CS-Cart and Wordpress.<p>

                <h2 style="margin-bottom: 25px;">What does it mean?</h2>
                <p style="margin-bottom: 5px;"><strong>For you</strong>, this means that in order to have access to future upgrades, you will have to renew your software upgrade subscription.</p>
                <p style="margin-bottom: 5px;">Nevertheless, you will continue to have access to your latest products versions indefinitely, regardless if you renew your subscription or not.</p>
                <p style="margin-bottom: 25px;">In short, the idea is simple: if you do not need to upgrade a product, or if the product itself doesn’t offer anything new, there’s no need to renew your subscription.</p>
                <p style="margin-bottom: 25px;"><strong>For us</strong>, this strategy will provide us more sustainability, the ability to deliver better products and release new upgrades in a shorter time.</p>
                <p style="margin-bottom: 35px;">This will also allow us to better focus our efforts on products that require continuous maintenance, either by popular demand among our customer base, or by their inherent complexity.</p>

                <h2 style="margin-bottom: 25px;">Now what..?</h2>
                <p style="margin-bottom: 5px;">We highly appreciate and value having you as our customer, and because of that, we don't want this change to take you by surprise. Hence, <strong>we are offering you a complimentary 12 months software upgrade subscription</strong> to all of the products in this order!</p>
                <p style="margin-bottom: 35px;">Therefore, at this time, you do not have to take any action. :)</p>

                <h2 style="margin-bottom: 25px;">Summary</h2>

                <p style="margin-bottom: 5px;">The products that are subject to this change are:</p>

                <ul style="padding-left: 0;">
                {foreach from=$subscriptions item='subscription'}
                    <li>
                        <b>{$subscription->extra['product$name']}</b>, now migrated to <b>{$subscription->extra['plan$name']}</b> plan, valid from
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



                <!-- CONTENT TABLE // -->
                <table border="0" cellpadding="0" cellspacing="0" width="50%" class="emailButton" style="background-color: #3498DB;">
                    <tr>
                        <td align="center" valign="middle" class="buttonContent" style="padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;">
                            <a style="color:#FFFFFF;text-decoration:none;font-family:Montserrat, Helvetica, sans-serif;font-size:20px;line-height:135%;" href="{fn_url("orders.details?order_id=`$order.order_id`", 'C', 'https')}" target="_blank">Review your order</a>
                        </td>
                    </tr>
                </table>
                <!-- // CONTENT TABLE -->

                <p style="text-align:left;font-family:Montserrat, Helvetica, sans-serif;font:18px/26px sans-serif;margin-bottom:25px;color:#000000;"><br>Thank you!<br>HELOstore</p>
                <img src="https://fontmeme.com/permalink/180127/73654fda150568377a84e53d04fc4c4c.png"/>


            </div>
        </td>
    </tr>
</table>
<!-- // CONTENT TABLE -->