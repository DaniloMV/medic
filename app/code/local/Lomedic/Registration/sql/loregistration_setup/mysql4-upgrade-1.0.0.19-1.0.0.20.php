<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$installer->startSetup();
$config = Mage::getModel('core/config');

// create new customer tax class for brand group
$taxModel = Mage::getModel('tax/class');
$taxModel->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
         ->setClassName(Mage::helper('core')->__('Lomedic Tax Class'));
$taxClass =$taxModel->save();


$groupModelSeller = Mage::getModel('customer/group');
// create new customer group for brand
$groupModelSeller->setCustomerGroupCode(Mage::helper('core')
        ->__('Seller'))->setTaxClassId($taxClass->getClassId())
        ->save();
// save brand group ig in config
$config->saveConfig('softeq/loregistration/seller', $groupModelSeller->getCustomerGroupId(), 'default', 0);

$groupModelBuyer = Mage::getModel('customer/group');
// create new customer group for brand
$groupModelBuyer->setCustomerGroupCode(Mage::helper('core')->__('Buyer'))
        ->setTaxClassId($taxClass->getClassId())
        ->save();
// save brand group ig in config
$config->saveConfig('softeq/loregistration/buyer', $groupModelBuyer->getCustomerGroupId(), 'default', 0);

$groupModelBuyerPrivate = Mage::getModel('customer/group');
// create new customer group for brand
$groupModelBuyerPrivate->setCustomerGroupCode(Mage::helper('core')->__('Private Buyer'))
        ->setTaxClassId($taxClass->getClassId())
        ->save();
// save brand group ig in config
$config->saveConfig('softeq/loregistration/privatebuyer', $groupModelBuyerPrivate->getCustomerGroupId(), 'default', 0);

$groupModelBuyerGovernment = Mage::getModel('customer/group');
// create new customer group for brand
$groupModelBuyerGovernment->setCustomerGroupCode(Mage::helper('core')->__('Government Buyer'))
        ->setTaxClassId($taxClass->getClassId())
        ->save();
// save brand group ig in config
$config->saveConfig('softeq/loregistration/govbuyer', $groupModelBuyerGovernment->getCustomerGroupId(), 'default', 0);

$installer->endSetup();