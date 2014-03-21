<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Edit_Wordpress
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function canShowTab(){
        return true;
    }

    public function getTabLabel(){
        return $this->__('Wordpress');
    }

    public function getTabTitle(){
        return $this->__('Wordpress');
    }

    public function isHidden(){
        return false;
    }

    public function getTabUrl(){
        return $this->getUrl('kidsplaza_admin/adminhtml_catalog_product/wpTab', array('_current' => true));
    }

    public function getTabClass(){
        return 'ajax';
    }
}