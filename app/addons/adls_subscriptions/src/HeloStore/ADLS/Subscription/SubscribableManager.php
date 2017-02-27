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

class SubscribableManager extends Manager
{

    /**
     * @var SubscribableRepository
     */
    protected $repository;


    /**
     * SubscribableManager constructor.
     */
    public function __construct()
    {
        $this->setRepository(SubscribableRepository::instance());
    }
    /**
     * Hooks
     */

    /**
     * @param $optionId
     * @param $productId
     * @param $fields
     * @param $condition
     * @param $join
     * @param $extraVariantFields
     * @param $langCode
     *
     * @return bool
     */

    public function onGetProductOptionDataPre($optionId, $productId, &$fields, $condition, &$join, $extraVariantFields, $langCode)
    {
        $join .= db_quote(' 
                LEFT JOIN ?p AS subscribable 
                    ON subscribable.objectType = ?s 
                    AND subscribable.objectId = a.option_id
            ',
            $this->getRepository()->getTable(),
            Subscribable::OBJECT_TYPE_PRODUCT_OPTION
        );

        $fields .= ', subscribable.id AS subscribableId';
        $fields .= ', subscribable.planId AS planId';

        return true;
    }

    /**
     * @param $option
     * @param $productId
     * @param $langCode
     *
     * @return bool
     */
    public function onGetProductOptionDataPost(&$option, $productId, $langCode)
    {
        if (empty($option)) {
            return false;
        }
        if (!$this->isSubscribable($option)) {
            return false;
        }
//        $subscribableId = $option['subscribableId'];

        if (!empty($option['variants'])) {
            foreach ($option['variants'] as $k => $variant) {
//                aa($variant);
            }
        }

        return true;
    }



    /**
     * Methods
     */


    /**
     * Checks if an object is subscribable (ie. it may have plans attached to it or its children)
     *
     * @param $object
     *
     * @return bool
     */
    public function isSubscribable($object)
    {
        return (!empty($object) && !empty($object['subscribableId']));
    }

    /**
     * @param $subscribableId
     * @param $planId
     * @return bool|null
     */
    public function updateProductOption($subscribableId, $planId)
    {
        $subscribable = $this->repository->findOneById($subscribableId);

        if (empty($subscribable)) {
            return null;
        }

        if ($subscribable->getPlanId() == $planId) {
            return null;
        }
        $subscribable->setPlanId($planId);

        return $this->repository->update($subscribable);
    }
    /**
     * @param $optionId
     * @param $planId
     * @return bool|int
     */
    public function linkProductOption($optionId, $planId)
    {
        $objectId = $optionId;
        $objectType = Subscribable::OBJECT_TYPE_PRODUCT_OPTION;

        return $this->repository->create($planId, $objectId, $objectType);
    }

    /**
     * Remove link between a product option and a plan (ie. make object no longer subscribable)
     *
     * @param $optionId
     * @return bool|int
     */
    public function unlinkProductOption($optionId)
    {
        $objectId = $optionId;
        $objectType = Subscribable::OBJECT_TYPE_PRODUCT_OPTION;
        
        return $this->repository->delete($objectId, $objectType);
    }
}