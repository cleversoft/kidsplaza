<?php
/******************************************************  
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Navigation_Top extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array('MagenThemes_Megamenu'),
        ));
    }

    public function getType($type)
    {
        return $this->getLayout()->getBlock('megamenu.nav')->getType($type);
    }
    
    public function getRootMenu() {
		$storeId = Mage::app()->getStore()->getId();
		$resource = Mage::getSingleton('core/resource')->getConnection('core_read');
		$megamenu_id = $resource->fetchOne('SELECT megamenu_id FROM '.Mage::getConfig()->getTablePrefix().'megamenu_store WHERE store_id='.$storeId);
        return Mage::getModel('megamenu/megamenu')->getCollection()
                ->addFieldToFilter('level', 1)
                ->addFieldToFilter('parent_id', $megamenu_id)
                ->setOrder('position', 'ASC');
    }

    public function getCacheKeyInfo()
    {
        return array(
            'MagenThemes_Megamenu',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate()
        );
    }

}