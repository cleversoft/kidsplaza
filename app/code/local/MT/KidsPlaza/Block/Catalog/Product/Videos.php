<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Catalog_Product_Videos extends Mage_Core_Block_Template{
    protected $_videos;

    protected function _construct(){
        parent::_construct();
        $this->setData('cache_lifetime', 86400*30);
        $this->setData('cache_tags', array(Mage_Catalog_Model_Product::CACHE_TAG));
        $this->setTemplate('kidsplaza/catalog/product/videos.phtml');
    }

    public function getCacheKeyInfo(){
        return array(
            'KIDSPLAZA',
            Mage::app()->getStore()->getId(),
            Mage::registry('current_product') ? Mage::registry('current_product')->getId() : ''
        );
    }

    public function getVideos(){
        if (!$this->_videos){
            $product = Mage::registry('product');
            if ($product){
                $videos = Mage::helper('core')->jsonDecode($product->getData('video'));
                $tmp = array();
                if (is_array($videos)){
                    foreach ($videos as $url){
                        if ($url){
                            $tmp[] = $this->_getYoutubeVideoId($url);
                        }
                    }
                }
                $this->_videos = $tmp;
            }else{
                $this->_videos = array();
            }
        }
        return $this->_videos;
    }

    protected function _getYoutubeVideoId($url){
        if (!$url) return;
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        return isset($params['v']) ? $params['v'] : '';
    }
}