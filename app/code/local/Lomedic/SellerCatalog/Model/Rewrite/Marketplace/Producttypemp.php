<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_Rewrite_Marketplace_Producttypemp extends Webkul_Marketplace_Model_Producttypemp
{
    /**
     * Get options
     * @return array
     */
    public function toOptionArray()
    {
        $data =  array(
                array('value'=>'simple', 'label'=>'Simple'),
                array('value'=>Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT, 'label'=>'Batch'),
            );
        return  $data;                

    }
}