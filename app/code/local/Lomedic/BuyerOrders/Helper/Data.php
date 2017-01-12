<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerOrders_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get order tracking
     *
     * @param  Mage_Sales_Model_Order $order
     * @return array Trackings
     */
    public function getTracking(Mage_Sales_Model_Order $order)
    {
        $tracking           = array();
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->setOrderFilter($order)
            ->load();

        foreach ($shipmentCollection as $shipment) {
            foreach($shipment->getAllTracks() as $track) {
                $tracking[] = $track;
            }
        }

        return $tracking;
    }

    /**
     * Get seller from order
     *
     * @param  Mage_Sales_Model_Order
     * @return Mage_Model_Customer_Customer
     */
    public function getSeller($order)
    {
        $orderItem   = array_shift($order->getAllVisibleItems());
        $productData = Mage::getModel('marketplace/product')->getCollection()
            ->addFieldToFilter('mageproductid', array($orderItem->getProductId()))
            ->getFirstItem();

        $customer = Mage::getModel('customer/customer')->load($productData->getUserid());
        return $customer;
    }
}
