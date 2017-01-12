<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Get product list by user id
     * 
     * @retrun \Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductList() 
    {
        $userId=Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('code')
            ->setOrder('name','ASC');
        $collection->getSelect()
                ->joinInner(array('mp'=>Mage::getSingleton('core/resource')->getTableName('marketplace/product')),'mp.mageproductid=e.entity_id','')
                ->where('e.type_id !=?',Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT)
                ->where('mp.userid=?',$userId);
        return $collection;
    }
}
