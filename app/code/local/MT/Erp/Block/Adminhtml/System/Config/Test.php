<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Block_Adminhtml_System_Config_Test extends Mage_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * Prepare button blocks and template
	 */
	protected function _prepareLayout() {
		$this->setTemplate('mt/erp/system/config/test.phtml');
		
		$this->setChild('testConnectionBtn', $this->getLayout()->createBlock('adminhtml/widget_button')
			->setId('testConnectionBtn')
			->setType('button')
			->setClass('testBtn')
			->setLabel(Mage::helper('mtsearch')->__('Test MSSQL Connection'))
			->setOnClick("testConnection()"));

		return $this;
	}

	/**
	 * Render html from layout
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		return $this->toHtml();
	}
}