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
class MT_Customer_Model_Entity_Setup extends Mage_Customer_Model_Entity_Setup {

    public function getDefaultEntities() {
        $entities = parent::getDefaultEntities();

        // Add flavour to customer attributes
        $entities['customer']['attributes']['phone_number'] = array(
            'label' => 'Telephone',
            'visible' => true,
            'required' => false,
            'sort_order' => '90',
            'position' => '90'
        );

        return $entities;
    }

}
