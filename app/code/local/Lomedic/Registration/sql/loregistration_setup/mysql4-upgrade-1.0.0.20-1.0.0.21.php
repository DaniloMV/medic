<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

$installer = $this;
$installer->startSetup();

$installer->run("UPDATE {$installer->getTable('eav_attribute')} SET is_required = 0 WHERE attribute_code = 'city'");


$installer->endSetup();