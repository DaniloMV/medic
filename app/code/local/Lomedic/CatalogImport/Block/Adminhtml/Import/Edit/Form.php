<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_CatalogImport_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/validate'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('loimport')->__('Import File')));
        
/*      This field not used now  
 
        $fieldset->addField('behavior', 'select', array(
            'name'     => 'behavior',
            'title'    => Mage::helper('loimport')->__('Import Behavior'),
            'label'    => Mage::helper('loimport')->__('Import Behavior'),
            'required' => true,
            'values'   => Mage::getModel('importexport/source_import_behavior')->toOptionArray()
        ));
 */
        $fieldset->addField(Lomedic_CatalogImport_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => Lomedic_CatalogImport_Model_Import::FIELD_NAME_SOURCE_FILE,
            'label'    => Mage::helper('loimport')->__('Select File to Import'),
            'title'    => Mage::helper('loimport')->__('Select File to Import'),
            'required' => true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
