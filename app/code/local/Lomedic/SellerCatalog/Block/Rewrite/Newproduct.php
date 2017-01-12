<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Block_Rewrite_Newproduct extends Webkul_Marketplace_Block_Newproduct
{
    public function getAllowedProductTypes()
    {
            $alloweds=explode(',',Mage::getStoreConfig('marketplace/marketplace_options/allow_for_seller'));
            $data =  array('simple'=>'Simple',
                        'downloadable'=>'Downloadable',
                        'virtual'=>'Virtual',
                        'configurable'=>'Configurable',
                        'grouped'=>'Grouped Product',
                        'bundle'=>'Bundle Product',
                        Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT=>'Batch'
                    );
            $allowedproducts=array();
            foreach($alloweds as $allowed){
                    array_push($allowedproducts,array('value'=>$allowed, 'label'=>$data[$allowed]));
            }
            return $allowedproducts;
    }
}
