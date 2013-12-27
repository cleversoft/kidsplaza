<?php
class MT_Review_Block_Ordering extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected $_collection = null;

    /**
     * GET parameter order variable
     *
     * @var string
     */
    protected $_orderVarName        = 'order';

    /**
     * GET parameter direction variable
     *
     * @var string
     */
    protected $_directionVarName    = 'dir';

    /**
     * List of available order fields
     *
     * @var array
     */
    protected $_availableOrder      = array();

    /**
     * Default direction
     *
     * @var string
     */
    protected $_direction           = 'asc';

    /**
     * Init Ordering
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mt/review/product/view/ordering.phtml');
    }

    public function setCollection($collection)
    {
        parent::setCollection($collection);
        if ($this->getCurrentOrder() && $this->getCurrentDirection()) {
            $this->_collection->setDateOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        return $this;
    }

    public function getCurrentOrder()
    {
        $ordering = $this->getRequest()->getParam($this->getOrderVarName());
        if (!$ordering)
        {
            $ordering_array = $this->getAvailableOrders();
            return $ordering_array[0]['value'];
        }
        else
        {
            return $ordering;
        }

        return $this->getRequest()->getParam($this->getOrderVarName());
    }

    public function getAvailableOrders()
    {
        $orderSelect = Mage::helper('mtreview')->confOrderingItemsArray();
        if(count($orderSelect)){
            $ordering_array = array();
            foreach (Mage::getModel('mtreview/system_config_source_ordering_items')->toOptionArray() as $item)
            {
                if (in_array($item['value'], $orderSelect))
                {
                    $ordering_array[] = array('value' => $item['value'], 'label' => $this->__($item['label']));
                }
            }
            return $ordering_array;
        }else{
            return array();
        }
    }

    /**
     * Retrieve current direction
     *
     * @return string
     */
    public function getCurrentDirection()
    {
        $direction = $this->getRequest()->getParam($this->getDirectionVarName());
        if (!$direction)
        {
            $direction = 'desc';
        }

        return $direction;
    }

    /**
     * Retrieve order field GET var name
     *
     * @return string
     */
    public function getOrderVarName()
    {
        return $this->_orderVarName;
    }

    /**
     * Retrieve sort direction GET var name
     *
     * @return string
     */
    public function getDirectionVarName()
    {
        return $this->_directionVarName;
    }

    /**
     * Compare defined order field vith current order field
     *
     * @param string $order
     * @return bool
     */
    public function isOrderCurrent($order)
    {
        return ($order == $this->getCurrentOrder());
    }

    /**
     * Retrieve Pager URL
     *
     * @param string $order
     * @param string $direction
     * @return string
     */
    public function getOrderUrl($order, $direction)
    {
        if (is_null($order)) {
            $order = $this->getCurrentOrder() ? $this->getCurrentOrder() : $this->_availableOrder[0];
        }
        return $this->getPagerUrl(array(
            $this->getOrderVarName()=>$order,
            $this->getDirectionVarName()=>$direction
        ));
    }

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }
}
