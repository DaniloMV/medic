<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$installer->startSetup();

// Save role admin id in config
$config = Mage::getModel('core/config');
if(!Mage::getStoreConfig('softeq/managers/admin')) {
    $role = Mage::getModel('admin/roles')->getCollection()->addFieldToFilter('role_name', 'Administrators');
    $roles = Mage::getModel('admin/roles')->getCollection();

    foreach($roles as $r){
        if($r->getData("role_name") == 'Head administrator') {
            $role_id = $r->getData("role_id");
            break;
        }
    }

    $config->saveConfig('softeq/managers/admin', $role_id, 'default', 0);
}
$installer->endSetup();