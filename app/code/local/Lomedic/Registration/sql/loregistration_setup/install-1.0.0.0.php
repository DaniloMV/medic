<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;
Mage::getDesign()->setArea('frontend') //Area (frontend|adminhtml)
            ->setPackageName('rwd') //Name of Package
            ->setTheme('lomedic');
Mage::getConfig()->saveConfig('design/header/logo_src', 'images/logo.png');
Mage::getConfig()->saveConfig('design/header/logo_src_small', 'images/logo.png');
