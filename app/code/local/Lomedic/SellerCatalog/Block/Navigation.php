<?php
class Lomedic_SellerCatalog_Block_Navigation extends Lomedic_Catalog_Block_Navigation
{
    public function getProductCollection() {
        $collection = parent::getProductCollection();
        $userId=Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection->getSelect()->joinInner(array('mp'=> Mage::getSingleton('core/resource')->getTableName('marketplace/product')),'e.entity_id=mp.mageproductid','');
        $collection->getSelect()->where('mp.userid=?',$userId);
        return $collection;
    }
}
