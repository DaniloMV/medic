<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
    /**
     * Initialize product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return \Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        // add base attributes to collection
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId())->addAttributeToSelect(array('generic_name','description_a','presentation','qty','category','code','medicine_manufacturer','expiration_date','url_key','batch_seller'))
            ->joinField('stock_qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.qty>0',
            'inner');
        
        // add filter by generic name and category
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
        
        // add buyer prices
        $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privatebuyer')) {
            $collection->joinField('customer_price',
                                    'locatalog/catalog_product_price',
                                    'value',
                                    'entity_id=entity_id',
                                    '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/privatebuyer'),
                                    'inner');
        }
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/govbuyer')) {
            $collection->joinField('customer_price',
                                    'locatalog/catalog_product_price',
                                    'value',
                                    'entity_id=entity_id',
                                    '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/govbuyer'),
                                    'inner');
        }
        
        // add filter by sector
         if(Mage::app()->getRequest()->getParam('public_sector')) {
            $collection->joinField('customer_pricegov',
                                    'locatalog/catalog_product_price',
                                    'value',
                                    'entity_id=entity_id',
                                    '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/govbuyer'),
                                    'inner');
            $collection->getSelect()->where('at_customer_pricegov.customer_group_id <>?',Mage::getStoreConfig('softeq/loregistration/privatebuyer'));
        }
        if(Mage::app()->getRequest()->getParam('private_sector')) {
            $collection->joinField('customer_pricepriv',
                                'locatalog/catalog_product_price',
                                'value',
                                'entity_id=entity_id',
                                '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/privatebuyer'),
                                'inner');
        }

        // add filter by seller
        if(Mage::app()->getRequest()->getParam('filter_seller')) {
            $collection->addAttributeToFilter('batch_seller',array('eq'=>Mage::app()->getRequest()->getParam('filter_seller')));
        }
        
        // add filter by product code
        if(Mage::app()->getRequest()->getParam('filter_code')) {
            $values = explode(',', Mage::app()->getRequest()->getParam('filter_code'));
            $values = array_filter($values);
            $collection->addAttributeToFilter('code',array('in'=>$values));
        }
        
        // add filter to product description
        if(Mage::app()->getRequest()->getParam('filter_description')) {
            $values = explode(',', Mage::app()->getRequest()->getParam('filter_description'));
            $values = array_filter($values);
            $collection->addAttributeToFilter('description_a',array('in'=>$values));
        }

        // add filter by search query. search by name, medicine_manufacturer,
        // stock_qty, expiration_date, description, generic_name, presentation,
        // qty, category, code
        if(($value = Mage::app()->getRequest()->getParam('search')) && $value!=="") {
            $conditions = array(array('attribute'=>'name','like'=>'%'.$value.'%'));
            $conditions[] = array('attribute'=>'medicine_manufacturer','like'=>'%'.$value.'%');
            $conditions[] = array('attribute'=>'stock_qty','eq'=>$value);
            $conditions[] = array('attribute'=>'expiration_date','like'=>'%'.$value.'%');
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
        
        $collection->getSelect()->where('e.type_id =?',Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }
}
