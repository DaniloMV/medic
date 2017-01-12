<?php

class Biztech_News_Block_Adminhtml_News_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('newsGrid');
      $this->setDefaultSort('news_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
       $store = $this->getRequest()->getParam('store', 0);
            
            $prefix = Mage::getConfig()->getTablePrefix();
            
      $collection = Mage::getModel('news/news')->getCollection();
      
      $collection->getSelect()->joinLeft($prefix.'news_data', 'main_table.news_id ='.$prefix.'news_data.news_id AND '.$prefix.'news_data.store_id = '.$store,array('status','title','news_content','intro','date_to_publish','date_to_unpublish',));
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('news_id', array(
          'header'    => Mage::helper('news')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'news_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('news')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('news')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('news')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('news')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit','params' => array('store' => $this->getRequest()->getParam('store', 0))),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('news')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('news')->__('XML'));
      
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('news_id');
        $this->getMassactionBlock()->setFormFieldName('news');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('news')->__('Delete'),
               'url'      => $this->getUrl('*/*/massDelete', array('store'=>$this->getRequest()->getParam('store', 0))),
             'confirm'  => Mage::helper('news')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('news/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('news')->__('Change status'),
               'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true,'store'=>$this->getRequest()->getParam('store', 0))),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('news')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId(),'store'=>$this->getRequest()->getParam('store', 0)));
  }

}