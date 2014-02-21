<?php
 /**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'CustomerController.php');
class MT_Review_Adminhtml_CustomerController
    extends Mage_Adminhtml_CustomerController
{
    /**
     * Get custom products grid and serializer block
     */
    public function mtreviewAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.reviews')
            ->setCustomerId(Mage::registry('current_customer')->getId())
            ->setUseAjax(true);
        $this->renderLayout();
    }
}