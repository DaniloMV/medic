<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
Mage::getModel('core/config')->saveConfig('customer/address/taxvat_show', "1", 'default', 'req');
$installer->updateAttribute('customer_address','telephone','is_required',0);
$installer->endSetup();