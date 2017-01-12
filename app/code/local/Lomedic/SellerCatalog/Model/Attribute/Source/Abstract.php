<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_Attribute_Source_Abstract extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_attribute_code;
    
    public function setAttributeCode($attribute) 
    {
        $this->_attribute_code = $attribute;
    }
    
    protected function getAttributeCode() 
    {
        return $this->_attribute_code;
    }

    
    /**
     * Get all options
     * 
     * @return array
     */
    public function getAllOptions()
    {
        $collection= Mage::getModel('loseller/goverment_catalog')->getCollection();
        $collection->addFieldToSelect($this->getAttributeCode());
        $collection->addFieldToSelect('entity_id');
        $collection->addFieldToFilter('is_remove',array('eq'=>0));
        $collection->getSelect()->group($this->getAttributeCode());

        $arr = array();
        foreach($collection as $coll){
            $arr[] = array(
                'label' => Mage::helper('loregistration')->__($coll->getData($this->getAttributeCode())),
                'value' => $coll->getData("entity_id")
            );
        }
        $this->_options = $arr;
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function getFlatColums()
    {
        $columns = array(
            $this->getAttribute()->getAttributeCode() => array(
                'type'      => 'int',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            )
        );
        return $columns;
    }

    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}