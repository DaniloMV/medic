<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step3 extends Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step_Abstract
{
    
    /**
     * Add html to page
     * 
     * @return string
     */
            
    protected function _toHtml() 
    {
        $html = "<div class='original_form hidden'>".parent::_toHtml()."</div>";
        return $html .= $this->getLayout()->createBlock('adminhtml/template','customer.step2')
                ->setTemplate('registration/customer/step3.phtml')
                ->toHtml();
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
            'legend' => Mage::helper('customer')->__('Registration Step 3')
        ));

        $attributes_old = $customerForm->getAttributes();

        $attributes = array();

        foreach ($attributes_old as $key => $attribute) {
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();

            if ($key == "taxation_purposes"
                || $key == 'taxes_department'
                || $key == 'association_registry'
                || $key == 'association_prpc'
                || $key == 'assoc_mod_registry'
                || $key == 'modification_associ_prpc'
                || $key == 'oficial_id'
                || $key == 'power_attorney'
                || $key == 'proof_address'
                || $key == 'financial_years'
                || $key == 'last_bank_statements'
                || $key == 'last_year_statement'
                || $key == 'operation_statement_cofepr'
                || $key == 'professional_before_cofep'
                || $key == 'license_health_professional'
                || $key == 'professional_before_cofepr'
                || $key == 'statement_before_cofepris'
                || $key == 'good_man_practices'
                || $key == 'health_certificate_document'
            ) {
                $attributes[$key] = $attribute;
            }

            if($key == "registration_status"
                && ($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::CORRECTION
                    || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::VALIDATION
                    || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::VALIDATED)
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
