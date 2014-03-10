<?php
 /**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_PhpStorm
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_Review_Block_Summary extends Mage_Core_Block_Template
{
    protected $_reviewsCollection;

    public function getSummaryBlock(){

        $productId = (int)$this->getRequest()->getParam('id');

        $parametrs = Mage::getModel('rating/rating')->getCollection();

        $ratings = array();
        foreach($parametrs as $parametr){
            $ratings[$parametr->getId()]['name'] = $parametr->getRatingCode();

            $votes = Mage::getModel('rating/rating_option_vote')->getCollection();
            $votes->getSelect()
                ->from($this->_name,array('COUNT(*) as qty','value as mark'))
                ->join(array('review'=>Mage::getSingleton('core/resource')->getTableName('mtreview/review')),'main_table.review_id = review.review_id',array('status_id'))
                ->join(array('review_store'=>Mage::getSingleton('core/resource')->getTableName('mtreview/review_store')),'main_table.review_id = review_store.review_id',array('store_id'))
                ->where('main_table.entity_pk_value = ?',$productId)
                ->where('value > 0')
                ///->where('store_id = ?',Mage::app()->getStore()->getId())
                ->where('rating_id = ?',$parametr->getId())
                ->where('review.status_id = 1')
                ->group('value')
                ->order('value DESC')
            ;

            $qtyVotes = 0;
            foreach($votes as $vote){
                $ratings[$parametr->getId()]['votes'][$vote->getMark()]['qty'] = $vote->getQty();
                $qtyVotes +=$vote->getQty();
            }
            $ratings[$parametr->getId()]['votes_qty'] = $qtyVotes;
            foreach($votes as $vote){
                $ratings[$parametr->getId()]['votes'][$vote->getMark()]['qty'] = $vote->getQty();
                $ratings[$parametr->getId()]['votes'][$vote->getMark()]['percent'] = round($vote->getQty()/$qtyVotes*100);
            }

        }
        return $ratings;

    }

    public function getProduct()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        if (!Mage::registry('product')) {
            $product = Mage::getModel('catalog/product')->load($productId);
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }

    public function getReviewsSummaryHtml()
    {
        return $this->getLayout()->createBlock('mtreview/rating')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml();
    }

    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                //->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();
        }
        return $this->_reviewsCollection;
    }
}