<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Form_Step1 extends Lomedic_Registration_Block_Form_Abstract
{
    /**
     * Retrieve form posting url
     * @return string
     */
    public function getPostActionUrl() {
        return $this->getUrl('*/*/step1Post',array('_secure'=>true));
    }

    /**
     * Get dropdown html
     * 
     * @return string
     */
    public function getCountryHtmlSelect($defValue=null, $name='country_id', $id='country', $title='Country') 
    {
        $defValue = $this->escapeHtml($this->getCustomer()->getAddress()->getCountry());
        return parent::getCountryHtmlSelect($defValue,'address[country_id]');
    }
}