<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'comments', array(
    'label'            => 'Comments',
    'type'             => 'text',
    'input'            => 'text',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
));

$installer->addAttribute('customer', 'company', array(
    'label'            => 'Company',
    'type'             => 'varchar',
    'input'            => 'text',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
));

$attributes = array('comments','registration_status','telephone_alternate','activity','sector','street_number',
                    'appartment_number','municipality','colonia','company'
    );
foreach($attributes as $attribute) {
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', $attribute)
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1', 'customer_account_edit', 'adminhtml_checkout'))
        ->save();
}
$installer->endSetup();