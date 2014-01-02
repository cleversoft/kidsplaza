<?php
/**
 * @category    MT
 * @package     MT_Data
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Data_Model_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product{
    /**
     * Process category path from import
     */
    public function parse(){
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    /**
     * Save product & category
     * @param array $importData
     * @return bool
     */
    public function saveRow(array $importData){
        //process categories
        if (isset($importData['categories_path'])){
            $categoryDelimiter = $this->getBatchParams('category_delimiter');
            $categoryDelimiter = $categoryDelimiter ? $categoryDelimiter : ';';
            $categoryPath = $this->getBatchParams('category_path');
            $categoryPath = $categoryPath ? $categoryPath : '/';
            $storeId = (int)$this->getBatchParams('store');
            $paths = explode($categoryDelimiter, $importData['categories_path']);
            $category_ids = array();
            foreach ($paths as $path){
                $categories = explode($categoryPath, $path);
                $currentPath = [1];
                foreach ($categories as $category){
                    if (!$category) continue;

                    $collection = Mage::getModel('catalog/category')->getCollection()
                        ->addAttributeToFilter('name', array('eq' => $category))
                        ->addAttributeToFilter('parent_id', array('eq' => end($currentPath)));

                    if (!$collection->getSize()){
                        $categoryModel = Mage::getModel('catalog/category');
                        $categoryModel->setData(array(
                            'store_id'  => $storeId,
                            'name'      => $category,
                            'path'      => implode('/', $currentPath),
                            'is_active' => 1,
                            'is_anchor' => 1,
                            'custom_use_parent_settings' => 1
                        ));
                        try{
                            $categoryModel->save();
                            $currentPath[] = $categoryModel->getId();
                            unset($categoryModel);
                        }catch (Exception $e){}
                    }else{
                        $categoryModel = $collection->getFirstItem();
                        $currentPath[] = $categoryModel->getId();
                        unset($categoryModel);
                    }
                }
                $category_ids[] = end($currentPath);
            }
            $importData['category_ids'] = implode(',', $category_ids);
        }

        //process store
        $storeCode = $this->getBatchParams('store_code');
        if (!is_null($storeCode)){
            $importData['store'] = $storeCode;
            $websites = $this->getBatchParams('websites');
            if(!is_null($websites)) $importData['websites'] = $websites;
        }

        //process product
        $product = $this->getProductModel()->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();
        $images = explode(';', $importData['images']);
        $mediaAttributes = $product->getMediaAttributes();
        foreach ($images as $image) {
            foreach ($mediaAttributes as $mediaAttributeCode => $mediaAttribute) {
                if (isset($importData[$mediaAttributeCode]) && $importData[$mediaAttributeCode] == $image) {
                    $product->setData($mediaAttributeCode, $image);
                }
            }
            if (!empty($image) && !$mediaGalleryBackendModel->getImage($product, $image)) {
                $arrayToMassAdd[] = array('file' => trim($image), 'mediaAttribute' => null);
            }
        }

        $media = $this->getBatchParams('media');
        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd,
            $media ? $media : Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($mediaAttributes as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $addedFile = '';
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
            if (isset($importData[$mediaAttributeCode])
                && in_array($importData[$mediaAttributeCode], $addedFilesCorrespondence['alreadyAddedFiles'])){

                $keyInAddedFiles = array_search($importData[$mediaAttributeCode], $addedFilesCorrespondence['alreadyAddedFiles'], true);
                $product->setData($mediaAttributeCode, $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFiles]);
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }
}