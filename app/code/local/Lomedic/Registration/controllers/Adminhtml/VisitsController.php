<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Adminhtml_VisitsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('visits')
            ->_title($this->__('Visit Action'));

        $this->_addContent($this->getLayout()->createBlock('loregistration/adminhtml_visits'));

        $this->renderLayout();
    }

    /**
     * Get grid html for AJAX requests
     */
    public function gridAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('visits');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('loregistration/adminhtml_visits/grid')->toHtml()
        );
    }
}
