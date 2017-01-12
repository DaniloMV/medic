<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Visits_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('visitsGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('visit_date')
            ->addAttributeToSelect('company')
            ->addAttributeToSelect('visit_address')
            ->addAttributeToSelect('registration_recruter');

        if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/manager')) {

            $user = Mage::getModel('admin/role')->getCollection();
            $user->addFieldToFilter('parent_id',Mage::getStoreConfig('softeq/managers/recruter'));

            $arr = array();
            foreach($user as $item) {
                $tmpArr = $item->getData();
                $arr[] = $tmpArr["user_id"];
            }
            $collection->addAttributeToFilter('registration_recruter', array('in' => $arr));
        }elseif(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')) {
            $collection->addAttributeToFilter('registration_recruter', array('eq' => Mage::getSingleton('admin/session')->getUser()->getUserId()));
        }

        $tmpArr = array(
            Lomedic_Registration_Model_System_Config_Source_Status::VISITATION,
            Lomedic_Registration_Model_System_Config_Source_Status::VISIT_COMPLETED,
            Lomedic_Registration_Model_System_Config_Source_Status::PRECLOSED
        );

        $collection->addAttributeToFilter('registration_status', array('in' => $tmpArr));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * 
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('loregistration')->__('ID'),
            'width'     => '130px',
            'index'     => 'entity_id',
            'type'  => 'text',
        ));
        $this->addColumn('visit_date', array(
            'header'    => Mage::helper('loregistration')->__('Visit date and time'),
            'width'     => '300px',
            'index'     => 'visit_date',
            'type'      => 'date',
            'time' => true,
            //'type'      => 'date',  // <-- change to date
            'format'    => 'YYYY-MM-dd HH:mm:ss',
            'filter_condition_callback' => array($this, '_filterDate')
        ));
        $this->addColumn('visit_address', array(
            'header'    => Mage::helper('loregistration')->__('Visit address'),
            'width'     => 'auto',
            'index'     => 'visit_address'
        ));
        $this->addColumn('company', array(
            'header'    => Mage::helper('loregistration')->__('Name/Company name'),
            'width'     => '300',
            'index'     => 'company'
        ));
        $recruters = Mage::getModel('loregistration/system_config_source_recruter')->toArray();
        $this->addColumn('registration_recruter', array(
            'header'    => Mage::helper('loregistration')->__('Recruiter'),
            'width'     => '300',
            'type'      =>  'options',
            'index'     => 'registration_recruter',
            'options'   => $recruters,
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
            $from =  $date->format('Y-m-d H:i:s');

            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['to'])));
            $to =  $date->format('Y-m-d H:i:s');

            $this->getCollection()->addAttributeToFilter('visit_date', array('gteq' => $from));
            $this->getCollection()->addAttributeToFilter('visit_date', array('lteq' => $to));
        }elseif($from){
            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['from'])));
            $from =  $date->format('Y-m-d H:i:s');

            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['from'])));
            $date->modify('+10 month');
            $to =  $date->format('Y-m-d H:i:s');
            $this->getCollection()->addAttributeToFilter('visit_date', array('gteq' => $from));
            $this->getCollection()->addAttributeToFilter('visit_date', array('lteq' => $to));
        }elseif($to){
            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['to'])));
            $date->modify('-10 month');
            $from =  $date->format('Y-m-d H:i:s');

            $date = new DateTime(date('Y-m-d H:i:s',strtotime($filters['to'])));
            $to =  $date->format('Y-m-d H:i:s');
            $this->getCollection()->addAttributeToFilter('visit_date', array('gteq' => $from));
            $this->getCollection()->addAttributeToFilter('visit_date', array('lteq' => $to));
        }
    }


    protected function _prepareMassaction() 
    {
        return $this;
    }

    public function getGridUrl() 
    {
        return $this->getUrl('adminhtml/visits/grid', array('_current'=> true));
    }

    public function getRowUrl($row) 
    {
        return $this->getUrl('adminhtml/customer/edit', array(
            'id'=>$row->getId(),
            "back"=>"edit",
            "tab"=>"customer_info_tabs_step4"
        ));
    }
}