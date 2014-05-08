<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Block_Adminhtml_System_Config_Comment extends Mage_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * Render html from layout
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		return $this->getElementData($element, 'note');
	}

    /**
     * Get raw data from field
     *
     * @param $element Varien_Data_Form_Element_Abstract
     * @param $field string
     * @return string
     */
    public function getElementData($element, $field){
        $form = $this->getForm()->getForm();
        foreach ($form->getElements() as $fieldset){
            $fieldsetId = $fieldset->getId();
            $group = $fieldset->getGroup();
            foreach ($group->fields as $elements){
                foreach ($elements as $id => $elm){
                    if ($fieldsetId . '_' . $id == $element->getId()){
                        return $elm->$field;
                        break 3;
                    }
                }
            }
        }
        return '';
    }
}