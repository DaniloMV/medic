<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Adminhtml_UsersCompanyController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('visits')
            ->_title($this->__('Users Company Action'));

        $this->_addContent($this->getLayout()->createBlock('loregistration/adminhtml_usersCompany'));

        $this->renderLayout();
    }

    /**
     * Get grid html for AJAX requests
     */
    public function gridAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('visits');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('loregistration/adminhtml_usersCompany/grid')->toHtml()
        );
    }

    /**
     * Create new user form
     */
    public function newAction()
    {
        $set=$this->getRequest()->getParams();
        $this->loadLayout()
            ->_setActiveMenu('visits');
        $this->renderLayout();
    }

    /**
     * Create new user
     */
    public function newpostAction()
    {
        $set=$this->getRequest()->getParams();
        if($set){

            if(!isset($set["name"]) || empty($set["name"])){
                Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Name required'));
                $this->_redirect('adminhtml/usersCompany/new/id/'.$set["id"]);
            }elseif((!isset($set["auto"]) || empty($set["auto"])) && (!isset($set["password"]) || empty($set["password"]))){
                Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Password required'));
                $this->_redirect('adminhtml/usersCompany/new/id/'.$set["id"]);
            }elseif(!isset($set["email"]) || empty($set["email"])){
                Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Email required'));
                $this->_redirect('adminhtml/usersCompany/new/id/'.$set["id"]);
            }else{

                $emails = Mage::getModel('customer/customer')->getCollection()
                    ->addFieldToFilter('email',array('eq' => $set["email"]));

                $emailsComp = Mage::getModel('loregistration/usersCompany')->getCollection()
                    ->addFieldToFilter('email',array('eq' => $set["email"]));

                if(count($emails)>0 || count($emailsComp)>0){
                    Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('User with this email already exists'));
                    Mage::getSingleton('core/session')->addData(array('new_us'=>$set));
                    $this->_redirect('adminhtml/usersCompany/new/id/'.$set["id"]);
                }else{
                    if(isset($set["auto"]) && !empty($set["auto"])){
                        $length = 8;
                        $set["password"]  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length).rand(1,999);
                    }

                    $this->sendTransactionalEmail($set,Mage::getStoreConfig('templates/email/loregistration_user_company'));

                    $set["password"] = sha1($set["password"]);
                    $collection = Mage::getModel('loregistration/usersCompany')->setData($set);
                    $collection->save();

                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('User is saved successfully. E-mail with credentials is sent to the user.'));
                    foreach($set["privilege"] as $priv){
                        $col = Mage::getModel('loregistration/usersCompanyPrivileges')
                            ->setPrivilegeId($priv)
                            ->setUserCompanyId($collection->getUserCompanyId());
                        $col->save();
                    }

                    Mage::getSingleton('core/session')->addData(array('new_us'=>array()));
                    $this->_redirect('adminhtml/usersCompany/index/id/'.$set["id"]);
                }
            }
        }
    }

    /**
     * Get edit user form
     */
    public function editAction()
    {
        $set=$this->getRequest()->getParams();
        $this->loadLayout()
            ->_setActiveMenu('visits');
        $this->renderLayout();
    }

    /**
     * Save changes for user
     */
    public function editpostAction()
    {
        $set=$this->getRequest()->getParams();
        if($set){
            if(!isset($set["name"]) || empty($set["name"])){
                Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Name required'));
                $this->_redirect('adminhtml/usersCompany/edit/id/'.$set["id"]);
            }elseif(!isset($set["email"]) || empty($set["email"])){
                Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Email required'));
                $this->_redirect('adminhtml/usersCompany/edit/id/'.$set["id"]);
            }else{
                $emails = Mage::getModel('customer/customer')->getCollection()
                    ->addFieldToFilter('email',array('eq' => $set["email"]));

                $emailsComp = Mage::getModel('loregistration/usersCompany')->getCollection()
                    ->addFieldToFilter('user_company_id',array('neq' => $set["id"]))
                    ->addFieldToFilter('email',array('eq' => $set["email"]));

                if(count($emails)>0 || count($emailsComp)>0){
                    Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('User with this email already exists'));
                    $this->_redirect('adminhtml/usersCompany/edit/id/'.$set["id"]);
                }else {
                    if(isset($set["auto"]) && !empty($set["auto"])){
                        $length = 8;
                        $set["password"]  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length).rand(1,999);
                    }
                    
                    if(isset($set["password"]) && !empty($set["password"])){
                        $this->sendTransactionalEmail($set,Mage::getStoreConfig('templates/email/loregistration_user_company'));
                        $password = sha1($set["password"]);
                    }

                    $collection = Mage::getModel('loregistration/usersCompany')->load($set["id"]);
                    $collection->setName($set["name"]);
                    $collection->setSurName($set["sur_name"]);
                    $collection->setUserName($set["user_name"]);

                    if(isset($set["password"]) && !empty($set["password"])){
                        $collection->setPassword($password);
                    }
                    $collection->save();

                    $priviliges = Mage::getModel('loregistration/usersCompanyPrivileges')->getCollection()
                        ->addFieldToFilter('user_company_id', array('eq' => $collection->getUserCompanyId()));

                    foreach ($priviliges as $priv) {
                        $priv->delete();
                    }

                    foreach ($set["privilege"] as $priv) {
                        $col = Mage::getModel('loregistration/usersCompanyPrivileges')
                            ->setPrivilegeId($priv)
                            ->setUserCompanyId($collection->getUserCompanyId());
                        $col->save();
                    }

                    $this->_redirect('adminhtml/usersCompany/index/id/' . $collection->getCustomerId());
                }
            }
        }
    }

    /**
     * Block user
     */
    public function blockAction()
    {
        $id = $this->getRequest()->getParam("id");
        $active = $this->getRequest()->getParam("active");

        if(!isset($active) || empty($active)){
            $active = 1;
        }else{
            $active = 0;
        }

        if($id){
            $customer = Mage::getModel('loregistration/usersCompany')->load($id);
            $customer_id = $customer->getUserCompanyId();
            $customer->setIsActive($active);
            $customer->save();
            echo $customer_id;
        }else{
            echo 0;
        }
    }

    /**
     * Delete user
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam("id");

        if($id){
            $collection = Mage::getModel('loregistration/usersCompany')->load($id);
            $customer_id = $collection->getUserCompanyId();
            $collection->delete();
            echo $customer_id;
        }else{
            echo 0;
        }
    }

    /*
     * Send email
     */
    private function sendTransactionalEmail($set,$templateId)
    {
        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);

        // Set recepient information
        $recepientEmail = $set["email"];
        $recepientName = $set["name"];

        // Get Store ID
        $store = Mage::app()->getStore()->getId();

        $std = new stdClass();
        $std->name = $set["name"];
        $std->email = $set["email"];
        $std->password = $set["password"];

        // Set variables that can be used in email template
        $vars = array(
            'customer' => $std,
            'name' => $set["name"],
            'password' => $set["password"]);

        $translate  = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $store);

        $translate->setTranslateInline(true);
    }

}
