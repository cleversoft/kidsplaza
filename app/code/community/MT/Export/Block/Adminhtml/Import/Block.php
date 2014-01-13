<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Export
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MT_Export_Block_Adminhtml_Import_Block extends Mage_Adminhtml_Block_Widget_Form_Container{
    public function __construct(){
        $this->_blockGroup      = 'export';
        $this->_controller      = 'adminhtml_import';
        $this->_mode            = 'block';
        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('export')->__('Import'));
    }

    public function getHeaderText(){
        return Mage::helper('export')->__('Import Static Block');
    }
}