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
namespace HeloStore\ADLS\Subscription;

use DateTime;
use Exception;
use Tygh\Registry;

class CycleManager extends Manager
{
    /**
     * @param Plan $plan
     */
    public function timeTravel(Plan $plan)
    {
        $start = new \DateTime();

        $date = $start;
        $end = new \DateTime();
        $end->modify('+3 years');
        while ($date <= $end) {
            $date->modify('+1 month');
            echo $date->format('Y-m-d') . PHP_EOL;
        }
    }

    /**
     * Activates/configures a new subscription
     *
     * @param Subscription $subscription
     * @param integer $initialPaidPeriod Initial paid period, number of months
     * @return bool
     */
    public function begin(Subscription $subscription, $initialPaidPeriod = null)
    {
        $planId = $subscription->getPlanId();
        $subscriptionRepository = SubscriptionRepository::instance();
        $planRepository = PlanRepository::instance();
        $plan = $planRepository->findOneById($planId);

        $subscription->setStartDate(new \DateTime());
        $subscription->setEndDate(new \DateTime());
        if (!empty($initialPaidPeriod)) {
            $paidCycles = $initialPaidPeriod / $plan->getCycle();
            $subscription->payCycle($paidCycles);
            $subscription->getEndDate()->modify('+ ' . $initialPaidPeriod . ' months');
        } else {
            $subscription->getEndDate()->modify('+ ' . $plan->getCycle() . ' months');
            $subscription->payCycle();
        }

        $subscription->activate();
        return $subscriptionRepository->update($subscription);
    }

    /**
     * Suspends a past-due subscription
     *
     * @param Subscription $subscription
     * @return bool
     * @throws Exception
     * @throws \Tygh\Exceptions\DeveloperException
     */
    public function suspend(Subscription $subscription)
    {
        $subscriptionRepository = SubscriptionRepository::instance();

        $subscription->elapseCycle();
        $subscription->inactivate();


        // Change order status to Expired (A)
        $order = fn_get_order_info($subscription->getOrderId());
        if (empty($order)) {
            throw new Exception('Failed while suspending subscription: order not found');
        }

        $settings = Utils::instance()->getSettings();

        $statusTo = $settings['order_status_on_suspend'];
        $orderId = $subscription->getOrderId();
        $forceNotification = array();

        if (defined('ADLS_SUBSCRIPTIONS_NO_EMAILS')) {
            $forceNotification = array('C' => false, 'A' => false, 'V' => false);
        }
        fn_change_order_status($orderId, $statusTo, $statusFrom = '', $forceNotification, $placeOrder = false);


        fn_set_hook('adls_subscriptions_post_suspend', $subscription);

        return $subscriptionRepository->update($subscription);
    }


    public function check(Subscription $subscription)
    {

        if ($subscription->isExpired()) {
            if ($this->suspend($subscription)) {
            } else {

            }

        } else if ($this->checkAlerts($subscription)) {

        }

        return true;
    }

    public function checkAlerts(Subscription $subscription)
    {
        if ($subscription->isNeverExpires()) {
            return false;
        }
        $utils = Utils::instance();
        $now = $utils->getCurrentDate();
        $endDate = $subscription->getEndDate();
        $utils->discardSeconds($endDate);

        // how many days before expiration send alerts
        $alerts = array(
            array(
                'days' => 7,
                'template' => '7_days_before.tpl',
                'subject' => 'Your subscription expires in 7 days',
                'title' => 'Your subscription expires in 7 days'
            ),
            array(
                'days' => 3,
                'template' => '3_days_before.tpl',
                'subject' => 'Your subscription expires in 3 days'
            ),
            array(
                'days' => 1,
                'template' => '1_day_before.tpl',
                'subject' => 'Your subscription expires tomorrow!'
            ),
        );


        foreach ($alerts as $alert) {
//            $modifier = "- $thresholdDays days";
//            $thresholdDate = clone $endDate;
//            $thresholdDate->modify($modifier);
            $interval = $now->diff($endDate);
            if ($interval->days && $interval->days == $alert['days']) {
//                aa('$now:           ' . $now->format('Y-m-d H:i:s'));
//                aa('$endDate:       ' . $endDate->format('Y-m-d H:i:s'));
//                aa('$thresholdDate: ' . $thresholdDate->format('Y-m-d H:i:s'));
//                aa($interval);
                SubscriptionManager::instance()->alert($subscription, $alert);
            }
        }
    }
}