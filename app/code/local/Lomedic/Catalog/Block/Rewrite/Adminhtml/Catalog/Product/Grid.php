<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Block_Rewrite_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * Prepare catalog product collection
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('batch_number')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        $collection->joinField('seller',
                'marketplace/product',
                'userid',
                'mageproductid=entity_id',
                null,
                'left');

        if ($store->getId()) {


            $collection->joinAttribute(
                'seller',
                'catalog_product/seller',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
   
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );

            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->joinField('private_price',
                'locatalog/catalog_product_price',
                'value',
                'entity_id=entity_id',
                '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/privatebuyer'),
                'left');
            $collection->joinField('gov_price',
                'locatalog/catalog_product_price',
                'value',
                'entity_id=entity_id',
                '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/govbuyer'),
                'left');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
    }

    /**
     * Prepare columns for product grid
     * 
     * @return \Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
         $this->addColumn('seller',
            array(
                'header'=> Mage::helper('catalog')->__('Seller'),
                'index' => 'seller',
                'width' => '200px',
                'type'  => 'seller',
                'renderer'  => 'locatalog/adminhtml_catalog_product_grid_renderer_seller',
                'filter_condition_callback' => array($this, '_filterDate')
        ));

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
                'renderer'  => 'locatalog/adminhtml_catalog_product_grid_renderer_name'
        ));
       
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '200px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Private/Government Price'),
                'type'  => 'price',
                'width' => '150px',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'renderer'  => 'locatalog/adminhtml_catalog_product_grid_renderer_price',
                'filter_condition_callback' => array($this, '_filterPrice')
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'   => false,
                'sortable' => false,
                'index'     => 'stores',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    /**
     * Add customer filter by date to collection
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function _filterDate($collection, $column)
    {
        $filters = $column->getFilter()->getValue();
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('company')
            ->addAttributeToFilter('company', array('like' => '%'.$filters."%"));

        $inArr = array();
        foreach($collection as $coll){
            $inArr[] = $coll->getEntityId();
        }

        $this->getCollection()->addAttributeToFilter('seller', array('in' => $inArr));
    }
    /**
     * Add customer filter by price to collection
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return void
     */
    protected function _filterPrice($collection, $column)
    {
        $filters = $column->getFilter()->getValue();
        if(!$filters['from'] && !$filters['to']) {
            return;
        }
        if(!$filters['from'] && $filters['to']) {
            $this->getCollection()->getSelect()->orWhere('`at_private_price`.`value`<='.$filters['to']);
            $this->getCollection()->getSelect()->orWhere('`at_gov_price`.`value`<='.$filters['to']);
        } elseif($filters['from'] && !$filters['to']) {
            $this->getCollection()->getSelect()->orWhere('`at_private_price`.`value`>='.$filters['from']);
            $this->getCollection()->getSelect()->orWhere('`at_gov_price`.`value`>='.$filters['from']); 
        } else {
            $this->getCollection()->getSelect()->orWhere('`at_private_price`.`value`>='.$filters['from'].' AND `at_private_price`.`value`<='.$filters['to']);
            $this->getCollection()->getSelect()->orWhere('`at_gov_price`.`value`>='.$filters['from'].' AND  `at_gov_price`.`value`<='.$filters['to']);
        }
    }
}
