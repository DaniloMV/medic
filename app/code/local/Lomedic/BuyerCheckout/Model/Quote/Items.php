<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Model_Quote_Items extends Mage_Core_Model_Abstract
{
    /**
     * Db table
     */
    const TABLE = 'buyercheckout/quote_items';

    /**
     * Initialize model
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init(self::TABLE);
    }
}
