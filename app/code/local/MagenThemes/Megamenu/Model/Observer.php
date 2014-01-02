<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Model_Observer extends Varien_Object
{
	public function controller_action_layout_generate_blocks_after($observer) {
		if(Mage::helper('megamenu')->isActive()) {
			if($observer->getLayout()->getArea() != 'adminhtml')
				if($observer->getLayout()->getBlock('top.menu'))
					$observer->getLayout()->getBlock('top.menu')->unsetChild('catalog.topnav');
		}
	}
}