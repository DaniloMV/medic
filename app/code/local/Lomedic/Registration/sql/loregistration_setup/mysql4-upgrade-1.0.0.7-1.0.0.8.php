<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->addAttribute('customer', 'registration_step',
        array(
            'type'             => 'int',
            'label'            => 'Current registration step',
            'input'            => 'text',
            'class'            => '',
            'source'           => '',
            'global'           => 1,
            'visible'          => 0,
            'required'         => 0,
            'user_defined'     => 1,
            'visible_on_front' => 0,
        )
    );
Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'registration_step')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1','customer_account_step2','customer_account_step3','customer_account_step4','customer_account_step5', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();
Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'telephone')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1','customer_account_step2','customer_account_step3','customer_account_step4','customer_account_step5', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();
        
$installer->endSetup();