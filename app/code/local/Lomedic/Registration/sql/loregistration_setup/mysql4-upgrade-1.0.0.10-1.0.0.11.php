<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$installer->startSetup();
    $installer->addAttribute('customer', 'registration_recruter',
        array(
            'type'             => 'int',
            'label'            => 'Recruiter',
            'input'            => 'select',
            'class'            => '',
            'source'           => 'loregistration/system_config_source_recruter',
            'global'           => 1,
            'visible'          => 1,
            'required'         => 0,
            'user_defined'     => 1,
            'visible_on_front' => 1,
        )
    );
Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'registration_recruter')
    ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1','customer_account_step2','customer_account_step3','customer_account_step4','customer_account_step5', 'customer_account_edit', 'adminhtml_checkout'))
    ->save();
 $installer->endSetup();