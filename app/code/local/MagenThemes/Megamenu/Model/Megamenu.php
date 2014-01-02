<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Model_Megamenu extends MagenThemes_Megamenu_Model_Abtract
{
	const TREE_ROOT_ID = 1;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/megamenu'); 
    }
    
    public function isActive() {
        if($this->getStatus() == MagenThemes_Megamenu_Model_Status::STATUS_ENABLED) {
            return true;
        }
        return false;
    }
    
    public function hasChild($statusFilter = false, $storeId = null) 
    {   
        $collection = Mage::getModel('megamenu/megamenu')->getCollection() 
			->addFieldToFilter('parent_id', $this->getId());
		if($statusFilter) {
			$collection->addStatusFilter();
		}
        if(count($collection))
            return true;
        return false;
    }
    
    public function getChildItem($col=null, $storeId = null) 
    {  
        $collection = Mage::getModel('megamenu/megamenu')->getCollection() 
			->addFieldToFilter('parent_id', $this->getId())
			->setOrder('position', 'ASC');
        if($col != null) {
            $collection->addFieldToFilter('col', $col);
        }
        return $collection;
    }
    
    public function isGroup() {
        if($this->getIsGroup() == 1) {
            return true;
        }
        return false;
    }
    
    public function showTitle() {
        if($this->getShowTitle() == 1) {
            return true;
        }
        return false;
    }
    
    public function isContent() {
        if($this->getIsContent() == 1) {
            return true;
        }
        return false;
    }
    
    public function isRoot() {
	if($this->getParentId() == 0)
	    return true;
	return false;
    }
    
    public function showSub() {
	if($this->getShowSub() == 1) {
		return true;
	}
	return false;
    }
    
    public function getRootId($storeId = null) 
    { 
    	if($storeId == null){
    		$storeId = 0;
    	} 
    	$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
    	if($storeId == 0){ 
    		$select = $readonce->fetchAll('SELECT DISTINCT megamenu_id FROM '.Mage::getConfig()->getTablePrefix().'megamenu_store');
    	}else{ 
    		$select = $readonce->fetchAll('SELECT megamenu_id FROM '.Mage::getConfig()->getTablePrefix().'megamenu_store WHERE store_id='.$storeId);
    	} 
    	$data = array();
    	foreach ($select as $megamenu) {
    		$data[] = $megamenu['megamenu_id'];
    	}
    	return $data;
    }
    
    public function renderTree($menu=null, $level=0, $activeId, $storeId = null)
    {
    	$html = '';  
    	if(!$menu) {   
			foreach($this->getRootId($storeId) as $rootId) { 
				$menu = Mage::getModel('megamenu/megamenu')->load($rootId);
				$html .= $this->renderTree($menu, 0, $activeId, $storeId);
			}
			return $html;
    	}
    	if($menu->isRoot() || $level==0){
    		$html .= '<script type="text/javascript">rootarray['.$menu->getId().'] = '.$menu->getId().';</script>';
    	}
    	$html .= '<li id="'.$menu->getId().'" class="';
    	if($menu->isRoot() || $level==0){ 
    	    $html .= 'root folder-open root-menu';
    	}
    	$html .= '"><span ';
		if($activeId == $menu->getId()){
	    	$html .= 'class="active"';
		}
		$html .= '>'.$menu->getTitle().'</span>';
    	
    	if($menu->hasChild(false, $storeId)) {
		    $html .= '<ul>';
		    foreach ($menu->getChildItem(null, $storeId) as $child) {
				$html .= $this->renderTree($child, $level+1, $activeId, $storeId);
		    }
		    $html .= '</ul>';	
    	}
    	$html .= '</li>';
    	return $html;
    }
    
    public function loadByCategoryId($categoryId)
    {
	return $this->getCollection()->addFieldToFilter('article', $categoryId)->getFirstItem();
    }
}