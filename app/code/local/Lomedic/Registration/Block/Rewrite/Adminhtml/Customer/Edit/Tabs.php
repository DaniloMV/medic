<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Rewrite_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    private $_removeTabs =  array('customer_edit_tab_agreements','customer_edit_tab_recurring_profile','customer_info_tabs_account');
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('customer')->__('Customer Information'));
    }
    
    /**
     * 
     * @return typeAdd tabs
     */
    protected function _beforeToHtml()
    {        
        foreach ($this->_removeTabs as $_tabId) {
            $this->removeTab($_tabId);
        }

        $this->addTab('step1', array(
            'label'     => Mage::helper('customer')->__('Registration Step 1'),
            'content'   => $this->getLayout()->createBlock('loregistration/adminhtml_customer_edit_tab_step1')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('step2', array(
            'label'     => Mage::helper('customer')->__('Registration Step 2'),
            'content'   => $this->getLayout()->createBlock('loregistration/adminhtml_customer_edit_tab_step2')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('step3', array(
            'label'     => Mage::helper('customer')->__('Registration Step 3'),
            'content'   => $this->getLayout()->createBlock('loregistration/adminhtml_customer_edit_tab_step3')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));


        $this->addTab('step4', array(
            'label'     => Mage::helper('customer')->__('Registration Step 4'),
            'content'   => $this->getLayout()->createBlock('loregistration/adminhtml_customer_edit_tab_step4')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('step5', array(
            'label'     => Mage::helper('customer')->__('Registration Step 5'),
            'content'   => $this->getLayout()->createBlock('loregistration/adminhtml_customer_edit_tab_step5')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));

        $this->addTab('addresses', array(
            'label'     => Mage::helper('customer')->__('Addresses'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_addresses')->initForm()->toHtml(),
        ));
        /*
        */

        $this->_updateActiveTab();
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }

    /**
     * Update active tabs
     */
    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
