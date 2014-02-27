<?php
/**
 * @category    MT
 * @package     MT_Groupon
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Groupon_Block_Adminhtml_Catalog_Product_Edit_Tab_Groupon extends Mage_Adminhtml_Block_Catalog_Form{
    protected function _prepareForm(){
        $product = Mage::registry('product');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('groupon_fieldset', array(
            'legend' => Mage::helper('mtgroupon')->__('Groupon Settings')
        ));
        $f1 = $fieldset->addField('groupon_enable', 'select', array(
            'name'      => 'product[groupon_enable]',
            'label'     => Mage::helper('mtgroupon')->__('Enable Groupon'),
            'note'      => Mage::helper('mtgroupon')->__('Apply group buy on this product'),
            'values'    => array(Mage::helper('mtgroupon')->__('No'), Mage::helper('mtgroupon')->__('Yes'))
        ));
        $f2 = $fieldset->addField('groupon_from', 'date', array(
            'name'      => 'product[groupon_from]',
            'label'     => Mage::helper('mtgroupon')->__('From Datetime'),
            'note'      => Mage::helper('mtgroupon')->__('Active when time occured'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'time'      => true,
            'format'    => Mage::app()->getLocale()->getDateTimeFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
            )
        ));
        $f3 = $fieldset->addField('groupon_to', 'date', array(
            'name'      => 'product[groupon_to]',
            'label'     => Mage::helper('mtgroupon')->__('To Datetime'),
            'note'      => Mage::helper('mtgroupon')->__('Inactive when time occured'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'time'      => true,
            'format'    => Mage::app()->getLocale()->getDateTimeFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
            )
        ));
        $f4 = $fieldset->addField('groupon_qty', 'text', array(
            'name'      => 'product[groupon_qty]',
            'label'     => Mage::helper('mtgroupon')->__('Minimum Qty'),
            'note'      => Mage::helper('mtgroupon')->__('Set minimum quantity for group buy')
        ));
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($f1->getHtmlId(), $f1->getName())
            ->addFieldMap($f2->getHtmlId(), $f2->getName())
            ->addFieldMap($f3->getHtmlId(), $f3->getName())
            ->addFieldMap($f4->getHtmlId(), $f4->getName())
            ->addFieldDependence($f2->getName(), $f1->getName(), 1)
            ->addFieldDependence($f3->getName(), $f1->getName(), 1)
            ->addFieldDependence($f4->getName(), $f1->getName(), 1)
        );
        $form->setValues($product->getData());
        $this->setForm($form);
    }
}