<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
require_once 'Mage/Checkout/controllers/CartController.php';
class Lomedic_BuyerCheckout_CartController extends Mage_Checkout_CartController
{

    /**
     * Update cart action
     * 
     * @return \Lomedic_BuyerCheckout_CartController
     */
    public function updatePostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        $updateAction = (string) $this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            case 'empty_seller':
                $this->_emptySeller();
                break;
            default:
                $this->_updateShoppingCart();
        }

        $this->_goBack();
    }

    /**
     * Delete quote items based on seller id
     *
     * @return void
     */
    public function _emptySeller()
    {
        $sellerId    = (int) $this->getRequest()->getParam('seller_id');
        $sellerItems = $this->_getCart()->getQuote()->getItemsGroupedBySeller();

        foreach ($sellerItems as $seller => $quoteItems) {
            if ($sellerId == $seller) {
                foreach ($quoteItems as $quoteItem) {
                    $this->_getCart()->removeItem($quoteItem->getId());
                }
            }
        }

        $this->_getCart()->save();
        $this->_goBack();
    }
}
