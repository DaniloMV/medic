<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/**
 * Update Maximum Qty Allowed for Shipping to Multiple Addresses
 */
$installer = $this;
$installer->startSetup();

$coreConfig = Mage::getModel('core/config');
$coreConfig ->saveConfig('shipping/option/checkout_multiple_maximum_qty', '9999999', 'default', 0);

$installer->endSetup();
