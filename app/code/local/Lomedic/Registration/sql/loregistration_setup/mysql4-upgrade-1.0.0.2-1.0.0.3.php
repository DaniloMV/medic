<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'registration_status',
    array(
        'type'             => 'int',
        'label'            => 'Registration Status',
        'input'            => 'select',
        'source'           => 'loregistration/system_config_source_status',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 1,
        'user_defined'     => 1,
        'visible_on_front' => 0,
        
    )
);
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'registration_status')
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
        ->save();
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'telephone_alternate')
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
        ->save();


$installer->endSetup();