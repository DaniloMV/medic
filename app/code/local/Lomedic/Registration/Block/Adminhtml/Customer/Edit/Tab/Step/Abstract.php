<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step_Abstract extends Mage_Adminhtml_Block_Customer_Edit_Tab_Account
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check customer addresses and add fake objects if not exists
     * 
     * @return array
     */
    public function getCustomerAddresses() 
    {
        $customer = Mage::registry('current_customer');
        $addresses = $customer->getAddresses();
        if(!count($addresses)) {
            $addresses['_item1'] = new Varien_Object();
        }
        if(count($addresses)==1) {
            $addresses['_item2'] = new Varien_Object();
        }
        if(count($addresses)==2) {
            $addresses['_item3'] = new Varien_Object();
        }
        return $addresses;
    }
}
