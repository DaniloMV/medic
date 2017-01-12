<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Widget_Grid_Column_Renderer_Inline extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $html .= '<div class="button m5"><a href="'.$this->getUrl('adminhtml/usersCompany/index/', array('id'=>$row->getCustomerId()?$row->getCustomerId():0)).'">' . Mage::helper('loregistration')->__('Users') . '</a></div>';
        return $html;
    }
}