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

use HeloStore\ADLSS\Plan\PlanRepository;
use HeloStore\ADLSS\Subscription\SubscriptionManager;
use HeloStore\ADLSS\Subscription\SubscriptionRepository;
use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
$subscriptionRepository = SubscriptionRepository::instance();
$subscriptionManager = SubscriptionManager::instance();
$planRepository = PlanRepository::instance();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update') {

        $requestData = $_POST['subscription'];
        $subscription = $subscriptionRepository->findOneById($_REQUEST['id']);
        if (empty($subscription)) {
            return array(CONTROLLER_STATUS_NO_PAGE);
        }

        if ($subscription->getPlanId() != $requestData['planId']) {

            $newPlan = $planRepository->findOneById($requestData['planId']);
            if (empty($newPlan)) {
                fn_set_notification('E', __('error'), 'Cannot update subscription plan: new plan not found.');

                return array(CONTROLLER_STATUS_OK, $_SERVER['HTTP_REFERER']);
            }

            $initialPaidPeriod = empty($requestData['initialPaidPeriod']) ? null : intval($requestData['initialPaidPeriod']);

            if ( $subscriptionManager->changePlan($subscription, $newPlan, $initialPaidPeriod)) {
                fnx('OK!');
            } else {
                fnx('Fail!');
            }
        }

		return array(CONTROLLER_STATUS_OK, $_SERVER['HTTP_REFERER']);
	}
}

if ($mode == 'update') {
    $subscription = $subscriptionRepository->findOneById($_REQUEST['id']);
    if (empty($subscription)) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    Tygh::$app['view']->assign('subscription', $subscription);

    $params = array();
    $params['extended'] = true;
    list($plans, $search) = $planRepository->find($params);
    Tygh::$app['view']->assign('plans', $plans);
    Tygh::$app['view']->assign('search', $search);
}

if ($mode == 'manage') {
	$params = $_REQUEST;
	$params['extended'] = true;
	$params['items_per_page'] = !empty($params['items_per_page']) ? $params['items_per_page'] : Registry::get('settings.Appearance.admin_elements_per_page');
	list($subscriptions, $search) = $subscriptionRepository->find($params);
	Tygh::$app['view']->assign('subscriptions', $subscriptions);
	Tygh::$app['view']->assign('search', $search);
}
