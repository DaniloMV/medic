<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Block_Marketplace_Batches extends Webkul_Marketplace_Block_Marketplace
{
    protected $userId;
    
    protected function _prepareLayout() 
    {
        Mage_Customer_Block_Account_Dashboard::_prepareLayout(); 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $grid_per_page_values = explode(",",Mage::getStoreConfig('catalog/frontend/grid_per_page_values'));
        $arr_perpage = array();
        foreach ($grid_per_page_values as $value) {
        	$arr_perpage[$value] = $value;
        }
        $pager->setAvailableLimit(array(99999));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    } 
    
    public function __construct()
    {		
        parent::__construct();

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('batch_number')
            ->addAttributeToSelect('expiration_date')
            
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('batch_parent_product')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->setOrder('entity_id','AESC');
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
        $collection->getSelect()
                ->joinInner(array('m'=>Mage::getSingleton('core/resource')->getTableName('marketplace/product')),'e.entity_id=m.mageproductid','')
                ->where('m.userid=?',$this->getUserId())
                ->where('e.type_id =?',Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT);
        if($this->getRequest()->getParam('search_name')) {
            $collection->addAttributeToFilter('batch_parent_product',array('eq'=>$this->getRequest()->getParam('search_name')));
        }

        $this->setCollection($collection);
    }
    
    protected function getUserId() 
    {
        if(!$this->userId) {
            $this->userId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        return $this->userId;
    }

    public function getAttributeSetId() 
    {
        $attributeSetName = "Batch"; // put your own attribute set name 
        $attribute_set = Mage::getModel("eav/entity_attribute_set")->getCollection(); 
        $attribute_set->addFieldToFilter("attribute_set_name", $attributeSetName);
        $set = $attribute_set->getFirstItem(); 
        return $set->getAttributeSetId();
    }
}