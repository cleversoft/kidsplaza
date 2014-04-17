<?php
/**
 * @category    MT
 * @package     MT_Customer
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

$this->startSetup();

$this->removeAttribute('customer', 'phone_number');
$this->addAttribute('customer', 'phone_number', array(
    'label'     => 'Telephone',
    'type'      => 'varchar',
    'input'     => 'text',
    'visible'   => true,
    'required'  => false,
    'position'  => 90
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'phone_number');
$attribute->setData('used_in_forms', array('adminhtml_customer', 'customer_account_edit', 'customer_account_create'))->save();

$this->endSetup();