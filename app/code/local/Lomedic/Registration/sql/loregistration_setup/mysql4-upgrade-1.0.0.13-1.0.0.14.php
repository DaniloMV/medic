<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'approve', array(
    'input'         => 'boolean',
    'type'          => 'int',
    'label'         => 'Approve',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'approve')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'contract', array(
    'label'        => 'Contract',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'contract')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'complete', array(
    'input'         => 'boolean',
    'type'          => 'int',
    'label'         => 'Complete',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'complete')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'approve_message', array(
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Message to recruiter',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'approve_message')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->endSetup();