<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// setup customer attributes
    $installer->addAttribute('customer', 'activity',
        array(
            'type'             => 'int',
            'label'            => 'Main activity',
            'input'            => 'select',
            'class'            => '',
            'source'           => 'loregistration/system_config_source_activity',
            'global'           => 1,
            'visible'          => 1,
            'required'         => 1,
            'user_defined'     => 1,
            'visible_on_front' => 1,
        )
    );
    
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'activity')
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
        ->save();
    
    $installer->addAttribute('customer', 'sector',
        array(
            'type'             => 'int',
            'label'            => 'Main sector',
            'input'            => 'select',
            'class'            => '',
            'source'           => 'loregistration/system_config_source_sector',
            'global'           => 1,
            'visible'          => 1,
            'required'         => 1,
            'user_defined'     => 1,
            'visible_on_front' => 1,
        )
    );
    
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'sector')
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'))
        ->save();

    // setup address attributes
    $attributes = array(
        'street_number'=>'Street number',
        'apartment_number'=>'Apartment number',
        'municipality'=>'Municipality',
        'colonia'=>'Colonia'
    );

    foreach($attributes as $attribute=>$label) {
        $installer->addAttribute('customer_address', $attribute, array(
            'type'              => 'varchar',
            'input'             => 'text',
            'label'             => $label,
            'visible'           => true,
            'required'          => false,
            'unique'            => false,
            'is_user_defined'   => 1,
            'is_system'         => 0,
        ));

        if(!$installer->getConnection()->tableColumnExists($this->getTable('sales_flat_quote_address'), $attribute)) {
            $installer->getConnection()
                ->addColumn($installer->getTable('sales_flat_quote_address'),$attribute, Varien_Db_Ddl_Table::TYPE_VARCHAR."(255) DEFAULT NULL");
        }

        $eavConfig = Mage::getSingleton('eav/config');

        $store     = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $attribute = $eavConfig->getAttribute('customer_address', $attribute);
        $attribute->setWebsite($store->getWebsite());

        $usedInForms = array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        );
        $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
$installer->endSetup();
