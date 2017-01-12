<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Model_Observer {
    /**
     * Add Real Name variable to product
     * 
     * @param Varien_Event_Observer $observer
     */
    public function frontendProductInit($observer) {
        $product = $observer->getProduct();
        $product->setRealName($product->getName());
    }
    
    /**
     * Change product price depending on customer group
     * 
     * @param Varien_Event_Observer $observer
     */
    public function addCustomerPrice($observer) {
        $product = $observer->getProduct();
        $productId = Mage::app()->getRequest()->getParam('product');
        if(!$productId) {
            $productId = $product->getId();
        }
        $collection = Mage::getResourceModel('locatalog/catalog_product_price_collection');
        $collection->getSelect()
                ->where('entity_id=?',$productId);
        $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privateseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/privatebuyer')) {
            $collection->getSelect()->where('customer_group_id=?',Mage::getStoreConfig('softeq/loregistration/privatebuyer'));
                                    
        }
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/govseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/govbuyer')) {
            $collection->getSelect()->where('customer_group_id=?',Mage::getStoreConfig('softeq/loregistration/govbuyer'));
        }
                
        $customerPriceModel = $collection->getFirstItem();
        $product->setCustomerPrice($customerPriceModel->getValue());
    }
    
    /**
     * Cnage product status for sales
     * 
     * @param Varien_Event_Observer $observer
     * @return \Mage_Catalog_Model_Product
     */
    public function isProductSalable($observer) {
        $product = $observer->getSalable();
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $product->setIsSalable(false);
        }
        $product = $product->getProduct();
        
        /**
         * FIX BUG MEDICJOINT-530
        $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privateseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/govseller') || Mage::getSingleton('customer/session')->getCustomer()->getRegistrationStatus()!=Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
            $product->setIsSalable(false);
        }
        */
        
        $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privateseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/govseller') || Mage::getSingleton('customer/session')->getCustomer()->getRegistrationStatus()!=Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED) {
            return $product->setHideAddToCart(false);
        }
        return $product->setHideAddToCart(true);
    }
}