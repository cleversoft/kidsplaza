<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Observer{
    public function modelSaveAfter($observer){
        $object = $observer->getEvent()->getObject();
        Mage::helper('kidsplaza')->cleanCacheByObject($object);
    }

    public function catalogProductPrepareSave($observer){
        $product = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();
        $videos = $request->getParam('videos', array());
        $product->setData('video', Mage::helper('core')->jsonEncode($videos));

        $links = $request->getParam('links', array());
        if (isset($links['wordpress'])){
            $wordpress = explode('&', $links['wordpress']);
            foreach ($wordpress as $index => $wp){
                if (!is_numeric($wp)) unset($wordpress[$index]);
            }
            $product->setData('related_wordpress', implode(',', $wordpress));
        }
        if (isset($links['combo'])){
            $productsCombo = explode('&', $links['combo']);
            foreach ($productsCombo as $index => $combo){
                if (!is_numeric($combo)) unset($productsCombo[$index]);
            }
            $product->setData('products_combo', implode(',', $productsCombo));
        }
    }

    public function saveAfterProduct($observer){
        $product = $observer->getProduct();
        $origPrice = $product->getOrigData('price');
        if ($product->getPrice() != $origPrice){
            $date = Mage::getModel('core/date')->date();
            $product->setPriceDate($date);
            $product->getResource()->saveAttribute($product, 'price_date');
        }
    }
}