<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Model_Catalog_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product)
    {
        if($product->getData('customer_price')) {
            return $product->getData('customer_price');
        }
        return $product->getData('price');
    }
}
