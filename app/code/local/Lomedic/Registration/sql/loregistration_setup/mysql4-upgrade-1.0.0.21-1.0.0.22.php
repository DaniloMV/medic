<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

$installer = $this;
$installer->startSetup();

$BD = Mage::getBaseDir();
$xmlFile = $BD . "/app/code/local/Lomedic/Registration/etc/config.xml";
if(!file_exists($xmlFile)) return true;
$xml = simplexml_load_file($xmlFile);
$locale = "en_US";
foreach ((array)$xml->global->template->email as $templateCode=>$node) {
    $template = Mage::getModel('adminhtml/email_template');
    $template->loadDefault($templateCode, $locale);

    try {
        $template->setId(NULL)
            ->setTemplateCode((string)$node->label)
            ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
            ->setOrigTemplateCode($templateCode)
            ->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
        $res = $template->save();

        $config = new Mage_Core_Model_Config();
        $config->saveConfig('templates/email/'.$templateCode, $res->getData("template_id"), 'default', 'req');
    }
    catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    }
}

$installer->endSetup();