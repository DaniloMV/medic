<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
    $config = Mage::getModel('core/config');
    $config->saveConfig('web/cookie/cookie_lifetime', '240', 'default', 0);
//    $config->saveConfig('admin/security/session_cookie_lifetime', '240', 'default', 0);

$installer->endSetup();