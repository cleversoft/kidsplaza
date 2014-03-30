<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
include_once 'Mage/CatalogSearch/controllers/AjaxController.php';
class  MT_Search_Catalogsearch_AjaxController extends Mage_CatalogSearch_AjaxController {
	/**
	 * Suggestions ajax request handler
	 */
	public function suggestAction() {
		if (!$this->getRequest()->getParam('q', false)) {
			$this->getResponse()->setRedirect(Mage::getSingleton('core/url')->getBaseUrl());
		}

		if (!Mage::getStoreConfigFlag('mtsearch/utils/autocomplete') || !Mage::helper('mtsearch')->usingSolrFrontend()){
			$this->getResponse()->setBody($this->getLayout()->createBlock('catalogsearch/autocomplete')->toHtml());
		}else{
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('mtsearch/autocomplete')
                    ->setData('query', $this->getRequest()->getParam('q', ''))
                    ->setData('fq', array('category_ids' => $this->getRequest()->getParam('category_ids')))
                    ->toHtml()
			);
		}
	}
}
