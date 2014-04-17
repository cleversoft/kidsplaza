<?php
/**
 * @category    MT
 * @package     MT_CatalogInventory
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogInventory_Model_Resource_Indexer_Stock_Default extends Mage_CatalogInventory_Model_Resource_Indexer_Stock_Default{
    public function reindexAll(){
        //exit immediately
        return $this;
    }

    public function reindexEntity($entityIds){
        //exit immediately
        return $this;
    }
}