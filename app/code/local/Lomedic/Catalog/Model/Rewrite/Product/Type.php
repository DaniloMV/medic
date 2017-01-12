<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Model_Rewrite_Product_Type extends Mage_Catalog_Model_Product_Type
{
    /**
     * Add new product type
     * 
     * @return array
     */
    static public function getOptionArray()
    {
        $options = array();
        /*
             - Product Simple: catalog/product_type_simple
             - Virtual Product : catalog/product_type_virtual
             - Bundle Product : catalog/bundle/product_type
             - Downloadable Product : downloadable/product_type
             - Configurable Product : catalog/product_type_configurable
             - Grouped Product : catalog/product_type_grouped
             - Batch Product : loseller/product_type_batch
            */
        $allowTypes = array('catalog/product_type_simple','loseller/product_type_batch');
        foreach(self::getTypes() as $typeId=>$type)
        {
            
            if(in_array($type['model'],$allowTypes)) {
                if($type['model']=='catalog/product_type_simple') {
                    $type['label'] = "Product";
                }
                $options[$typeId] = Mage::helper('catalog')->__($type['label']);
            }
        }

        return $options;
    }
}
