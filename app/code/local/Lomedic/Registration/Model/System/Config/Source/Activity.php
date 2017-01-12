<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_System_Config_Source_Activity extends Mage_Eav_Model_Entity_Attribute_Source_Table
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
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Pharmacy')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Hospital')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('Distributor')),
            array('value' => 4, 'label'=>Mage::helper('adminhtml')->__('Pharmacy laboratory')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(){
        return array(
            1 => 'Pharmacy',
            2 => 'Hospital',
            3 => 'Distributor',
            4 => 'Pharmacy laboratory'
        );
    }
}