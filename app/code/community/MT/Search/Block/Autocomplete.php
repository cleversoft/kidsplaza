<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Block_Autocomplete extends Mage_Core_Block_Abstract {
	/**
	 * Compile html on-the-fly for quick response
	 */
	protected function _toHtml() {
		$query = $this->getData('query');
		if ($query){
			$store = Mage::app()->getStore()->getId();
			$service = Mage::getModel('mtsearch/service');
			$limit = Mage::getStoreConfig('mtsearch/utils/limit');
			$html = '';
			$templateProduct = Mage::getStoreConfig('mtsearch/utils/tpl');
			preg_match_all('/{{\w+}}/', $templateProduct, $vars);
			$vars = isset($vars[0]) ? $vars[0] : array();
			//$vars += array('{{url_key}}');
			$attrs = array('id');
			foreach ($vars as $var){
				$attrs[] = 'attr_' . str_replace(array('{', '}'), array('', ''), $var);
			}
            $fq = array('store_id' => $store);
            if ($filters = $this->getData('fq')){
                if (is_array($filters)){
                    foreach ($filters as $k => $v){
                        if ($v) $fq[$k] = $v;
                    }
                }
            }

			try{
				$service->query($query, $fq, $attrs, $limit ? $limit : 10);
				
				$templateSuggest = '<li title="{{word}}">'.$this->__('Did you mean').' <b>{{word}}</b></li>';

				foreach ($service->getSuggestions() as $word){
					$html .= str_replace(
						array('{{word}}', '{{url}}'),
						array($word, $this->getUrl('catalogsearch/result', array('q' => $word))),
						$templateSuggest
					);
				}

				foreach ($service->getDocs() as $doc){
					$values = array();
					foreach ($vars as $var){
						$var = str_replace(array('{', '}'), array('', ''), $var);
						if ($var == 'url_key'){
							$f = $doc->getField('attr_' . $var);
							$values[] = $this->getUrl(isset($f['value']) ? $f['value'].'.html' : '');
						}else{
							$f = $doc->getField('attr_' . $var);
							$values[] = $f ? (isset($f['value']) ? $f['value'] : '') : '';
						}
					}
					$html .= str_replace($vars, $values, $templateProduct);
				}
			}catch(Exception $e){}

			return '<ul>' . $html . '</li>';
		}
	}
}