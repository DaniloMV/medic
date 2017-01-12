<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Rewrite_Adminhtml_Permissions_User_Edit_Tabs extends Mage_Adminhtml_Block_Permissions_User_Edit_Tabs
{
    /**
     * Add tabs
     * 
     * @return \Mage_Adminhtml_Block_Widget_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('adminhtml')->__('User Info'),
            'title'     => Mage::helper('adminhtml')->__('User Info'),
            'content'   => $this->getLayout()->createBlock('loregistration/adminhtml_permissions_user_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('roles_section', array(
            'label'     => Mage::helper('adminhtml')->__('User Role'),
            'title'     => Mage::helper('adminhtml')->__('User Role'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_user_edit_tab_roles', 'user.roles.grid')->toHtml(),
        ));
        return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
    }
    
  
}
