<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();


$installer->addAttribute('customer', 'account_type',
    array(
        'type'             => 'int',
        'label'            => 'Account Type',
        'input'            => 'select',
        'source'           => 'loregistration/system_config_source_accounttype',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'real_name',
    array(
        'type'             => 'varchar',
        'label'            => 'Name/Company name',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'association_number',
    array(
        'type'             => 'varchar',
        'label'            => 'Article association registry number',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'association_number_before',
    array(
        'type'             => 'varchar',
        'label'            => 'Article association registration number before Public Registry of property and commerce (PRPC)',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'association_number_mod',
    array(
        'type'             => 'varchar',
        'label'            => 'Article association registry modification number',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'association_number_mod_before',
    array(
        'type'             => 'varchar',
        'label'            => 'Article association registry modification number before PRPC',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'year_amount',
    array(
        'type'             => 'varchar',
        'label'            => 'Amount of equity for the previous year',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'employees_qty',
    array(
        'type'             => 'varchar',
        'label'            => 'Quantity of employees of the company',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'computer_qty',
    array(
        'type'             => 'varchar',
        'label'            => 'Quantity of computer equipment',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'office_area',
    array(
        'type'             => 'varchar',
        'label'            => 'Office area',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'warehouse_area',
    array(
        'type'             => 'varchar',
        'label'            => 'Warehouse area',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'vehicles_qty',
    array(
        'type'             => 'varchar',
        'label'            => 'Quantity of vehicles of the company',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'operation_statment',
    array(
        'type'             => 'varchar',
        'label'            => 'Operation statement before COFEPRIS',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);
$installer->addAttribute('customer', 'resp_health_professional',
    array(
        'type'             => 'varchar',
        'label'            => 'Responsible health professional before COFEPRIS',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'resp_health_prof_license_num',
    array(
        'type'             => 'varchar',
        'label'            => 'Professional license number of the health professional',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$installer->addAttribute('customer', 'health_license_number',
    array(
        'type'             => 'varchar',
        'label'            => 'Health license certificate number',
        'input'            => 'text',
        'class'            => '',
        'global'           => 1,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'visible_on_front' => 1,
        
    )
);

$attributes = array('comments','registration_status','telephone_alternate','activity','sector','street_number',
                    'apartment_number','municipality','colonia','company','account_type','real_name','association_number',
                    'association_number_before','association_number_mod','association_number_mod_before','year_amount','employees_qty',
                    'computer_qty','office_area','warehouse_area','vehicles_qty','operation_statment','resp_health_professional',
                    'resp_health_prof_license_num','health_license_number'
    );
foreach($attributes as $attribute) {
    Mage::getSingleton('eav/config')
        ->getAttribute('customer', $attribute)
        ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_step1','customer_account_step2','customer_account_step3','customer_account_step4','customer_account_step5', 'customer_account_edit', 'adminhtml_checkout'))
        ->save();
}
$installer->endSetup();