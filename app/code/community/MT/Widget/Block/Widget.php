<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Widget_Block_Widget extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface {
    protected $_categories;
    protected $_productCollection;
    protected $_customerGroupId;

    protected function _construct(){
        parent::_construct();
        $this->setData('cache_tags', array('MT_WIDGET'));
        $session = Mage::getSingleton('customer/session');
        $this->_customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;
    }

    protected function _prepareLayout(){
        $js = $this->getLayout()->getBlock('js_bottom');
        $js->addJs('mt/extensions/jquery/jquery-1.10.2.min.js');
        $js->addJs('mt/extensions/jquery/plugins/flexslider/jquery.flexslider.js');
        $js->addJs('mt/widget/frontend.js');
        $css = $this->getLayout()->getBlock('head');
        $css->addItem('js_css', 'mt/extensions/jquery/plugins/flexslider/flexslider.css');
        return parent::_prepareLayout();
    }

    public function getCacheLifetime(){
        return $this->getData('cache') ? (int)$this->getData('cache') : null;
    }

    public function getCacheKeyInfo(){
        return array(
            'MT_WIDGET',
            Mage::app()->getStore()->getId(),
            $this->getData('widget_type'),
            $this->getData('category_ids'),
            $this->getData('product_ids'),
            $this->getData('attribute'),
            $this->getData('attribute_mode'),
            $this->getData('block_ids'),
            $this->getData('mode'),
            $this->getData('scroll'),
            $this->getData('column'),
            $this->getData('namespace'),
            $this->getData('speed'),
            $this->_customerGroupId
        );
    }

    protected function _beforeToHtml(){
        if ($this->getTemplate() == 'mt/widget/default.phtml'){
            switch ($this->getData('widget_type')){
                case 'product':
                    switch ($this->getData('mode')){
                        case 'related':
                            $this->setTemplate('mt/widget/related.phtml');
                            break;
                        default:
                            $this->setTemplate('mt/widget/product.phtml');
                            break;
                    }
                    break;
                case 'attribute':
                    $this->setTemplate('mt/widget/attribute.phtml');
                    break;
                case 'block':
                    $this->setTemplate('mt/widget/block.phtml');
                    break;
            }
        }
        return parent::_beforeToHtml();
    }

    public function getBlocks(){
        $blocks = array();
        $layout = $this->getLayout();
        $storeId = Mage::app()->getStore()->getId();

        $classes = array();
        $order = array();
        foreach(array('lg', 'md', 'sm', 'xs') as $l){
            foreach (explode('|', $this->getData('block_' . $l)) as $block){
                list($blockId, $column, $cls) = explode(',', $block);

                if (!isset($classes[$blockId])){
                    $classes[$blockId] = "col-{$l}-{$column} ";
                }else{
                    $classes[$blockId] .= "col-{$l}-{$column} ";
                }
                $classes[$blockId] .= "{$cls} ";

                if (!in_array($blockId, $order)) $order[] = $blockId;
            }
        }

        foreach ($order as $blockId){
            $collection = Mage::getModel('cms/block')
                ->getCollection()
                ->addFieldToFilter('identifier', array('eq' => $blockId));

            if ($collection->count()){
                $model = $collection->getFirstItem();
                $model->load($model->getId());
                $storeIds = $model->getStoreId();
                if ($model->getIsActive() && (in_array($storeId, $storeIds) || in_array(0, $storeIds))){
                    $blocks[] = array(
                        'class' => isset($classes[$blockId]) ? $classes[$blockId] : '',
                        'content' => $layout->createBlock('cms/block')->setStoreId()->setBlockId($blockId)->toHtml()
                    );
                }
            }
        }
        return $blocks;
    }

    public function getAttibuteOptions(){
        $showOptions = explode(',', $this->getData('attribute_options'));
        list($attributeId, $attributeCode) = explode(',' ,$this->getData('attribute'));
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attributeId)
            ->setStoreFilter()
            ->load();
        $options = array();
        foreach ($optionCollection as $option){
            if ($option->getImage() && in_array($option->getId(), $showOptions)){
                $options[] = array(
                    'id' => $option->getId(),
                    'label' => $option->getValue(),
                    'image' => $option->getImage(),
                    'link' => $this->getUrl('catalogsearch/result/index', array('q' => $option->getValue()))
                );
            }
        }
        if ($this->getData('attribute_mode') == 1 && $attributeCode){
            $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter()
                ->addAttributeToSelect($attributeCode)
                ->addAttributeToFilter($attributeCode, array('neq' => ''))
                ->addAttributeToFilter($attributeCode, array('notnull' => true));
            $optionsInUse = array_unique($productCollection->getColumnValues($attributeCode));
            foreach ($options as $i=>$option){
                if (in_array($option['id'], $optionsInUse)){
                    unset($options[$i]);
                }
            }
        }
        return $options;
    }

    public function getConfig($name){
        switch ($name){
            case 'href':
                $href = $this->getData('href');
                return $href ? (strpos($href, 'http') === 0 ? $href : $this->getUrl($href)) : '';
                break;
            case 'move':
                return (int)$this->getData('move');
                break;
            case 'margin':
                return (int)$this->getData('margin');
                break;
            case 'widget_title':
                $title = $this->escapeHtml($this->getData('widget_title'));
                if (!$title){
                    switch ($this->getData('mode')){
                        case 'related':
                            $title = Mage::helper('catalog')->__('Related Products');
                            break;
                        case 'up':
                            $title = Mage::helper('catalog')->__('You may also be interested in the following product(s)');
                            break;
                        case 'cross':
                            $title = Mage::helper('catalog')->__('Based on your selection, you may be interested in the following items:');
                            break;
                    }
                }
                return $title;
                break;
            case 'responsive':
                $obj = new stdClass;
                $obj->type = $this->getData('responsive');
                $obj->data = $obj->type == 'width' ? (int)$this->getData('responsive_width') : $this->getData('responsive_bkp');
                $obj->margin = $this->getConfig('margin');
                return json_encode($obj);
                break;
            case 'paging':
                return $this->getData('paging') ? 'true' : 'false';
                break;
            case 'loop':
                return $this->getData('loop') ? 'true' : 'false';
                break;
            case 'speed':
                return is_numeric($this->getData('speed')) ? $this->getData('speed') : 5000;
                break;
            case 'autoplay':
                return $this->getData('autoplay') ? 'true' : 'false';
                break;
            case 'namespace':
                return $this->getData('namespace') ? $this->getData('namespace') : 'flex-';
                break;
            case 'id':
                return Mage::helper('core')->uniqHash('flexslider_');
                break;
            case 'column':
                return is_numeric($this->getData('column')) ? $this->getData('column') : 4;
                break;
            case 'row':
                return is_numeric($this->getData('row')) ? $this->getData('row') : 1;
                break;
            case 'mode':
                return 'grid';
                break;
            case 'scroll':
                return $this->getData('scroll');
                break;
            case 'limit':
                return is_numeric($this->getData('limit')) ? $this->getData('limit') : 1;
                break;
            default:
                return '';
                break;
        }
    }

    public function getLoadedProductCollection() {
        if ($this->_productCollection) return $this->_productCollection;

        $mode = $this->_getData('mode');
        switch ($mode) {
            case 'new':
                $collection = $this->getNewCollection();
                break;
            case 'latest':
                $collection = $this->getLatestCollection();
                break;
            case 'bestsell':
                $collection = $this->getBestSellerCollection();
                break;
            case 'mostviewed':
                $collection = $this->getMostViewedCollection();
                break;
            case 'featured':
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'featured');
                if($attribute->getId()) {
                    $collection = $this->getFeaturedCollection();
                }
                break;
            case 'random':
            default:
                $collection = $this->getRandomCollection();
                break;
            case 'specificed':
                $collection = $this->getSpecificedCollection();
                break;
            case 'related':
                $collection = $this->getRelatedCollection();
                break;
            case 'up':
                $collection = $this->getUpSellCollection();
                break;
            case 'cross':
                $collection = $this->getCrossSellCollection();
                break;
            case 'discount':
                $collection = $this->getDiscountCollection();
                break;
        }
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $collection
        ));
        $this->_productCollection = $collection;

        return $this->_productCollection;
    }

    protected function getUpSellCollection(){
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        if ($product){
            $collection = $product->getUpSellProductCollection()
                ->setPositionOrder()
                ->addStoreFilter();

            if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')){
                Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($collection,
                    Mage::getSingleton('checkout/session')->getQuoteId()
                );
                $this->_addProductAttributesAndPrices($collection);
            }

            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            $collection->setPage(1, $this->getLimit());
            $collection->load();

            /**
             * Updating collection with desired items
             */
            Mage::dispatchEvent('catalog_product_upsell', array(
                'product'       => $product,
                'collection'    => $collection,
                'limit'         => $this->getLimit()
            ));

            foreach ($collection as $product) {
                $product->setDoNotUseCategoryId(true);
            }

            return $collection;
        }
        return array();
    }

    protected function getCrossSellCollection(){
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        if ($product){
            $collection = $product->getCrossSellProductCollection()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setPositionOrder()
                ->addStoreFilter();

            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            $collection->setPage(1, $this->getLimit());
            $collection->load();

            foreach ($collection as $product) {
                $product->setDoNotUseCategoryId(true);
            }

            return $collection;
        }
        return array();
    }

    protected function getRelatedCollection(){
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        if ($product){
            $collection = $product->getRelatedProductCollection()
                ->addAttributeToSelect('required_options')
                ->setPositionOrder()
                ->addStoreFilter();

            if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')){
                Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($collection,
                    Mage::getSingleton('checkout/session')->getQuoteId()
                );
                $this->_addProductAttributesAndPrices($collection);
            }

            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            $collection->setPage(1, $this->getLimit());
            $collection->load();

            foreach ($collection as $product) {
                $product->setDoNotUseCategoryId(true);
            }

            return $collection;
        }
        return array();
    }

    protected function getDiscountCollection(){
        $catIds = explode(',', $this->getCategoryIds());
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('catalogrule/rule_product');
        $tableAlias = 'catalogrule_product_idx';
        $subSelect  = $connection->select()->from($tableName, array('product_id', 'customer_group_id'));
        $conditions = array(
            "{$tableAlias}.product_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.customer_group_id = ?", $this->_customerGroupId)
        );
        $collection->getSelect()->join(
            array($tableAlias => $subSelect),
            join(' AND ', $conditions),
            array()
        );

        if ($catIds){
            $catProIds = $this->getProductIdsByCategories($catIds);
            if (count($catProIds)) $collection->addIdFilter($catProIds);
        }

        $collection->setPage(1, $this->getLimit());
        $collection->load();
        return $collection;
    }

    protected function getSpecificedCollection(){
        $catIds = explode(',', $this->getCategoryIds());
        $proIds = explode(',', $this->getProductIds());
        if($catIds) {
            $catProIds = $this->getProductIdsByCategories($catIds);
            $proIds = array_intersect($proIds, $catProIds);
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addIdFilter($proIds)
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        } else {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addStoreFilter()
                ->addUrlRewrite()
                ->addIdFilter($proIds)
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addTaxPercents();
        }
        $products->setPage(1, $this->getLimit());
        $products->load();
        return $products;
    }

    protected function getRandomCollection(){
        $catIds = explode(',', $this->getCategoryIds());
        $catProIds = $this->getProductIdsByCategories($catIds);
        $collection = Mage::getResourceModel('catalog/product_collection');
        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->getSelect()->order('RAND()');
        $collection->addStoreFilter();
        if (count($catProIds)) $collection->addIdFilter($catProIds);
        $collection->setPage(1, $this->getLimit());
        return $collection;
    }

    protected function getNewCollection(){
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $catids = $this->getCategoryIds();
        if($catids) {
            $catIds = explode(',', $catids);
            $arr_productids = $this->getProductIdsByCategories($catIds);
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addIdFilter($arr_productids)
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                ->addAttributeToFilter(array(
                    array('attribute' => 'news_to_date', 'date' => true, 'from' => $todayDate),
                    array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('null'))),
                    '',
                    'left')
                ->addAttributeToSort('news_from_date', 'desc');
        } else {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                ->addAttributeToFilter(array(
                    array('attribute' => 'news_to_date', 'date' => true, 'from' => $todayDate),
                    array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('null'))),
                    '',
                    'left')
                ->addAttributeToSort('news_from_date', 'desc');
        }
        $products->setPage(1, $this->getLimit());
        $products->load();
        return $products;
    }

    protected function getLatestCollection($fieldorder='updated_at', $order='desc') {
        $catIds = $this->getCategoryIds();
        if($catIds) {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->setOrder($fieldorder, $order);

            $catIds = explode(',', $catIds);
            if (count($catIds) > 1){
                $proIds = $this->getProductIdsByCategories($catIds);
                $products->addIdFilter($proIds);
            }else{
                $category = Mage::getModel('catalog/category')->load($catIds[0]);
                $products->addCategoryFilter($category);
            }
        } else {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addStoreFilter()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->setOrder($fieldorder, $order);
        }
        $products->setPage(1, $this->getLimit());
        $products->load();
        return $products;
    }

    protected function getBestSellerCollection() {
        $catIds = $this->getCategoryIds();
        if($catIds) {
            $catIds = explode(',', $catIds);
            $ctf = array();
            foreach ($catIds as $cat){
                $ctf[]['finset'] = $cat;
            }
            $products = Mage::getModel('catalog/product')->getCollection();
            $products->addAttributeToSelect('*')->addStoreFilter();
            $products->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                     ->addAttributeToFilter('category_id', array($ctf));
        } else {
            $products = Mage::getModel('catalog/product')->getCollection();
            $products->addAttributeToSelect('*')->addStoreFilter();
        }
        $orderItems = $this->getTableName('sales/order_item');
        $orderMain = $this->getTableName('sales/order');
        $products->getSelect()
            ->join(array('items' => $orderItems), "items.product_id = e.entity_id", array('count' => 'SUM(items.qty_ordered)'))
            ->join(array('trus' => $orderMain), "items.order_id = trus.entity_id", array())
            ->where('trus.status = ?', 'complete')
            ->group('e.entity_id')
            ->order('count DESC');
        $products->setPage(1, $this->getLimit());
        $products->load();
        return $products;
    }

    protected function getMostViewedCollection() {
        $ids = Mage::getResourceModel('reports/product_collection')->addViewsCount()->load()->getLoadedIds();
        $catIds = $this->getCategoryIds();
        if($catIds) {
            $catIds = explode(',', $catIds);
            $arr_productids = array_intersect($ids, $this->getProductIdsByCategories($catIds));
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addIdFilter($arr_productids);
            $products->getSelect()->order(sprintf('FIELD(e.entity_id, %s)', implode(',', $arr_productids)));
        } else {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addIdFilter($ids);
            $products->getSelect()->order(sprintf('FIELD(e.entity_id, %s)', implode(',', $ids)));
        }
        $products->setPage(1, $this->getLimit());
        $products->load();
        return $products;
    }

    protected function getFeaturedCollection() {
        $catIds = $this->getCategoryIds();
        if($catIds) {
            $catIds = explode(',', $catIds);
            $arr_productids = $this->getProductIdsByCategories($catIds);
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addIdFilter($arr_productids)
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToFilter("featured", 1);
        } else {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToFilter("featured", 1);
        }
        $products->setPage(1, $this->getLimit());
        $products->load();
        return $products;
    }

    public function getProductsByCategory($catId) {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addStoreFilter();

        if ($catId) {
            $categories = $this->getCategories();
            if (isset($categories[$catId])) {
                $collection->addCategoryFilter($categories[$catId]);
            }
        }
        return $collection;
    }

    public function getProductIdsByCategories($catIds) {
        $productIds = array();
        if(is_array($catIds) && count($catIds)) {
            foreach($catIds as $catId) {
                if (is_numeric($catId)) {
                    $productIdArr = $this->getProductsByCategory($catId);
                    if(count($productIdArr)) {
                        foreach($productIdArr as $product) {
                            $productIds[] = $product->getId();
                        }
                    }
                }
            }
        }
        $productIds = array_unique($productIds);
        return $productIds;
    }

    public function getTableName($modelEntity) {
        try {
            $table = Mage::getSingleton('core/resource')->getTableName($modelEntity);
        } catch (Exception $e){
            Mage::throwException($e->getMessage());
        }
        return $table;
    }

    /**
     * Create reviews summary helper block once
     *
     * @return boolean
     */
    protected function _initReviewsHelperBlock() {
        if (!$this->_reviewsHelperBlock) {
            if (!Mage::helper('catalog')->isModuleEnabled('Mage_Review')) {
                return false;
            } else {
                $this->_reviewsHelperBlock = $this->getLayout()->createBlock('mtreview/helper');
            }
        }
        return true;
    }

    public function getCategories(){
        if ($this->_categories) return $this->_categories;

        $categories = explode(',', $this->getData('category_ids'));
        if (!count($categories)) return array();

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addIdFilter($categories)
            ->addAttributeToSelect('*');

        foreach ($collection as $category){
            $this->_categories[$category->getId()] = $category;
        }

        return $this->_categories;
    }

    public function getProductCollection($category){
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        switch ($this->getData('mode')){
            case 'discount':
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $websiteId = (int)Mage::app()->getWebsite()->getId();
                /* @var $session Mage_Customer_Model_Session */
                $session = Mage::getSingleton('customer/session');
                $customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;

                $select = $connection->select()
                    ->from('catalog_product_index_price', array('entity_id'))
                    ->where('price > final_price')
                    ->where('website_id = ?', $websiteId)
                    ->where('customer_group_id = ?', $customerGroupId);

                $collection->getSelect()->join(
                    array('e2' => $select),
                    join(' AND ', array('e2.entity_id = e.entity_id')),
                    array()
                );
                unset($connection, $select);
                break;
            case 'bestsell':
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

                $select = $connection->select()
                    ->from('sales_flat_order_item', array('product_id', 'count' => 'SUM(sales_flat_order_item.qty_ordered)'))
                    ->join(
                        'sales_flat_order',
                        'sales_flat_order_item.order_id = sales_flat_order.entity_id',
                        array())
                    ->where('sales_flat_order.status = ?', 'complete')
                    ->group('sales_flat_order_item.product_id')
                    ->order('count DESC');

                $collection->getSelect()->join(
                    array('e2' => $select),
                    join(' AND ', array('e2.product_id = e.entity_id')),
                    array()
                );
                unset($connection, $select);
                break;
            case 'mostviewed':
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $storeId = (int)Mage::app()->getStore()->getId();

                $select = $connection->select()
                    ->from('report_event', array('object_id', 'views' => 'COUNT(report_event.event_id)'))
                    ->join('report_event_types', 'report_event.event_type_id = report_event_types.event_type_id', array())
                    ->where('report_event_types.event_name = ?', 'catalog_product_view')
                    ->where('report_event.store_id = ?', $storeId)
                    ->group('report_event.object_id')
                    ->order('views desc')
                    ->having('views > ?', 0);

                $collection->getSelect()->join(
                    array('e2' => $select),
                    join(' AND ', array('e2.object_id = e.entity_id')),
                    array()
                );
                unset($connection, $select);
                break;
            case 'latest':
            default:
                $collection->setOrder('updated_at', 'desc');
                break;
        }

        $collection->setPage(1, $this->getData('limit'));

        if ($category instanceof Mage_Catalog_Model_Category){
            $collection->addCategoryFilter($category);
        }

        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $collection
        ));

        return $collection;
    }

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array()){
        if (!$product->getTypeInstance(true)->hasRequiredOptions($product)) {
            return $this->helper('mtwidget')->getAddUrl($product, $additional);
        }
        if (!isset($additional['_escape'])) {
            $additional['_escape'] = true;
        }
        if (!isset($additional['_query'])) {
            $additional['_query'] = array();
        }
        $additional['_query']['options'] = 'cart';
        return $this->getProductUrl($product, $additional);
    }
}