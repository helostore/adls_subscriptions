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

namespace HeloStore\ADLSS\Payment;



use HeloStore\ADLSS\Base\Entity;

class Payment extends Entity
{
	const STATUS_OPENED = 'O';
	const STATUS_PAID = 'P';
	const STATUS_FAILED = 'F';

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
    protected $subscriptionId;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var integer
     */
    protected $orderId;

	/**
	 * @var integer
	 */
	protected $itemId;

	/**
	 * @var float
	 */
	protected $amount;

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
        return __('adlss.payment.status.' . strtolower($this->status));
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
	 * @return int
	 */
	public function getSubscriptionId() {
		return $this->subscriptionId;
	}

	/**
	 * @param int $subscriptionId
	 *
	 * @return $this
	 */
	public function setSubscriptionId( $subscriptionId ) {
		$this->subscriptionId = $subscriptionId;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param float $amount
	 *
	 * @return $this
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getItemId() {
		return $this->itemId;
	}

	/**
	 * @param int $itemId
	 *
	 * @return $this
	 */
	public function setItemId( $itemId ) {
		$this->itemId = $itemId;

		return $this;
	}




    /**
     * Methods
     */

    /**
     * @return $this
     */
    public function pay()
    {
        $this->setStatus(self::STATUS_PAID);

        return $this;
    }

    /**
     * @return $this
     */
    public function fail()
    {
        $this->setStatus(self::STATUS_FAILED);

        return $this;
    }

    /**
     * @return $this
     */
    public function open()
    {
        $this->setStatus(self::STATUS_OPENED);

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }
    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->status == self::STATUS_FAILED;
    }
    /**
     * @return bool
     */
    public function isOpened()
    {
        return $this->status == self::STATUS_OPENED;
    }
}