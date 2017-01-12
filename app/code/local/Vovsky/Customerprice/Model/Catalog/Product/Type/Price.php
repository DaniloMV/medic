<?php

class Vovsky_Customerprice_Model_Catalog_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    public function getPrice($product)
    {
        $customerPrice = Mage::helper('customerprice')->getCustomerprice($product);
        if ($customerPrice) {
            $product->setFinalPrice($customerPrice);
            return $customerPrice;
        }
        return $product->getData('price');
    }
}
