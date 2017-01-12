<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Widget_Grid_Column_Renderer_UserCompany extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $html .= '<div id="del'.$row->getUserCompanyId().'" class="button m5"><a class="comp-del" url="'.$this->getUrl("adminhtml/usersCompany/delete/", array("id"=>$row->getUserCompanyId())).'" >' . Mage::helper('loregistration')->__('Delete') . '</a></div>';
        return $html;
    }
}