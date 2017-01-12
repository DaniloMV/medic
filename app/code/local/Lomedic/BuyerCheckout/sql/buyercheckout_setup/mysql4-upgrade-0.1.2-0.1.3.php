<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/**
 * Add customer address seller_id attribute
 */
$installer = $this;
$installer->startSetup();

$this->addAttribute('customer_address', 'seller', array(
    'input'         => 'text',
    'type'          => 'int',
    'label'         => 'Seller Id',
    'visible'       => 0,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'seller')
    ->setData('used_in_forms', array())
    ->save();

$installer->endSetup();
