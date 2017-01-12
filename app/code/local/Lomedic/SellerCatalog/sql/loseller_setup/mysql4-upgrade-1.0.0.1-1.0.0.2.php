<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'generic_name', array(
    'group'                   => 'General',
    'label'                   => 'Generic Name',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'		      => 'loseller/attribute_source_genericname',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'description_a', array(
    'group'                   => 'General',
    'label'                   => 'Description Product Catalog',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_description',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'category', array(
    'group'                   => 'General',
    'label'                   => 'Category',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_category',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'code', array(
    'group'                   => 'General',
    'label'                   => 'Code',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_code',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'qty', array(
    'group'                   => 'General',
    'label'                   => 'Qty',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_qty',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'presentation', array(
    'group'                   => 'General',
    'label'                   => 'Presentation',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_presentation',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'group_presentation', array(
    'group'                   => 'General',
    'label'                   => 'Group Presentation',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_grouppresentation',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'level', array(
    'group'                   => 'General',
    'label'                   => 'Level',
    'note'                    => '',
    'type'                    => 'int',	//backend_type
    'input'                   => 'select',
    'frontend_class'	      => '',
    'source'			      => 'loseller/attribute_source_level',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'public_sector', array(
    'group'                   => 'General',
    'label'                   => 'For sale to the public sector',
    'note'                    => '',
    'type'                    => 'int',
    'input'                   => 'boolean',
    'frontend_class'	      => '',
    //'source'			      => 'loregistration/attribute_source_level',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->addAttribute('catalog_product', 'private_sector', array(
    'group'                   => 'General',
    'label'                   => 'For sale to the private sector',
    'note'                    => '',
    'type'                    => 'int',
    'input'                   => 'boolean',
    'frontend_class'	      => '',
    //'source'			      => 'loregistration/attribute_source_level',
    'backend'                 => '',
    'frontend'                => '',
    'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'                => true,
    'visible_on_front'        => false,
    'apply_to'                => 'simple',
    'is_configurable'         => false,
    'used_in_product_listing' => false,
    'sort_order'              => 5,
));

$installer->endSetup();