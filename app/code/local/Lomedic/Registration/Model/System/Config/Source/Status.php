<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_System_Config_Source_Status extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    const NEW_REQUEST_FOR_REGISTRATION  = 1; //Visitor initiates registration
    const NEW_PROCESS                   = 2; //Manager assigns recruiter
    const STANDBY_PROCESS               = 3; //Registration process doesn’t change during predefined number of days
    const PREREGISTRATION_COMPLETED     = 4; //Registrated person fills in pre-registration form
    const VALIDATION                    = 5; //Registrated person uploads documents
    const CORRECTION                    = 6; //Registrated person corrected documents
    const VALIDATED                     = 7; //Recruiter validates documents as correct and can assign visit date
    const VISIT_APPOINTMENT             = 8; //Recruiter validates documents as correct
    const VISITATION                    = 9; //Recruiter assigned date and time of the visit
    const VISIT_COMPLETED               = 10;//When the next day comes after appointed date of the visit
    const PREAPPROVED                   = 11;//Recruiter pre-approves registrated user
    const PRECLOSED                     = 12;//Recruiter initiates closure of the process
    const NOT_APPROVED                  = 13;//Manager don’t approve user
    const APPROVED                      = 14;//Manager approves user
    const COMPLETED                     = 15;//The manager complete the process
    const CLOSED                        = 16;//The manager closed the process
    const RESTART                       = 17;//The manager restart the process
 
    /**
     * Registration object
     * @var Mage_Customer_Model_Customer
     */
    private $_registration;

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
        $request = Mage::app()->getRequest();
        $list = array(
            array('value' => self::NEW_REQUEST_FOR_REGISTRATION, 'label'=>Mage::helper('adminhtml')->__('New request for registration')),
            array('value' => self::NEW_PROCESS, 'label'=>Mage::helper('adminhtml')->__('New process')),
            array('value' => self::STANDBY_PROCESS, 'label'=>Mage::helper('adminhtml')->__('Stand-by process')),
            array('value' => self::PREREGISTRATION_COMPLETED, 'label'=>Mage::helper('adminhtml')->__('Pre-registration completed')),
            array('value' => self::VALIDATION, 'label'=>Mage::helper('adminhtml')->__('Validation')),
            array('value' => self::CORRECTION, 'label'=>Mage::helper('adminhtml')->__('Correction')),
            array('value' => self::VALIDATED, 'label'=>Mage::helper('adminhtml')->__('Validated')),
            array('value' => self::VISIT_APPOINTMENT, 'label'=>Mage::helper('adminhtml')->__('Visit appointment')),
            array('value' => self::VISITATION, 'label'=>Mage::helper('adminhtml')->__('Visitation')),
            array('value' => self::VISIT_COMPLETED, 'label'=>Mage::helper('adminhtml')->__('Visit complete')),
            array('value' => self::PREAPPROVED, 'label'=>Mage::helper('adminhtml')->__('Pre-approve')),
            array('value' => self::PRECLOSED, 'label'=>Mage::helper('adminhtml')->__('Pre-close')),
            array('value' => self::NOT_APPROVED, 'label'=>Mage::helper('adminhtml')->__('Not approve')),
            array('value' => self::APPROVED, 'label'=>Mage::helper('adminhtml')->__('Approve')),
            array('value' => self::COMPLETED, 'label'=>Mage::helper('adminhtml')->__('Complete')),
            array('value' => self::CLOSED, 'label'=>Mage::helper('adminhtml')->__('Close')),
        );
        
        if($request->getControllerName()=='customer' && $request->getParam('id',false)) {    
            $registrationId = $request->getParam('id');
            $this->_registration = Mage::getModel('customer/customer')->load($registrationId);
            $list = $this->_filterManagerList($list);
            $list = $this->_filterRecruterList($list);
            $labels = $this->toArray();
            $status = $this->_registration->getRegistrationStatus();
            array_unshift($list,array('value'=>$status, 'label'=>$labels[$status]));
        }
        return $list;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getOption($id)
    {
        $list = array(
            self::NEW_REQUEST_FOR_REGISTRATION=> Mage::helper('adminhtml')->__('New request for registration'),
            self::NEW_PROCESS=>Mage::helper('adminhtml')->__('New process'),
            self::STANDBY_PROCESS=>Mage::helper('adminhtml')->__('Stand-by process'),
            self::PREREGISTRATION_COMPLETED=>Mage::helper('adminhtml')->__('Pre-registration completed'),
            self::VALIDATION=>Mage::helper('adminhtml')->__('Validation'),
            self::CORRECTION=>Mage::helper('adminhtml')->__('Correction'),
            self::VALIDATED=>Mage::helper('adminhtml')->__('Validated'),
            self::VISIT_APPOINTMENT=>Mage::helper('adminhtml')->__('Visit appointment'),
            self::VISITATION=>Mage::helper('adminhtml')->__('Visitation'),
            self::VISIT_COMPLETED=>Mage::helper('adminhtml')->__('Visit complete'),
            self::PREAPPROVED=>Mage::helper('adminhtml')->__('Pre-approve'),
            self::PRECLOSED=>Mage::helper('adminhtml')->__('Pre-close'),
            self::NOT_APPROVED=>Mage::helper('adminhtml')->__('Not approve'),
            self::APPROVED=>Mage::helper('adminhtml')->__('Approve'),
            self::COMPLETED=>Mage::helper('adminhtml')->__('Complete'),
            self::CLOSED=>Mage::helper('adminhtml')->__('Close'),
            self::RESTART=>Mage::helper('adminhtml')->__('Restart'),
        );

        return isset($list[$id])?$list[$id]:false;
    }
    
    /**
     * Filter actions for Manager
     * @param array $list
     * 
     * @return array
     */
    private function _filterManagerList($list) 
    {
        if(Mage::helper('loregistration')->getManagerType()!=Mage::getStoreConfig('softeq/managers/manager')) return $list;
        
        $list = array();
        switch ($status = $this->_registration->getRegistrationStatus()) {
            case $status == self::COMPLETED:
                break;
            case $status<self::NEW_PROCESS:
                $list[] = array('value' => self::NEW_PROCESS, 'label'=>Mage::helper('adminhtml')->__('New process'));
                break;
            case $status==self::PREAPPROVED:
                $list[] = array('value' => self::NOT_APPROVED, 'label'=>Mage::helper('adminhtml')->__('Not approved'));
                $list[] = array('value' => self::APPROVED, 'label'=>Mage::helper('adminhtml')->__('Approved'));
                break;
            case $status==self::PRECLOSED:
                $list[] = array('value' => self::RESTART, 'label'=>Mage::helper('adminhtml')->__('Restart'));
                break;
            case $status==self::APPROVED:
	        $list[] = array('value' => self::COMPLETED, 'label'=>Mage::helper('adminhtml')->__('Completed'));
                break;
            case $status==self::CLOSED:
            case $status==self::STANDBY_PROCESS:
		$list[] = array('value' => self::RESTART, 'label'=>Mage::helper('adminhtml')->__('Restart'));
                break;
	}
        if(!in_array($status,array(self::CLOSED,self::COMPLETED))) {
            $list[] = array('value' => self::CLOSED, 'label'=>Mage::helper('adminhtml')->__('Closed'));
        }
        return $list;
    }
    
    /**
     * Filter actions for Recruter
     * @param array $list
     * 
     * @return array
     */
    private function _filterRecruterList($list) 
    {
        $type = Mage::helper('loregistration')->getManagerType();
        if(Mage::helper('loregistration')->getManagerType()!=Mage::getStoreConfig('softeq/managers/recruter')) return $list;
        
        $list = array();
        switch ($status = $this->_registration->getRegistrationStatus()) {
            case $status==self::PREREGISTRATION_COMPLETED:
            case $status==self::VALIDATION:
            case $status==self::CORRECTION:
                break;
            case $status==self::VALIDATED:
	        $list[] = array('value' => self::VISIT_APPOINTMENT, 'label'=>Mage::helper('adminhtml')->__('Visit appointment'));
                break;
            case $status==self::VISITATION:
	        $list[] = array('value' => self::VISIT_COMPLETED, 'label'=>Mage::helper('adminhtml')->__('Visit completed'));
                break;
            case $status==self::VISIT_COMPLETED:
	        $list[] = array('value' => self::PREAPPROVED, 'label'=>Mage::helper('adminhtml')->__('Pre-approve'));
                $list[] = array('value' => self::PRECLOSED, 'label'=>Mage::helper('adminhtml')->__('Pre-close'));
                break;
            case $status==self::NOT_APPROVED:
		$list[] = array('value' => self::PREAPPROVED, 'label'=>Mage::helper('adminhtml')->__('Pre-approve'));
                $list[] = array('value' => self::PRECLOSED, 'label'=>Mage::helper('adminhtml')->__('Pre-close'));
	}
        return $list;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() 
    {
        return array(
            self::NEW_REQUEST_FOR_REGISTRATION=>Mage::helper('adminhtml')->__('New request for registration'),
            self::NEW_PROCESS=>Mage::helper('adminhtml')->__('New process'),
            self::STANDBY_PROCESS=>Mage::helper('adminhtml')->__('Stand-by process'),
            self::PREREGISTRATION_COMPLETED=>Mage::helper('adminhtml')->__('Pre-registration completed'),
            self::VALIDATION=>Mage::helper('adminhtml')->__('Validation'),
            self::CORRECTION=>Mage::helper('adminhtml')->__('Correction'),
            self::VALIDATED=>Mage::helper('adminhtml')->__('Validated'),
            self::VISIT_APPOINTMENT=>Mage::helper('adminhtml')->__('Visit appointment'),
            self::VISITATION=>Mage::helper('adminhtml')->__('Visitation'),
            self::VISIT_COMPLETED=>Mage::helper('adminhtml')->__('Visit completed'),
            self::PREAPPROVED=>Mage::helper('adminhtml')->__('Pre-approved'),
            self::PRECLOSED=>Mage::helper('adminhtml')->__('Pre-closed'),
            self::NOT_APPROVED=>Mage::helper('adminhtml')->__('Not approved'),
            self::APPROVED=>Mage::helper('adminhtml')->__('Approved'),
            self::COMPLETED=>Mage::helper('adminhtml')->__('Completed'),
            self::CLOSED=>Mage::helper('adminhtml')->__('Closed'),
        );
    }
}