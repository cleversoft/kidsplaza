<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Model_System_Config_Source_Group{
    protected $_options;

    public function toOptionArray(){
        if (is_null($this->_options)) {
            $this->_options = Mage::getResourceModel('customer/group_collection')->toOptionArray();
        }

        return $this->_options;
    }
}