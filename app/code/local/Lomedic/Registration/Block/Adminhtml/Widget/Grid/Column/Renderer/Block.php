<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Widget_Grid_Column_Renderer_Block extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $customer = Mage::getModel('customer/customer')->load($row->getCustomerId());
        $action = $customer->getIsActive() == 1? Mage::helper('loregistration')->__('Block') : Mage::helper('loregistration')->__('Activate');
        $html .= '<div class="button m5"><a class="comp-block" url="'.$this->getUrl('adminhtml/companies/block/', array('active' =>$customer->getIsActive(),'id'=>$row->getCustomerId())).'">'.$action.'</a></div>';
        return $html;
    }
}