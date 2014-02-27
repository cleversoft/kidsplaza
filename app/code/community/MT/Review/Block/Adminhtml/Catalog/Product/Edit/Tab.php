<?php
 /**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_PhpStorm
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_Review_Block_Adminhtml_Catalog_Product_Edit_Tab
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return $this->__('Reviews');
    }

    public function getTabTitle()
    {
        return $this->__('MT Product Review');
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/mtreview', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

}