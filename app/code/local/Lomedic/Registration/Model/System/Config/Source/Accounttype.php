<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_System_Config_Source_Accounttype extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    const ACCOUNT_TYPE_NATURAL = 1;
    const ACCOUNT_TYPE_LEGAL = 2;
    
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
            array('value' => self::ACCOUNT_TYPE_NATURAL, 'label'=>Mage::helper('adminhtml')->__('Natural person')),
            array('value' => self::ACCOUNT_TYPE_LEGAL, 'label'=>Mage::helper('adminhtml')->__('Legal person')),
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
            self::ACCOUNT_TYPE_NATURAL => 'Natural person',
            self::ACCOUNT_TYPE_LEGAL => 'Legal person',
        );
    }
}