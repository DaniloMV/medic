<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = new Mage_Sales_Model_Mysql4_Setup();
$installer->startSetup();

$installer->addAttribute('quote_address', 'seller_name', array('type'=>'text'));
$installer->addAttribute('order', 'seller_name', array('type'=>'text'));
$installer->addAttribute('invoice', 'seller_name', array('type'=>'text'));

$installer->endSetup();
