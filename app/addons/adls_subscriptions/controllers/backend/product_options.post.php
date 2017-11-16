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

use HeloStore\ADLSS\Plan\PlanRepository;
use HeloStore\ADLSS\Subscribable\SubscribableManager;
use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if (in_array($mode, array('update', 'add', 'manage'))) {

		if (!empty($_POST) && !empty($_POST['option_data'])) {
            if (isset($_POST['option_data']['planId'])) {
                $option = $_POST['option_data'];
                $optionId = intval($_POST['option_id']);
                $planId = intval($option['planId']);
                $subscribableId = intval($option['subscribableId']);
                $subscribableManager = SubscribableManager::instance();



                if (!empty($planId)) {
                    if (!empty($subscribableId)) {
                        $result = $subscribableManager->updateProductOption($subscribableId, $planId);
                        if ($result == true) {
                            fn_set_notification('N', __('notice'), __('adlss.subscribable.update.success'));
                        } elseif ($result === false) {
                            fn_set_notification('E', __('error'), __('adlss.subscribable.update.fail'));
                        }
                    } else {
                        if ($subscribableManager->linkProductOption($optionId, $planId)) {
                            fn_set_notification('N', __('notice'), __('adlss.subscribable.create.success'));
                        } else {
                            fn_set_notification('E', __('error'), __('adlss.subscribable.create.fail'));
                        }
                    }

                } else if ($subscribableManager->isSubscribable($option) && empty($planId)) {
                    if ($subscribableManager->unlinkProductOption($optionId)) {
                        fn_set_notification('N', __('notice'), __('adlss.subscribable.delete.success'));
                    } else {
                        fn_set_notification('E', __('error'), __('adlss.subscribable.delete.fail'));
                    }
                }
            }
		}
	}

    return;
}

if (in_array($mode, array('update', 'add', 'manage'))) {
	$view = &Tygh::$app['view'];

	list($plans, ) = PlanRepository::instance()->find(array(
//		'status' => Plan::STATUS_ACTIVE
	));
	$view->assign('plans', $plans);
}
