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
class MT_Review_Model_Resource_Report extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Review Detail table
     *
     * @var string
     */
    protected $_reviewDetailTable;
    /**
     * Resource status model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('mtreview/review_report', 'id');
        $this->_reviewDetailTable   = $this->getTable('mtreview/review_detail');
    }

}
