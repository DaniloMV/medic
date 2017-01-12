<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('loregistration/lock_registration'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn(
        'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer Id'
    )
    ->addColumn(
        'manager_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Manager ID'
    )    
    ->addColumn(
        'update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Customer Last Update'
    )    
    ->addIndex($installer->getIdxName('loregistration/lock_registration', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('loregistration/lock_registration', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();