<?php

class Vovsky_Customerprice_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCustomerprice($product)
    {
        $customerPrice = $product->getCustomerPrice();
        if ($customerPrice && $customerPrice>0){
            //check if module enabled
            $attribute = Mage::getResourceModel('eav/entity_attribute_collection')->addFieldToFilter('attribute_code', 'customer_price')->load()->getFirstItem();
            if ($attribute->getBackendModel()=='customerprice/customerprice'){
                return $customerPrice;
            }
        }
        return false;
    }
}