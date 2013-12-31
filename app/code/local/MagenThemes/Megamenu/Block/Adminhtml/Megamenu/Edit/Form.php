<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Edit_Form extends MagenThemes_Megamenu_Block_Adminhtml_Megamenu_Tree
{
  protected $_additionalButtons = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('megamenu/edit/form.phtml');
    }
    
    public function getMegamenu() {
      	return Mage::registry('current_megamenu');
    }
    
    protected function _prepareLayout()
    {
        $megamenu = $this->getMegamenu();
        $megamenuId = (int) $megamenu->getId();

        // Save button
		$this->setChild('save_button',
		    $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
			    'label'     => Mage::helper('catalog')->__('Save Megamenu'),
			    'onclick'   => "megamenuForm.submit('" . $this->getUrl('*/*/save', array('id' => $megamenuId)) . "', true)",
			    'class' => 'save'
			))
		);
	
	        // Delete button
		$this->setChild('delete_button',
		    $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
			    'label'     => Mage::helper('catalog')->__('Delete Megamenu'),
			    'onclick'   => "megamenuDelete('" . $this->getUrl('*/*/delete') . "', {$megamenuId})",
			    'class' => 'delete'
			))
		);
	
	        // Reset button
		$resetPath = $megamenuId ? '*/*/edit' : '*/*/add';
		$this->setChild('reset_button',
		    $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
			    'label'     => Mage::helper('catalog')->__('Reset'),
			    'onclick'   => "categoryReset('".$this->getUrl($resetPath, array('_current'=>true))."',true)"
			))
		);

        return parent::_prepareLayout();
    }
    
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
    
    public function getChildrenStore()
    {
    	$megamenu = $this->getMegamenu();
    	$megamenuId = (int) $megamenu->getId();   

    	$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$select = $readonce->fetchAll('SELECT store_id FROM '.Mage::getConfig()->getTablePrefix().'megamenu_store WHERE megamenu_id='.$megamenuId); 
    	$arrayStoreId = array();
    	foreach ($select as $row){
    		$arrayStoreId[] = $row['store_id'];
    	} 
    	$websiteCollection = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(); 
    	$html = '<select name="store_id[]" size="10" multiple="multiple" class=" required-entry select multiselect">';
    		if(in_array(0, $arrayStoreId)){
    			$html .='<option value="0" selected="selected">'.$this->__('All Store Views').'</option>';
    		}else{
    			$html .='<option value="0">'.$this->__('All Store Views').'</option>';
    		} 
		    foreach ($websiteCollection as $website): 
		       	$showWebsite=false;
	        	foreach ($website as $group):
	            	$showGroup=false;
	        		if(!is_array($group)){ 
	        			if ($showGroup == false):
	        			$showGroup = true;
	        			$html .= '<optgroup label="&nbsp;&nbsp;&nbsp;'.$group.'">';
	        			endif;
	        		}
	        		if(is_array($group)){
		           		foreach ($group as $store): 
		           			if(in_array($store['value'], $arrayStoreId)){
		           				$html .= '<option value="'.$store['value'].'" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;'.$store['label'].'</option>';
		           			}else{
		           				$html .= '<option value="'.$store['value'].'">&nbsp;&nbsp;&nbsp;&nbsp;'.$store['label'].'</option>';
		           			}
		            	endforeach;
	        		}
	            	if(!is_array($group)){
		            	if ($showGroup):
		                $html .= '</optgroup>';
		            	endif;
	            	}
	        	endforeach;
	     	endforeach;
		$html .= '</select>';
    	return $html;
    }

    public function getSaveButtonHtml()
    {
      	return $this->getChildHtml('save_button');
    }

    public function getResetButtonHtml()
    {
      	return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild($alias . '_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config));
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    } 
    /**
     * Remove additional button
     *
     * @param string $alias
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        } 
        return $this;
    }
    
    public function getStoreConfigurationUrl()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $params = array();
        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $params['website'] = $store->getWebsite()->getCode();
            $params['store']   = $store->getCode();
        }
        return $this->getUrl('*/system_store', $params);
    }

    public function getHeader()
    {
      	if ($this->getMegamenu()->getId()) {
	  		return $this->getMegamenu()->getTitle(). ' (ID : ' .$this->getMegamenu()->getId(). ')';
      	} else {
	  		$parentId = (int) $this->getRequest()->getParam('parent');
	  		if ($parentId) {
	      		return Mage::helper('catalog')->__('New Submegamenu');
	  		} else {
	      		return Mage::helper('catalog')->__('New Root Megamenu');
	  		}
      	}
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }
    
    public function getSaveUrl()
    {
      	return $this->getUrl('*/*/save', array('id' => $this->getMegamenu()->getId()));
    }
    
    public function getArticleHtml($type, $disabled) 
    {
		$html = ''; 
		if($type) {
		    if($this->getMegamenu()->getId()) {
				$html .= $this->getLayout()->getBlock('megamenu.type')->nodeToSelectHtml($type, $this->getMegamenu()->getArticle(), $disabled);
		    } else {
				$html .= $this->getLayout()->getBlock('megamenu.type')->nodeToSelectHtml($type, 0, $disabled);
		    }
		}
		return $html;
    }
    
    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}