<span class="adlss-availability">
{if $subscription->hasStartDate()}
    {$subscription->getStartDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
{else}
    &infin;
{/if}
&rarr;
{if $subscription->hasEndDate()}
    {$subscription->getEndDate()->getTimestamp()|date_format:"`$settings.Appearance.date_format`"}
{else}
    &infin;
{/if}
{if $subscription->isActive()}
    <br /><small>remaining: {$subscription->getRemainingTime() nofilter}</small>
{/if}
</span>