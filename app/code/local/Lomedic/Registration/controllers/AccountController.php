<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
require_once 'Mage/Customer/controllers/AccountController.php';

class Lomedic_Registration_AccountController extends Mage_Customer_AccountController
{
    /**
     * Action predispatch
     * Check customer authentication for some actions
     * 
     * @return void
     */
    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        $ExitsopenActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $newOpenAction=array('zip','product','municipality','postcodes','colonia');
        $allActions=array_merge($ExitsopenActions,$newOpenAction);
        $Custompattern = '/^(' . implode('|', $newOpenAction) . ')/i';

        if (preg_match($Custompattern, $action)) {
            $this->getRequest()->setActionName('create');
        }
        parent::preDispatch();

        if ($action != $this->getRequest()->getActionName()) {
            $this->getRequest()->setActionName($action);
        }
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $mypattern = '/^(' . implode('|', $allActions) . ')/i';

        if (!preg_match($mypattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
    
    /**
     * Default customer account page
     */
    public function indexAction()
    {
        if($this->_getSession()->getCustomer()->getRegistrationStatus()!=Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
            $step = $this->_getSession()->getCustomer()->getRegistrationStep();
            if(!$step) {
               $step = 1;
            }
            return $this->_forward('step'.$step);
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }

    /**
     * Login post action
     */
    public function loginPostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);

                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                     Mage::log($e->getMessage()); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }
        $this->_loginPostRedirect();
    }
    
    /**
     * 
     * @return typeCheck registration status after login
     */
    protected function _loginPostRedirect() 
    {
        if($this->_getSession()->getCustomer()->getRegistrationStatus()==Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
            $partner = Mage::getModel('marketplace/userprofile')->isPartner($this->_getSession()->getCustomerId());
            return $this->_redirect($partner?'marketplace/marketplaceaccount/mydashboard':'customer/account/');
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer)
    {
        $errors = array();
        $request = $this->getRequest();
        $streetData = array_filter($request->getPost('street'));
        if ($request->getPost('create_address') && count($streetData)>0 && $request->getPost('postcode')) {
            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);

        if(isset($customerData["email"]) && !$customer->getId()){
            $emailsComp = Mage::getModel('loregistration/usersCompany')->getCollection()
                ->addFieldToFilter('email',array('eq' => $customerData["email"]));
            if(count($emailsComp)>0){
                $errors[] = $this->__('There is already an account with this email address.');
            }
        }
        if($request->getPost('taxvat',false)) {
            $activity = array(3,4); // seller
            if(in_array($request->getPost('activity'),array(1,2))) {
                $activity = array(1,2); // buyer
            }
            $taxCollection = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToFilter('taxvat',array('eq' => strtoupper($request->getPost('taxvat'))))
                ->addAttributeToFilter('activity',array('in' => $activity));
            if($customer->getId()) {
                $taxCollection->getSelect()->where('e.entity_id !=?',$customer->getId());
            }
            if($taxCollection->getSize()>0){
                $errors[] = $this->__('The R.F.C. you provided is already registered! For clarification please contact one of our consultants through our chat or call 01 800 xxx ####');
            }
        }

        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setPasswordConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }
        return $errors;
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }
    
        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
                $customer->setTaxvat(strtoupper($this->getRequest()->getPost('taxvat')));
                $customer->save();
                $this->_successProcessRegistration($customer);
                $customer->cleanPasswordsValidationData();
                $this->_dispatchRegisterSuccess($customer);
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a class="fancybox" href="%s">click here</a> to get your password and access your account.', '#fancy');
                $session->setEscapeMessages(false);
            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save the customer.'));
        }
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
        $this->_redirectError($errUrl);
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(Mage_Customer_Model_Customer $customer)
    {
        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
            $customer->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId()
            );
            $customerHelper = $this->_getHelper('customer');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
            $url = $this->_getUrl('*/*/step1', array('_secure' => true));
        } else {
            $session->setCustomerAsLoggedIn($customer);
            $url = $this->_welcomeCustomer($customer);
        }
        $this->_redirectSuccess($url);
        return $this;
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        Mage::getSingleton('core/session')->addSuccess(
            $this->__('Thank you! We sent a letter to you with instructions to continue your registration! Please follow explained instructions.')
        );
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType =  $this->_getHelper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
            }
            Mage::getSingleton('core/session')->addSuccess($userPrompt);
        }


        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = $this->_getUrl('*/*/step1', array('_secure' => true));
        return $successUrl;
    }
    
    /**
     * Renderer First step
     */
    public function step1Action() 
    {
        if($this->_getSession()->getCustomer()->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
            return $this->_redirect('*/*/');
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Step 1 Edit Customer Data'));
        $this->renderLayout();
    }
    
    /**
     * Renderer Second step
     */
    public function step2Action() 
    {
        if($this->_getSession()->getCustomer()->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
            return $this->_redirect('*/*/');
        }

        if($this->_getSession()->getCustomer()->getRegistrationStep()<2) {
            return $this->_forward('step'.$this->_getSession()->getCustomer()->getRegistrationStep());
        }
        $this->loadLayout(); 
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Step 2 Edit Customer Data'));
        $this->renderLayout();
    }
    
    /**
     * Post data for second step
     */
    public function step2PostAction() 
    {
        $session = $this->_getSession();

        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/step2', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }

        $data = $this->getRequest()->getPost();
        $addresses =  $data['address'];
        unset($data['address']);
        if($data['account_type']==Lomedic_Registration_Model_System_Config_Source_Accounttype::ACCOUNT_TYPE_NATURAL) {
            $data['association_number'] = '';
            $data['association_number_before'] = '';
            $data['is_association_number_modified'] = '';
        }
        if(!$data['is_association_number_modified']) {
            $data['association_number_mod'] = '';
            $data['association_number_mod_before'] = '';
        }
        if(!$data['handle_controlled_medicines_or_products_of_biological_origin']) {
            $data['health_license_number'] = '';
        }
        
        if($session->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
        } else {
            $customer = $session->getCustomer();
        }
        if($customer->getRegistrationStatus()>Lomedic_Registration_Model_System_Config_Source_Status::VALIDATED) {
            $data['registration_status'] = $customer->getRegistrationStatus();
        }
        $customer->addData($data);
        try {
            foreach ($addresses as $key=>$address) {
                if(!$address['postcode'] || !$address['country_id'] || !$address['street'][0]) continue;
                
                $address['lastname'] = $customer->getCompany();
                $addressModel = Mage::getModel('customer/address');
                if(isset($address['id']) && $address['id']) {
                    $addressModel->load($address['id']);
                }
                $addressModel->addData($address)
                        ->setParentId($customer->getId())
                        ->setEntityTypeId(2)
                        ->setSaveInAddressBook('1');
                $result = $addressModel->save();
                if($key=='billing') {
                    $customer->setDefaultBilling($result->getId());
                }
            }
            $customer->save();
            $session->setEscapeMessages(false);
            Mage::getSingleton('core/session')->addSuccess($this->__('Data saved'));
            return $this->_redirect('*/*/step3');
            
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('Cannot save the customer.%s',$e->getMessage()));
            return $this->_redirect('*/*/step2');
        }
    }

    /**
     * Renderer and save step 3
     */
    public function step3Action() 
    {
        if($this->_getSession()->getCustomer()->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED){
            return $this->_redirect('*/*/');
        }

        if($this->_getSession()->getCustomer()->getRegistrationStep()<3) {
            return $this->_forward('step'.$this->_getSession()->getCustomer()->getRegistrationStep());
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Step 3 Edit Customer Data'));
        $this->renderLayout();
    }
    
    /**
     * Post data for 3 step
     */
    public function step3PostAction() 
    {
        $session = $this->_getSession();
        $customer = $session->getCustomer();

        if($this->getRequest()->isPost()){
           try {
                $errors = array();

                if(empty($_FILES)){
                    $errors[] = "Not required files";
                }

                $errTmp = 0;
                foreach($_FILES as $key => $value){
                      
                    $_FILES[$key]['name'] = strtolower($value['name']);
                    if(!empty($value['tmp_name'])){
                        $fileType = substr($value['name'], strrpos($value['name'], "."));
                        if(is_string($fileType) && $fileType != ".pdf") {
                            $errTmp = 2;
                        }

                        if($key == "association_registry" && $value["size"] > 40*1024*1024) {
                            $errors[] = "Please select file ".$key." less than 40Mb";
                        } elseif($key != "association_registry" && $value["size"] > 10*1024*1024) {
                            $errors[] = "Please select file ".$key." less than 10Mb";
                        }
                    }
                }

                if($errTmp == 2){
                    $errors[] = "Please select only .pdf files";
                }

                $customerForm = $this->_getCustomerForm($customer);
                $customerData = $customerForm->extractData($this->getRequest());
                $customerForm->compactData($customerData);
                $customerErrors = $customer->validate();

                if (is_array($customerErrors)) {
                    $errors = array_merge($customerErrors, $errors);
                }

                if (empty($errors)) {
                    $customer->save();
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('customer')->__("Your form is processed, after passing the test you will be notified"));
                    $this->sendTransactionalEmail(Mage::getStoreConfig('templates/email/lomedicregistration_files_to_check'));
                } else {
                    foreach ($errors as $errorMessage) {
                        Mage::getSingleton('core/session')->addError($errorMessage);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $message = $e->getMessage();
                Mage::getSingleton('core/session')->addError($message);
            } catch (Exception $e) {
                $message = $e->getMessage();
                Mage::getSingleton('core/session')->addError($message);
            }
        }
        return $this->_redirect('*/*/step3');
    }

    /**
     * Delete file to customer
     */
    public function step3DeleteFileAction() 
    {
        $session = $this->_getSession();
        $customer = $session->getCustomer();

        if($this->getRequest()->isPost()){
            $id = $this->getRequest()->getParams("id");
            $customer->setData($id["id"],"");
            $customer->save();
            return true;
        }
        return false;
    }

    /**
     * Delete file comment to customer
     */
    public function step3DeleteCommentAction() 
    {
        $session = $this->_getSession();
        $customer = $session->getCustomer();

        if($this->getRequest()->isPost()){
            $id = $this->getRequest()->getParams("id");

            $customerFiles = Mage::getModel('loregistration/files')->getCollection();
            $customerFiles->addFieldToFilter('customer_id',$customer->getId());

            foreach($customerFiles as $collection) {
                $tmpArr = $collection->getData();
                if($tmpArr["attribute"] == $id["id"]){
                    $collection->setData("show_comment",0);
                    $collection->save();
                    break;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Renderer step 4
     */
    public function step4Action() 
    {
        if($this->_getSession()->getCustomer()->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED){
            return $this->_redirect('*/*/');
        }

        if($this->_getSession()->getCustomer()->getRegistrationStep()<4) {
            return $this->_forward('step'.$this->_getSession()->getCustomer()->getRegistrationStep());
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Step 4 Visit'));
        $this->renderLayout();
    }

    public function step4PostAction() {

    }

    /**
     * Renderer step 5
     */
    public function step5Action() 
    {
        if($this->_getSession()->getCustomer()->getRegistrationStep()<5) {
            return $this->_forward('step'.$this->_getSession()->getCustomer()->getRegistrationStep());
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Step 5 Contact'));
        $this->renderLayout();
    }

    /*
     * Post data for 5 step
     */
    public function step5PostAction() 
    {
    }

    /**
     * Get file action
     */
    public function getfileAction()
    {
        echo 555; exit;
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
     * Send email for restore password
     */
    private function sendTransactionalEmailRestorePassword($set,$templateId)
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
    
    /*
     * Send email to customer
     * @param int $id
     * @return void
     * */
    public function sendTransactionalEmail($templateId)
    {
        $customer = $this->_getSession()->getCustomer();

        // Set sender information
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);

        // Set recepient information
        $recepientEmail = $customer->getEmail();
        $recepientName = $customer->getName();

        // Get Store ID
        $store = Mage::app()->getStore()->getId();

        // Set variables that can be used in email template
        $vars = array('customerName' => $customer->getEmail(),
            'customerEmail' => $customer->getName());

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($recepientEmail, $recepientName);
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $store));
        $mailer->setStoreId($store);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams($vars);
        $mailer->send();
    }


    /**
     * Remove parazite code
     * @param string|array $item
     * @return string
     */
    public function removeHTML($item)  {
        if(is_array($item)) {
            array_map(array($this, 'removeHTML'),$item);
        }
        $item = str_replace('<br>', '', $item);
        $item = str_replace('\r\n', '', $item);
        $item = str_replace('\r', '', $item);
        $item = str_replace('\n', '', $item);
        return $item;
    }
    
    /**
     * Save first step data to customer and address
     */
    public function step1PostAction() {

        $session = $this->_getSession();

        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/index', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }

        $customerData = $this->getRequest()->getPost();
        array_map(array($this, 'removeHTML'),$customerData);
        $addressData = $customerData['address'];
        $addressData['lastname'] = $customerData['company'];
        unset($customerData['address']);
        $errors = array();
        if($session->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
        } else {
            $customer = $session->getCustomer();
        }

        $customerData['registration_status'] = $customer->getRegistrationStatus();
        $customerData['taxvat'] = strtoupper($customerData['taxvat']);
        if($customer->getRegistrationStatus()>Lomedic_Registration_Model_System_Config_Source_Status::VALIDATED) {
            $customerData['registration_status'] = $customer->getRegistrationStatus();
        }
        if(isset($customerData["activity"]) && isset($customerData["sector"])){

            $grop = Mage::getModel('customer/group')->getCollection();
            if($customerData["activity"] > 2){
                if($customerData["sector"] == 1){
                    $grop->addFieldToFilter('customer_group_code',array('eq' => 'Seller/Private sector'));
                }elseif($customerData["sector"] == 2){
                    $grop->addFieldToFilter('customer_group_code',array('eq' => 'Seller/Government sector'));
                }
            }else{
                if($customerData["sector"] == 1){
                    $grop->addFieldToFilter('customer_group_code',array('eq' => 'Buyer/Private sector'));
                }elseif($customerData["sector"] == 2){
                    $grop->addFieldToFilter('customer_group_code',array('eq' => 'Buyer/Government sector'));
                }
            }
            $groupData = $grop->getData();
            $customerData["group_id"] = $groupData[0]["customer_group_id"];
        }

        try {
            // validate address data
            if($addressData['id']) {
                $address = $this->_getModel('customer/address');
                $addressForm = $this->_getModel('customer/form');
                $addressForm->setFormCode('customer_edit_address')
                    ->setEntity($address);
                $addressErrors = $addressForm->validateData($addressData);
                if(!is_array($addressErrors)) {
                    $address = Mage::getModel('customer/address')->load($addressData['id']);
                    $address->addData($addressData)
                            ->setSaveInAddressBook('1')
                            ->save();
                } else {
                    $errors = array_merge($errors,$addressErrors);
                }
            }
            //validate customer data 
            $customerForm = $this->_getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

//            $customerErrors = $customerForm->validateData($customerData);
            $customerErrors = $this->_getCustomerErrors($customer);
            if(!is_array($customerErrors)) {
                $customer->setData($customerData);
            } else {
                $errors = array_merge($errors,$customerErrors);
            }
            if (empty($errors)) {
                if($address) {
                    $customer->setDefaultBilling($address->getId())
                        ->setDefaultShipping($address->getId());
                }
                $customer->setId($session->getCustomerId())->save();
                $session->setEscapeMessages(false);
                Mage::getSingleton('core/session')->addSuccess($this->__('Data saved'));
                return $this->_redirect('*/*/step2');
            } else {
                $this->_addCoreSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                $session->setEscapeMessages(false);
            } else {
                $message = $e->getMessage();
            }
            Mage::getSingleton('core/session')->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            Mage::getSingleton('core/session')->addError($this->__('Cannot save the customer.%s',$e->getMessage()));
        }

        $errUrl = $this->_getUrl('*/*/step1', array('_secure' => true));
        $this->_redirectError($errUrl);
    }
    
    /**
     * Add session error method
     *
     * @param string|array $errors
     */
    protected function _addCoreSessionError($errors)
    {
        $session = Mage::getSingleton('core/session');
        $session->setCustomerFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($errorMessage);
            }
        } else {
            $session->addError($this->__('Invalid customer data'));
        }
    }
    
     /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);

                echo json_encode(array("success"=> false,"mes"=>$this->__('Verify the information you have entered.')));
                exit;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {

                    $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                    $password = substr(str_shuffle($letters), 0, 7);
                    $customerId = (int) $customer->getId();

                    $customer = $this->_getModel('customer/customer')->load($customerId);

                    $customer->setData('password', $password);
                    $customer->setData('password_confirmation', $password);

                    $customer->setRpToken(null);
                    $customer->setRpTokenCreatedAt(null);
                    $customer->save();

                    $storeId =  Mage::app()->getStore()->getId();

                    $logo_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/rwd/lomedic/".Mage::getStoreConfig('design/header/logo_src');

                    $this->_sendEmailTemplatePas('templates/email/loregistration_password_new', 'customer/password/forgot_email_identity',
                        array('customer' => $customer /*, "logo_url" => $logo_url, $storeId*/));

                } catch (Exception $exception) {

                    var_dump($exception);
                    exit();
                    echo json_encode(array("success"=> false,"mes"=>$this->__('Verify the information you have entered.')));
                    exit;
                }
            } else {
                $customer = Mage::getModel('loregistration/usersCompany')->load($email,'email');
                if($customer->getId()) {
                    try {
                        $set = $customer->getData();
                        $length = 8;
                        $set["password"]  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length).rand(1,999);
                        if(isset($set["password"]) && !empty($set["password"])){
                            $this->sendTransactionalEmailRestorePassword($set,Mage::getStoreConfig('templates/email/loregistration_user_company'));
                            $password = sha1($set["password"]);
                        }

                        if(isset($set["password"]) && !empty($set["password"])){
                            $customer->setPassword($password);
                        }
                        $customer->save();
                    }catch (Exception $exception) {
                        echo json_encode(array("success"=> false,"mes"=>$this->__('Verify the information you have entered. %s',$exception->getMessage())));
                        exit;
                    }
                } else {
                    echo json_encode(array("success"=> false,"mes"=>$this->__('Verify the information you have entered.')));
                    exit;
                }
            }

            echo json_encode(array("success"=> true,"mes"=>$this->__('We sent an e-mail with instructions for password recovery.')));
            exit;
        } else {
            echo json_encode(array("success"=> false,"mes"=>$this->__('Please enter your email.')));
            exit;
        }

    }

    /**
     *  Send email by action
     * 
     * @param string $template
     * @param string $sender
     * @param array $templateParams
     * @param int $storeId
     */
    private function _sendEmailTemplatePas($template, $sender, $templateParams = array(), $storeId = null) 
    {
        try {

            /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($templateParams["customer"]->getEmail(), $templateParams["customer"]->getName());
            $mailer->addEmailInfo($emailInfo);

            // Set all required params and send emails
            $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
            $mailer->setStoreId($storeId);
            $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
            $mailer->setTemplateParams($templateParams);
            $mailer->send();
            return $this;
        }catch (Mage_Core_Exception $e) {
            var_dump($e);
            exit();
        } catch (Exception $e) {
            var_dump($e);
            exit();
        }
    }

    /**
     * Get Zip Code html code
     * @return string
     */
    public function zipAction() 
    {
        $state = (string) $this->getRequest()->getPost('state');
        if($state) {
            $testmodel = Mage::getModel('loregistration/zip')
                ->getCollection()
                ->addFieldToSelect('state')
                ->addFieldToSelect('zip_code')
                ->addFieldToFilter('state', $state);
            $tmpArr = array();
            foreach($testmodel as $val) {
                $data = $val->getData();
                $tmpArr[] = $data["zip_code"];
            }
            echo json_encode($tmpArr);
            exit();
        }else{
            echo "";
            exit();
        }
        exit();
    }

    /**
     * Get Zip Code html code
     * @return string
     */
    public function productAction() {
        $post = $this->getRequest()->getPost();

        if($post){
            $arr = array(
                'code',
                'generic_name',
                'description',
                'presentation',
                'qty',
                'category',
                'level',
            );

            $result = array();
            $i = $post["id"];

            if($post["id"] == 2){
                $collection= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $collection->addFieldToSelect("code");
                $collection->addFieldToSelect('entity_id');
                $collection->addFieldToFilter("generic_name",array('eq'=>$post["data"]["generic_name"]));
                $collection->addFieldToFilter('is_remove',array('eq'=>0));
                $collection->getSelect()->group("code");

                foreach($collection as $k => $val){
                    $tmpArr =$val->getData();
                    $result["code"][] = array(
                        "name" => $tmpArr["code"],
                        "value" => $tmpArr["entity_id"]
                    );

                    $result['code'] = array_map("unserialize", array_unique(array_map("serialize", $result['code'])));
                }
                $post["data"]["code"] = $result["code"][0]['name'];
            }

            for($i;$i<7; $i++){
                $collection= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $collection->addFieldToSelect($arr[$i]);
                $collection->addFieldToSelect('entity_id');
                foreach($post["data"] as $key => $value){
                    if($key == 'description_a'){
                        $collection->addFieldToFilter('description',array('eq'=>$value));
                    }else{
                        $collection->addFieldToFilter($key,array('eq'=>$value));
                    }
                }
                $collection->addFieldToFilter('is_remove',array('eq'=>0));
              
                foreach($collection as $k => $val){
                    $tmpArr =$val->getData();

                    $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                    $coll->addFieldToSelect($arr[$i]);
                    $coll->addFieldToSelect('entity_id');
                    $coll->addFieldToFilter('is_remove',array('eq'=>0));
                    $coll->getSelect()->group($arr[$i]);

                    $tmpTwoArr = array();
                    foreach($coll as $kkkk => $vvv){
                        $tmp = $vvv->getData();
                        if(strcasecmp($tmp[$arr[$i]],$tmpArr[$arr[$i]]) == 0){
                            $tmpTwoArr = $tmp;
                        }
                    }

                    if($arr[$i] == "description"){
                        $result["description_a"][] = array(
                            "name" => $tmpArr[$arr[$i]],
                            "value" => $tmpTwoArr["entity_id"]
                        );
                        $result['description_a'] = array_map("unserialize", array_unique(array_map("serialize", $result['description_a'])));
                    }else{
                        $result[$arr[$i]][] = array(
                            "name" => $tmpArr[$arr[$i]],
                            "value" => $tmpTwoArr["entity_id"]
                        );
                    }

                    $result[$arr[$i]] = array_map("unserialize", array_unique(array_map("serialize", $result[$arr[$i]])));
                }
            }

            $result = array_map("unserialize", array_unique(array_map("serialize", $result)));
            echo json_encode($result);
        }
        exit();
    }

    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        $this->_getSession()->logout()
            ->renewSession();

        Mage::getSingleton('customer/session')->setUserCompany(null);
        Mage::getSingleton('customer/session')->setManageProfileSettings(null);
        Mage::getSingleton('customer/session')->setManageSales(null);
        Mage::getSingleton('customer/session')->setManageBatches(null);
        Mage::getSingleton('customer/session')->setManageProducts(null);

        $this->_redirect('/');
    }
    
    public function getZipCollection($selectRestrict=array()) {
        return Mage::helper('loregistration')->getZipCollection($selectRestrict);
    }
    
    public function municipalityAction() {
        $result = array('');
        
        $collection = $this->getZipCollection(array('municipality'));
        foreach($collection as $item) {
            $result[] = $item->getMunicipality();
        }
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($jsonData);
    }
    
    public function coloniaAction() {
        $result = array('');
        $collection = $this->getZipCollection(array('colonia'));
        foreach($collection as $item) {
            $result[] = $item->getColonia();
        }
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($jsonData);
    }
    
    public function postcodesAction() {
        $result = array();
        $collection = $this->getZipCollection(array('zip_code'));
        foreach($collection as $item) {
            $result[] = $item->getZipCode();
        }
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($jsonData);
    }
}
