{if !empty($product.custom_product_name)}
    {if !$cart.products.$key.extra.parent}
        <li class="ty-order-products__item">
            <a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="ty-order-products__a">{$product.custom_product_name nofilter}</a>{* ws@hs *}
            {if !$product.exclude_from_calculate}
                {include file="buttons/button.tpl" but_href="checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`" but_meta="ty-order-products__item-delete delete" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}
            {/if}
            <div class="ty-order-products__price">
                {$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}
            </div>
            {include file="common/options_info.tpl" product_options=$product.product_options no_block=true}
            {hook name="block_checkout:product_extra"}{/hook}
        </li>
    {/if}
{/if}