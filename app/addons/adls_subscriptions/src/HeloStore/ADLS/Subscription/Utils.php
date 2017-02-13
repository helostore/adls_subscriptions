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

class Utils extends Singleton
{

    protected $overrodePresentDate = null;


    public function snakeToCamel($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    public function camelToSnake($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function overridePresentDate(\DateTime $date)
    {
        $this->overrodePresentDate = $date;

        return $this;
    }
    /**
     * @return DateTime
     */
    public function getCurrentDate()
    {
        return (empty($this->overrodePresentDate)) ? new \DateTime() : $this->overrodePresentDate;
    }

    /**
     * @param DateTime $date
     *
     * @return DateTime
     */
    public function discardSeconds(DateTime $date)
    {
        return $date->setTime($date->format('H'), $date->format('i'), 0);
    }

}