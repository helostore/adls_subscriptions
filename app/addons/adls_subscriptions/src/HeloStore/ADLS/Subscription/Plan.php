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


class Plan extends Entity
{
	const STATUS_ACTIVE = 'A';
	const STATUS_DISABLED = 'D';
	const STATUS_INACTIVE = 'I';

	const CYCLE_YEARLY = '12';
	const CYCLE_MONTHLY = '1';
	const CYCLE_PERPETUAL = '-1';

    /**
     * @var array
     */
    protected $_fieldsMap = array(
        'planId' => 'id'
    );

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $cycle;

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
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCycle()
	{
		return $this->cycle;
	}

	/**
	 * @param string $cycle
	 *
	 * @return $this
	 */
	public function setCycle($cycle)
	{
		$this->cycle = $cycle;

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
	 * Helpers
	 */

	public function __toString()
	{
		return $this->getName();
	}
}