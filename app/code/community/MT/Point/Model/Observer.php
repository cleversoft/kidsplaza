<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Model_Observer{
    public function adminhtmlCustomerSaveAfter($observer){
        $request = $observer->getEvent()->getRequest()->getPost();
        if (isset($request['point']) && $request['point']['delta']){
            $point = Mage::getModel('mtpoint/point');
            $point->loadByCustomer($request['customer_id']);
            if ($point->getId()){
                $balance = $point->getData('balance') + $request['point']['delta'];
                $point->setData('balance', $balance < 0 ? 0 : $balance);
            }else{
                $balance = $request['point']['delta'];
                $point->setData('customer_id', $request['customer_id']);
                $point->setData('balance', $balance < 0 ? 0 : $balance);
            }
            try{
                $point->save();
                $history = Mage::getModel('mtpoint/history');
                $history->setData(array(
                    'point_id'  => $point->getId(),
                    'balance'   => $point->getBalance(),
                    'delta'     => $request['point']['delta'],
                    'comment'   => Mage::helper('mtpoint')->__('[Admin] %s', $request['point']['comment']),
                    'by'        => Mage::getSingleton('admin/session')->getUser()->getId()
                ));
                $history->save();
            }catch (Exception $e){}
        }
    }

    public function salesOrderInvoicePay($observer){
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        /* @var $order Mage_Sales_Model_Order */
        $order = $invoice->getOrder();
        $customerId = $order->getCustomerId();
        if (!$customerId) return;
        $rate = Mage::helper('mtpoint')->getPointRate();
        if (!$rate) return;
        $delta = $invoice->getSubtotal() / $rate;
        /* @var $balance MT_Point_Model_Point */
        $balance = Mage::getModel('mtpoint/point');
        $balance->loadByCustomer($customerId);
        if (!$balance->getId()){
            $balance->setCustomerId($customerId);
        }
        $balance->setBalance($balance->getBalance() + $delta);
        try{
            $balance->save();
            $history = Mage::getModel('mtpoint/history');
            $history->setData(array(
                'point_id'  => $balance->getId(),
                'balance'   => $balance->getBalance(),
                'delta'     => $delta,
                'comment'   => Mage::helper('mtpoint')->__('[Order] From order #%s (Invoice)', $order->getIncrementId())
            ));
            $history->save();
        }catch (Exception $e){}
    }

    public function salesOrderCreditmemoRefund($observer){
        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        /* @var $order Mage_Sales_Model_Order */
        $order = $creditmemo->getOrder();
        $customerId = $order->getCustomerId();
        if (!$customerId) return;
        $rate = Mage::helper('mtpoint')->getPointRate();
        if (!$rate) return;
        $delta = $creditmemo->getSubtotal() / $rate;
        /* @var $balance MT_Point_Model_Point */
        $balance = Mage::getModel('mtpoint/point');
        $balance->loadByCustomer($customerId);
        if (!$balance->getId()){
            $balance->setCustomerId($customerId);
        }
        $balance->setBalance($balance->getBalance() - $delta);
        try{
            $balance->save();
            $history = Mage::getModel('mtpoint/history');
            $history->setData(array(
                'point_id'  => $balance->getId(),
                'balance'   => $balance->getBalance(),
                'delta'     => -1*$delta,
                'comment'   => Mage::helper('mtpoint')->__('[Order] From order #%s (Refund)', $order->getIncrementId())
            ));
            $history->save();
        }catch (Exception $e){}
    }

    public function onReviewSubmited($observer){
        $review = $observer->getEvent()->getReview();
        $customerId = $review->getCustomerId();
        if (!$customerId) return;
        $status = $review->getStatusId();
        $origin = $review->getOrigData();
        $delta = null;
        if ($status == 1){
            if ($origin['status_id'] != 1){
                $delta = (int)Mage::getStoreConfig('mtpoint/general/review');
            }
        }else{
            if ($origin['status_id'] == 1){
                $delta = -1*(int)Mage::getStoreConfig('mtpoint/general/review');
            }
        }
        if (is_null($delta)) return;
        /* @var $balance MT_Point_Model_Point */
        $balance = Mage::getModel('mtpoint/point');
        $balance->loadByCustomer($customerId);
        if (!$balance->getId()){
            $balance->setCustomerId($customerId);
        }
        $balance->setBalance($balance->getBalance() + $delta);
        try{
            $balance->save();
            $history = Mage::getModel('mtpoint/history');
            $history->setData(array(
                'point_id'  => $balance->getId(),
                'balance'   => $balance->getBalance(),
                'delta'     => $delta,
                'comment'   => Mage::helper('mtpoint')->__('[Review] From reviewing (Submit <a href="%s" target="_blank">#%d</a>)',
                        Mage::getUrl('mtreview/adminhtml_review/edit', array('id'=>$review->getReviewId())),
                        $review->getReviewId())
            ));
            $history->save();
            /*Clean Cache Product*/
            Mage::app()->cleanCache(array(Mage_Catalog_Model_Product::CACHE_TAG));
        }catch (Exception $e){}
    }

    public function onReviewRemoved($observer){
        $review = $observer->getEvent()->getReview();
        $customerId = $review->getCustomerId();
        if (!$customerId) return;
        $status = $review->getStatusId();
        if ($status == 1){
            $delta = -1*(int)Mage::getStoreConfig('mtpoint/general/review');
            /* @var $balance MT_Point_Model_Point */
            $balance = Mage::getModel('mtpoint/point');
            $balance->loadByCustomer($customerId);
            if (!$balance->getId()){
                $balance->setCustomerId($customerId);
            }
            $balance->setBalance($balance->getBalance() + $delta);
            try{
                $balance->save();
                $history = Mage::getModel('mtpoint/history');
                $history->setData(array(
                    'point_id'  => $balance->getId(),
                    'balance'   => $balance->getBalance(),
                    'delta'     => $delta,
                    'comment'   => Mage::helper('mtpoint')->__('[Review] From reviewing (Delete)',
                            Mage::getUrl('mtreview/adminhtml_review/edit', array('id'=>$review->getReviewId())),
                            $review->getReviewId())
                ));
                $history->save();
                /*Clean Cache Product*/
                Mage::app()->cleanCache(array(Mage_Catalog_Model_Product::CACHE_TAG));
            }catch (Exception $e){}
        }
    }
}