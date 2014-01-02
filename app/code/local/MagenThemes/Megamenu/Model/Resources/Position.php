<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Model_Resources_Position
{
    public function toOptionArray() {
        return array(
                    array('label' => 'Main Menu', 'value' => 'top'), 
                );
    }
    
    public function toOptionArrayGrid() {
        return array(
                    'top'               => 'Main Menu', 
                );
    }
}