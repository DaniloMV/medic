<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/**
 * Update Maximum Qty Allowed for Sale
 */
$installer = $this;
$installer->startSetup();

$coreConfig = Mage::getModel('core/config');
$coreConfig ->saveConfig('tax/sales_display/zero_tax', '1', 'default', 0);
$coreConfig ->saveConfig('tax/cart_display/zero_tax', '1', 'default', 0);

$installer->endSetup();
