<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Block_Catalog_Product_Renderer_Certificate extends Varien_Data_Form_Element_Text
{
    
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        return $html."<a href='".$this->getEscapedValue()."'>".Mage::helper('loseller')->__('Download')."</a>";
    }
    
    public function getElementHtml()
    {
        if($this->getEscapedValue()) {
            $html = $this->getAfterElementHtml();
        } else {
            $html = Mage::helper('loseller')->__('No file');
        }
        return $html;
    }
   
}