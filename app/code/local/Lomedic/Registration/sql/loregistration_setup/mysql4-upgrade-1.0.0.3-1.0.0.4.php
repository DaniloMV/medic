<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'taxation_purposes', array(
    'label'        => 'ID for taxation purposes',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'taxation_purposes')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'taxes_department', array(
    'label'        => 'Subscription in taxes department',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'taxes_department')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'association_registry', array(
    'label'        => 'Article association registry',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'association_registry')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'association_prpc', array(
    'label'        => 'Article association registration before PRPC',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'association_prpc')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'assoc_mod_registry', array(
    'label'        => 'Article association modification registry',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'assoc_mod_registry')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'modification_associ_prpc', array(
    'label'        => 'Modification to Article association registration before PRPC',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'modification_associ_prpc')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'proof_address', array(
    'label'        => 'Proof of address',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'proof_address')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'financial_years', array(
    'label'        => 'Financial statements of the last three years',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'financial_years')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();


$installer->addAttribute('customer', 'last_bank_statements', array(
    'label'        => 'Last 3 bank statements',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'last_bank_statements')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'last_year_statement', array(
    'label'        => 'Last year taxes statement',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'last_year_statement')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'operation_statement_cofepr', array(
    'label'        => 'Operation statement before COFEPRIS',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'operation_statement_cofepr')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'professional_before_cofep', array(
    'label'        => 'ID of the Responsible health professional before COFEPRIS',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'professional_before_cofep')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'license_health_professional', array(
    'label'        => 'Professional license of the health professional',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'license_health_professional')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'professional_before_cofepr', array(
    'label'        => 'Statement of Responsible health professional before COFEPRIS',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'professional_before_cofepr')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'statement_before_cofepris', array(
    'label'        => 'Statement of Responsible health professional before COFEPRIS',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'statement_before_cofepris')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'good_man_practices', array(
    'label'        => 'Certificates of good manufacturing practices',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'good_man_practices')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->addAttribute('customer', 'health_certificate_document', array(
    'label'        => 'Health license certificate document',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'health_certificate_document')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->endSetup();