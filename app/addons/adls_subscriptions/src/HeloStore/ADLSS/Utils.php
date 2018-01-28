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

use DateTime;
use Tygh\Registry;

class Utils extends Singleton
{

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
    public function overridePresentDate(DateTime $date)
    {
        global $_timeTravelDate;
        $_timeTravelDate = $date;

        return $this;
    }

    /**
     * @param DateTime $x
     * @param DateTime|null $y
     * @param int $precision
     * @return string
     */
    public function getDuration(\DateTime $x, \DateTime $y = null, $precision = 2)
    {
        if ($y === null) {
            $y = $this->getCurrentDate();
        }
        $interval = $y->diff($x);
        if (empty($interval)) {
            return 'error';
        }
        $parts = array();
        if ($interval->y > 0) {
            $parts[] = sprintf('%s year(s)', $interval->y);
        }
        if ($interval->m > 0) {
            $parts[] = sprintf('%s month(s)', $interval->m);
        }
        if ($interval->d > 0) {
            $parts[] = sprintf('%s day(s)', $interval->d);
        }
        if ($interval->h > 0) {
            $parts[] = sprintf('%s hour(s)', $interval->h);
        }
        if ($interval->i > 0) {
            $parts[] = sprintf('%s minute(s)', $interval->i);
        }

        if (empty($parts)) {
            return 'now';
        }

        $parts = array_slice($parts, 0, $precision);

        return implode(' ', $parts);
    }
    /**
     * @return DateTime
     */
    public function getCurrentDate()
    {
        global $_timeTravelDate;

        return (empty($_timeTravelDate)) ? new \DateTime() : clone $_timeTravelDate;
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

    public function createDateFromTimestamp($timestamp)
    {
        return \DateTime::createFromFormat('U', $timestamp);
    }
    
    public function getSettings()
    {
        return Registry::get('addons.adls_subscriptions');
    }

    /**
     * @param $variant
     *
     * @param $productPrice
     *
     * @return float|int|null
     */
	public function getVariantModifierValue($variant, $productPrice) {
		$amount = null;
		if ($variant['modifier_type'] == 'A') {
			// Absolute
			if ($variant['modifier']{0} == '-') {
				$amount = -1 * floatval(substr($variant['modifier'],1));
			} else {
				$amount = floatval($variant['modifier']);
			}
		} else {
            // Percentage
            if ($variant['modifier']{0} == '-') {
                $amount = -1 * ((floatval(substr($variant['modifier'],1)) * $productPrice)/100);
            } else {
                $amount = ((floatval($variant['modifier']) * $productPrice)/100);
            }
//			throw new \Exception("Percentage modifiers not supported, variant #" . $variant['variant_id']);
		}

		return $amount;
	}
}