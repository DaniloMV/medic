<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Rewrite_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Dashboard
{
    private $_registrationStatuses;
    
    public function __construct() 
    {
        parent::__construct();

        if(in_array(Mage::helper('loregistration')->getManagerType(), array(Mage::getStoreConfig('softeq/managers/admin'),Mage::getStoreConfig('softeq/managers/recruter'),Mage::getStoreConfig('softeq/managers/manager')))) {
        $this->_getRegistrationCollection();
        $this->setTemplate('registration/dashboard/index.phtml');
        }
    }
    
    /**
     * Get registration grid html
     * 
     * @return html
     */
    public function getRegistrationsGrid() 
    {
        return $this->getLayout()->addBlock('loregistration/adminhtml_dashboard_registration_grid', 'registration_grid')->toHtml();
    }
    
    /**
     * Prepare registration collection
     */
    private function _getRegistrationCollection() 
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'registration_status');
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read'); 
        $select = $adapter->select()
                ->from(array('main' => $attributeModel->getBackend()->getTable()),array('status'=>'main.value','count'=>'COUNT(main.value_id)'))
                ->group('main.value')
                ->where('main.attribute_id = ?', $attributeModel->getId());
        if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
            $attributeRecruterModel = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'registration_recruter');
            $select = $adapter->select()
                ->from(array('main' => $attributeRecruterModel->getBackend()->getTable()),array('count'=>'COUNT(main.value_id)'))
                ->where('main.attribute_id = ?', $attributeRecruterModel->getId())
                ->where('main.value=?',Mage::getSingleton('admin/session')->getUser()->getUserId())
                ->joinLeft(array('status'=>$attributeModel->getBackend()->getTable()),'main.entity_id=status.entity_id',array('status'=>'status.value'))
                ->group('status.value')
                ->where('status.attribute_id=?',$attributeModel->getId());
        }
        $result = array();
        foreach ($adapter->fetchAll($select) as $item) {
            $result[$item['status']] = $item['count'];
        }
        $this->_registrationStatuses = $result;
    }
    
    /**
     * Get registration without recruiter
     */
    private function _getNonRecruiteredRegistrations() 
    {
        $collection = Mage::getModel('customer/customer')->getCollection();
        $collection->addAttributeToFilter('registration_recruter',array(array('null'=>true),array('eq'=>0)),'left');
        unset($this->_registrationStatuses[Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION]);
        $this->_registrationStatuses[Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION] = $collection->getSize();
    }
    
    /**
     * Count new registration
     * 
     * @return Varien_Object|bool
     */
    public function getNewRegistration() 
    {
        $this->_getNonRecruiteredRegistrations();
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION);
    }
    
    /**
     * Count new recruiter registration
     * 
     * @return Varien_Object|bool
     */
    public function getNewRecruiterRegistration() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::NEW_PROCESS);
    }
    
    /**
     * Count standby recruiter registration
     * 
     * @return Varien_Object|bool
     */
    public function getStandByRecruiterProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::STANDBY_PROCESS);
    }
    
    /**
     * Count preregistration complete recruiter registration
     * 
     * @return Varien_Object|bool
     */
    public function getPreregistrationCompleatedRecruiterProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::PREREGISTRATION_COMPLETED);
    }
    
    /**
     * Count correction registration
     * 
     * @return Varien_Object|bool
     */
    public function getCorrectionRecruiterProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::CORRECTION);
    }
    
    /**
     * Count validation registration
     * 
     * @return Varien_Object|bool
     */
    public function getValidationRecruiterProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::VALIDATION);
    }
    
    /**
     * Count standby registration
     * 
     * @return Varien_Object|bool
     */
    public function getStandByProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::STANDBY_PROCESS);
    }
    
    /**
     * Count visit appointment registration
     * 
     * @return Varien_Object|bool
     */
    public function getAppointmentProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::VISITATION);
    }
    
    /**
     * Count visit appointment recruiter registration
     * 
     * @return Varien_Object|bool
     */
    public function getAppointmentRecruiterProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::VISIT_APPOINTMENT);
    }
    
    /**
     * Count preapproval recruiter registration
     * 
     * @return Varien_Object|bool
     */
    public function getPreapprovalRecruiterProcess() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::VISIT_COMPLETED);
    }
    
    /**
     * Count not approve registration
     * 
     * @return Varien_Object|bool
     */
    public function getNotApprove() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::NOT_APPROVED);
    }
    
    /**
     * Count need approve registration
     * 
     * @return Varien_Object|bool
     */
    public function getNeedApprove() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::PREAPPROVED);
    }
    
    /**
     * Count need sign registration
     * 
     * @return Varien_Object|bool
     */
    public function getNeedSign() 
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::APPROVED);
    }
    
    /**
     * Count need close registration
     * 
     * @return Varien_Object|bool
     */
    public function getNeedClose()
    {
        return $this->_prepareRequestResult(Lomedic_Registration_Model_System_Config_Source_Status::PRECLOSED);
    }
    
    /**
     * Prepare results to count
     * 
     * @param int $status
     * @return Varien_Object|bool
     */
    private function _prepareRequestResult($status) 
    {
        if(isset($this->_registrationStatuses[$status])) {
            $url = $this->getUrl('*/*/*',array('_current'=>true,'filter'=> base64_encode('registration_status='.$status)));
            if($status == 1) {
                $url = $this->getUrl('*/*/*',array('_current'=>true,'filter'=> base64_encode('recruiter=none')));
            }
            return new Varien_Object(array(
                'count'=>$this->_registrationStatuses[$status],
                'url'=> $url));
        }
        return false;
    }
}
