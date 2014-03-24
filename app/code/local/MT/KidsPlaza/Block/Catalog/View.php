<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Catalog_View extends Mage_Core_Block_Template{
    protected $_category;

    public function _construct(){
        parent::_construct();
        $this->setData('cache_lifetime', 86400*30);
        $this->setTemplate('kidsplaza/catalog/view.phtml');
    }

    public function getCacheTags(){
        $tags = parent::getCacheTags();
        $tags = array_merge($tags, array('KIDSPLAZA_CATEGORY_'.$this->getCategory()->getId(), Mage_Catalog_Model_Category::CACHE_TAG));
        return array_unique($tags);
    }

    public function getCacheKeyInfo(){
        return array(
            'KIDSPLAZA',
            'CATALOG_VIEW',
            $this->getCategory()->getId(),
            Mage::app()->getStore()->getId()
        );
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return $this
     */
    public function setCategory($category){
        $this->_category = $category;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory(){
        return $this->_category;
    }
}