<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/**
 * @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'drug_registration',
    'frontend_input_renderer',
    'loseller/catalog_product_renderer_certificate'
);
$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'previous_order_number',
    'is_required',
    0
);
//$installer->updateAttribute('catalog_product', 'previous_order_number', 'is_required', 0);

$installer->endSetup();