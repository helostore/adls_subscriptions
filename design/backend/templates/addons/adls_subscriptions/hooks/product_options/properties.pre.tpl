{if !empty($option_data) && !empty($option_data.subscribableId)}
    <input type="hidden" name="option_data[subscribableId]" value="{$option_data.subscribableId}" />
{/if}
<fieldset>
	<div class="control-group">
		<label for="adlss_plan_{$id}" class="control-label">{__("adlss.plan")}</label>
		<div class="controls">
			<select id="adlss_plan_{$id}" name="option_data[planId]">
				<option>{__('none')}</option>
				{foreach from=$plans item="plan"}
					{$selected = ''}
					{if !empty($option_data.planId) && $option_data.planId == $plan->getId()}
						{$selected = 'selected="selected"'}
					{/if}
					<option value="{$plan->getId()}" {$selected}>{$plan|strval}</option>
				{/foreach}
			</select>
		</div>
	</div>
</fieldset>