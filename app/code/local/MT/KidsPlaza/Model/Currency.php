<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Currency extends Mage_Directory_Model_Currency{
    /**
     * Format price to currency format
     *
     * @param float $price
     * @param array $options
     * @param bool $includeContainer
     * @param bool $addBrackets
     * @return string
     */
    public function format($price, $options = array(), $includeContainer = true, $addBrackets = false)
    {
        if ($this->getCurrencyCode() == 'VND'){
            return $this->formatPrecision($price, 0, $options, $includeContainer, $addBrackets);
        }else{
            return $this->formatPrecision($price, 2, $options, $includeContainer, $addBrackets);
        }
    }
}