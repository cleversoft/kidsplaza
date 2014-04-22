<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Catalog_Layer_Filter_Item extends MT_Filter_Model_Layer_Filter_Item{
    public function getUrl(){
        if ($this->getHref()){
            return Mage::getUrl(null, array(
                '_current'  => true,
                '_direct'   => str_replace(Mage::getBaseUrl(), '', $this->getHref()),
                '_query'    => array(
                    Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null,
                    'toolbar' => null
                )
            ));
        }
        $query = array(
            $this->getFilter()->getRequestVar() => $this->getValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        return Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $query));
    }

    public function getClasses(){
        return 'skip-ajax';
    }

    public function isActive(){
        return $this->getIsActive();
    }

    public function getRemoveUrl(){
        if ($this->getHref()) return $this->getHref();
        $query = array($this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue());
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        return Mage::getUrl('*/*/*', $params);
    }
}