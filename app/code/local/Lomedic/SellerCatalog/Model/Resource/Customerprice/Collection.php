<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_Resource_Customerprice_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() 
    {
        $this->_init('loseller/customerprice');
    }
}