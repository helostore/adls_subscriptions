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
namespace HeloStore\ADLSS\Plan;



use HeloStore\ADLSS\Base\Manager;
use HeloStore\ADLSS\Plan;
use HeloStore\ADLSS\Subscribable\SubscribableRepository;

class PlanManager extends Manager
{
    /**
     * @var PlanRepository
     */
    protected $repository;

	public function __construct()
	{
		$this->setRepository(PlanRepository::instance());
	}

    /**
     * @param Plan $id Plan ID
     *
     * @return bool|int
     */
    public function delete($id)
    {
        $plan = $this->repository->findOneById($id);

        if (empty($plan)) {
            return false;
        }

        $subscribableRepository = SubscribableRepository::instance();
        $subscribableRepository->deleteByPlanId($plan->getId());

        return $this->repository->delete($plan->getId());
    }
}