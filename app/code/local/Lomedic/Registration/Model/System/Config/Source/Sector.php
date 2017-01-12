<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_System_Config_Source_Sector extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions() 
    {
        return $this->toOptionArray();
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Private sector')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Government sector')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            1 => 'Private sector',
            2 => 'Government sector',
        );
    }
}