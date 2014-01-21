<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Observer {
	/**
	 * Alter attribute edit form
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function adminhtmlCatalogProductAttributeEditPrepareForm($observer) {
		$form = $observer->getForm();
		$attribute = $observer->getAttribute();

		if (!$attribute->getId() || $attribute->getIsSearchable()){
			$fieldset = $form->getElement('front_fieldset');
			$fieldset->addField('search_weight', 'text', array(
				'name'	=> 'search_weight',
				'label'	=> Mage::helper('mtsearch')->__('Search Weight Number'),
				'title'	=> Mage::helper('mtsearch')->__('Enter search weight number here'),
				'note'	=> Mage::helper('mtsearch')->__('Only used when enable <b>MT Solr Search</b>, <a target="_blank" href="http://wiki.apache.org/solr/SolrRelevancyFAQ#How_can_I_make_.22superman.22_in_the_title_field_score_higher_than_in_the_subject_field">more info</a>')
			), 'is_searchable');
		}
	}
}