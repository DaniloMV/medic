<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Model_Resource_Catalog_Product_Price_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{
    protected function _construct() 
            {
        $this->_init('locatalog/catalog_product_price');
    }
}