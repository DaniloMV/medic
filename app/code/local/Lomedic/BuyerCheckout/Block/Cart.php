<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Block_Cart extends Mage_Checkout_Block_Cart
{
    /**
     * Get quote items grouped by seller
     *
     * @return array
     */
    public function getItemsGroupedBySeller()
    {
        if ($this->getCustomItems()) {
            return $this->getCustomItems();
        }

        $items = array();

        foreach (parent::getItems() as $item) {
            $sellerId           = Mage::helper('buyercheckout')->getCustomerIdFromQuoteItem($item);
            $items[$sellerId][] = $item;
        }

        return $items;
    }

    /**
     * Get customer company name
     * 
     * @param  int $customerId Customer id
     * @return void|string Company name
     */
    public function getCompanyName($customerId)
    {
        if ($customer = Mage::getModel('customer/customer')->load($customerId)) {
            return $customer->getCompany();
        }
    }
}
