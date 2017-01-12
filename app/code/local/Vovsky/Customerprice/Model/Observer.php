<?php

class Vovsky_Customerprice_Model_Observer
{

    public function setFormElemRenderer($observer)
    {
        $form = $observer->getForm();
        if ($customerPrice = $form->getElement('customer_price')) {
            $customerPrice->setRenderer(
                Mage::app()->getLayout()->createBlock('customerprice/adminhtml_catalog_product_edit_tab_price_customer')
            );
        }
    }

    public function joinCollection($observer)
    {
        $cId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $wId = Mage::app()->getWebsite()->getId();
        $connection = $observer->getCollection()->getConnection();
        
        $joinCond = join(' AND ', array(
            'customer_price.entity_id = e.entity_id',
            $connection->quoteInto('customer_price.customer_group_id = (?)', $cId),
            $connection->quoteInto('customer_price.website_id IN(?)', array(0, $wId)),
        ));

        $colls = array('customer_price' => 'value');
        $select = $observer->getCollection()->getSelect();
        $select->joinLeft(
            array('customer_price' => $observer->getCollection()->getTable('customerprice/customer_price')),
            $joinCond,
            $colls
        );
    }

    public function applyCustomerPrice($observer)
    {
        $customerPrice = $observer->getQuoteItem()->getProduct()->getCustomerPrice();
        if ($customerPrice) {
            $item = $observer->getQuoteItem();
            if ($item->getParentItem()) {
                $item = $item->getParentItem();
            }
            $item->setCustomPrice($customerPrice);
        }
    }

    public function joinSalesQuoteCollection($observer)
    {
        foreach ($observer->getCollection()->getItems() as $item) {
            $item = $this->addCustomerPrice($item);
        }
    }

    public function addCustomerPrice($product) {
        $product = Mage::getModel('customerprice/customerprice')->addCustomerPrice($product);
        return $product;
    }
    public function setProduct($observer)
    {
        $item = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $item->setCustomerPrice($product->getCustomerPrice());

    }
    public function updateCustomerPrice($observer)
    {
        foreach ($observer->getCart()->getQuote()->getItemsCollection() as $item/* @var $item Mage_Sales_Model_Quote_Item */) {
            if ($item->getParentItem()) {
                $item = $item->getParentItem();
            }

            $customerPrice = $item->getCustomerPrice();
            if ($customerPrice) {
                $item->setCustomPrice($customerPrice);
            }
        }
    }
}