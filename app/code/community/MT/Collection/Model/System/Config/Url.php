<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_Model_System_Config_Url extends Mage_Core_Model_Config_Data {
	/**
	 * Update url rewrite record
	 */
	public function _afterSave(){
		if ($this->isValueChanged()){
            $data = $this->getData();

            switch ($data['field']){
                case 'lastest':
                    $target = 'collection/view/lastest';
                    break;
                case 'bestsell':
                    $target = 'collection/view/bestseller';
                    break;
                case 'mostviewed':
                    $target = 'collection/view/mostviewed';
                    break;
                case 'promotion':
                    $target = 'collection/view/promotion';
                    break;
                case 'daily':
                    $target = 'collection/view/daily';
                    break;
                default:
                    $target = '';
            }

            $stores = array();
            switch ($data['scope']){
                case 'stores':
                    $stores[] = $data['scope_id'];
                    break;
                case 'websites':
                    $websiteId = $data['scope_id'];
                    foreach (Mage::app()->getWebsite($websiteId)->getStores() as $store){
                        $stores[] = $store->getId();
                    }
                    break;
                case 'default':
                    foreach (Mage::app()->getWebsites() as $website){
                        foreach ($website->getStores() as $store){
                            $stores[] = $store->getId();
                        }
                    }
            }

            foreach ($stores as $storeId){
                /* @var $rewrite Mage_Core_Model_Url_Rewrite */
                $rewrite = Mage::getModel('core/url_rewrite');
                $rewrite->setStoreId($storeId);
                $rewrite->loadByIdPath($this->getPath());

                if ($rewrite->getId()){
                        $rewrite->setRequestPath($this->getValue())->save();
                }else{
                    $rewrite->setIdPath($this->getPath())
                        ->setRequestPath($this->getValue())
                        ->setTargetPath($target)
                        ->setIsSystem(0)
                        ->save();
                }

                unset($rewrite);
            }
		}
	}
}