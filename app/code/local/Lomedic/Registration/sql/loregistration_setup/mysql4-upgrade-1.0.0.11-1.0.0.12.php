<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_files'))
    ->addColumn('file_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn(
        'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer'
    )
    ->addColumn(
        'attribute', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Customer attribute'
    )
    ->addColumn(
        'approve', Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array(), 'Approve file'
    )
    ->addColumn(
        'comment', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Comment file'
    )
    ->addColumn(
        'show_comment', Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array(), 'Show comment file'
    );
    //->addIndex($installer->getIdxName('loregistration/customer_files', array('customer_id')),
    //    array('customer_id'))
    //->addForeignKey($installer->getFkName('loregistration/customer_files', 'customer_id', 'customer/entity', 'entity_id'),
    //    'customer_id', $installer->getTable('customer/entity'), 'entity_id',
    //    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);;

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->run("ALTER TABLE {$this->getTable('customer_files')}
ADD CONSTRAINT `FK_CUSTOMER_FILES_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID`
FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE;");


$installer->endSetup();