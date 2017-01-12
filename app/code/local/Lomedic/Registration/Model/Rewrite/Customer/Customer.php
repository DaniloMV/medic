<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_Rewrite_Customer_Customer extends Mage_Customer_Model_Customer
{
    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }
        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('customer')->__('The Date of Birth is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('customer')->__('The TAX/VAT number is required.');
        }
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
            $errors[] = Mage::helper('customer')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {
        $usersColl = Mage::getModel('loregistration/usersCompany')->getCollection()
            ->addFieldToFilter('email', array('eq' => $login))
            ->addFieldToFilter('password', array('eq' => sha1($password)));

        if(count($usersColl) > 0){
            $user =  $usersColl->getFirstItem();
            $priviligesUser = Mage::getModel('loregistration/usersCompanyPrivileges')->getCollection()
                ->addFieldToFilter('user_company_id',array('eq' => $user->getUserCompanyId()));


            foreach($priviligesUser as $privus){

                $priviliges = Mage::getModel('loregistration/userCompanyPrivileges')->load($privus->getPrivilegeId());

                if($priviliges->getName() == 'Manage Products'){
                    Mage::getSingleton('customer/session')->setManageProducts(1);
                }elseif($priviliges->getName() == 'Manage Batches'){
                    Mage::getSingleton('customer/session')->setManageBatches(1);
                }elseif($priviliges->getName() == 'Manage Sales'){
                    Mage::getSingleton('customer/session')->setManageSales(1);
                }elseif($priviliges->getName() == 'Manage profile settings'){
                    Mage::getSingleton('customer/session')->setManageProfileSettings(1);
                }
            }

            $customer = Mage::getModel('customer/customer')->load($user->getCustomerId());
            Mage::getSingleton('customer/session')->setUserCompany($user->getUserCompanyId());

            $userData = $usersColl->getData();
            $userData = $userData[0];
        }


        $this->loadByEmail(isset($customer)?$customer->getEmail():$login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if ((!isset($customer) && !$this->validatePassword($password)) || (isset($customer) && count($usersColl) == 0)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }

        if(isset($customer) && ($customer->getIsActive() == 0 || $userData["is_active"] == 0)){
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is blocked. Contact your personal recruiter for clarifications'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        } elseif(!isset($customer) && $this->getIsActive() == 0) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is blocked. Contact your personal recruiter for clarifications'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }

        Mage::dispatchEvent('customer_customer_authenticated', array(
            'model'    => isset($customer)?$customer:$this,
            'password' => $password,
        ));

        return true;
    }
    
    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS;
        $pass = Mage::helper('core')->getRandomString($length, $chars).rand(1,999);
        $pass .= Mage::helper('core')->getRandomString(2,Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS);
        return $pass;
    }
}
