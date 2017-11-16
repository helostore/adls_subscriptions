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

namespace HeloStore\ADLSS;


use HeloStore\ADLSS\Base\Entity;

class Subscribable extends Entity
{
	const OBJECT_TYPE_PRODUCT_OPTION = 'product_option';

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
	protected $planId;

	/**
	 * @var string
	 */
	protected $objectType;

	/**
	 * @var integer
	 */
	protected $objectId;

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
	 * @return string
	 */
	public function getObjectType()
	{
		return $this->objectType;
	}

	/**
	 * @param string $objectType
	 *
	 * @return $this
	 */
	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @param int $objectId
	 *
	 * @return $this
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;

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
}