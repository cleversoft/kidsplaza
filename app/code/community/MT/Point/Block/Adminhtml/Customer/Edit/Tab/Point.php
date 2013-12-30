<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Customer_Edit_Tab_Point
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface{

    protected function _construct(){
        parent::_construct();
        $this->setTemplate('mt/point/customer/edit/tab/point.phtml');
    }

    public function getTabLabel(){
        return Mage::helper('mtpoint')->__('MT Points');
    }

    public function getTabTitle(){
        return Mage::helper('mtpoint')->__('MT Points');
    }

    public function canShowTab(){
        return true;
    }

    public function isHidden(){
        return false;
    }

    protected function _prepareLayout(){
        $this->setChild('mtpoint_balance', $this->getLayout()->createBlock('mtpoint/adminhtml_customer_edit_tab_point_balance'));
        $this->setChild('mtpoint_manage', $this->getLayout()->createBlock('mtpoint/adminhtml_customer_edit_tab_point_manage'));
        $this->setChild('mtpoint_history', $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->addItem('mtpoint_history', array(
                'title'     => Mage::helper('mtpoint')->__('Points History'),
                'open'      => false,
                'ajax'      => true,
                'content_url' => $this->getUrl('mtpointadmin/customer/history', array('_current' => true))
            ))
        );
        return parent::_prepareLayout();
    }
}