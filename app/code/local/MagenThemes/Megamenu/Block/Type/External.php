<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Block_Type_External extends MagenThemes_Megamenu_Block_Type{
    protected $_hasLink = true;
    protected $_hasContent = false;
    protected $_type = 'external_link';

    public function drawItem(){
        return $this->_menu->status == 1 ? sprintf('
            <li class="root level-0"><a href="%s" target="%s" class="megamenu-title"><span>%s</span></a></li>',
            strpos($this->_menu->url, 'http') === 0 ? $this->_menu->url : $this->getUrl($this->_menu->url),
            $this->_menu->nofollow == 1 ? '_blank' : '_self',
            $this->escapeHtml($this->_menu->title)
        ) : '';
    }
}