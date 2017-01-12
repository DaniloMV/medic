<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_System_Config_Source_Recruter extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    private $_loading = array();
      
    /**
     * Get all options 
     * 
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $list = array();
        if(Mage::helper('loregistration')->getManagerType()!=Mage::getStoreConfig('softeq/managers/recruter')) {
            $list[] = array('value' => '', 'label' => Mage::helper('loregistration')->__('No Recruiter'));
        }
        foreach ($this->_loadAllRecruters() as $recruter) {
            $list[$recruter->getId()] = array(
                'value' => $recruter->getId(), 
                'label' => $recruter->getFirstname().' '.$recruter->getLastname().'    '."(". (isset($this->_loading[$recruter->getId()])?$this->_loading[$recruter->getId()]:0) .")");
        }
        ksort($list);
        return $list;
    }
    
    /**
     * Create options list
     * @return array
     */
    public function toArray() 
    {
        foreach ($this->_loadAllRecruters() as $recruter) {
            $list[$recruter->getId()] = $recruter->getFirstname().' '.$recruter->getLastname();
        }
        ksort($list);
        return $list;
    }
    
    /**
     * Load Recruiter list
     * 
     * @return \Mage_Customer_Model_Resource_Customer_Collection
     */
    private function _loadAllRecruters() 
    {
        $customerCollection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('registration_status', array(
                                                            'nin' => array(
                                                                    Lomedic_Registration_Model_System_Config_Source_Status::CLOSED,
                                                                    Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED,
                                                                    Lomedic_Registration_Model_System_Config_Source_Status::STANDBY_PROCESS
                                                                )
                                                            )
            );
        $customerCollection->addExpressionAttributeToSelect('loading', 'COUNT(e.entity_id)', array('registration_recruter'));
        $customerCollection->getSelect()
                ->group('registration_recruter')
                ->where('`at_registration_recruter`.`value`>0');
        
        if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
             $customerCollection->getSelect()->where('`at_registration_recruter`.`value`=?',Mage::helper('loregistration')->getCurrentUser()->getId());
        }
        
        foreach ($customerCollection as $item) {
            $this->_loading[$item->getRegistrationRecruter()] = $item->getLoading();
        }
        $collection = Mage::getResourceModel('admin/roles_user_collection');
        $collection->getSelect()->reset(Zend_Db_Select::WHERE);
        $collection->getSelect()
                ->joinInner(array('role'=>Mage::getSingleton('core/resource')->getTableName('admin/role')),'role.user_id=main_table.user_id',array())
                ->where('role.parent_id=?',Mage::getStoreConfig('softeq/managers/recruter'))
                ->where('main_table.is_active=1');
        if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
             $collection->getSelect()->where('main_table.user_id=?',Mage::helper('loregistration')->getCurrentUser()->getId());
         }
        return $collection;
    }
}
