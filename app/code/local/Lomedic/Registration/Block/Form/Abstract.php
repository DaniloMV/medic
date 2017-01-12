<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Form_Abstract extends Mage_Customer_Block_Form_Register
{
    private $_customer;
    
    /**
     * Get customer
     * 
     * @return \Mage_Customer_Model_Customer
     */
    public function getCustomer() 
    {
        if(!$this->_customer) {
            $session = Mage::getSingleton('customer/session');
            $customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
            $customerAddresses  = $customer->getAddresses();
            if(count($customerAddresses)==1) {
                $shippingAddressId = $customer->getDefaultShipping();
                $billingAddressId = $customer->getDefaultBilling();
                if(isset($customerAddresses[$billingAddressId]) && $customer->getRegistrationStep()>=3) {
                   $customer->setAddress(new Varien_Object());
                    $customer->setBilingAddress(array_shift($customerAddresses)); // special mistake for exclude rewrite customer model
                    $customer->setOtherAddress(new Varien_Object());
                }
                elseif(isset($customerAddresses[$shippingAddressId])) {
                     $customer->setAddress(array_shift($customerAddresses));
                    $customer->setBilingAddress(new Varien_Object()); // special mistake for exclude rewrite customer model
                    $customer->setOtherAddress(new Varien_Object());
                } 
                else {
                    $customer->setAddress(array_shift($customerAddresses));
                    $customer->setBilingAddress(new Varien_Object()); // special mistake for exclude rewrite customer model
                    $customer->setOtherAddress(new Varien_Object());
                }
            } 
            elseif(!count($customerAddresses)) {
                $customer->setAddress(new Varien_Object());
                    $customer->setBilingAddress(new Varien_Object()); // special mistake for exclude rewrite customer model
                    $customer->setOtherAddress(new Varien_Object());
            } else {
                if($shippingAddressId = $customer->getDefaultShipping()) {
                    $customer->setAddress($customerAddresses[$shippingAddressId]);
                    unset($customerAddresses[$shippingAddressId]);
                } else {
                    if(count($customerAddresses)) {
                        $customer->setAddress(array_shift($customerAddresses));
                    } else {
                        $customer->setAddress(new Varien_Object());
                    }
                }
                if($billingAddressId = $customer->getDefaultBilling() && isset($customerAddresses[$billingAddressId])) {
                $customer->setBilingAddress($customerAddresses[$billingAddressId]); // special mistake for exclude rewrite customer model
                    if(isset($customerAddresses[$billingAddressId])){
                        unset($customerAddresses[$billingAddressId]);
                    }
                } else {
                    $bilingAddress = array_shift($customerAddresses); // special mistake for exclude rewrite customer model
                    $customer->setBilingAddress((($bilingAddress))?$bilingAddress:(new Varien_Object()));
                }
                $otherAddress = array_pop($customerAddresses);
                $customer->setOtherAddress((($otherAddress))?$otherAddress:(new Varien_Object()));
            }
            
            $customer->setBillingAddress(new Varien_Object());

            $this->_customer = $customer;
        }

        return $this->_customer;
    }
    
    /**
     * Get customer recruter
     * 
     * @return Varien_Object
     */
    public function getRecruter() 
    {
        $recruter = Mage::helper('loregistration')->getRecruter($this->getCustomer()->getRegistrationRecruter());
        if($recruter->getId() && $recruter->getIsActive()) {
            return $recruter;
        }
        return FALSE;
    }
    
    /**
     * Get Support Url
     * 
     * @return string
     */
    public function getSupportUrl() 
    {
        return $this->getUrl('support',array('_secure'=>true));
    }
    
    /**
     * Get Main activity attribute html code
     * 
     * @return string
     */
    public function getActivityHtmlSelect($params=false) 
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'activity');
        $html = "<div class='form_field'><span class='label' for='".$attributeModel->getAttributeCode()."'>".$this->__($attributeModel->getFrontendLabel())."</label></div>";
        $html .= '<div class="form_field">';
        $html .= $this->_rendererAttribute($attributeModel,$this->getCustomer()->getActivity(),false,$params);
        $html .='</div>';
        return $html;
    }
    
    /**
     * Get Sector Activity attribute html code
     * 
     * @return string
     */
    public function getSectorHtmlSelect($params=false)
    {
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'sector');
        $html = "<div class='form_field'><span class='label' for='".$attributeModel->getAttributeCode()."'>".$this->__($attributeModel->getFrontendLabel())."</label></div>";
        $html .= '<div class="form_field">';
        $html .= $this->_rendererAttribute($attributeModel,$this->getCustomer()->getSector(),false,$params);
        $html .='</div>';
        return $html;
    }

    /**
     * Get Zip Code html code
     * 
     * @return string
     */
    public function getZipCode($params='',$value='') 
    {

        $html = "<div class='form_field'><span class='label' for='postcode'><span>*</span>".$this->__("Zip Code")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="postcode" class="validate-select" placeholder="'.$this->__('Please select').'" id="postcode" name="postcode" '.$params.'>';
        $html .= '<option value=""></option>';
        if($value) {
            $html .= '<option selected="selected" value="'.$value.'">'.$value.'</option>';
        }
        $html .='</select>';
        $html .='</div>';
        return $html;
    }

    /**
     * Get State Activity attribute html code
     * 
     * @return string
     */
    public function getState($params='',$value='') 
    {
        $testmodel = Mage::getModel('loregistration/zip')
            ->getCollection()
            ->addFieldToSelect('state')
            ->addFieldToSelect('zip_code');

        $testmodel ->getSelect()
            ->group('state');

        $html = "<div class='form_field'><span class='label' for='state'><span>*</span>".$this->__("State")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="'.$this->__("State").'" class="select-with-search validate-select" placeholder="'.$this->__('Please select').'" require="require" id="state" name="region" '.$params.'>';
        $html .= '<option value=""></option>';
        foreach($testmodel as $val) {
            $selected = '';
            $data = $val->getData();
            if($value==$data["state"]) {
                $selected='selected="selected"';
            }
            $html .= '<option '.$selected.' value="'.$data["state"].'">'.$data["state"].'</option>';
        }

        $html .='</select>';
        $html .='</div>';
        return $html;
    }


    /**
     * Get Zip Code html code
     * 
     * @return string
     */
    public function getZipCodeTwo($code=false,$code_name=false,$params='') 
    {
        $html = "<div class='form_field'><span class='label' for='postcode'><span>*</span>".$this->__("Zip Code")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="'.$this->__("Zip Code").'" placeholder="'.$this->__("Please select").'" class="validate-select" id="postcode" name="address[postcode]" '.$params.'>';
        $html .='<option value="'.$code.'">'.$code.'</option>';
        $html .='</select>';
        $html .='</div>';

        return $html;
    }

    /**
     * Get State Activity attribute html code
     * 
     * @return string
     */
    public function getStateTwo($code=false,$params='') 
    {
        $testmodel = Mage::getModel('loregistration/zip')
            ->getCollection()
            ->addFieldToSelect('state')
            ->addFieldToSelect('zip_code');

        $testmodel ->getSelect()
            ->group('state');

        $html = "<div class='form_field'><span class='label' for='state'><span>*</span>".$this->__("State")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="'.$this->__("State").'" placeholder="'.$this->__("Please select").'" class="select-with-search validate-select" id="state" name="address[region]" '.$params.'>';
        $html .= '<option value=""></option>';
        foreach($testmodel as $val) {
            $data = $val->getData();

            if($code && $code == $data["state"]) {
                $html .= '<option selected="selected" value="' . $data["state"] . '">' . $data["state"] . '</option>';
            } else {
                $html .= '<option value="' . $data["state"] . '">' . $data["state"] . '</option>';
            }
        }

        $html .='</select>';
        $html .='</div>';
        return $html;
    }

    /**
     * Get Zip Code html code
     * 
     * @return string
     */
    public function getZipCodeBilling($code = false, $code_name = false,$params='') 
    {
        $html = "<div class='form_field'><span class='label' for='postcode'><span>*</span>".$this->__("Zip Code")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="'.$this->__("Zip Code").'"  require="require" placeholder="'.$this->__("Please select").'"  class="validate-select" id="postcode_billing" name="address[billing][postcode]" '.$params.'>';
        $html .='<option value="'.$code.'">'.$code.'</option>';
        $html .='</select>';
        $html .='</div>';
        return $html;
    }

    /**
     * Get State Activity attribute html code
     * 
     * @return string
     */
    public function getStateBilling($code = false,$params='') 
    {
        $testmodel = Mage::getModel('loregistration/zip')
            ->getCollection()
            ->addFieldToSelect('state')
            ->addFieldToSelect('zip_code');

        $testmodel->getSelect()->group('state');

        $html = "<div class='form_field '><span class='label' for='state'><span>*</span>".$this->__("State")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="'.$this->__("State").'" placeholder="'.$this->__("Please select").'" require="require" class="select-with-search validate-select" id="state_billing" name="address[billing][region]" '.$params.'>';
        $html .= '<option value=""></option>';
        
        foreach($testmodel as $val) {
            $data = $val->getData();
            if($code == $data["state"]) {
                $html .= '<option selected="selected" value="'.$data["state"].'">'.$data["state"].'</option>';
            } else {
                $html .= '<option value="'.$data["state"].'">'.$data["state"].'</option>';
            }
        }

        $html .='</select>';
        $html .='</div>';
        return $html;
    }

    /**
     * Get Zip Code html code
     * 
     * @return string
     */
    public function getZipCodeOther($code = false, $code_name = false,$params='') 
    {
        $html = "<div class='form_field'><span class='label' for='postcode'><span>*</span>".$this->__("Zip Code")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="'.$this->__("Zip Code").'" placeholder="'.$this->__("Please select").'" class="validate-select" id="postcode_other" name="address[other][postcode]" '.$params.'>';
        $html .= '<option value="'.$code.'">'.$code.'</option>';
        $html .='</select>';
        $html .='</div>';
        return $html;
    }

    /**
     * Get State Activity attribute html code
     * 
     * @return string
     */
    public function getStateOther($code = false,$params='') {

        $testmodel = Mage::getModel('loregistration/zip')
            ->getCollection()
            ->addFieldToSelect('state')
            ->addFieldToSelect('zip_code');

        $testmodel ->getSelect()->group('state');
        
        $html = "<div class='form_field '><span class='label' for='state'><span>*</span>".$this->__("State")."</label></div>";
        $html .= '<div class="form_field">';
        $html .= '<select require="require" title="State" class="select-with-search validate-select" placeholder="'.$this->__("Please select").'" id="state_other" name="address[other][region]" '.$params.'>';
        $html .= '<option value=""></option>';
        foreach($testmodel as $val) {
            $data = $val->getData();
            if($code == $data["state"]) {
                $html .= '<option selected="selected" value="'.$data["state"].'">'.$data["state"].'</option>';
            } else {
                $html .= '<option value="'.$data["state"].'">'.$data["state"].'</option>';
            }
        }

        $html .='</select>';
        $html .='</div>';
        return $html;
    }

    /**
     * Renderer attribute html
     * 
     * @param Mage_Eav_Model_Entity_Atribute $attributeModel
     * @return string
     */
    protected function _rendererAttribute($attributeModel,$value=false,$defaultOption=false,$params='') 
    {
        $options = $attributeModel->getSource()->getAllOptions($defaultOption);
       
        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName($attributeModel->getAttributeCode())
            ->setId($attributeModel->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attributeModel))
            ->setClass('')
            ->setExtraParams($params)
            ->setValue($value?$value:$this->getAttributeValue($attributeModel))
            ->setOptions($options)
            ->getHtml();
        return $html;
    }
    
    /*
     * Get registration page 2 url
     * 
     * @return string|bool
     */
    public function getPage2Url() 
    {
        if($this->getCustomer()->getRegistrationStep()>=2) {
            return $this->getUrl('*/*/step2');
        }
        return false;
    }
    
    /*
     * Get registration page 3 url
     * 
     * @return string|bool
     */
    public function getPage3Url() {
        if($this->getCustomer()->getRegistrationStep()>=3) {
            return $this->getUrl('*/*/step3');
        }
        return false;
    }
    
    /*
     * Get registration page 4 url
     * 
     * @return string|bool
     */
    public function getPage4Url() {
        if($this->getCustomer()->getRegistrationStep()>=4) {
            return $this->getUrl('*/*/step4');
        }
        return false;
    }
    
    /*
     * Get registration page 5 url
     * 
     * @return string|bool
     */
    public function getPage5Url() {
        if($this->getCustomer()->getRegistrationStep()>=5) {
            return $this->getUrl('*/*/step5');
        }
        return false;
    }

    /**
     * Get customer validated files
     * @return array
     */
    public function getValidatedFiles() {
        $customer = $this->getCustomer();

        $customerFiles = Mage::getModel('loregistration/files')->getCollection();
        $customerFiles->addFieldToFilter('customer_id',$customer->getId());

        $arr = array();
        foreach($customerFiles as $collection) {
            $tmpArr = $collection->getData();
            $arr[$tmpArr["attribute"]] = $tmpArr;
        }
        return $arr;
    }
}