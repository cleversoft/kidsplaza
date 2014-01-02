<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'megamenu';
        $this->_controller = 'adminhtml_megamenu';
        
        //$this->_updateButton('save', 'label', Mage::helper('megamenu')->__('Continue'));
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }
    
    public function getHeaderText()
    {
        return Mage::helper('megamenu')->__('Add Megamenu');
    }
    
    public function getFormHtml()
    {
        return $this->getLayout()->getBlock('megamenu.type')->toHtml();
    }
}