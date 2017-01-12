<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerOrders_Model_Observer
{
    /**
     * Add seller name to order
     * 
     * @param Varien_Event_Observer $observer
     */
    public function beforePlaceOrder($observer) { 
        $order = $observer->getEvent()->getOrder();
        $quote = $order->getQuote();
        $address = $observer->getAddress();
        $orderItems = $address->getItemsCollection();
        $orderItem = $orderItems->getFirstItem();
        $sellerId  = Mage::helper('buyercheckout')->getCustomerIdFromQuoteItem($orderItem);
        $seller = Mage::getModel("customer/customer")->load($sellerId);
        $quote->setSellerName($seller->getCompany());
        $order->setSellerName($seller->getCompany());
    }
    
	public function afterPlaceOrder($observer) {
	    $order = $observer->getOrder();
            foreach($order->getAddressesCollection() as $_address) {
                $parentAddress = $_address->getCustomerAddressId();
                $address = Mage::getModel('customer/address')->load($parentAddress);
                foreach($address->getData() as $key=>$val) {
                    if(!$_address->getData($key) || !$_address->hasData($key)) {
                        $_address->setData($key,$val);
                    }
                }
                $_address->save();
            }
	}
    /**
     * Add item attributes from quote to order
     * 
     * @param Varien_Event_Observer $observer
     * @return \Lomedic_BuyerOrders_Model_Observer
     */
    public function setAttributes($observer) {
        
        $item = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $item->setBatchNumber($product->getBatchNumber());
        $item->setExpirationDate($product->setExpirationDate());
        return $this;
    }

}
