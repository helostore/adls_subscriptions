<?php
/**
 * HELOstore
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://helostore.com/ and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    HELOstore
 * @copyright  Copyright (c) 2017 HELOstore. (https://helostore.com/)
 * @license    https://helostore.com/legal/license-agreement/   License Agreement
 * @version    $Id$
 */

if (!defined('BOOTSTRAP')) { die('Access denied'); }

require_once __DIR__ . '/vendor/autoload.php';

DEFINE('ADLS_SUBSCRIPTIONS_ADDON_PATH', __DIR__);


fn_register_hooks(
    'get_product_option_data_pre'
    , 'get_product_option_data_post'
    , 'change_order_status'
    , 'get_order_info'
);