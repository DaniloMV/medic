<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Block_Adminhtml_Catalog_Product_Grid_Renderer_Seller extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  \Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
     */
    public function render(Varien_Object $row)
    {
        if(Mage::registry('admin_product_grid_sellers')) {
            $sellers = Mage::registry('admin_product_grid_sellers');
            Mage::unregister('admin_product_grid_sellers');
        }
        if(!isset($sellers[$row->getSeller()])) {
            $customer = Mage::getModel('customer/customer')->load($row->getSeller());
            $sellers[$row->getSeller()] = $customer->getCompany();
        }
        $row->setSeller($sellers[$row->getSeller()]);
        Mage::register('admin_product_grid_sellers', $sellers);
        return parent::render($row); 
    }
}
