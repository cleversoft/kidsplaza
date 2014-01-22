<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getConfig($path=null){
        if ($path) return Mage::getStoreConfig($path);
        else{
            $module = Mage::app()->getRequest()->getModuleName();
            $bar    = $this->getConfig('mtfilter/general/bar');
            return Mage::helper('core')->jsonEncode(
                array(
                    'loadMore'  => (bool)$this->getConfig("mtfilter/{$module}/load"),
                    'loadDOM'   => trim($this->getConfig("mtfilter/{$module}/load_container")),
                    'loadText'  => trim($this->getConfig("mtfilter/{$module}/load_text")),
                    'loadAuto'  => (bool)$this->getConfig("mtfilter/{$module}/load_auto"),
                    'mainDOM'   => trim($this->getConfig("mtfilter/{$module}/main_selector")),
                    'layerDOM'  => trim($this->getConfig("mtfilter/{$module}/layer_selector")),
                    'enable'    => (bool)$this->getConfig("mtfilter/{$module}/enable"),
                    'bar'       => (bool)$bar
                )
            );
        }
    }

    public function isPriceEnable(){
        $module = Mage::app()->getRequest()->getModuleName();
        return $this->getConfig("mtfilter/{$module}/price");
    }
}