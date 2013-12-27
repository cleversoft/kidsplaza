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
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MT_Review_Block_Helpfulness extends Mage_Core_Block_Template
{
    protected $_reviewId;

    public function _construct() {
        parent::_construct();
    }

    public function setReviewId($reviewId)
    {
        $this->_reviewId = $reviewId;
        return $this;
    }

    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function getLinkHelpfulness($helpful = 'none')
    {
        if($helpful=='Yes'){
            return Mage::getUrl('mtreview/review/helpful', array('reviewId' => $this->_reviewId, 'val' => 1));
        }else{
            return Mage::getUrl('mtreview/review/helpful', array('reviewId' => $this->_reviewId, 'val' => 0));
        }
    }

    public function canShowHelpfulnessLink()
    {
        $helper = Mage::helper('mtreview');
        if( $helper->confAllowOnlyLoggedToVote())
            return !($helper->isHelpfulnessLogged($this->_reviewId));
        else
            return ($helper->isUserLogged() && !($helper->isHelpfulnessLogged($this->_reviewId)));
    }
}
