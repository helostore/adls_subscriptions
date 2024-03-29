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

namespace HeloStore\ADLSS\Base;

use HeloStore\ADLSS\Singleton;

class Manager extends Singleton {

    /**
     * @var EntityRepository
     */
	protected $repository;

	/**
	 * @return EntityRepository
	 */
	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * @param EntityRepository $repository
	 *
	 * @return $this
	 */
	public function setRepository($repository)
	{
		$this->repository = $repository;

		return $this;
	}
}