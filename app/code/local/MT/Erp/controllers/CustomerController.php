<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_CustomerController extends Mage_Core_Controller_Front_Action{
    protected function _getAttributeValue($table, $entityId, $attributeCode){
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        $sql = "
            SELECT v.value
            FROM {$table} as v
            INNER JOIN {$resource->getTableName('eav_attribute')} AS a ON v.attribute_id = a.attribute_id
            WHERE a.attribute_code = ? AND v.entity_id = ?
        ";

        return $connection->fetchOne($sql, array($attributeCode, $entityId));
    }

    protected function _getCustomerInfo($phoneNumber){
        if (!$phoneNumber) return;

        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        $emailValidator = new Zend_Validate_EmailAddress();
        $isEmail = false;
        if ($emailValidator->isValid($phoneNumber)){
            $isEmail = true;

            $sql = "
                SELECT entity_id
                FROM {$resource->getTableName('customer_entity')}
                WHERE email = ?
            ";

            $customerId = $connection->fetchOne($sql, array($phoneNumber));
        }else {
            $sql = "
                SELECT v.entity_id
                FROM {$resource->getTableName('customer_entity_varchar')} AS v
                INNER JOIN {$resource->getTableName('eav_attribute')} AS a ON a.attribute_id = v.attribute_id
                WHERE a.attribute_code = ? AND v.value = ?
            ";

            $customerId = $connection->fetchOne($sql, array('phone_number', $phoneNumber));
        }

        if ($customerId){
            $sql = "
                SELECT v.value
                FROM {$resource->getTableName('customer_entity_varchar')} AS v
                INNER JOIN {$resource->getTableName('eav_attribute')} AS a ON a.attribute_id = v.attribute_id
                WHERE a.attribute_code = ? AND v.entity_id = ?
            ";

            if  (!$connection->fetchOne($sql, array('password_hash', $customerId))){
                $data = array();

                $data['fn'] = $this->_getAttributeValue($resource->getTableName('customer_entity_varchar'), $customerId, 'firstname');
                $data['ln'] = $this->_getAttributeValue($resource->getTableName('customer_entity_varchar'), $customerId, 'lastname');
                $data['gender'] = $this->_getAttributeValue($resource->getTableName('customer_entity_int'), $customerId, 'gender');
                $sql = "SELECT email FROM {$resource->getTableName('customer_entity')} WHERE entity_id = ?";
                $data['email'] = $connection->fetchOne($sql, array($customerId));
                $data['mobile'] = $isEmail ? $this->_getAttributeValue($resource->getTableName('customer_entity_varchar'), $customerId, 'phone_number') : $phoneNumber;

                return $data;
            }
        }
    }

    public function queryAction(){
        if (!$this->_validateFormKey()) return;

        $phoneNumber = $this->getRequest()->getParam('value');
        if (!$phoneNumber) return;

        if (!Mage::getStoreConfigFlag('kidsplaza/customer/query')) return;

        $data = $this->_getCustomerInfo($phoneNumber);

        $this->getResponse()->setHeader('Content-Type', 'applicaion/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
}