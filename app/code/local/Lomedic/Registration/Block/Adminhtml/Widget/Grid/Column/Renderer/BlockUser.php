<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Widget_Grid_Column_Renderer_BlockUser extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $action = $row->getIsActive() == 1 ? Mage::helper('loregistration')->__('Block') : Mage::helper('loregistration')->__('Activate');
        $html .= '<div class="button m5"><a class="comp-block" url="'.$this->getUrl('adminhtml/usersCompany/block/', array('active' =>$row->getIsActive(),'id'=>$row->getUserCompanyId())).'">'.$action.'</a></div>';
        return $html;
    }
}