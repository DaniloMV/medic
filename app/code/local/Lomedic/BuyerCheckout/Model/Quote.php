<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Model_Quote extends Mage_Sales_Model_Quote
{
    /**
     * Get quote items grouped by seller
     *
     * @return array
     */
    public function getItemsGroupedBySeller()
    {
        $items = array();

        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $sellerId           = Mage::helper('buyercheckout')->getCustomerIdFromQuoteItem($item);
                $items[$sellerId][] = $item;
            }
        }
        return $items;
    }
}
