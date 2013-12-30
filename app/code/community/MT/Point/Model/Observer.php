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
            $point->save();
            $history = Mage::getModel('mtpoint/history');
            $history->setData(array(
                'point_id'  => $point->getId(),
                'balance'   => $point->getBalance(),
                'delta'     => $request['point']['delta'],
                'comment'   => $request['point']['comment'] ? $request['point']['comment'] : Mage::helper('mtpoint')->__('N/A'),
                'by'        => Mage::getSingleton('admin/session')->getUser()->getId()
            ));
            $history->save();
        }
    }
}