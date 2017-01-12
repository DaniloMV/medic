<?php

class Webkul_Marketplace_Block_Products extends Mage_Core_Block_Template
{
    protected $_productsCollection = null;

    /**
     * Allowed order statuses
     *
     * @var array
     */
    protected $_allowedStatuses = array(
        'canceled',
        'complete',
        'payment_review',
        'pending',
        'pending_payment',
        'processing'
    );

    public function __construct()
    {
        parent::__construct();

        $userId         = Mage::getSingleton('customer/session')->getId();

        $filterOrderId  = $this->getRequest()->getParam('s');
        $filterDataFrom = $this->getRequest()->getParam('from_date');
        $filterDataTo   = $this->getRequest()->getParam('to_date');

        if ($filterDataTo) {
            $toDate = date_create($filterDataTo);
            $to     = date_format($toDate, 'Y-m-d 23:59:59');
        }

        if ($filterDataFrom) {
            $fromDate = date_create($filterDataFrom);
            $from     = date_format($fromDate, 'Y-m-d H:i:s');
        }

        $collection = Mage::getModel('marketplace/saleslist')->getCollection();
        $collection->addFieldToFilter('cleared_at', array('datetime' => true,'from' => $from,'to' =>  $to))
            ->addFieldToFilter('mageproownerid', array('eq' => $userId))
            ->distinct(true);

        if($filterOrderId){
            $collection->addFieldToFilter('magerealorderid', array('like' => "%$filterOrderId%"));
        }

        $collection->setOrder('autoid','AESC');
        $collection->getSelect()->group('magerealorderid');

        $filterPriceFrom = (float) $this->getRequest()->getParam('from_price');
        $filterPriceTo   = (float) $this->getRequest()->getParam('to_price');
        $filterPayment   = $this->getRequest()->getParam('payment');
        $filterBuyer     = (int) $this->getRequest()->getParam('buyer');
        $filterStatus    = $this->getRequest()->getParam('orderstatus');

        if ($filterPriceFrom || $filterPriceTo || $filterPayment || $filterBuyer || $filterStatus) {
            $filteredByPrice   = array();
            $filteredByPayment = array();
            $filteredByBuyer   = array();
            $filteredByStatus  = array();

            foreach ($collection as $salesOrder) {
                $order        = Mage::getModel('sales/order')->load($salesOrder->getMageorderid());
                $orderPrice   = (float) Mage::getModel('marketplace/saleslist')->getPricebyorder($salesOrder->getMageorderid());
                $orderPayment = $order->getPayment()->getMethodInstance()->getCode();
                $orderStatus  = $order->getStatus();
                $buyerId      = $order->getCustomerId();

                // Price filter
                if ($filterPriceFrom && $filterPriceTo) {
                    if ($filterPriceFrom <= $orderPrice && $filterPriceTo >= $orderPrice) {
                        $filteredByPrice[] = $salesOrder->getAutoid();
                    }
                } else {
                    if (($filterPriceFrom && $filterPriceFrom <= $orderPrice) || ($filterPriceTo && $filterPriceTo >= $orderPrice)) {
                        $filteredByPrice[] = $salesOrder->getAutoid();
                    }
                }

                // Payment filter
                if ($filterPayment) {
                    if ($filterPayment == $orderPayment) {
                        $filteredByPayment[] = $salesOrder->getAutoid();
                    }
                }

                // Buyer filter
                if ($filterBuyer) {
                    if ($filterBuyer == $buyerId) {
                        $filteredByBuyer[] = $salesOrder->getAutoid();
                    }
                }

                // Status filter
                if ($filterStatus) {
                    if ($filterStatus == $orderStatus) {
                        $filteredByStatus[] = $salesOrder->getAutoid();
                    }
                }
            }

            $this->_applyFilter($collection, $filterPriceFrom || $filterPriceTo, $filteredByPrice)
                ->_applyFilter($collection, $filterPayment, $filteredByPayment)
                ->_applyFilter($collection, $filterBuyer, $filteredByBuyer)
                ->_applyFilter($collection, $filterStatus, $filteredByStatus);
        }

        $collection->clear();
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->getCollection()->load();

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get buyers
     *
     * @return array
     */
    public function getBuyers()
    {
        $userId      = Mage::getSingleton('customer/session')->getId();
        $salesOrders = Mage::getModel('marketplace/saleslist')->getCollection()
            ->addFieldToFilter('mageproownerid', array('eq' => $userId));

        $customers = array();
        foreach ($salesOrders as $salesOrder) {
            $order       = Mage::getModel('sales/order')->load($salesOrder->getMageorderid());
            $customers[] = array(
                'id'   => $order->getCustomerId(),
                'name' => $order->getCustomerName()
            );
        }

        return array_unique($customers, SORT_REGULAR);
    }

    /**
     * Get sales statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        $statusesData = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
        $statuses     = array();

        foreach ($statusesData as $status) {
            if (!in_array($status['status'], $this->getAllowedStatuses())) {
                continue;
            }

            $statuses[] = array(
                'id'   => $status['status'],
                'name' => $status['label']
            );
        }

        return $statuses;
    }

    /**
     * Get allowed statuses
     *
     * @return array
     */
    public function getAllowedStatuses()
    {
        return $this->_allowedStatuses;
    }

    /**
     * Get order status
     *
     * @return string
     */
    public function getStatus($order)
    {
        $stateCode  = $order->getStatus();
        $status     = Mage::getModel('sales/order_status')
            ->getResourceCollection()
            ->addFieldToFilter('status', array('eq' => $order->getStatus()))
            ->getFirstItem();

        return $status->getLabel();
    }

    /**
     * Apply filter
     *
     * @param  Mage_Sales_Model_Resource_Order_Collection $collection
     * @param  bool $filter Apply filter?
     * @param  array $rules
     * @return Lomedic_BuyerOrders_Block_Order_History
     */
    protected function _applyFilter(&$collection, $filter, array $rules)
    {
        if ($filter) {
            if (count($rules)) {
                $collection->addFieldToFilter('autoid', array('in' => $rules));
            } else {
                $collection->addFieldToFilter('autoid', array('in' => array(0)));
            }
        }

        return $this;
    }
}
