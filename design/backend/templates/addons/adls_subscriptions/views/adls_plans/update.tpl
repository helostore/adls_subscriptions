{if $plan->getId()}
    {assign var="id" value=$plan->getId()}
{else}
    {assign var="id" value=0}
{/if}

{assign var="allow_save" value=true}
{if "ULTIMATE"|fn_allowed_for}
    {*{assign var="allow_save" value=$plan|fn_allow_save_object:"adls_plans"}*}
    {assign var="allow_save" value=true}
{/if}
{$show_save_btn = $allow_save scope = root}
{capture name="mainbox"}

    {capture name="tabsbox"}

        <form action="{""|fn_url}" method="post" name="plan_update_form" class="form-horizontal form-edit  {if !$allow_save}cm-hide-inputs{/if}" enctype="multipart/form-data">

            <div id="update_plan_form_{$plan->getId()}">
                <input type="hidden" class="cm-no-hide-input" id="selected_section" name="selected_section" value="{$selected_section}"/>
                <input type="hidden" class="cm-no-hide-input" id="id" name="id" value="{$id}" />
                <input type="hidden" class="cm-no-hide-input" name="result_ids" value="update_plan_form_{$plan->getId()}"/>

                <div id="content_basic">

                    {include file="common/subheader.tpl" title=__("information") target="#plans_information_setting"}
                    <div id="plans_information_setting" class="in collapse">
                        <fieldset>
                            <div class="control-group">
                                <label for="elm_plan_name" class="control-label cm-required">{__("adlss.plan.name")}:</label>
                                <div class="controls">
                                    <input type="text" name="plan[name]" id="elm_plan_name" size="55" value="{$plan->getName()}" class="input-large" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="elm_plan_cycle" class="control-label cm-required">{__("adlss.plan.cycle")}:</label>
                                <div class="controls">
                                    <input type="text" name="plan[cycle]" id="elm_plan_cycle" size="55" value="{$plan->getCycle()}" class="input-large" />
                                </div>
                            </div>

                            {*{include file="common/select_status.tpl" input_name="plan[status]" id="elm_plan_status" obj=$plan hidden=true}*}

                        </fieldset>
                    </div>


                </div>

                {if !$id}
                    {assign var="_title" value=__('adlss.plan.new')}
                {else}
                    {assign var="_title" value="{__('adlss.plan.edit')}: `$plan->getName()`"}
                    {assign var="select_languages" value=true}
                {/if}

                {capture name="buttons"}
                    {if $id}
                        {capture name="tools_list"}
                            {hook name="adls_plans:tools_list"}
                            {/hook}
                            <li class="divider"></li>
                            {$smarty.capture.preview nofilter}
                            {if $allow_save}
                                <li>{btn type="list" text=__("adlss.plan.delete") class="cm-confirm cm-post" href="adls_plans.delete?id=$id"}</li>
                            {/if}
                        {/capture}
                    {/if}
                    {dropdown content=$smarty.capture.tools_list}

                    {if !$show_save_btn}
                        {assign var="hide_first_button" value=true}
                        {assign var="hide_second_button" value=true}
                    {/if}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[adls_plans.update]" hide_first_button=$hide_first_button hide_second_button=$hide_second_button but_target_form="plan_update_form" save=$id}
                {/capture}

                <!--update_plan_form_{$plan->getId()}--></div>
        </form>

        {hook name="adls_plans:tabs_extra"}
        {/hook}

    {/capture}
    {include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox track=true}

{/capture}

{capture name="sidebar"}

{/capture}

{include file="common/mainbox.tpl" title=$_title sidebar=$smarty.capture.sidebar sidebar_position="left" content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}
