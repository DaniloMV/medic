<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Form_Step2 extends Lomedic_Registration_Block_Form_Abstract
{
    /**
     * Retrieve form posting url
     * @return string
     */
    public function getPostActionUrl() 
    {
        return $this->getUrl('*/*/step2Post',array('_secure'=>true));
    }
    
    /**
     * Get dropdown html
     * 
     * @param array $params
     * @return string
     */
    public function getAccountTypeHtmlSelect($params='') 
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'account_type');
        $html .= $this->_rendererAttribute($attributeModel,$this->escapeHtml($this->getCustomer()->getAccountType()),true,$params);
        return $html;
    }
}