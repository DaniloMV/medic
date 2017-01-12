<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_Captcha_Observer extends Mage_Captcha_Model_Observer
{
    /**
     * Check Captcha On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLogin($observer)
    {
        if(Mage::app()->getRequest()->getPost('no-captcha')) {
            return $this;
        }
        return parent::checkUserLogin($observer);
    }
    
    /**
     * Reset Attempts
     *
     * @param string $login
     * @return Mage_Captcha_Model_Observer
     */
    protected function _resetAttempt($login)
    {
        Mage::getResourceModel('captcha/log')->deleteUserAttempts($login);
        Mage::getSingleton('customer/session')->setData('user_login_show_captcha',false);
        return $this;
    }
}