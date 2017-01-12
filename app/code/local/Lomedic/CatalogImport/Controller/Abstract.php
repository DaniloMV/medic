<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_CatalogImport_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init action for add title
     * 
     * @return \Lomedic_CatalogImport_Controller_Abstract
     */
    protected function _initAction()
    {
        $this->_title($this->__('Manage Government Catalog'))
            ->loadLayout()
            ->_setActiveMenu('loimport');

        return $this;
    }
}