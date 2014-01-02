<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  	public function __construct()
  	{
    	$this->_controller = 'adminhtml_megamenu';
    	$this->_blockGroup = 'megamenu';
    	$this->_headerText = Mage::helper('megamenu')->__('Megamenu Manager');
    	$this->_addButtonLabel = Mage::helper('megamenu')->__('Add Megamenu');
    	parent::__construct();
  	}
}