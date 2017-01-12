<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/**
 * Add customer address is_clone attribute
 */
$installer = $this;
$installer->startSetup();

$this->addAttribute('customer_address', 'is_clone', array(
    'input'         => 'boolean',
    'type'          => 'int',
    'label'         => 'Is Clone',
    'visible'       => 0,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'is_clone')
    ->setData('used_in_forms', array())
    ->save();

$installer->endSetup();
