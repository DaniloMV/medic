<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step2 extends Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step_Abstract
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Add html to page
     * 
     * @return string
     */
    protected function _toHtml() 
    {
        $html = "<div class='original_form hidden'>".parent::_toHtml()."</div>";
        return $html .= $this->getLayout()->createBlock('adminhtml/template','customer.step2')
                ->setTemplate('registration/customer/step2.phtml')
                ->toHtml();
    }

    /**
     * Preparing  layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => Mage::helper('customer')->__('Delete Address'),
                    'name'   => 'delete_address',
                    'element_name' => 'delete_address',
                    'disabled' => true,
                    'class'  => 'delete' . (true ? ' disabled' : '')
                ))
        );
        $this->setChild('add_address_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => Mage::helper('customer')->__('Add New Address'),
                    'id'     => 'add_address_button',
                    'name'   => 'add_address_button',
                    'element_name' => 'add_address_button',
                    'disabled' => true,
                    'class'  => 'add'  . (true ? ' disabled' : ''),
                    'onclick'=> 'customerAddresses.addNewAddress()'
                ))
        );
        $this->setChild('cancel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => Mage::helper('customer')->__('Cancel'),
                    'id'     => 'cancel_add_address'.$this->getTemplatePrefix(),
                    'name'   => 'cancel_address',
                    'element_name' => 'cancel_address',
                    'class'  => 'cancel delete-address'  . (true? ' disabled' : ''),
                    'disabled' => true,
                    'onclick'=> 'customerAddresses.cancelAdd(this)',
                ))
        );
        return parent::_prepareLayout();
    }

    /**
     * Initialize form
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        $form->setFieldNameSuffix('account');

        $customer = Mage::registry('current_customer');

        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer')
            ->initDefaultValues();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('customer')->__('Registration Step 2')
        ));

        $attributes_old = $customerForm->getAttributes();

        $attributes = array();

        foreach ($attributes_old as $key => $attribute) {
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();

            if ($key == "account_type"
                || $key == 'real_name'
                || $key == 'association_number'
                || $key == 'association_number_before'
                || $key == 'association_number_mod'
                || $key == 'association_number_mod_before'
                || $key == 'year_amount'
                || $key == 'employees_qty'
                || $key == 'computer_qty'
                || $key == 'office_area'
                || $key == 'warehouse_area'
                || $key == 'vehicles_qty'
                || $key == 'operation_statment'
                || $key == 'resp_health_professional'
                || $key == 'resp_health_prof_license_num'
                || $key == 'health_license_number'
            ) {
                $attributes[$key] = $attribute;
            }

            if($key == "registration_status"
                && ($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::PREREGISTRATION_COMPLETED)
            ){
                $attributes[$key] = $attribute;
            }
        }

        $disableAutoGroupChangeAttributeName = 'disable_auto_group_change';
        $this->_setFieldset($attributes, $fieldset, array($disableAutoGroupChangeAttributeName));

        if ($customer->isReadonly()) {
            foreach ($customer->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $form->setValues($customer->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_boolean'),
        );
    }
}
