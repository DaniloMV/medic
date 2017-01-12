<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'CustomerController.php');

class Lomedic_Registration_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
    public function validateAction() {
        $errors = array();
        $response       = new Varien_Object();
        $response->setError(0);
        $post = $this->getRequest()->getPost();
        $strMinLenTax = $post['taxmask'];
        if($strMinLenTax>strlen($post['account']['taxvat'])){
            $errors[] = Mage::helper('adminhtml')->__('Please check Social security number (Tax ID)');
        }
        if (count($errors)) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }
            $response->setError(1);
        } 
        if ($response->getError()) {
            $this->_initLayoutMessages('adminhtml/session');
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        return $this->getResponse()->setBody($response->toJson());
    }
    
    /**
     * Save customer action
     */
    public function saveAction() {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->ignoreInvisible(false);

            $formData = $customerForm->extractData($this->getRequest(), 'account');

            // Handle 'disable auto_group_change' attribute
            if (isset($formData['disable_auto_group_change'])) {
                $formData['disable_auto_group_change'] = empty($formData['disable_auto_group_change']) ? '0' : '1';
            }

            $errors = true;
            if ($customer->getId()&& !empty($data['account']['new_password'])
                && Mage::helper('customer')->getIsRequireAdminUserToChangeUserPassword()
            ) {
                //Validate current admin password
                if (isset($data['account']['current_password'])) {
                    $currentPassword = $data['account']['current_password'];
                } else {
                    $currentPassword = null;
                }
                unset($data['account']['current_password']);
                $errors = $this->_validateCurrentPassword($currentPassword);
            }

            if (!is_array($errors)) {
//  DISABLE VALIDATION
//  $errors = $customerForm->validateData($formData);
            }

            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }

            $customerForm->compactData($formData);

            // Unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();
            if (!empty($data['address'])) {
                /** @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);

                foreach (array_keys($data['address']) as $index) {
                    $address = $customer->getAddressItemById($index);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                    }

                    $requestScope = sprintf('address/%s', $index);
                    $formData = $addressForm->setEntity($address)
                        ->extractData($this->getRequest(), $requestScope);

                    // Set default billing and shipping flags to address
                    $isDefaultBilling = isset($data['account']['default_billing'])
                        && $data['account']['default_billing'] == $index;
                    $address->setIsDefaultBilling($isDefaultBilling);
                    $isDefaultShipping = isset($data['account']['default_shipping'])
                        && $data['account']['default_shipping'] == $index;
                    $address->setIsDefaultShipping($isDefaultShipping);

// DISABLE VALIDATION
//  $errors = $addressForm->validateData($formData);
                    if ($errors !== true) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }
                        $this->_getSession()->setCustomerData($data);
                        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array(
                            'id' => $customer->getId())
                        ));
                        return;
                    }

                    $addressForm->compactData($formData);

                    // Set post_index for detect default billing and shipping addresses
                    $address->setPostIndex($index);

                    if ($address->getId()) {
                        $modifiedAddresses[] = $address->getId();
                    } else {
                        $customer->addAddress($address);
                    }
                }
            }

            // Default billing and shipping
            if (isset($data['account']['default_billing'])) {
                $customer->setData('default_billing', $data['account']['default_billing']);
            }
            if (isset($data['account']['default_shipping'])) {
                $customer->setData('default_shipping', $data['account']['default_shipping']);
            }
            if (isset($data['account']['confirmation'])) {
                $customer->setData('confirmation', $data['account']['confirmation']);
            }

            // Mark not modified customer addresses for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/newsletter')
                && !$customer->getConfirmation()
            ) {
                $customer->setIsSubscribed(isset($data['subscription']));
            }

            if (isset($data['account']['sendemail_store_id'])) {
                $customer->setSendemailStoreId($data['account']['sendemail_store_id']);
            }

            $isNewCustomer = $customer->isObjectNew();
            try {
                $sendPassToEmail = false;
                // Force new customer confirmation
                if ($isNewCustomer) {
                    $customer->setPassword($data['account']['password']);
                    $customer->setForceConfirmed(true);
                    if ($customer->getPassword() == 'auto') {
                        $sendPassToEmail = true;
                        $customer->setPassword($customer->generatePassword());
                    }
                }

                Mage::dispatchEvent('adminhtml_customer_prepare_save', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                $customer->save();

                // Send welcome email
                if ($customer->getWebsiteId() && (isset($data['account']['sendemail']) || $sendPassToEmail)) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    } elseif ((!$customer->getConfirmation())) {
                        // Confirm not confirmed customer
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                if (!empty($data['account']['new_password'])) {
                    $newPassword = $data['account']['new_password'];
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The customer has been saved.')
                );
                Mage::dispatchEvent('adminhtml_customer_save_after', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $customer->getId(),
                        '_current' => true
                    ));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the customer.%s',$e->getMessage()));
                $this->_getSession()->setCustomerData($data);
                return $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                exit;
            }
            if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
                echo $this->getResponse()->setRedirect($this->getUrl('*/dashboard/'));
            } else {
                echo $this->getResponse()->setRedirect($this->getUrl('*/customer/'));
            }
            exit();
            return;
        }
        if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
            echo $this->getResponse()->setRedirect($this->getUrl('*/dashboard/'));
        } else {
            echo $this->getResponse()->setRedirect($this->getUrl('*/customer/'));
        }
        exit();
        return;
    }
    
    public function indexAction()
    {
        if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
          
            echo $this->getResponse()->setRedirect($this->getUrl('*/dashboard/'));
            exit();
            return;
        }
        return parent::indexAction();
    }
    
    public function getZipCollection($selectRestrict=array()) {
        return Mage::helper('loregistration')->getZipCollection($selectRestrict);
    }
    
    public function municipalityAction() 
    {
        $result = array('');
        $collection = $this->getZipCollection(array('municipality'));
        foreach($collection as $item) {
            $result[] = $item->getMunicipality();
        }
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($jsonData);
    }
    
    public function coloniaAction() 
    {
        $result = array('');
        $collection = $this->getZipCollection(array('colonia'));
        foreach($collection as $item) {
            $result[] = $item->getColonia();
        }
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($jsonData);
    }
    
    public function postcodesAction() 
    {
        $result = array();
        $collection = $this->getZipCollection(array('zip_code'));
		if($collection->getSize()>1) {
			$result[] = '';
		}
        foreach($collection as $item) {
		    $result[] = $item->getZipCode();
        }
		
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($jsonData);
    }
    
    public function saveandinformAction()
    {
		$doc_num = 14;
		$data = $this->getRequest()->getPost();
		//echo "<pre>"; print_r($data); exit;
		if(isset($data['account']['health_license_number']) && $data['account']['health_license_number']) {
			$doc_num += 1;
		}if($data['account']['account_type'] && isset($data['account']['association_number_mod']) && $data['account']['association_number_mod']) {
			$doc_num += 2;
		}
		$this->getRequest()->setParam('back','edit')->setParam('tab','customer_info_tabs_step3');
        if(count($data['approve'])<$doc_num) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__("Client can't be informed. You need to validate all the documents."));
			return $this->_forward('save',null,null,array('back'=>true));
        }
        $approved = 0;
        foreach($data['approve'] as $key=>$value) {
            if($value=='1') {
                $approved++;
            }
        }
        if($approved<$doc_num) {
            $this->sendTransactionalEmail('file_nonvalidated_info');
        } else {
            $this->sendTransactionalEmail('file_validated_info');
        }
        return $this->_forward('save');
    }
    
    /**
     * Send email to customer
     * 
     * @param Varien_Event_Observer $observer
     * @param int $templateId
     */
    public function sendTransactionalEmail($templateId)
    {
        $customerId = $this->getRequest()->getPost('customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        
        // Set sender information
        $senderName = Mage::getStoreConfig('customer/create_account/email_identity');
        $senderEmail = Mage::getStoreConfig('customer/create_account/email_domain');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);

        // Set recepient information
        $recepientEmail = $customer->getEmail();
        $recepientName = $customer->getName();

        // Get Store ID
        $store = Mage::app()->getStore()->getId();

        // Set variables that can be used in email template
        $vars = array(
            'customer'      => $customer,
            'customerName'  => $customer->getName(),
            'customerEmail' => $customer->getEmail()
        );

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($recepientEmail, $recepientName);
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender($sender);
        $mailer->setStoreId($store);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams($vars);
        $result = $mailer->send();
    }
}
