<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->addAttributeSet(Mage_Catalog_Model_Product::ENTITY, 'Batch');
$installer->addAttributeGroup(Mage_Catalog_Model_Product::ENTITY, 'Batch', 'Batch');

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'batch_number', array(
    'group'             => 'Batch',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Batch Number',
    'input'             => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => true,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => true,
    'unique'            => false,
    'apply_to'          => Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT,
    'is_configurable'   => false,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'batch_warehouse', array(
    'group'             => 'Batch',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Batch Warehouse',
    'input_renderer'    => 'loseller/catalog_product_helper_form_example',//definition of renderer
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => true,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => true,
    'unique'            => false,
    'apply_to'          => Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT,
    'is_configurable'   => false,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'expiration_date', array(
    'group'             => 'Batch',
    'type'              => 'datetime',
    'backend'           => 'eav/entity_attribute_backend_datetime',
    'frontend'          => 'eav/entity_attribute_frontend_datetime',
    'label'             => 'Expiration Date',
    'time'              => false,
    'input'             => 'datetime',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => true,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => true,
    'unique'            => false,
    'apply_to'          => Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT,
    'is_configurable'   => false,
));
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'batch_parent_product', array(
    'group'             => 'Batch',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Parent product',
    'time'              => false,
    'input'             => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT,
    'is_configurable'   => false,
));
$installer->endSetup();
