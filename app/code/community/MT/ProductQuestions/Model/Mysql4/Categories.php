<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
Class MT_ProductQuestions_Model_Mysql4_Categories extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('productquestions/categories', 'cat_id');
    }

    /**
     * Returns a common url in the form of /module/controller/action
     *
     * @return string
     */
    public function getUrl()
    {
        return Mage::getUrl(
            'productquestions/questions/index/category',
            array(
                'id' => $this->getCatId(),
            )
        );
    }

    /**
     * Returns a SEO url with fallback to $this->getUrl().
     *
     * @return string
     */
    public function getUrlRewrite()
    {
        $id_path = "cat/{$this->getCatId()}";
        /* @var $mainUrlRewrite Mage_Core_Model_Url_Rewrite */
        $mainUrlRewrite = Mage::getModel('core/url_rewrite')
            ->loadByIdPath($id_path);
        // If found return our URL rewrite
        if ($mainUrlRewrite->getId())
        {
            return Mage::getUrl($mainUrlRewrite->getRequestPath());
        }
        else
        {
            return $this->getUrl();
        }
    }

    public function setStores($storeIds)
    {
        $stores = Mage::app()->getStores();
        $setStoreIds = array();
        foreach($storeIds as $storeId){
            if($storeId==0){
                foreach ($stores as $_eachStoreId => $val)
                {
                    $setStoreIds[] = Mage::app()->getStore($_eachStoreId)->getId();
                }
            }else{
                $setStoreIds[] = $storeId;
            }
        }
        return $setStoreIds;
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);
        $urlCatParam = MT_ProductQuestions_Helper_Data::CATEGORY_URI_PARAM;
        $id_path = "{$urlCatParam}/{$object->getCatId()}";
        $Collections = Mage::getModel('core/url_rewrite')->getCollection()
            ->addFilter('id_path', $id_path);
        foreach($Collections as $Collection){
            $mainUrlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($Collection->getIdPath());
            $mainUrlRewrite->delete();
        }
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $identifier = $object->getIdentifier();
        $storeIds = explode(',', $object->getStores());
        $getStoreIds = $this->setStores($storeIds);
        $urlCatParam = MT_ProductQuestions_Helper_Data::CATEGORY_URI_PARAM;
        $urlQuestionsParam = MT_ProductQuestions_Helper_Data::QUESTIONS_URI_PARAM;
        $id_path = "{$urlCatParam}/{$object->getCatId()}";
        $request_path = "{$urlQuestionsParam}/{$urlCatParam}/{$object->getIdentifier()}.html";
        $target_path = "productquestions/questions/index/category/{$object->getCatId()}";
        if (!empty($identifier))
        {
            foreach($getStoreIds as $storeId){
                $rewrite = Mage::getModel('core/url_rewrite');
                $rewrite->setStoreId($storeId)
                    ->setIdPath($id_path)
                    ->setRequestPath($request_path)
                    ->setTargetPath($target_path)
                    ->setIsSystem(false)
                    ->save();
            }
        }
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $urlCatParam = MT_ProductQuestions_Helper_Data::CATEGORY_URI_PARAM;
        $id_path = "{$urlCatParam}/{$object->getCatId()}";
        $Collections = Mage::getModel('core/url_rewrite')->getCollection()
            ->addFilter('id_path', $id_path);
        foreach($Collections as $Collection){
            $mainUrlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($Collection->getIdPath());
            $mainUrlRewrite->delete();
        }
        return parent::_beforeDelete($object);
    }

}