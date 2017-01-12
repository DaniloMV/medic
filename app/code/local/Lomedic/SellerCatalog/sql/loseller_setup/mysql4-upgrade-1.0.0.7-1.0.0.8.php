<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'bar_code_number', array(
    'group'                   => 'General',
    'label'                   => 'Bar code number',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'classification_according', array(
    'group'                   => 'General',
    'label'                   => 'Classification according with article 226 General Health Law',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'number_of_health', array(
    'group'                   => 'General',
    'label'                   => 'Number of Health Ministry registration',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'order_number', array(
    'group'                   => 'General',
    'label'                   => 'Order number',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'previous_order_number', array(
    'group'                   => 'General',
    'label'                   => 'Previous order number',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'registration_holder', array(
    'group'                   => 'General',
    'label'                   => 'Registration holder',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'drug_registration', array(
    'group'                   => 'General',
    'label'                   => 'The drug registration',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'file',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'drug_manufacturer', array(
    'group'                   => 'General',
    'label'                   => 'Drug Manufacturer',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'medicine_manufacturer', array(
    'group'                   => 'General',
    'label'                   => 'Medicine Manufacturer laboratory',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'aconditioned_by', array(
    'group'                   => 'General',
    'label'                   => 'Aconditioned by',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->addAttribute('catalog_product', 'distributed_by', array(
    'group'                   => 'General',
    'label'                   => 'Distributed by',
    'note'                    => '',
    'type'                    => 'varchar',
    'input'                   => 'text',
    'frontend_class'	      => '',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
));

$installer->endSetup();