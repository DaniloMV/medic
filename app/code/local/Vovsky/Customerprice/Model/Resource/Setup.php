<?php

class Vovsky_Customerprice_Model_Resource_Setup  extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model' => 'catalog/product',
                'attribute_model' => 'catalog/resource_eav_attribute',
                'table' => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes' => array(
                    'customer_price' => array(
                        'type' => 'decimal',
                        'label' => 'Customer Price',
                        'input' => 'text',
                        'backend' => 'customerprice/customerprice',
                        'required' => false,
                        'sort_order' => 6,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'apply_to' => 'simple,configurable,virtual',
                        'group' => 'Prices',
                    )
                )
            )
        );
    }
}