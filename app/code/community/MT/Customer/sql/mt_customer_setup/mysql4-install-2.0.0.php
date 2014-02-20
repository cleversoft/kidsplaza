<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Customer
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */


$installer = $this;
$installer->startSetup();
$installer->addAttribute('customer', 'phone_number', array(
    'label' => 'Telephone',
    'type' => 'int',
    'input' => 'text',
    'visible' => true,
    'required' => false,
    'position' => 90,
));

$attrs = array('phone_number');

foreach ($attrs as $item) {
    $attr = Mage::getSingleton('eav/config')->getAttribute('customer', $item);
    $attr->setData('used_in_forms', array('adminhtml_customer','customer_account_edit','customer_account_create'))->save();
}

$installer->endSetup();