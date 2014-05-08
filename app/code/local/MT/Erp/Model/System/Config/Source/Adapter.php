<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Model_System_Config_Source_Adapter{
    public function toOptionArray(){
        return array(
            //array('value' => 'db', 'label' => Mage::helper('mterp')->__('Microsoft SQL Server Direct')),
            array('value' => 'api', 'label' => Mage::helper('mterp')->__('API Wrapper'))
        );
    }
}