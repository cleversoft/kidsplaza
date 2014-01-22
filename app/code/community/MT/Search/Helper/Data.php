<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Format duggestions html
	 *
	 * @param array $words
	 * @return string
	 */
	public function getSuggestionsHtml($words) {
		$links = array();
		foreach ($words as $word) {
			$url = Mage::getUrl('catalogsearch/result', array('q'=>$word));
			$links[] = sprintf('<a href="%s" title="%s">%s</a>', $url, $word, $word);
		}
		return $this->__('Did you mean: ') . implode(', ', $links);
	}

	/**
	 * Check if use Solr for frontend
	 *
	 * @return boolean
	 */
	public function usingSolrFrontend() {
		$flag = Mage::getStoreConfigFlag('mtsearch/enable/solr');
		$module = Mage::app()->getRequest()->getModuleName();

		if ($flag && $module == 'catalogsearch') return true;
		return false;
	}

	/**
	 * Get current filter params
	 *
	 * @return array
	 */
	public function getCurrentFilters() {
		$filters = array();
		$order = 'score';
		$dir = 'desc';
		foreach (Mage::app()->getRequest()->getParams() as $key => $value){
			switch ($key){
				case 'cat':
					$filters['category_ids'] = $value;
					break;
				case 'q':
					$q = $value;
					break;
				case 'limit': break;
				case 'p': break;
				case 'mode': break;
				case 'order':
					if ($value == 'name') $order = $value;
					elseif ($value == 'price') $order = $this->getCurrentPriceName();
					break;
				case 'dir':
					$dir = $value;
					break;
				case 'price':
					$priceName = $this->getCurrentPriceName();
					list($from, $to) = explode('-', $value);
					$filters[$priceName] = sprintf('[%s TO %s]', $from ? $from : 0, $to ? $to : '*');
					break;
				default:
					$filters['attr_' . $key . '_value'] = $value;
					break;
			}
		}
		return array($q, $filters, $order . ' ' . $dir);
	}

	/**
	 * Get current price attribute name
	 *
	 * @return string
	 */
	public function getCurrentPriceName() {
		$websiteId = Mage::app()->getStore()->getWebsiteId();
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$customerGroupId = $customer->getId() ? $customer->getGroupId() : 0;

		return sprintf('price_%d_%d_f', $websiteId, $customerGroupId);
	}
}