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

$schema['central']['adlss.subscriptions'] = array(
	'items' => array(
		'adlss.subscriptions.manage' => array(
			'href' => 'adls_subscriptions.manage',
			'position' => 50
		),
		'adlss.plans.manage' => array(
			'href' => 'adls_plans.manage',
			'position' => 20
		)
	),
	'position' => 900,
);

return $schema;
