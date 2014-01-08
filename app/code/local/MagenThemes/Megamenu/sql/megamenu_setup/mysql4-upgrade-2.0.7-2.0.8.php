<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php

$installer = $this;

$installer->startSetup();

$conn = $installer->getConnection();
$menuTable = $this->getTable('megamenu');
if (!$conn->tableColumnExists($menuTable, 'label')) {
    $installer->run("ALTER TABLE {$menuTable} ADD `label` VARCHAR( 20 ) NOT NULL;");
}

$installer->endSetup();