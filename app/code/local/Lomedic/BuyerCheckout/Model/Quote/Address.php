<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * {@inheritdoc}
     *
     * Phone validation has been removed
     */
    protected function _basicCheck()
    {
    }
}
