<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_System_Config_Solr extends Mage_Core_Model_Config_Data {
	/**
	 * Update search engine to Solr
	 */
	public function _afterSave() {
		if($this->isValueChanged()){
			if ($this->getValue()) {
				Mage::app()->getConfig()->saveConfig('catalog/search/engine', 'mtsearch/solr_engine');
			}else {
				if (Mage::getStoreConfig('catalog/search/engine') == 'mtsearch/solr_engine'){
					Mage::app()->getConfig()->saveConfig('catalog/search/engine', '');
				}
			}
		}
	}
}