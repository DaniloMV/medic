<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('loseller/goverment_catalog'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn(
        'generic_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Generic Name'
    )
    ->addColumn(
        'description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Description'
    )
    ->addColumn(
        'category', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Category'
    )
    ->addColumn(
        'code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Code'
    )
    ->addColumn(
        'qty', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Qty'
    )
    ->addColumn(
        'presentation', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Presentation'
    )
    ->addColumn(
        'group_presentation', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Group Presentation'
    )
    ->addColumn(
        'level', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Level'
    )
    ->addColumn(
        'is_remove', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array('default'=>'0','unsigned'  => true,'nullable'  => false), 'Flag if entity might be removed'
    )    
    ->addColumn(
        'updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Last time update'
    )
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('generic_name')),
        array('generic_name'))
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('category')),
        array('category'))
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('code')),
        array('code'))
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('qty')),
        array('qty'))
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('presentation')),
        array('presentation'))
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('group_presentation')),
        array('group_presentation'))
    ->addIndex($installer->getIdxName('loseller/goverment_catalog', array('level')),
        array('level'))
    ;

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
