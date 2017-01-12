<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
$installer = $this;

/**
 * Rename order statuses
 *
 * New Order -> pending state
 * Order has been shipped -> processing state
 * Order has been delivered -> complete state
 */

$orderStatuses = array(
    'new'            => 'New order',
    'processing'     => 'Shipped',
    'complete'       => 'Delivered',
    'payment_review' => 'Payment received'
);

foreach ($orderStatuses as $state => $label) {
    $status = Mage::getModel('sales/order_status')->loadDefaultByState($state);

    if ($status->getId()) {
        $status->setLabel($label)->save();
    }
}

$installer->endSetup();
