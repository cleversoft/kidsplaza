<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Edit_Tab_Combocf extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $product = Mage::registry('product');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('combocf_fieldset', array('legend' => Mage::helper('kidsplaza')->__('Combo Settings')));
        $fieldset->addField('combo_enable', 'select', array(
            'name'      => 'product[combo_enable]',
            'label' => Mage::helper('kidsplaza')->__('Combo enable'),
            'note' => 'Enable product combo',
            'values'    => array(Mage::helper('kidsplaza')->__('No'), Mage::helper('kidsplaza')->__('Yes'))
        ));
        $form->setValues($product->getData());
        $this->setForm($form);
    }

}