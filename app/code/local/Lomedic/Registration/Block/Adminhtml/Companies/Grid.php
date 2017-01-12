<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Companies_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('companiesGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('company_id');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('loregistration/companies')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * 
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('company_id', array(
            'header'    => Mage::helper('loregistration')->__('ID'),
            'width'     => '70px',
            'index'     => 'company_id',
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('loregistration')->__('Company name'),
            'index'     => 'name',
        ));
        $this->addColumn('create_date', array(
            'header'    => Mage::helper('loregistration')->__('Create date'),
            'width'     => '250px',
            'index'     => 'create_date',
            'type'      => 'date',
            'time'      => true,
            //'type'    => 'date',  // <-- change to date
            'format'    => 'YYYY-MM-dd',
            'filter_condition_callback' => array($this, '_filterDate')
        ));
        $this->addColumn('users', array(
            'header'    => Mage::helper('marketplace')->__('Users'),
            'index'     => 'users',
            'width'     => '100px',
            'align'     => 'center',
            'renderer'  => 'loregistration/adminhtml_widget_grid_column_renderer_inline',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('block', array(
            'header'    => Mage::helper('marketplace')->__('Block / Activate'),
            'index'     => 'block',
            'width'     => '100px',
            'align'     => 'center',
            'renderer'  => 'loregistration/adminhtml_widget_grid_column_renderer_block',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('delete', array(
            'header'    => Mage::helper('marketplace')->__('Delete'),
            'index'     => 'delete',
            'width'     => '100px',
            'align'     => 'center',
            'renderer'  => 'loregistration/adminhtml_widget_grid_column_renderer_delete',
            'filter'    => false,
            'sortable'  => false
        ));
        return parent::_prepareColumns();
    }

    /**
     * Filter column by date range
     * 
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection
     * @param object $column
     */
    protected function _filterDate($collection, $column)
    {
        $filters = $column->getFilter()->getValue();

        $from = $filters['from']?$filters['from']:false;
        $to = $filters['to']?$filters['to']:false;

        if($from && $to){
            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['from'])));
            $from =  $date->format('Y-m-d');

            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['to'])));
            $to =  $date->format('Y-m-d');

            $this->getCollection()->addFieldToFilter('create_date', array('gteq' => $from));
            $this->getCollection()->addFieldToFilter('create_date', array('lteq' => $to));
        }elseif($from){
            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['from'])));
            $from =  $date->format('Y-m-d H:i:s');

            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['from'])));
            $date->modify('+10 month');
            $to =  $date->format('Y-m-d H:i:s');
            $this->getCollection()->addFieldToFilter('create_date', array('gteq' => $from));
            $this->getCollection()->addFieldToFilter('create_date', array('lteq' => $to));
        }elseif($to){
            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['to'])));
            $date->modify('-10 month');
            $from =  $date->format('Y-m-d H:i:s');

            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['to'])));
            $to =  $date->format('Y-m-d H:i:s');
            $this->getCollection()->addFieldToFilter('create_date', array('gteq' => $from));
            $this->getCollection()->addFieldToFilter('create_date', array('lteq' => $to));
        }
    }

    protected function _prepareMassaction() 
    {
        return $this;
    }

    public function getGridUrl() 
    {
        return $this->getUrl('adminhtml/companies/grid', array('_current'=> true));
    }

    public function getRowUrl($row) 
    {
        return $this->getUrl('adminhtml/customer/edit', array(
            'id'=>$row->getCustomerId(),
            "back"=>"edit",
            "tab"=>"customer_info_tabs_step1"
        ));
    }
}