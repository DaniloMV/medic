<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Multishipping
{
    /**
     * Get quote items assigned to different quote addresses populated per item qty.
     * Based on result array we can display each item separately
     *
     * @return array
     */
    public function getQuoteShippingAddressesItems()
    {
        if ($this->_quoteShippingAddressesItems !== null) {
            return $this->_quoteShippingAddressesItems;
        }
        $items = array();
        $addresses  = $this->getQuote()->getAllAddresses();
        foreach ($addresses as $address) {
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getProduct()->getIsVirtual()) {
                    $items[] = $item;
                    continue;
                }
                    $item->setCustomerAddressId($address->getCustomerAddressId());
                    $items[] = $item;
            }
        }
        $this->_quoteShippingAddressesItems = $items;
        return $items;
    }
    
    
    /**
     * Reimport customer billing address to quote
     *
     * @param int $addressId customer address id
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function setQuoteCustomerBillingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $address->setIsClone(true);
            if(!$address->getTelephone()) {
                $address->setTelephone('11111111111');
            }
            if(!$address->getFirstname()) {
                $address->setFirstname($address->getCompany());
            }
            $this->getQuote()->getBillingAddress($addressId)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->collectTotals()->save();
        }
        return $this;
    }
    
    /**
     * Sest shipping items information
     * 
     * @param array $info
     * @return \Lomedic_BuyerCheckout_Model_Type_Multishipping
     */
    public function setShippingItemsInformation($info)
    {
        $addressId       = $info['address'];
        Mage::getSingleton('core/session')->setData('address_id', $addressId);

        $sellerAddresses = $this->cloneAddress($addressId);
        $info            = $this->updateRequestInfo($sellerAddresses);

        /**
         * Code after this line inherited from parent as is
         */
        if (is_array($info)) {
            $allQty    = 0;
            $itemsInfo = array();

            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $allQty += $data['qty'];
                    $itemsInfo[$quoteItemId] = $data;
                }
            }

            $maxQty = (int)Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty');

            if ($allQty > $maxQty) {
                Mage::throwException(Mage::helper('checkout')->__('Maximum qty allowed for Shipping to multiple addresses is %s', $maxQty));
            }

            $quote     = $this->getQuote();
            $addresses = $quote->getAllShippingAddresses();

            foreach ($addresses as $address) {
                $quote->removeAddress($address->getId());
            }

            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $this->_addShippingItem($quoteItemId, $data);
                }
            }

            /**
             * Delete all not virtual quote items which are not added to shipping address
             * MultishippingQty should be defined for each quote item when it processed with _addShippingItem
             */
            foreach ($quote->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual() &&
                    !$_item->getParentItem() &&
                    !$_item->getMultishippingQty()
                ) {
                    $quote->removeItem($_item->getId());
                }
            }

            if ($billingAddress = $quote->getBillingAddress()) {
                $quote->removeAddress($billingAddress->getId());
            }

            if ($customerDefaultBilling = $this->getCustomerDefaultBillingAddress()) {
                $quote->getBillingAddress()->importCustomerAddress($customerDefaultBilling);
            }

            foreach ($quote->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual()) {
                    continue;
                }

                if (isset($itemsInfo[$_item->getId()]['qty'])) {
                    if ($qty = (int) $itemsInfo[$_item->getId()]['qty']) {
                        $_item->setQty($qty);
                        $quote->getBillingAddress()->addItem($_item);
                    } else {
                        $_item->setQty(0);
                        $quote->removeItem($_item->getId());
                    }
                 }
            }

            $this->save();

            Mage::dispatchEvent('checkout_type_multishipping_set_shipping_items', array('quote'=>$quote));
        }

        return $this;
    }

    /**
     * Get sellers
     *
     * @return array Sellers ids
     */
    public function getSellers()
    {
        $quoteItems = $this->getQuoteShippingAddressesItems();
        $products   = array();

        foreach ($quoteItems as $quoteItem) {
            $products[] = $quoteItem->getProductId();
        }

        $productsCollection = Mage::getModel('marketplace/product')->getCollection()
            ->addFieldToSelect('userid')
            ->addFieldToFilter('mageproductid', array('in' => $products));

        return $productsCollection->getColumnValues('userid');
    }

    /**
     * Clone customer address. Create fake address for each seller
     *
     * @param  int $addressId Address id
     * @return void|array Seller/Address association
     */
    protected function cloneAddress($addressId)
    {
        // Get customer address
        $defaultAddress     = Mage::getModel('customer/address')->load($addressId);
        $defaultAddressData = $defaultAddress->getData();

        unset($defaultAddressData['entity_id']);

        if (!$defaultAddress->getId()) {
            return;
        }

        $addresses = array();

        // Clone customer address for each seller
        foreach ($this->getSellers() as $sellerId) {
            $address = Mage::getModel('customer/address')
                ->setData($defaultAddressData)
                ->setIsDefaultShipping('0')
                ->setSaveInAddressBook('0')
                ->setIsClone(true)
                ->setSeller($sellerId)
                ->save();

            $addresses[$sellerId] = $address->getId();
        }

        return $addresses;
    }

    /**
     * Update addresses request data
     * 
     * @see    setShippingItemsInformation method
     *
     * @param  array $addressesData
     * @return array Request info
     */
    protected function updateRequestInfo($addressesData)
    {
        $quoteItems  = $this->getQuoteItems();
        $requestInfo = array();

        foreach ($quoteItems as $quoteItem) {
            // Get seller id by quote product
            $product      = $quoteItem->getProductId();
            $salesProduct = Mage::getModel('marketplace/product')
                ->getCollection()
                ->addFieldToFilter('mageproductid', array('eq' => $product))
                ->getFirstItem();

            $sellerId = $salesProduct->getUserid();

            // Create new request data
            if ($addressId = $addressesData[$sellerId]) {
                $requestInfo[][$quoteItem->getId()] = array(
                    'qty'     => $quoteItem->getQty(),
                    'address' => $addressId
                );
            }
        }

        return $requestInfo;
    }
}
