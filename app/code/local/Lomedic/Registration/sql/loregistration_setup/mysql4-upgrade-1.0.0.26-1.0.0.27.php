<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_users_company'))
    ->addColumn('user_company_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Name'
    )
    ->addColumn(
        'sur_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Surname'
    )
    ->addColumn(
        'email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Email'
    )
    ->addColumn(
        'password', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Password'
    )
    ->addColumn(
        'is_active', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Username'
    )
    ->addColumn(
        'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer'
    )
    ->addIndex($installer->getIdxName('loregistration/usersCompany', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('loregistration/usersCompany', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_user_company_privileges'))
    ->addColumn('privilege_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Name'
    );

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);

    $installer->run("INSERT INTO {$installer->getTable('customer_user_company_privileges')} (name) VALUES('Manage Products')");
    $installer->run("INSERT INTO {$installer->getTable('customer_user_company_privileges')} (name) VALUES('Manage Batches')");
    $installer->run("INSERT INTO {$installer->getTable('customer_user_company_privileges')} (name) VALUES('Manage Sales')");
    $installer->run("INSERT INTO {$installer->getTable('customer_user_company_privileges')} (name) VALUES('Manage profile settings')");
}

$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_users_company_privileges'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn(
        'privilege_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Privilege'
    )
    ->addColumn(
        'user_company_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer'
    )
    ->addIndex($installer->getIdxName('loregistration/usersCompanyPrivileges', array('privilege_id')),
        array('privilege_id'))
    ->addIndex($installer->getIdxName('loregistration/usersCompanyPrivileges', array('user_company_id')),
        array('user_company_id'))
    ->addForeignKey($installer->getFkName('loregistration/usersCompanyPrivileges', 'privilege_id', 'loregistration/userCompanyPrivileges', 'privilege_id'),
        'privilege_id', $installer->getTable('loregistration/userCompanyPrivileges'), 'privilege_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('loregistration/usersCompanyPrivileges', 'user_company_id', 'loregistration/usersCompany', 'user_company_id'),
        'user_company_id', $installer->getTable('loregistration/usersCompany'), 'user_company_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();