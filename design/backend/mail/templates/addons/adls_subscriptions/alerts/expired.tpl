<!-- CONTENT TABLE // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top" class="textContent">
            <!--
                The "mc:edit" is a feature for MailChimp which allows
                you to edit certain row. It makes it easy for you to quickly edit row sections.
                http://kb.mailchimp.com/templates/code/create-editable-content-areas-with-mailchimps-template-language
            -->
            <h3 mc:edit="header" style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Subscription #{$subscription->getId()} has expired!</h3>
            <div mc:edit="body" style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {*<p>Subscription #{$subscription->getId()} has expired.</p>*}
                {strip}
                {if !empty($license)}
                    <p>The attached license has been temporarily disabled: <strong>{$license->getLicenseKey()}</strong>
                    {if $license->hasDomains()}
                        , attached to domains: &nbsp;
                        {foreach from=$license->getNonEmptyDomains() item="domain" name="license_domains"}
                            <strong>{$domain.name}</strong>
                            {if !$smarty.foreach.license_domains.last},&nbsp;{/if}
                        {/foreach}.
                    {/if}
                    </p>
                {/if}
                {/strip}

                <!-- CONTENT TABLE // -->
                <table border="0" cellpadding="0" cellspacing="0" width="50%" class="emailButton" style="background-color: #3498DB;">
                    <tr>
                        <td align="center" valign="middle" class="buttonContent" style="padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;">
                            <a style="color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:135%;" href="{$subscriptionRenewalLink}" target="_blank">Click Here to Renew</a>
                        </td>
                    </tr>
                </table>
                <!-- // CONTENT TABLE -->

            </div>
        </td>
    </tr>
</table>
<!-- // CONTENT TABLE -->