<?php
/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class AW_Blog_Block_Widget_Grid_Column_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $path = $this->_getPath($row);
        $html = array('Root');
        for ($i=1;$i<=count($path);$i++){
            $html[] = sprintf('<div class="blog-cat-tree">%s %s</div>', str_repeat('<span class="blog-cat-padding"></span>', $i), $path[$i-1]);
        }
        return implode('', $html);
    }

    protected function _getPath($model){
        $path = array();
        if ($model->getParent()){
            $parent = Mage::getModel('blog/cat')->load($model->getParent());
            if ($parent->getId()){
                if ($parent->getParent()) $path = array_merge($path, $this->_getPath($parent));
                else $path[] = $parent->getTitle();
            }
        }
        $path[] = $model->getTitle();
        return $path;
    }
}