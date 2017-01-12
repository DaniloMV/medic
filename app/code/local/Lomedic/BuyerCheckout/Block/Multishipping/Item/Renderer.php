<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Block_Multishipping_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    protected $_governmentCatalog ;
    
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
     * 
     * @param int $attrId
     * @return \Varien_Object|\Varien_Object()Get value by code
     * @param int $attrId
     * @return \Varien_Object()
     */
    public function getGovernmentValueById($attrId) {
        $catalog = $this->getGovermentCatalogCollection();
        if(isset($catalog[$attrId])) {
            return new Varien_Object($catalog[$attrId]);
        }
        return new Varien_Object();
    }
    
    /**
     * Get item product
     *
     * @return \Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $parentProduct = parent::getProduct();
        return Mage::getModel('catalog/product')->load($parentProduct->getId());
    }
}
