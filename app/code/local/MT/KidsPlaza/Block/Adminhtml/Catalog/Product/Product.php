<?php
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Product extends Mage_Adminhtml_Block_Catalog_Product
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/kidsplaza/catalog/product.phtml');
    }
}