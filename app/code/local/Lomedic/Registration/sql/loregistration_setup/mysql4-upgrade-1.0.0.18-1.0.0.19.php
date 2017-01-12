<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
$installer->startSetup();
$installer->run(file_get_contents(Mage::getModuleDir("data", "Lomedic_Registration")."/zip.sql"));
$installer->endSetup();