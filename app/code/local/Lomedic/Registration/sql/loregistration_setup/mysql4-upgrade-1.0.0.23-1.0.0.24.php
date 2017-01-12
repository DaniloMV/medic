<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'company_id',
    array(
        'type'             => 'varchar',
        'label'            => 'Company',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'default'          => NULL,
        'visible_on_front' => 1,
    )
);

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'company_id')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1','customer_account_step2','customer_account_step3','customer_account_step4','customer_account_step5', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'is_active',
    array(
        'type'             => 'int',
        'label'            => 'Active',
        'input'            => 'text',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'default'          => 1,
        'visible_on_front' => 0,
    )
);

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'is_active')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1','customer_account_step2','customer_account_step3','customer_account_step4','customer_account_step5', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->endSetup();