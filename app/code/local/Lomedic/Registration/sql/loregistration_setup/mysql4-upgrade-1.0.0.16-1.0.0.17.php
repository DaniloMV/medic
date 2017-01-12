<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'oficial_id', array(
    'label'        => 'Oficial ID (Voting card or Passport)',
    'type'         => 'varchar',
    'input'        => 'file',
    'global'       => 1,
    'visible'      => 1,
    'required'     => 0,
    'user_defined' => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'oficial_id')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();

$installer->endSetup();