<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Block_Marketplace_Batchproduct extends Lomedic_SellerCatalog_Block_Marketplace_Batches
{
    private $_parentProduct;
    private $_product;
    
    public function __construct()
    {
        $parentProductId = Mage::app()->getRequest()->getParam('search_name',false);
        $batchId = $this->getRequest()->getParam('id',false);
        if(!$parentProductId && !$batchId) {
           return Mage::app()->getFrontController()->getResponse()->setRedirect($this->getUrl('marketplace/marketplaceaccount/batches'));
        }
        
        $parentProduct = Mage::getModel('catalog/product')->load($parentProductId);
        $collection = Mage::getModel('catalog/product')->getCollection();
        if($batchId) {
            $parentProductId = $batchId;
            $collection->joinField('stock_qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->setOrder('entity_id','AESC');
            
            $prices = Mage::getResourceModel('loseller/customerprice_collection');
            $prices->getSelect()
                ->where('main_table.entity_id=?',$batchId)
                ->where('main_table.customer_group_id IN(?)',array(Mage::getStoreConfig('softeq/loregistration/privatebuyer'),Mage::getStoreConfig('softeq/loregistration/govbuyer')));
        }
        $collection->addFieldToFilter('entity_id',array('eq'=>$parentProductId))->addAttributeToSelect('*');
        $collection->getSelect()
            ->joinInner(array('m'=>Mage::getSingleton('core/resource')->getTableName('marketplace/product')),'e.entity_id=m.mageproductid','')
            ->where('m.userid=?',$this->getUserId());
     
        $this->setCollection($collection);
        $product = $collection->getFirstItem();
        
        if($batchId) {
            $price = array();
            foreach ($prices as $item) {
                if($item->getCustomerGroupId()==Mage::getStoreConfig('softeq/loregistration/privatebuyer')) {
                    $price['private'] = $item->getValue();
                }
                
                if($item->getCustomerGroupId()==Mage::getStoreConfig('softeq/loregistration/govbuyer')) {
                    $price['government'] = $item->getValue();
                }
                
            }
            $product->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($product);
            $product->setPrices($price);
            $this->_product = $product;
        }
        $this->_parentProduct = $parentProduct;
    }
    
    public function getParentProduct() {
        return $this->_parentProduct;
    }
    public function getProduct() {
        return $this->_product;
    }
}