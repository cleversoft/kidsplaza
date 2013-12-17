<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Websites extends Mage_Core_Block_Template{
    protected function _construct(){
        parent::_construct();
        //$this->setData('cache_lifetime', 30*24*60*60);
    }

    public function getCacheKeyInfo(){
        return array(
            'KIDSPLAZA',
            'WEBSITES'
        );
    }

    public function getWebsites(){
        $out = array();

        $app = Mage::app();
        $currentStore = $app->getStore()->getId();
        foreach($app->getWebsites() as $website){
            $stores = $app->getWebsite($website->getId())->getStores();
            foreach ($stores as $store){
                $out[] = array(
                    'code'      => $store->getCode(),
                    //'url'     => $store->getUrl(),
                    'label'     => $this->escapeHtml($store->getName()),
                    'selected'  => (bool)($currentStore == $store->getId())
                );
            }
        }

        return $out;
    }
}