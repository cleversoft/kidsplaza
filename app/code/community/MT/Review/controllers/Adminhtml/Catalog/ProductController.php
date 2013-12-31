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
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php');
class MT_Review_Adminhtml_Catalog_ProductController
    extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Get custom products grid and serializer block
     */
    public function mtreviewAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.mtreview')
            ->setProductId(Mage::registry('product')->getId())
            ->setUseAjax(true);
        $this->renderLayout();
    }
}