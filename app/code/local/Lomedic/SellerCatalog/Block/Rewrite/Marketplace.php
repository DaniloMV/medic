<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Block_Rewrite_Marketplace extends Mage_Catalog_Block_Product_List
{
        
    protected $_governmentCatalog ;
     
    /**
     * Get product collection
     * 
     * @return object
     */
    protected function _getProductCollection() 
            {
        if(!$this->_productCollection) {
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection = $this->prepareProductCollection($collection);
            $userId=Mage::getSingleton('customer/session')->getCustomer()->getId();
            $collection->getSelect()->joinInner(array('mp'=> Mage::getSingleton('core/resource')->getTableName('marketplace/product')),'e.entity_id=mp.mageproductid','');
            $collection->getSelect()->where('mp.userid=?',$userId);
            $this->_productCollection = $collection;
      }
        return $this->_productCollection;
    }
    
    /**
     * Prepare product collection by seller
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return \Mage_Catalog_Model_Resource_Product_Collection
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
        $collection->addAttributeToSelect(array('generic_name','description_a','presentation','qty','category','code','medicine_manufacturer','expiration_date','url_key','batch_seller'));
      
        if($attrIds = Mage::app()->getRequest()->getParam('generic_name')) {
            $values = array_keys($attrIds);
            $names = array();
            $speciality = array();
            foreach($values as $v) {
                list($cat,$val) = explode('-',$v);
                $names[] = $val;
                $speciality[] = $cat;
            }
            $collection->addAttributeToFilter('generic_name',array('in'=>$names));
            $collection->addAttributeToFilter('category',array('in'=>$speciality));
        }
   
        if(Mage::app()->getRequest()->getParam('public_sector') AND !Mage::app()->getRequest()->getParam('private_sector')) {
            $attributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'batch_parent_product')->getAttributeId();
            $table = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'batch_parent_product')->getBackend()->getTable();

            $priceCollection = Mage::getModel('catalog/product')->getCollection();
            $priceCollection->getSelect()
                    ->joinInner(array('attributeTable' => $table), 'e.entity_id = attributeTable.entity_id', array('batch_parent_product' => 'attributeTable.value'))
                    ->joinInner(array('priceTable' => Mage::getSingleton('core/resource')->getTableName('locatalog/catalog_product_price')), 'e.entity_id = priceTable.entity_id', array())
                    ->where("priceTable.customer_group_id = ?", Mage::getStoreConfig('softeq/loregistration/govbuyer'))
                    ->where("attributeTable.attribute_id = ?", $attributeId);

            if($priceCollection->getSize()) {
                $hasPrivate = array();
                foreach($priceCollection as $item) {
                    $hasPrivate[] = $item->getBatchParentProduct();
                }
                $hasPrivate = array_unique($hasPrivate);
                $collection->addAttributeToFilter('batch_parent_product',array('in'=>$hasPrivate));
            }
       
        }
        if(!Mage::app()->getRequest()->getParam('public_sector') && Mage::app()->getRequest()->getParam('private_sector')) {
            $attributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'batch_parent_product')->getAttributeId();
            $table = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'batch_parent_product')->getBackend()->getTable();

            $priceCollection = Mage::getModel('catalog/product')->getCollection();
            $priceCollection->getSelect()
                    ->joinInner(array('attributeTable' => $table), 'e.entity_id = attributeTable.entity_id', array('batch_parent_product' => 'attributeTable.value'))
                    ->joinInner(array('priceTable' => Mage::getSingleton('core/resource')->getTableName('locatalog/catalog_product_price')), 'e.entity_id = priceTable.entity_id', array())
                    ->where("priceTable.customer_group_id = ?", Mage::getStoreConfig('softeq/loregistration/privatebuyer'))
                    ->where("attributeTable.attribute_id = ?", $attributeId);

            if($priceCollection->getSize()) {
                $hasPrivate = array();
                foreach($priceCollection as $item) {
                    $hasPrivate[] = $item->getBatchParentProduct();
                }
                $hasPrivate = array_unique($hasPrivate);
                $collection->addAttributeToFilter('batch_parent_product',array('in'=>$hasPrivate));
            }
        }
        if(Mage::app()->getRequest()->getParam('public_sector') && Mage::app()->getRequest()->getParam('private_sector')) {
            $attributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'batch_parent_product')->getAttributeId();
            $table = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'batch_parent_product')->getBackend()->getTable();

            $priceCollection = Mage::getModel('catalog/product')->getCollection();
            $priceCollection->getSelect()
                    ->joinInner(array('attributeTable' => $table), 'e.entity_id = attributeTable.entity_id', array('batch_parent_product' => 'attributeTable.value'))
                    ->joinInner(array('priceTable' => Mage::getSingleton('core/resource')->getTableName('locatalog/catalog_product_price')), 'e.entity_id = priceTable.entity_id', array())
                    ->joinInner(array('priceTable2' => Mage::getSingleton('core/resource')->getTableName('locatalog/catalog_product_price')), 'priceTable.entity_id = priceTable2.entity_id', array())
                    ->where("priceTable.customer_group_id = ?", Mage::getStoreConfig('softeq/loregistration/govbuyer'))
                    ->where("priceTable2.customer_group_id = ?", Mage::getStoreConfig('softeq/loregistration/privatebuyer'))
                    ->where("attributeTable.attribute_id = ?", $attributeId);
            
            if($priceCollection->getSize()) {
                $hasPrivate = array();
                foreach($priceCollection as $item) {
                    $hasPrivate[] = $item->getBatchParentProduct();
                }
                $hasPrivate = array_unique($hasPrivate);
                $collection->addAttributeToFilter('batch_parent_product',array('in'=>$hasPrivate));
            }
        }
        
        if(Mage::app()->getRequest()->getParam('filter_code')) {
            $values = explode(',', Mage::app()->getRequest()->getParam('filter_code'));
            $values = array_filter($values);
            $collection->addAttributeToFilter('code',array('in'=>$values));
        }
        if(Mage::app()->getRequest()->getParam('filter_description')) {
            $values = explode(',', Mage::app()->getRequest()->getParam('filter_description'));
            $values = array_filter($values);
            $collection->addAttributeToFilter('description_a',array('in'=>$values));
        }
  
        if(($value = Mage::app()->getRequest()->getParam('search')) && $value!=="") {
            $value = trim($value);
            $conditions = array(array('attribute'=>'name','like'=>'%'.$value.'%'));
            $fields = array('generic_name','description_a','presentation','qty','category','code');
            foreach ($fields as $field) {
                $results = array();
                if($field == 'description_a') {
                    $field = 'description';
                }
                $govCollection = Mage::getModel('loseller/goverment_catalog')->getCollection();
                $govCollection->getSelect()->where('`'.$field.'` LIKE ?',"%".$value."%");
                
                if($field == 'description') {
                    $field = 'description_a';
                }
                if($govCollection->getSize()) {
                    foreach($govCollection as $item) {
                        $results[] = $item->getEntityId();
                    }
                    $conditions[] = array('attribute'=>$field,'IN (?)'=>$results);
                }
            }
            $collection->addAttributeToFilter($conditions);
            
        }
        
        $collection->addAttributeToFilter('type_id','simple');
        return $collection;
    }

    
    /** 
     * Get government product items
      * 
     * @return array
     */
    public function getGovermentCatalogCollection() 
    {
        if(!$this->_governmentCatalog) {
            $collection = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect(array('category','generic_name','description','code','presentation','qty'))
                ->addFieldToFilter('is_remove',array('eq'=>0));
            $collection->setOrder('code','ASC');
        
            foreach ($collection as $item) {
                $this->_governmentCatalog[$item->getEntityId()] = $item->getData();
            }
        }
        return $this->_governmentCatalog;
    }
    
    /**
     * Get seller orders
     * 
     * @return array
     */
    public function getAvailableOrders() 
    {
        $orders = $this->getToolbarBlock()->getAvailableOrders();
        foreach ($orders as $k=>$v) {
            unset($orders[$k]);
        }
        $orders['name'] = $this->__("Name");
        return $orders;
    }
}
