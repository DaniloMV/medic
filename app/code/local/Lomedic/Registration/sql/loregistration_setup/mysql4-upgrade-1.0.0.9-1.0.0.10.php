<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$installer->startSetup();

// Save role id in config
$config = Mage::getModel('core/config');
if(!Mage::getStoreConfig('softeq/managers/recruter')) {
    // Create dealer role
    $role = Mage::getModel('admin/roles')
        ->setName('Recruiter')
        ->setRoleType('G')
        ->save();

    // Set role privileges
    Mage::getModel('admin/rules')
        ->setRoleId($role->getId())
        ->setResources(array('all'))
        ->saveRel();

    $config->saveConfig('softeq/managers/recruter', $role->getId(), 'default', 0);
}
if(!Mage::getStoreConfig('softeq/managers/manager')) {
    // Create dealer role
    $role = Mage::getModel('admin/roles')
        ->setName('Manager')
        ->setRoleType('G')
        ->save();

    // Set role privileges
    Mage::getModel('admin/rules')
        ->setRoleId($role->getId())
        ->setResources(array('all'))
        ->saveRel();

    $config->saveConfig('softeq/managers/manager', $role->getId(), 'default', 0);
}
$installer->endSetup();