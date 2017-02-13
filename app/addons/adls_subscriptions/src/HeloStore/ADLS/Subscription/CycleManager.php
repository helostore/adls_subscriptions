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
     *
     * @return bool
     */
    public function begin(Subscription $subscription)
    {
        $planId = $subscription->getPlanId();
        $subscriptionRepository = SubscriptionRepository::instance();
        $planRepository = PlanRepository::instance();
        $plan = $planRepository->findOneById($planId);

        $subscription->setStartDate(new \DateTime());
        $subscription->setEndDate(new \DateTime());
        $subscription->getEndDate()->modify('+ ' . $plan->getCycle() . ' months');
        $subscription->payCycle();

        $subscription->activate();
        return $subscriptionRepository->update($subscription);
    }

    /**
     * Suspends a past-due subscription
     *
     * @param Subscription $subscription
     *
     * @return bool
     */
    public function suspend(Subscription $subscription)
    {
        $subscriptionRepository = SubscriptionRepository::instance();

        $subscription->elapseCycle();
        $subscription->disable();

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
        $thresholds = array(
            1, 3, 7
//            '- 3 days',
//            '- 7 days'
        );


        foreach ($thresholds as $thresholdDays) {
//            $modifier = "- $thresholdDays days";
//            $thresholdDate = clone $endDate;
//            $thresholdDate->modify($modifier);
            $interval = $now->diff($endDate);
            if ($interval->days && $interval->days == $thresholdDays) {
//                aa('$now:           ' . $now->format('Y-m-d H:i:s'));
//                aa('$endDate:       ' . $endDate->format('Y-m-d H:i:s'));
//                aa('$thresholdDate: ' . $thresholdDate->format('Y-m-d H:i:s'));
//                aa($interval);
                SubscriptionManager::instance()->alert($subscription, $thresholdDays);
            }
        }
    }
}