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



class Subscription extends Entity
{
	const STATUS_ACTIVE = 'A';
	const STATUS_DISABLED = 'D';
	const STATUS_INACTIVE = 'I';

    /**
     * @var array
     */
    protected $_fieldsMap = array(
    );

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var integer
     */
    protected $planId;

    /**
     * @var integer
     */
    protected $orderId;

    /**
     * @var integer
     */
    protected $itemId;

    /**
     * @var integer
     */
    protected $productId;

    /**
     * @var integer
     */
    protected $companyId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var boolean
     */
    protected $neverExpires;

    /**
     * @var integer
     */
    protected $paidCycles;

    /**
     * @var integer
     */
    protected $elapsedCycles;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPlanId()
    {
        return $this->planId;
    }

    /**
     * @param int $planId
     *
     * @return $this
     */
    public function setPlanId($planId)
    {
        $this->planId = $planId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     *
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     *
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        return __('adlss.subscription.status.' . strtolower($this->status));
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasStartDate()
    {
        return !empty($this->startDate);
    }
    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasEndDate()
    {
        return !empty($this->endDate);
    }
    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNeverExpires()
    {
        return $this->neverExpires;
    }

    /**
     * @param boolean $neverExpires
     *
     * @return $this
     */
    public function setNeverExpires($neverExpires)
    {
        $this->neverExpires = $neverExpires;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaidCycles()
    {
        return $this->paidCycles;
    }

    /**
     * @param int $paidCycles
     *
     * @return $this
     */
    public function setPaidCycles($paidCycles)
    {
        $this->paidCycles = $paidCycles;

        return $this;
    }

    /**
     * @param int $incrementValue
     *
     * @return $this
     */
    public function payCycle($incrementValue = 1)
    {
        $this->paidCycles += $incrementValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getElapsedCycles()
    {
        return $this->elapsedCycles;
    }

    /**
     * @param int $elapsedCycles
     *
     * @return $this
     */
    public function setElapsedCycles($elapsedCycles)
    {
        $this->elapsedCycles = $elapsedCycles;

        return $this;
    }

    /**
     * @return $this
     */
    public function elapseCycle()
    {
        $this->elapsedCycles++;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Methods
     */

    /**
     * @return $this
     */
    public function activate()
    {
        $this->setStatus(self::STATUS_ACTIVE);

        return $this;
    }

    /**
     * @return $this
     */
    public function disable()
    {
        $this->setStatus(self::STATUS_DISABLED);

        return $this;
    }

    /**
     * @return $this
     */
    public function inactivate()
    {
        $this->setStatus(self::STATUS_INACTIVE);

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }
    /**
     * @return bool
     */
    public function isInactive()
    {
        return $this->status == self::STATUS_INACTIVE;
    }
    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->status == self::STATUS_DISABLED;
    }





    /**
     * Methods
     */

    /**
     * @return string
     */
    public function getDates()
    {
        $start = !empty($this->startDate) ? $this->startDate->format('Y-m-d') : 'n/a';
        $end = !empty($this->endDate) ? $this->endDate->format('Y-m-d') : 'n/a';

        return $start . ' - ' . $end;
    }

    /**
     * Checks if a subscription is newly created
     *
     * @return bool
     */
    public function isNew()
    {
        return empty($this->startDate) && empty($this->endDate) /*&& empty($this->paidCycles) && empty($this->elapsedCycles)*/;
    }

    /**
     * Checks if subscription is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if (empty($this->endDate)) {
            return false;
        }

        $now = Utils::instance()->getCurrentDate();
        $endDate = clone $this->endDate;

        // discard seconds in comparison (for testing purposes)
        Utils::instance()->discardSeconds($now);
        Utils::instance()->discardSeconds($endDate);
        
        return ($endDate <= $now);
    }

}