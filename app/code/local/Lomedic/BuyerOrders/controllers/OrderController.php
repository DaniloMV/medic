<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
require_once 'Mage/Sales/controllers/OrderController.php';
class Lomedic_BuyerOrders_OrderController extends Mage_Sales_OrderController
{
    /**
     * Order complete state
     */
    const COMPLETE_STATE = 'complete';

    public function receiveAction()
    {
        if (!$id = $this->getRequest()->getParam('id', false)) {
            $this->_redirect('sales/order/history');
            return;
        }

        if (!$order = $this->_getOrder($id)) {
            $this->_redirect('sales/order/history');
            return;
        }

        try {
            $order->setData('state', self::COMPLETE_STATE)
                ->setStatus(self::COMPLETE_STATE);

            $history = $order->addStatusHistoryComment('Order was set to Complete.', false);
            $history->setIsCustomerNotified(false);

            $order->save();

            Mage::getSingleton('core/session')->addSuccess('Order has been received');
            $this->_redirect('sales/order/history');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addSuccess('Error status update. Try again later.');
            $this->_redirect('sales/order/view', array('id' => $id));
        }
    }

    /**
     * Get order
     *
     * @param  int $id
     * @return Mage_Sales_Model_Order|null
     */
    protected function _getOrder($id)
    {
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->addFieldToFilter('entity_id', (int) $id);
        ;

        return ($orders->getSize()) ? $orders->getFirstItem() : null;
    }
}
