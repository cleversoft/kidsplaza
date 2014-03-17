<?php
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Price_Switcher extends Mage_Adminhtml_Block_Template
{
    protected $_hasDefaultOption = true;

    protected $_priceVarName = 'price_change';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/kidsplaza/price/switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName($this->__('Choose Price No Change'));
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, $this->_priceVarName => null));
    }

    public function getPriceSelected()
    {
        return $this->getRequest()->getParam($this->_priceVarName);
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if (null !== $hasDefaultOption) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }
}