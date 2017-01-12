<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Block_Adminhtml_Catalog_Product_Grid_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if($row->getTypeId()=='batch') {
            return Mage::helper('core')->currency($row->getPrivatePrice(), true, false) ."/". Mage::helper('core')->currency($row->getGovPrice(), true, false);
        } else {
            return '';
        }
    }
}
