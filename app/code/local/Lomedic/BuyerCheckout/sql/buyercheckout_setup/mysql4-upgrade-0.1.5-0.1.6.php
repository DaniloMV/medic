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
$coreConfig ->saveConfig('cataloginventory/item_options/max_sale_qty', '9999999', 'default', 0);

$installer->endSetup();
