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

use HeloStore\ADLSS\Subscription\SubscriptionRepository;
use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update') {

		return array(CONTROLLER_STATUS_OK, 'adls_subscriptions.manage');
	}
}

if ($mode == 'manage') {
	$subscriptionRepository = SubscriptionRepository::instance();
	$params = $_REQUEST;
	$params['extended'] = true;
	$params['items_per_page'] = !empty($params['items_per_page']) ? $params['items_per_page'] : Registry::get('settings.Appearance.admin_elements_per_page');
	list($subscriptions, $search) = $subscriptionRepository->find($params);

	Tygh::$app['view']->assign('subscriptions', $subscriptions);
	Tygh::$app['view']->assign('search', $search);
}