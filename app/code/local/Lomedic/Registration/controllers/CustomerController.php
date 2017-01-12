<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_CustomerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Create customer grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}