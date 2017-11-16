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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$subscriptionId = 0;
if ( ! empty( $_REQUEST['subscription_id'] ) ) {
	$subscriptionId = intval( $_REQUEST['subscription_id'] );
} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
	$parts = parse_url( $_SERVER['HTTP_REFERER'] );
	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $q );
		if ( ! empty( $q['subscription_id'] ) ) {
			$subscriptionId = intval( $q['subscription_id'] );
		}
	}
}

if ( ! empty( $subscriptionId ) ) {
	if ( ! empty( $_SESSION['adlss_cart_copy'] ) ) {
		$_SESSION['cart'] = $_SESSION['adlss_cart_copy'];
		fn_save_cart_content($_SESSION['cart'], $auth['user_id']);
	}
	$display_steps = Tygh::$app['view']->getTemplateVars('display_steps');
	$display_steps['step_one'] = false;
	$display_steps['step_two'] = false;
	Tygh::$app['view']->assign('display_steps', $display_steps);
	Tygh::$app['view']->assign('adlss_checkout_subscription_renewal', true);
}