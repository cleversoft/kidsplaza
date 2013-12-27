<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Review collection resource model
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MT_Review_Model_Resource_Comment_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Review detail table
     *
     * @var string
     */
    protected $_reviewDetailTable;

    protected function _construct()
    {
        $this->_init('mtreview/comment');
        $this->_reviewDetailTable   = $this->getTable('mtreview/review_detail');

    }

    /**
     * init select
     *
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * join fields to review detail
     *
     *
     */
    protected function _joinFields()
    {
        $this->getSelect()
            ->join(array('rdt' => $this->_reviewDetailTable),
                'rdt.review_id = main_table.review_id',
                array('rdt.title','rdt.nickname', 'rdt.detail', 'rdt.customer_id', 'rdt.store_id'));
        return $this;
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     *
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('main_table.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     *
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('main_table.created_at', $dir);
        return $this;
    }

    /**
     * Add reivew filter
     *
     * @param int $reviewId
     */
    public function addReviewFilter($reviewId)
    {
        $this->getSelect()
            ->where('main_table.review_id = ?', $reviewId);
        return $this;
    }
}
