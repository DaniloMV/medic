<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_Observer 
{
    /**
     * Reset Customer Save
     * 
     * @param Varien_Event_Observer $observer
     */
    public function customerSave($observer) 
    {
        $data = Mage::app()->getRequest()->getPost();
        $customer = $observer->getCustomer();
        if(isset($data['account'])) {
            $data['activity'] = $data['account']['activity'];
            $data['company'] = $data['account']['company'];
            $data['sector'] = $data['account']['sector'];
        }
        if(isset($data["company"])){
            $customer->setLastname($data["company"]);
        }

        if(isset($data["activity"]) && ($data["activity"] == 3 || $data["activity"] == 4)) {
            $collectionselect = Mage::getModel('marketplace/userprofile')->getCollection();
            $collectionselect->addFieldToFilter('mageuserid',array($customer->getId()));
            if(count($collectionselect)>=1) {
                foreach($collectionselect as $coll) {
                    $coll->setWantpartner('1');
                    $coll->setpartnerstatus('Seller');
                    $coll->setProfileurl("");
                    $coll->save();
                }
            } else {
                $collection=Mage::getModel('marketplace/userprofile');
                $collection->setwantpartner(1);
                $collection->setpartnerstatus('Seller');
                $collection->setProfileurl("");
                $collection->setmageuserid($customer->getId());
                $collection->save();
            }
            $grop = Mage::getModel('customer/group')->getCollection();

            if($data["sector"] == 1) {
                $grop->addFieldToFilter('customer_group_code',array('eq' => 'Seller/Private sector'));
            }elseif($data["sector"] == 2) {
                $grop->addFieldToFilter('customer_group_code',array('eq' => 'Seller/Government sector'));
            }

            $groupData = $grop->getData();
            $customer->setGroupId($groupData[0]["customer_group_id"]);

        } elseif(isset($data["activity"]) && ($data["activity"] == 1 || $data["activity"] == 2)) {
            $collectionselect = Mage::getModel('marketplace/userprofile')->getCollection();
            $collectionselect->addFieldToFilter('mageuserid',array($customer->getId()));
            if(count($collectionselect)>=1) {
                foreach($collectionselect as $coll) {
                    $coll->delete();
                }
            }

            $grop = Mage::getModel('customer/group')->getCollection();
            if($data["sector"] == 1) {
                $grop->addFieldToFilter('customer_group_code',array('eq' => 'Buyer/Private sector'));
            }elseif($data["sector"] == 2) {
                $grop->addFieldToFilter('customer_group_code',array('eq' => 'Buyer/Government sector'));
            }

            $groupData = $grop->getData();
            $customer->setGroupId($groupData[0]["customer_group_id"]);
        }

        if(isset($data["approve"]) && !empty($data["approve"])) {
            $this->_setFileApprove($observer,$data["approve"],$data["comment"]);
        }

        if(isset($data["account"]["visit_date"]) && !empty($data["account"]["visit_date"])) {
            $date = new DateTime(date('Y-m-d H:i:s',strtotime($data["account"]["visit_date"])));
            $currect_date =  $date->format('Y-m-d H:i:s');
            $customer->setVisitDate($currect_date);
        }

        if($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::VISIT_APPOINTMENT) {

            if(isset($data["account"]["visit_date"]) && !empty($data["account"]["visit_date"])) {
                $date = new DateTime(date('Y-m-d H:i:s',strtotime($data["account"]["visit_date"])));
                $currect_date =  $date->format('Y-m-d H:i:s');
                $customer->setVisitDate($currect_date);

                $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::VISITATION);
            }
        }

        if($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::APPROVED) {
            $customer->setRegistrationStep(5);
        }

        if(isset($data["account"]["registration_status"]) && !empty($data["account"]["registration_status"])
            && $data["account"]["registration_status"] == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {

            $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED);
            $date = new DateTime("now");

            if($customer->getCompanyId() != null && $customer->getCompanyId() >0) {
                $company = Mage::getModel('loregistration/companies')->load($customer->getCompanyId());
                $company->setName($customer->getRealName());
                $company->setCustomerId($customer->getId());
                $company->setCreateDate($date->format("Y-m-d H:i"));
                $company->save();
                $customer->setCompanyId($company->getCompanyId());
            } else {
                $company = Mage::getModel('loregistration/companies')->setName($customer->getRealName());
                $company->setCreateDate($date->format("Y-m-d H:i"));
                $company->setCustomerId($customer->getId());
                $company->save();
                $customer->setCompanyId($company->getCompanyId());
            }
            $loadCustomer = Mage::getModel('customer/customer')->load($customer->getId());

            if($loadCustomer->getRegistrationStatus() != Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED && $data["account"]['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
                $this->_sendTransactionalEmailSuc($observer, Mage::getStoreConfig('templates/email/loregistration_registration_success'));
            }
        }

        $account = $data['account'];
        if(!$account['registration_recruter'] && $data['account']['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::NEW_PROCESS) {
                $account['registration_status'] = Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION;
                Mage::app()->getRequest()->setPost('account',$account);
                $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loregistration')->__('Please select recruiter'));
        }
        if($account['registration_recruter'] && $account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION) {
            Mage::app()->getRequest()->setPost('account',$account);
            $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::NEW_PROCESS);
        }
    }

    /**
     * Send email if registration success
     * 
     * @param Varien_Event_Observer $observer
     * @param int $templateId
     */
    private function _sendTransactionalEmailSuc($observer,$templateId)
    {
        $customer = $observer->getCustomer();
        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);

        $recruter = Mage::helper('loregistration')->getRecruter($customer->getRegistrationRecruter());
        if(!$recruter) {
            $recruter = $customer;
        }

        // Set recepient information
        $recepientEmail = $customer->getEmail();
        $recepientName = $customer->getName();

        // Get Store ID
        $store = Mage::app()->getStore()->getId();

        // Set variables that can be used in email template
        $vars = array(
            'customer' => $customer,
            'recruterEmail' => $recruter->getEmail(),
            'recruterName' => $recruter->getName());

        $translate  = Mage::getSingleton('core/translate');

        $transactionalEmail = Mage::getModel('core/email_template');
        $file = Mage::getBaseDir('media')."/customer".$customer->getContract();

        if(file_exists($file)) {
            $transactionalEmail->getMail()
                ->createAttachment(
                    file_get_contents($file),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($customer->getContract())
                );
        }

        // Send Transactional Email
        $transactionalEmail->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $store);
        $translate->setTranslateInline(true);
    }

    /**
     * Return file from customer registration
     * 
     * @return file
     */
    public function getfileAction()
    {
        $customer = $this->_getSession()->getCustomer();
        $file = Mage::getBaseDir('media')."/customer".$customer->getContract();

        if (file_exists($file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            readfile($file);
            exit;
        }
    }

    /**
     * Save customer registration status before closed and after restart
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function customerAdminPrepareSave($observer) 
    {
        $customer = $observer->getCustomer();
        $loadCustomer = Mage::getModel('customer/customer')->load($customer->getId());
        $request = $observer->getRequest();
        $data = $request->getPost();
        $account = $data['account'];
        
        // check if status really changed
        if($loadCustomer->getRegistrationStatus()==$customer->getRegistrationStatus()) {
            return;
        }
        
        // send emails about status changed
        if($account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::CLOSED) {
            $this->_sendTransactionalEmail($observer, Mage::getStoreConfig('templates/email/close_registration'));
        }
        if($account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::VISIT_APPOINTMENT) {
            $this->_sendTransactionalEmail($observer, Mage::getStoreConfig('templates/email/visit_registration'));
        }
        if($account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::CORRECTION) {
            $this->_sendTransactionalEmail($observer, Mage::getStoreConfig('templates/email/correction_file_registration'));
        }
        
        // save status in DB
        if($account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::CLOSED 
           || $account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::STANDBY_PROCESS
           || $account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::PRECLOSED) {
            $a = $loadCustomer->getRegistrationStatus();
            if($account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::CLOSED 
                    && $loadCustomer->getRegistrationStatus()==Lomedic_Registration_Model_System_Config_Source_Status::PRECLOSED) {
                return;
            }
            if(Mage::getStoreConfig('lomedic/registration/closed'.$customer->getId()) 
                    && $loadCustomer->getRegistrationStatus()==Lomedic_Registration_Model_System_Config_Source_Status::STANDBY_PROCESS) {
                return;
            }
            $config = Mage::getModel('core/config');
            $config->saveConfig('lomedic/registration/closed'.$loadCustomer->getId(), $loadCustomer->getRegistrationStatus(), 'default', 0);
        }

        // restore status
        if($account['registration_status']==Lomedic_Registration_Model_System_Config_Source_Status::RESTART) {
            $account['registration_status'] = Mage::getStoreConfig('lomedic/registration/closed'.$customer->getId());
            Mage::app()->getRequest()->setPost('account',$account);
            $customer->setRegistrationStatus(Mage::getStoreConfig('lomedic/registration/closed'.$customer->getId()));
        }
    }

    /**
     * Set file to APPROVE
     * 
     * @param Varien_Event_Observer $observer
     * @param array $data
     * @param string $comments
     */
    private function _setFileApprove($observer,$data,$comments)
    {
        $customer = $observer->getCustomer();
        $customer_id = $customer->getId();
        $count_files = isset($data["count_files"]) ? $data["count_files"] : 0;
        $attributes = $data;

        $data = array();
        foreach($attributes as $key => $attribute) {
            $data[] = array(
                "customer_id" => $customer_id,
                "attribute" => $key,
                "approve" => $attribute,
                "comment" => isset($comments[$key])?$comments[$key]:"",
                "show_comment" => 1
            );
        }

        $customerFiles = Mage::getModel('loregistration/files')->getCollection();
        $customerFiles->addFieldToFilter('customer_id',$customer_id);

        // clear approve files
        foreach($customerFiles as $collection) {
            $collection->delete();
        }

        foreach($data as $key => $value) {
            $model = Mage::getModel('loregistration/files')->setData($value);
            $model->save();
        }

        $customerFiles = Mage::getModel('loregistration/files')->getCollection();
        $customerFiles->addFieldToFilter('customer_id',$customer_id);

        $flagApproveAll = true;
        $countApprove = 0;
        $countApproveFile = 0;
        foreach($customerFiles as $collection) {
            $tmpArr = $collection->getData();

            if($tmpArr["approve"] == 0) {
                $flagApproveAll = false;
            }
            $countApprove++;
            if($tmpArr["approve"] == 1) {
                $countApproveFile++;
            }
        }

        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer')
            ->initDefaultValues();
        $attributes_old = $customerForm->getAttributes();

        if($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::PREREGISTRATION_COMPLETED
            || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::VALIDATION
            || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::VALIDATED
            || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::CORRECTION ) {

            if($flagApproveAll == true && $count_files == 1) { 
                $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::VISIT_APPOINTMENT);
                $customer->setRegistrationStep(4);
            } else {
                if($customer->getRegistrationStatus() != Lomedic_Registration_Model_System_Config_Source_Status::PREREGISTRATION_COMPLETED
                && $countApproveFile > 0){
                    $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::CORRECTION);
                }
            }
        }
    }

    /**
     * Send email to customer
     * 
     * @param Varien_Event_Observer $observer
     * @param int $templateId
     */
    private function _sendTransactionalEmail($observer,$templateId)
    {
        $customer = $observer->getCustomer();
        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);


        $recruter = Mage::helper('loregistration')->getRecruter($customer->getRegistrationRecruter());
        if(!$recruter){
            $recruter = $customer;
        }

        // Set recepient information
        $recepientEmail = $customer->getEmail();
        $recepientName = $customer->getName();

        // Get Store ID
        $store = Mage::app()->getStore()->getId();

        // Set variables that can be used in email template
        $vars = array(
            'customer' => $customer,
            'recruterEmail' => $recruter->getEmail(),
            'recruterName' => $recruter->getName());

        $translate  = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $store);

        $translate->setTranslateInline(true);
    }

    /**
     * Lock registration on 5 min if somebody open it
     * 
     * @param Varien_Event_Observer $observer
     * @return \Lomedic_Registration_Model_Observer
     */
    public function lockRegistration(Varien_Event_Observer $observer) 
    {
        $customer = Mage::getModel('customer/customer')->load(Mage::app()->getRequest()->getParam('id',false));
        if(Mage::helper('loregistration')->getManagerType()!=Mage::getStoreConfig('softeq/managers/manager') || $customer->getRegistrationRecruter()) {
            return $this;
        }
        $session = Mage::getSingleton('admin/session');
        $managerId = $session->getUser()->getId();
        $lockModel = Mage::getModel('loregistration/lock')->load($customer->getId(),'customer_id');
        
        // checkupdate time and lock registration
        if($lockModel->getUpdateTime() && (time()-strtotime($lockModel->getUpdateTime())<=5*60 && $lockModel->getManagerId()!=$managerId)) {
            $returnUrl = Mage::helper('adminhtml')->getUrl('*/dashboard/index');
            $managerName = Mage::helper('loregistration')->getManager($lockModel->getManagerId())->getName();
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loregistration')->__('Manager `%s` views the registration process. Return to <a href="%s">dashboard</a>',$managerName,$returnUrl));
            $request = Mage::app()->getRequest();
            $request->initForward()
                ->setControllerName('adminhtml')
                ->setModuleName('customer')
                ->setActionName('index');

        } else {
            if(!$lockModel->getId()) {
                $lockModel->setCustomerId($customer->getId());
            }
            $lockModel->setUpdateTime(time())
                ->setManagerId($managerId)
                ->save();
        }
    }
    
    /**
     * Save customer after in admin area
     * 
     * @param Varien_Event_Observer $observer
     * @return \Zend_Controller_Response_Abstract
     */
    public function customerAdminSaveAfter($observer) 
    {
        if(!Mage::app()->getRequest()->getParam('back', false)) {
            return Mage::app()->getResponse()->setRedirect( Mage::helper("adminhtml")->getUrl('adminhtml/dashboard/index'));
        }
    }
    
    /**
     * Reset recruiter from users
     * 
     * @param Varien_Event_Observer $observer
     * @return \Lomedic_Registration_Model_Observer
     */
    public function userAdminSaveAfter($observer) 
    {
        if(Mage::app()->getRequest()->getPost('is_active')) {
            return $this;
        }
        $userId = Mage::app()->getRequest()->getPost('user_id');
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addAttributeToFilter('registration_recruter', array('eq' => $userId));
        foreach ($collection as $customer) {
            $customer->setRegistrationRecruter(0)->save();
        }
    }
    
    /**
     * Check status registrations and set automatic statuses
     */
    public function checkStatusRegistration() 
    {
        $timeToStandby       = 3*24*3600;
        $timeToVisitComplete = 24*3600;
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('registration_status')
            ->addAttributeToSelect('visit_date');
            
        $collection->addAttributeToFilter('registration_status', array('in' => array(Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION,Lomedic_Registration_Model_System_Config_Source_Status::NEW_PROCESS,Lomedic_Registration_Model_System_Config_Source_Status::VISITATION)));
        foreach ($collection as $customer) {
            switch ($customer->getRegistrationStatus()) {
                case Lomedic_Registration_Model_System_Config_Source_Status::NEW_REQUEST_FOR_REGISTRATION:
                case Lomedic_Registration_Model_System_Config_Source_Status::NEW_PROCESS:
                    $a = time()-strtotime($customer->getUpdatedAt());
                    $updatedAt = strtotime($customer->getUpdatedAt());
                    if(time()-$updatedAt>=$timeToStandby) {
                        $config = Mage::getModel('core/config');
                        $config->saveConfig('lomedic/registration/closed'.$customer->getId(), $customer->getRegistrationStatus(), 'default', 0);
                        $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::STANDBY_PROCESS)->save();
                    }
                    break;
                case Lomedic_Registration_Model_System_Config_Source_Status::VISITATION:
                    $visitTime = strtotime($customer->getVisitDate());
                    if(time()-$visitTime>=$timeToVisitComplete) {
                        $customer->setRegistrationStatus(Lomedic_Registration_Model_System_Config_Source_Status::VISIT_COMPLETED)->save();
                    }
                    break;
            }
        }
    }

    /**
     * Update address template format
     * 
     * @param  Varien_Event_Observer $observer
     */
    public function updateAddressTemplate(Varien_Event_Observer $observer)
    {
        $eventData = $observer->getData();
        $template  = $eventData['type'];

        if ('html' == $template->getCode()) {
            $format =<<<HTML
{{var firstname}}<br/>
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}}{{depend street_number}} {{var street_number}}{{/depend}}{{depend apartment_number}}/{{var apartment_number}}{{/depend}}<br />{{/if}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br/>
{{depend municipality}}{{var municipality}}<br/>{{/depend}}
{{depend colonia}}{{var colonia}}<br/>{{/depend}}
{{var country}}<br/>
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}<br/>F: {{var fax}}<br/>{{/depend}}
{{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}
HTML;
            $template->setDefaultFormat($format);
        }
    }
}
