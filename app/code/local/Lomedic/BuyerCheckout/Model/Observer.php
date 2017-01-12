<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Model_Observer
{
    /**
     * Checkout quote
     *
     * @var Lomedic_BuyerCheckout_Model_Quote
     */
    private $_quote;

    /**
     * Seller id from request
     *
     * @var int
     */
    private $_seller;

    /**
     * Current customer session
     *
     * @var Mage_Customer_Model_Session
     */
    private $_customerSession;

    /**
     * Storage model
     *
     * @var BuyerCheckout_Model_Quote_Items
     */
    private $_storageModel;

    /**
     * Backup lost products after checkout
     *
     * @var array
     */
    private $_lostProducts = array();

    /**
     * Address fields to copy in sales order address
     *
     * @var array
     */
    private $_addressFields = array(
        'municipality',
        'colonia',
        'streetNumber',
        'apartmentNumber'
    );

    /**
     * Update session quote before checkout starts
     * @see controller_action_predispatch_checkout_multishipping_index event
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function updateQuote(Varien_Event_Observer $observer)
    {
        $quote      = $this->getQuote();
        $quoteItems = $quote->getItemsGroupedBySeller();

        $this->_handle($quoteItems);
        $this->_backup();

        $quote->save();
    }

    /**
     * Restore saved quote
     * @see controller_action_predispatch event
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function restoreQuote(Varien_Event_Observer $observer)
    {
        $statusesData = Mage::getModel('sales/order_status')->getResourceCollection()->getData();

        if (!$this->getCustomerSession()->isLoggedIn() || $this->isCheckoutRequest()) {
            return;
        }

        $customer   = $this->getCustomerSession()->getId();
        $quoteItems = $this->getStorageModel()->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('product_id')
            ->addFieldToSelect('product_qty');

        if (count($quoteItems)) {
            $quoteItems = $quoteItems->toArray();
            $this->_restore($quoteItems['items']);
        }
        $this->_removeAddresses();
    }

    /**
     * Update sales quote address
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function handleAddressCopy(Varien_Event_Observer $observer)
    {
        $data   = $observer->getEvent()->getData();
        $source = $data['source'];
        $target = $data['target'];

        foreach ($this->_addressFields as $field) {
            $methodName = 'get' . ucfirst($field);
            $target->setDataUsingMethod($field, $source->$methodName());
        }
        $target->setDataUsingMethod('is_clone', 1);
    }

    /**
     * Update order address. Copy some fields from quote address.
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function handleOrderAddress(Varien_Event_Observer $observer)
    {
        $data  = $observer->getEvent()->getData();
        $quote = $data['quote'];

        $salesOrderAddressModel = Mage::getModel('sales/order_address');

        $quoteShippingAddress   = $quote->getShippingAddress();
        $quoteBillingAddress    = $quote->getBillingAddress();

        foreach ($data['orders'] as $order) {
            $orderShippingAddress = $order->getShippingAddress();
            $orderBillingAddress  = $order->getBillingAddress();

            foreach ($this->_addressFields as $field) {
                $methodName = 'get' . ucfirst($field);
                $orderShippingAddress->setDataUsingMethod($field, $quoteShippingAddress->$methodName());
                $orderBillingAddress->setDataUsingMethod($field, $quoteBillingAddress->$methodName());
            }

            $orderShippingAddress->save();
            $orderBillingAddress->save();
        }
    }

    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        if (null === $this->_customerSession) {
            $this->_customerSession = Mage::getSingleton('customer/session');
        }

        return $this->_customerSession;
    }

    /**
     * Get quote from session
     *
     * @return Lomedic_BuyerCheckout_Model_Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Get seller id
     *
     * @return int
     */
    public function getSellerId()
    {
        return (int) Mage::app()->getRequest()->getParam('seller', 0);
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        return (int) Mage::app()->getRequest()->getParam('product', 0);
    }

    /**
     * Get lost products
     *
     * @return array
     */
    public function getLostProducts()
    {
        return $this->_lostProducts;
    }

    /**
     * Get storage model
     *
     * @return BuyerCheckout_Model_Quote_Items
     */
    public function getStorageModel()
    {
        if (null === $this->_storageModel) {
            $this->_storageModel = Mage::getModel('buyercheckout/quote_items');
        }

        return $this->_storageModel;
    }

    /**
     * Add lost products for backup
     *
     * @param  array $quoteItems
     * @return void
     */
    public function addLostProducts(array $quoteItems)
    {
        foreach ($quoteItems as $quoteItem) {
            $this->addLostProduct($quoteItem);
        }
    }

    /**
     * Add lost products for backup
     *
     * @param  Mage_Sales_Model_Quote_Item $quoteItem
     * @return void
     */
    public function addLostProduct(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        array_push($this->_lostProducts, array(
            'customer_id' => $this->getCustomerSession()->getId(),
            'product_id'  => $quoteItem->getProduct()->getId(),
            'product_qty' => $quoteItem->getQty()
        ));
    }

    /**
     * Check if checkout page (except cart and success pages)
     *
     * @return bool Is checkout request
     */
    public function isCheckoutRequest()
    {
        $request        = Mage::app()->getRequest();

        $moduleName     = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $action         = $request->getActionName();

        return 'checkout' == $moduleName && 'cart' !== $controllerName && 'success' !== $action;
    }

    /**
     * Clear quote items
     *
     * @param  array $quoteItems Quote items
     * @return void
     */
    private function _clear(array $quoteItems)
    {
        $quote = $this->getQuote();

        foreach ($quoteItems as $quoteItem) {
            $quote->removeItem($quoteItem->getId());
        }

        $quote->save();
    }

    /**
     * Backup lost quote products
     *
     * @return void
     */
    private function _backup()
    {
        if ($products = $this->getLostProducts()) {
            foreach ($products as $productData) {
                $this->getStorageModel()->setData($productData)->save();
            }
        }
    }

    /**
     * Restore quote products
     *
     * @param  array $products
     * @return void
     */
    private function _restore(array $products)
    {
        $customer   = $this->getCustomerSession()->getId();
        $cart = Mage::getSingleton('checkout/cart');
        $cart->init();
        foreach ($products as $productData) {
            $product = Mage::getModel('catalog/product')->load($productData['product_id']);

            // Add to cart 
            if ($product) {
                $cart->addProduct($product, array('qty' => $productData['product_qty']));
            }
        }
        try {
            $cart->save();
            $this->getStorageModel()->getCollection()
                ->addFieldToFilter('customer_id', $customer)
                ->walk('delete');
        } catch (Exception $e) {
            throw new Exception('Cant restore products');
        }
        
        
        
    }

    /**
     * Handle quote items
     *
     * @param  array $quoteItems Quote items grouped by seller
     * @return void
     */
    private function _handle(array $quoteItems)
    {
        $sellerId  = $this->getSellerId();
        $productId = $this->getProductId();

        if (!$sellerId) {
            return;
        }

        // Check if quote have seller items
        if (!array_key_exists($sellerId, $quoteItems)) {
            return Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
        }

        foreach ($quoteItems as $seller => $sellerItems) {
            if ($sellerId !== $seller) {
                $this->addLostProducts($sellerItems);
                $this->_clear($sellerItems);

                continue;
            }

            foreach ($sellerItems as $item) {
                if (!in_array($productId, array(0, (int) $item->getProductId()))) {
                   $this->addLostProducts(array($item));
                   $this->_clear(array($item));
                }
            }
        }
    }

    /**
     * Remove cloned addresses
     *
     * @return void
     */
    private function _removeAddresses()
    {
        $toDelete = array();
        $customer = $this->getCustomerSession()->getCustomer();
        $collection = $customer->getAddressCollection();
        $collection->getSelect()->where('parent_id=?',$customer->getId());
        $customerAddresses = $customer->getAddresses();
        foreach ($customerAddresses as $address) {
            if ($address->getIsClone()) {
                $toDelete[] = $address->getId();
            }
        }
        foreach ($collection as $_address) {
            if(in_array($_address->getId(),$toDelete)) {
                $_address->delete();
            }
        }
    }
}
