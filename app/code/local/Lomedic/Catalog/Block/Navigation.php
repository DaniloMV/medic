<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Block_Navigation extends Mage_Core_Block_Template 
{
    // formated list of all government catalog entities
    protected $_governmentCatalog;

    // formatted government catalog description
    protected $_govementCatalogDescription;
    
    // formatted government catalog code
    protected $_govementCatalogCode;
    /**
     * Get product collection with limit attributes
     * 
     * @return \Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection() {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('type_id',array('eq'=>Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT))
            ->setOrder('entity_id','AESC');
        $collection->addAttributeToFilter('status',array('eq'=>1));
        $collection->addAttributeToSelect(array('generic_name','description_a','presentation','qty','category','code','medicine_manufacturer','expiration_date','url_key','batch_seller'))
            ->joinField('stock_qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.qty>0',
            'inner');
        $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'inner');
        $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privateseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/privatebuyer')) {
            $collection->joinField('customer_price',
                                    'locatalog/catalog_product_price',
                                    'value',
                                    'entity_id=entity_id',
                                    '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/privatebuyer'),
                                    'inner');
        }
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/govseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/govbuyer')) {
            $collection->joinField('customer_price',
                                    'locatalog/catalog_product_price',
                                    'value',
                                    'entity_id=entity_id',
                                    '{{table}}.customer_group_id='.Mage::getStoreConfig('softeq/loregistration/govbuyer'),
                                    'inner');
        }
        return $collection;
    }

    /** 
     * Get government product items
     * 
     * @return array Government catalog
     */
    public function getGovermentCatalogCollection() {
        if(!$this->_governmentCatalog) {
            $collection = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect(array('category','generic_name','description','code'))
                ->addFieldToFilter('is_remove',array('eq'=>0));
            $collection->setOrder('code','ASC');
        
            foreach ($collection as $item) {
                $this->_governmentCatalog[$item->getEntityId()] = $item;
                $this->_govementCatalogDescription[$item->getDescription()] .= ",".$item->getEntityId();
                $this->_govementCatalogCode[$item->getCode()] .= ",".$item->getEntityId();
            }
            $this->_govementCatalogCode = array_flip($this->_govementCatalogCode);
            $this->_govementCatalogDescription = array_flip($this->_govementCatalogDescription);
        }
        return $this->_governmentCatalog;
    }
    
    /**
     * Return government catalog's codes
     * 
     * @return array Government catalog's Codes
     */
    public function getGovermentCatalogId() {
        return $this->_govementCatalogCode;
    }

    /**
     * Compare values
     * 
     * @param void $a
     * @param void $b
     * @return int result
     */
    private function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
    
    /**
     * Return Government catalog's Descriptions
     * 
     * @return array Government catalog's Descriptions
     */
    public function getGovermentCatalogDescription() {
        uasort($this->_govementCatalogDescription, array("Lomedic_Catalog_Block_Navigation", "cmp"));
        return $this->_govementCatalogDescription;
    }
    
    /**
     * Prepare data for product filters
     * 
     * @return array
     */
    public function prepareProductFilters() {
        $result = array();
        $this->getGovermentCatalogCollection();
        $collection = $this->getProductCollection();
        
        foreach($collection as $item){
            if($item->getCategory() && $item->getGenericName()){
                if(!isset($this->_governmentCatalog[$item->getCategory()])) {
                    continue;
                }
                
                $governmentItem = $this->_governmentCatalog[$item->getCategory()];
                
                if(!isset($result[$item->getCategory()])) {
                    $result[$item->getCategory()] = array(
                        "name" => $governmentItem->getCategory(),
                        "id" => $item->getCategory(),
                        "child"  => array()
                    );
                }

                if(isset($this->_governmentCatalog[$item->getGenericName()])) {
                    $child = $this->_governmentCatalog[$item->getGenericName()];
                    
                    $result[$item->getCategory()]['child'][$item->getGenericName()] = array(
                        "name" => $child->getGenericName(),
                        "id" => $item->getGenericName(),
                    );
                }
            }
        }
        return $result;
    }
    
    /**
     * Add Filter by customer group in customer collection
     * 
     * @return \Mage_Customer_Model_Resource_Customer_Collection
     */
    public function prepareSellerFilter() {
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addAttributeToSelect('company')
            ->addFieldToFilter('group_id', array('in'=>array(Mage::getStoreConfig('softeq/loregistration/privateseller'),Mage::getStoreConfig('softeq/loregistration/govseller'))));
        $collection->addAttributeToFilter('registration_status',array('eq'=>Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED));
        $collection->setOrder('company','ASC');
        return $collection;
    }
}