<?php
/**
 * HELOstore
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://helostore.com/ and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    HELOstore
 * @copyright  Copyright (c) 2015-2016 HELOstore. (https://helostore.com/)
 * @license    https://helostore.com/legal/license-agreement/   License Agreement
 * @version    $Id$
 */

use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'details') {
	$view = &Tygh::$app['view'];

    Registry::set('navigation.tabs.adls_subscriptions', array (
        'title' => __('adlss.subscriptions'),
        'js' => true
    ));

    $subscriptionRepository = \HeloStore\ADLSS\Subscription\SubscriptionRepository::instance();
    list($orderSubscriptions, $search) = $subscriptionRepository->findByOrder($_REQUEST['order_id'], array(
        'extended' => true
    ));
    Tygh::$app['view']->assign('orderSubscriptions', $orderSubscriptions);
    Tygh::$app['view']->assign('orderSubscriptionsSearch', $search);
}
