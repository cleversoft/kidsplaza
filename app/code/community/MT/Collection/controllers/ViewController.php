<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_ViewController extends Mage_Core_Controller_Front_Action{
    public function processRequest(){
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getParam('isAjax') == true){
            $output['main'] = $this->getLayout()->getBlock('content')->toHtml();
            if ($this->getRequest()->getParam('toolbar') != 1){
                foreach($this->getLayout()->getAllBlocks() as $block){
                    if ($block->getType() == 'mtcollection/layer_view'){
                        $output['layer'] = $block->toHtml();
                    }
                }
            }
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($output));
        }else $this->renderLayout();
    }

    public function lastestAction(){
        Mage::register('current_collection', 'lastest');
        $this->loadLayout();
        $layout = $this->getLayout();
        $title = $this->__('Lastest Products');
        $layout->getBlock('head')->setTitle($title);
        $layout->getBlock('collection.view')->setTitle($title);
        $this->processRequest();
    }

    public function bestsellerAction(){
        Mage::register('current_collection', 'bestseller');
        $this->loadLayout();
        $layout = $this->getLayout();
        $title = $this->__('Bestseller Products');
        $layout->getBlock('head')->setTitle($title);
        $layout->getBlock('collection.view')->setTitle($title);
        $this->processRequest();
    }

    public function mostviewedAction(){
        Mage::register('current_collection', 'mostviewed');
        $this->loadLayout();
        $layout = $this->getLayout();
        $title = $this->__('Most Viewed Products');
        $layout->getBlock('head')->setTitle($title);
        $layout->getBlock('collection.view')->setTitle($title);
        $this->processRequest();
    }

    public function promotionAction(){
        Mage::register('current_collection', 'promotion');
        $this->loadLayout();
        $layout = $this->getLayout();
        $title = $this->__('Promotion Products');
        $layout->getBlock('head')->setTitle($title);
        $layout->getBlock('collection.view')->setTitle($title);
        $this->processRequest();
    }

    public function dailyAction(){
        Mage::register('current_collection', 'daily');
        $this->loadLayout();
        $layout = $this->getLayout();
        $title = $this->__('Daily Deal Products');
        $layout->getBlock('head')->setTitle($title);
        $layout->getBlock('collection.view')->setTitle($title);
        $this->processRequest();
    }
}