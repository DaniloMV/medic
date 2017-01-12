<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Dashboard_Registration_Grid extends Mage_Adminhtml_Block_Customer_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('registrationGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(false);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() 
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addAttributeToSelect('firstname')
                ->addAttributeToSelect('company')
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('comments')
                ->addAttributeToSelect('registration_status')
                ->addAttributeToSelect('registration_recruter')
                ->addAttributeToSelect('taxvat');

        if (Mage::helper('loregistration')->getManagerType() == Mage::getStoreConfig('softeq/managers/recruter')) {
            $collection->addAttributeToFilter('registration_recruter', array('eq' => Mage::getSingleton('admin/session')->getUser()->getUserId()));
        }

        if ($this->getRequest()->getParam('filter')) {
            list($condition, $value) = explode('=', base64_decode($this->getRequest()->getParam('filter')));
            if ($condition == 'recruiter' && $value = 'none') {
                $collection->addAttributeToFilter('registration_recruter', array(array('null' => true), array('eq' => 0)), 'left');
            }
        }
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * 
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() 
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('loregistration')->__('ID'),
            'width' => '130px',
            'index' => 'entity_id',
            'type' => 'text',
        ));
        $this->addColumn('company', array(
            'header' => Mage::helper('loregistration')->__('Name/Company name'),
            'width' => '150',
            'index' => 'company'
        ));
        $this->addColumn('taxvat', array(
            'header' => Mage::helper('loregistration')->__('Social security number (Tax ID)'),
            'width' => '150',
            'index' => 'taxvat'
        ));
        $this->addColumn('firstname', array(
            'header' => Mage::helper('loregistration')->__('Contact name'),
            'index' => 'firstname'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('loregistration')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));

        $recruters = Mage::getModel('loregistration/system_config_source_recruter')->toArray();

        $this->addColumn('registration_recruter', array(
            'header' => Mage::helper('loregistration')->__('Recruiter'),
            'width' => '300',
            'type' => 'options',
            'index' => 'registration_recruter',
            'options' => $recruters,
        ));
        $this->addColumn('registration_status', array(
            'header' => Mage::helper('loregistration')->__('Registration status'),
            'index' => 'registration_status',
            'type' => 'options',
            'options' => Lomedic_Registration_Model_System_Config_Source_Status::toArray()
        ));

        $this->addColumn('comments', array(
            'header' => Mage::helper('loregistration')->__('Comments'),
            'index' => 'comments'
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getGridUrl() 
    {
        return $this->getUrl('loregistration/customer/grid', array('_current' => true));
    }

    public function getRowUrl($row) 
    {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getId()));
    }

}
