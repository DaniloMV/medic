<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/**
 * Create buyer quotes table
 * @see module observer for details
 */
define('BUYER_QUOTES_TABLE', 'buyercheckout/quote_items');
define('CUSTOMER_TABLE', 'customer/entity');
define('PRODUCT_TABLE', 'catalog/product');

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable(BUYER_QUOTES_TABLE))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer')
    ->addColumn('product_id',  Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Product')
    ->addColumn('product_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Product Qty')
    ->addForeignKey(
        $installer->getFkName($installer->getTable(BUYER_QUOTES_TABLE), 'customer_id', $installer->getTable(CUSTOMER_TABLE), 'entity_id'),
        'customer_id',
        $installer->getTable(CUSTOMER_TABLE),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName($installer->getTable(BUYER_QUOTES_TABLE), 'product_id', $installer->getTable(PRODUCT_TABLE), 'entity_id'),
        'product_id',
        $installer->getTable(PRODUCT_TABLE),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
