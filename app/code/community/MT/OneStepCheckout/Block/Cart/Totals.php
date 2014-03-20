<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_OneStepCheckout_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals{
    /**
     * Render totals html for specific totals area (footer, body)
     *
     * @param   null|string $area
     * @param   int $colspan
     * @return  string
     */
    public function renderTotals($area = null, $colspan = 1){
        $html = '';
        foreach($this->getTotals() as $total) {
            if ($total->getCode() == 'shipping' || ($total->getArea() != $area && $area != -1)){
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }
}