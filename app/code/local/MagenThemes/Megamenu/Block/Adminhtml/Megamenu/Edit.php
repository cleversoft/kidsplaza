<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
		$this->_blockGroup  = 'megamenu';
        $this->_objectId    = 'megamenu_id';
        $this->_controller  = 'adminhtml_megamenu';
        $this->_mode        = 'edit'; 
        parent::__construct();
        $this->setTemplate('megamenu/megamenu/edit.phtml');
    }

    protected function _prepareLayout()
    { 
        return parent::_prepareLayout();
    } 
}