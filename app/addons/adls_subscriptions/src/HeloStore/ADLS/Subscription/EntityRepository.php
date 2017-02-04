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


abstract class EntityRepository extends Singleton {

	protected $table = null;

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

//	abstract public function create($a1, $a2=null, $c3=null, $a4=null, $a5=null, $a6=null, $a7=null, $a8=null, $a9=null);
} 