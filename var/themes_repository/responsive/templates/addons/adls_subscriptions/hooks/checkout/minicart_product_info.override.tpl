{if !empty($product.extra) &&  !empty($product.extra.custom_product_name)}
    {if $block.properties.products_links_type == "thumb"}
        <div class="ty-cart-items__list-item-image">
            {include file="common/image.tpl" image_width="40" image_height="40" images=$product.main_pair no_ids=true}
        </div>
    {/if}
    <div class="ty-cart-items__list-item-desc">
        <a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.extra.custom_product_name nofilter}</a>{* ws@hs *}
        <p>
            <span>{$product.amount}</span><span>&nbsp;x&nbsp;</span>{include file="common/price.tpl" value=$product.display_price span_id="price_`$key`_`$dropdown_id`" class="none"}
        </p>
    </div>
    {if $block.properties.display_delete_icons == "Y"}
        <div class="ty-cart-items__list-item-tools cm-cart-item-delete">
            {if (!$runtime.checkout || $force_items_deletion) && !$product.extra.exclude_from_calculate}
                {include file="buttons/button.tpl" but_href="checkout.delete.from_status?cart_id=`$key`&redirect_url=`$r_url`" but_meta="cm-ajax cm-ajax-full-render" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}
            {/if}
        </div>
    {/if}
{/if}