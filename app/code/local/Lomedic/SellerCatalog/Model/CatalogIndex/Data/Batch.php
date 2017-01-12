<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_CatalogIndex_Data_Batch extends Mage_CatalogIndex_Model_Data_Abstract
{
    public function getTypeCode()
    {
        return Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT;
    }
}
