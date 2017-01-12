<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Widget_Grid_Column_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $html .= '<div class="button m5"><a class="comp-del" url="'.$this->getUrl("adminhtml/companies/delete/", array("id"=>$row->getCustomerId())).'" >' . Mage::helper('loregistration')->__('Delete') . '</a></div>';

        return $html;
    }
}