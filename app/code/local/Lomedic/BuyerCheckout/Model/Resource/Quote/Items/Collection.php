<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Model_Resource_Quote_Items_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Table name
     */
    const TABLE = 'buyercheckout/quote_items';

    /**
     * Collection initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(self::TABLE);
    }
}
