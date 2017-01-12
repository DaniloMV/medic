<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'batch_certificate', array(
    'group'         => 'Batch',
    'backend'       => '',
    'frontend'      => '',
    'label'        => 'Analytical certificate',
    'input'         => 'file',
    'source'        => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => true,
    'required'      => true,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT,
    'input_renderer'   => 'loseller/catalog_product_renderer_certificate',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'batch_certificate',
    'frontend_input_renderer',
    'loseller/catalog_product_renderer_certificate'
);
$installer->endSetup();
