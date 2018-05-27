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

use HeloStore\ADLSS\Plan;
use HeloStore\ADLSS\Plan\PlanManager;
use HeloStore\ADLSS\Plan\PlanRepository;
use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$planRepository = PlanRepository::instance();
$planManager = PlanManager::instance();

if (!empty($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
} else {
    $id = 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if (($mode == 'update' || $mode == 'add') && !empty($_POST['plan'])) {

        $name = !empty($_POST['plan']['name']) ? $_POST['plan']['name'] : '';
        $cycle = !empty($_POST['plan']['cycle']) ? $_POST['plan']['cycle'] : null;
        $id = !empty($_POST['id']) ? $_POST['id'] : null;

        if (empty($name) || empty($cycle)) {
            fn_set_notification('E', __('error'), __('adlss.plan.error.missing_fields'));

            $extra = '';
            if (!empty($id)) {
                $extra = '?id=' . $id;
            }

            return array(CONTROLLER_STATUS_OK, 'adls_plans.' . $mode . $extra);
        }

        if (empty($id)) {
            $id = $planRepository->create($name, $cycle);
            if (empty($id)) {
                fn_set_notification('E', __('error'), __('adlss.plan.add.error'));
                $redirect = 'adls_plans.add';
            } else {
                fn_set_notification('N', __('notice'), __('adlss.plan.add.success'));
                $redirect = 'adls_plans.update?id=' . $id;
            }
        } else {
            $plan = $planRepository->findOneById($id);
            $plan
                ->setName($name)
                ->setCycle($cycle);
            $planRepository->update($plan);
            fn_set_notification('N', __('notice'), __('adlss.plan.update.success'));
            $redirect = 'adls_plans.update?id=' . $id;
        }

        return array(CONTROLLER_STATUS_OK, $redirect);
	}

    if ($mode == 'delete') {
        $suffix = '.manage';

        if (!empty($id)) {
            if ($planManager->delete($id)) {
                fn_set_notification('N', __('notice'), __('adlss.plan.delete.success'));
            } else {
                fn_set_notification('E', __('error'), __('adlss.plan.delete.error'));
            }

        }
    }

    return array(CONTROLLER_STATUS_OK, 'adls_plans' . $suffix);
}

if ($mode == 'add') {
    $plan = new Plan();
    Tygh::$app['view']->assign('plan', $plan);
}

if ($mode == 'update') {
    $plan = $planRepository->findOneById($id);
    Tygh::$app['view']->assign('plan', $plan);
}

if ($mode == 'manage') {
	$planRepository = PlanRepository::instance();
	$params = $_REQUEST;
	$params['extended'] = true;
	$params['items_per_page'] = !empty($params['items_per_page']) ? $params['items_per_page'] : Registry::get('settings.Appearance.admin_elements_per_page');
	list($plans, $search) = $planRepository->find($params);

	Tygh::$app['view']->assign('plans', $plans);
	Tygh::$app['view']->assign('search', $search);
}