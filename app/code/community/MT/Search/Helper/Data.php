<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Helper_Data extends Mage_CatalogSearch_Helper_Data {
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
		return $this->__('Did you mean') .': '. implode(', ', $links);
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
		$filters = array(
            'store_id' => Mage::app()->getStore()->getStoreId()
        );
        if (!Mage::getStoreConfigFlag('cataloginventory/options/show_out_of_stock')){
            $filters['in_stock'] = 1;
        }
		$order = 'score';
		$dir = 'desc';
        $q = '';
		foreach (Mage::app()->getRequest()->getParams() as $key => $value){
			switch ($key){
				case 'cat':
					$filters['category_ids'] = $value;
					break;
				case 'q':
					$q = $value;
					break;
                case 'isAjax':
                case 'toolbar':
                case '___SID':
				case 'limit':
				case 'p':
				case 'mode':
                    break;
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
                case 'category_ids':
                    $filters['category_ids'] = $value;
                    break;
                case 'discount':
                    $priceName = $this->getCurrentPriceName('b');
                    $filters[$priceName] = true;
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
     * @param string $suffix
	 * @return string
	 */
	public function getCurrentPriceName($suffix='f') {
		$websiteId = Mage::app()->getStore()->getWebsiteId();
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$customerGroupId = $customer->getId() ? $customer->getGroupId() : 0;

		return sprintf('price_%d_%d_%s', $websiteId, $customerGroupId, $suffix);
	}

    /**
     * Retrieve search query text
     *
     * @return string
     */
    public function getQueryText() {
        if (!isset($this->_queryText)) {
            $this->_queryText = $this->_getRequest()->getParam($this->getQueryParamName());
            if ($this->_queryText === null) {
                $this->_queryText = '';
            } else {
                /* @var $stringHelper Mage_Core_Helper_String */
                $stringHelper = Mage::helper('core/string');
                $this->_queryText = is_array($this->_queryText) ? '' : trim($this->_queryText);
                $maxQueryLength = $this->getMaxQueryLength();
                if ($maxQueryLength !== '' && $stringHelper->strlen($this->_queryText) > $maxQueryLength) {
                    $this->_queryText = $stringHelper->substr($this->_queryText, 0, $maxQueryLength);
                    $this->_isMaxLength = true;
                }
            }
        }
        return $this->_queryText;
    }

    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    public function getQuery() {
        if (!$this->_query) {
            $this->_query = Mage::getModel('catalogsearch/query')->loadByQuery($this->getQueryText());
            $this->_query->setQueryText($this->getQueryText());
        }
        return $this->_query;
    }
}