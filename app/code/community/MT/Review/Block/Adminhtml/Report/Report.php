<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_Review_Block_Adminhtml_Report_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function awRemoveButton( $id )
    {
        foreach ($this->_buttons as $level => $buttons) {
            if (isset($buttons[$id])) {
                unset($this->_buttons[$level][$id]);
            }
        }
        return $this;
    }

    public function __construct()
    {
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'mtreview';
        $this->_headerText = Mage::helper('mtreview')->__('Review Report');
        parent::__construct();
        $this->awRemoveButton('add');

    }
}
