<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Rewrite_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    public function __construct()
    {
        parent::__construct();

        $this->_updateButton('back', 'onclick', 'history.back()');
        $this->_addButton('saveandinform', array(
                'label' => Mage::helper('customer')->__('Save and inform client'),
                'onclick'   => 'jQuery(\'#edit_form\').attr(\'action\',\''.$this->getUrl('*/*/saveandinform').'\'); editForm.submit();',
                'class' => 'saveandinform')
            );
        $this->_removeButton('order');
    }
}
