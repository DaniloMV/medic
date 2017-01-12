<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_UsersCompany_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('usersCompanyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('user_company_id');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $set=$this->getRequest()->getParams();

        if(isset($set["id"]) && !empty($set["id"])){
            $collection = Mage::getResourceModel('loregistration/usersCompany_collection')
            ->addFieldToFilter('customer_id',array('eq' => $set["id"]));
            $this->setCollection($collection);
        }
        parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * 
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_company_id', array(
            'header'    => Mage::helper('loregistration')->__('ID'),
            'width'     => '70px',
            'index'     => 'user_company_id',
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('loregistration')->__('Name'),
            'index'     => 'name',
        ));
        $this->addColumn('sur_name', array(
            'header'    => Mage::helper('loregistration')->__('Surname'),
            'index'     => 'sur_name',
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('loregistration')->__('Email'),
            'index'     => 'email',
        ));
        $this->addColumn('delete', array(
            'header'    => Mage::helper('loregistration')->__('Delete'),
            'index'     => 'delete',
            'width'     => '100px',
            'align'     => 'center',
            'renderer'  => 'loregistration/adminhtml_widget_grid_column_renderer_userCompany',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('block', array(
            'header'    => Mage::helper('marketplace')->__('Block / Activate'),
            'index'     => 'block',
            'width'     => '100px',
            'align'     => 'center',
            'renderer'  => 'loregistration/adminhtml_widget_grid_column_renderer_blockUser',
            'filter'    => false,
            'sortable'  => false
        ));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() 
    {
        return $this;
    }

    public function getGridUrl() 
    {
        return $this->getUrl('adminhtml/usersCompany/grid', array('_current'=> true));
    }

    public function getRowUrl($row) 
    {
        return $this->getUrl('adminhtml/usersCompany/edit', array(
            'id'=>$row->getUserCompanyId(),
        ));
    }
    
    /**
     * Check if company is seller
     * 
     * @param type Object
     * @return boolean
     */
    public function isSeller($user=false) 
    {
        
        $companyId = $this->getRequest()->getParam('id');
        if($user) {
            $companyId = $user->getCustomerId();
        }
        $customer = Mage::getModel('customer/customer')->load($companyId);
        $customerGroup = $customer->getGroupId();
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privateseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/govseller')) {
            return true;
        }
        return false;
    }
}