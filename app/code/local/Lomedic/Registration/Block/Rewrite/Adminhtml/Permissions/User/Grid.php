<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Rewrite_Adminhtml_Permissions_User_Grid extends Mage_Adminhtml_Block_Permissions_User_Grid
{
    /**
     * Prepare collection
     * 
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('admin/user_collection');
        $this->setCollection($collection);
        $collection->getSelect()
                ->joinInner(array('roles'=>Mage::getSingleton('core/resource')->getTableName('admin/role')),'main_table.user_id=roles.user_id','')
                ->joinInner(array('roles2'=>Mage::getSingleton('core/resource')->getTableName('admin/role')),'roles.parent_id=roles2.role_id',array('role_name'=>'roles2.role_name'));
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Prepare columns
     * 
     * @return \Lomedic_Registration_Block_Rewrite_Adminhtml_Permissions_User_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'user_id'
        ));

        $this->addColumn('username', array(
            'header'    => Mage::helper('adminhtml')->__('User Name'),
            'index'     => 'username'
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('adminhtml')->__('First Name'),
            'index'     => 'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('adminhtml')->__('Last Name'),
            'index'     => 'lastname'
        ));
        
        $this->addColumn('role_name', array(
            'header'    => Mage::helper('adminhtml')->__('Role Name'),
            'index'     => 'role_name'
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('adminhtml')->__('Email'),
            'width'     => 40,
            'align'     => 'left',
            'index'     => 'email'
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
        ));

        return parent::_prepareColumns();
    }
}
