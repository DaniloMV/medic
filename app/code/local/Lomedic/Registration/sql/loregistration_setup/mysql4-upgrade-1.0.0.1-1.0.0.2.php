<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->updateAttribute('customer','lastname','is_required',0);
$installer->updateAttribute('customer_address','lastname','is_required',0);

$installer->addAttribute('customer', 'telephone_alternate',
    array(
        'type'             => 'varchar',
        'label'            => 'Alternative Telephone',
        'input'            => 'text',
        'class'            => 'telephone',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'is_required'      => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1
    )
);
$installer->endSetup();
