<?php
/**
 * @category    MT
 * @package     MT_Data
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Data_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product{
    protected $_categories = array();

    /**
     * Process category path for export
     */
    public function unparse(){
        $entityIds = $this->getData();
        $sourcePath = Mage::getBaseDir('media').DS.'catalog'.DS.'product';
        $destPath = Mage::getBaseDir('var').DS.'export'.DS.'images';

        foreach ($entityIds as $i => $entityId) {
            //if ($i++ > 1) break;
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'type'          => $product->getTypeId(),
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                    $product->getAttributeSetId())
            );

            $delimiter = $this->getVar('delimiter', ';');
            $pathSeparator = $this->getVar('path', '/');
            $categoryPaths = array();
            foreach ($product->getCategoryIds() as $categoryId){
                $categoryModel = Mage::getModel('catalog/category')->load($categoryId, array('name'));
                /* @var $categoryModel Mage_Catalog_Model_Category */
                if (!isset($this->_categories[$categoryId])){
                    $this->_categories[$categoryId] = $categoryModel->getName();
                }

                $path = explode('/', $categoryModel->getPath());
                //array_shift($path);
                $categories = array();
                foreach ($path as $category){
                    if (!isset($this->_categories[$category])){
                        $parentCategoryModel = Mage::getModel('catalog/category')->load($category, array('name'));
                        /* @var $parentCategoryModel Mage_Catalog_Model_Category */
                        $this->_categories[$category] = $parentCategoryModel->getName();
                        unset($parentCategoryModel);
                    }
                    $categories[] = $this->_categories[$category];
                }
                $categoryPaths[] = implode($pathSeparator, $categories);
                unset($categoryModel);
            }
            $row['categories_path'] = implode($delimiter, $categoryPaths);

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }

            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }

            $images = array();
            $gallery = $product->getData('media_gallery');
            foreach ($gallery['images'] as $image){
                if ($this->_copyImage($image['file'], $sourcePath, $destPath)){
                    $images[] = $image['file'];
                }
            }
            $row['images'] = implode(';', $images);

            $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();

            $product->reset();
        }

        return $this;
    }

    protected function _copyImage($image, $sourcePath, $destPath){
        $filePath = $sourcePath.$image;
        $dest = $destPath.$image;
        $dir = dirname($dest);
        if (!is_dir($dir)){
            @mkdir($dir, 0777, true);
        }
        if (file_exists($filePath)){
            return @copy($filePath, $dest);
        }
    }
}