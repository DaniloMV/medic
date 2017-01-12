<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step5 extends Lomedic_Registration_Block_Adminhtml_Customer_Edit_Tab_Step_Abstract
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
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
            'legend' => Mage::helper('customer')->__('Registration Step 5')
        ));

        $attributes_old = $customerForm->getAttributes();

        $attributes = array();

        $session = Mage::getSingleton('admin/session');
        $user = Mage::getModel('admin/user')->load($session->getUser()->getUserId());
        $data = $user->getRole()->getData();
        $role = $data["role_name"];


        foreach ($attributes_old as $key => $attribute) {
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();

            if(Mage::helper('loregistration')->getManagerType()==Mage::getStoreConfig('softeq/managers/recruter')){
                if($key == 'contract'){
                    $attributes[$key] = $attribute;
                }

                if($key == "registration_status"
                    && ($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::PREAPPROVED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::NOT_APPROVED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::APPROVED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::CLOSED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::PRECLOSED)){
                    $attributes[$key] = $attribute;
                }
            }else {
                if($key == 'contract'
                    || $key == 'approve_message'){
                    $attributes[$key] = $attribute;
                }
                if($key == "registration_status"
                    && ($customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::APPROVED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::NOT_APPROVED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::PREAPPROVED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::COMPLETED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::CLOSED
                        || $customer->getRegistrationStatus() == Lomedic_Registration_Model_System_Config_Source_Status::RESTART)){
                    $attributes[$key] = $attribute;
                }
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

    /**
     * Add html to page
     *
     * @return string
     */
    protected function _toHtml() {
        $html = "<div class='original_form hidden'>".parent::_toHtml()."</div>";
        return $html .= $this->getLayout()->createBlock('adminhtml/template','customer.step2')
            ->setTemplate('registration/customer/step5.phtml')
            ->toHtml();
    }
}
