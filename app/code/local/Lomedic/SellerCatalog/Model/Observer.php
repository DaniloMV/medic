<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_Observer
{
    /**
     * Load batch form
     * 
     * @param Varien_Event_Observer $observer
     * @return \Zend_Controller_Response_Abstract
     */
    public function loadNewBatchLayout($observer) 
    {
        $type   = $observer->getType();
        $layout = $observer->getLayout();
        
        if($type==Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT) {
            $productId = Mage::app()->getRequest()->getParam('search_name');
            $product = Mage::getModel('catalog/product')->load($productId);
            if(!$productId || !$product->getId()) {
               return Mage::app()->getResponse()->setRedirect(Mage::getUrl("seller/marketplaceaccount/batches"))->sendResponse();
            }
            $layout->loadLayout(array('default','marketplace_account_batchproduct'));
            $layout->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('MarketPlace Product Type: Configurable Product'));
        }
    }
    
    /**
     * Change product list
     * 
     * @param Varien_Event_Observer $observer
     * @return \Zend_Controller_Response_Abstract
     */
    public function changeMarketplaceProductListTemplate($observer) 
    {

        if(Mage::getSingleton('customer/session')->getUserCompany()){
            $usersColl = Mage::getModel('loregistration/usersCompany')->load(Mage::getSingleton('customer/session')->getUserCompany());
            $usersData = $usersColl->getData();
        }

        if((Mage::getSingleton('customer/session')->getCustomer()->getIsActive() !== null && Mage::getSingleton('customer/session')->getCustomer()->getIsActive() === '0')
            || (isset($usersData) && $usersData["is_active"] == 0)){

            Mage::getSingleton('customer/session')->setUserCompany(null);
            Mage::getSingleton('customer/session')->setManageProfileSettings(null);
            Mage::getSingleton('customer/session')->setManageSales(null);
            Mage::getSingleton('customer/session')->setManageBatches(null);
            Mage::getSingleton('customer/session')->setManageProducts(null);

            echo Mage::app()->getResponse()->setRedirect(Mage::getUrl("customer/account/logout"))->sendResponse();
            exit();
        }

        $fullActionName = $observer->getEvent()->getAction()->getFullActionName();
        $layout = $observer->getLayout();
        if ($fullActionName=='marketplace_marketplaceaccount_myproductslist') {

            if(Mage::getSingleton('customer/session')->getUserCompany() && !Mage::getSingleton('customer/session')->getManageProducts()){
                $layout->getBlock('root')->setTemplate('page/1column.phtml');
                $layout->getBlock('marketplace_myproductslist')->setTemplate('seller/error.phtml');
            }
        }

        if ($fullActionName=='marketplace_marketplaceaccount_batches') {
            if(Mage::getSingleton('customer/session')->getUserCompany() && !Mage::getSingleton('customer/session')->getManageBatches()){
                $layout->getBlock('root')->setTemplate('page/1column.phtml');
                $layout->getBlock('marketplace_batches')->setTemplate('seller/error.phtml');
            }
        }
        if ($fullActionName=='marketplace_marketplaceaccount_myorderhistory') {
            if(Mage::getSingleton('customer/session')->getUserCompany() && !Mage::getSingleton('customer/session')->getManageSales()){
                $layout->getBlock('root')->setTemplate('page/1column.phtml');
                $layout->getBlock('marketplace_myorderhistory')->setTemplate('seller/error.phtml');
            }
        }
        if ($fullActionName=='loseller_marketplaceaccount_new') {
            $layout->getBlock('root')->setTemplate('page/1column.phtml');
        }
	    if ($fullActionName=='marketplace_marketplaceaccount_new') {
            $layout->getBlock('root')->setTemplate('page/1column.phtml');
        }

        if ($fullActionName=='contacts_index_index') {
            $layout->getBlock('root')->setTemplate('page/1column.phtml');
        }

        if ($fullActionName=='marketplace_marketplaceaccount_editprofile') {

            $layout->getBlock('root')->setTemplate('page/1column.phtml');
            $layout->getBlock('edit_myprofile')->setTemplate('seller/edit_myprofile.phtml');
        }
    }

    /**
     * Reset Customer Save
     *
     */
    public function saveSimpleProduct($observer)
    {
        //
    }
    
    /**
     * Delete batches after delete product
     * 
     * @param Varien_Event_Observer $observer
     * @return \Lomedic_SellerCatalog_Model_Observer
     */
    public function deleteBatchesAfterProduct($observer) 
    {
        $product = $observer->getProduct();
        if($product->getTypeId()!=Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT) {
            return $this;
        }
        $productId = $product->getId(); 
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('batch_parent_product',array('eq'=>$productId));
        Mage::register('isSecureArea',true,true);
        foreach ($collection as $item) {
            $item->delete();
        }
        Mage::unregister('isSecureArea');
    }
}