<?php

class Vovsky_Customerprice_IndexController extends Mage_Core_Controller_Front_Action {

    public function disableAction() {
        $key = $this->getRequest()->getParam('key');
        $attribute = Mage::getResourceModel('eav/entity_attribute_collection')->addFieldToFilter('attribute_code', 'customer_price')->load()->getFirstItem();
        if ($key==1){
            $attribute->setBackendModel(NULL);
            $attribute->save();
            $this->getResponse()->setBody("1");
        }
        elseif ($key==2){
            $attribute->setBackendModel('customerprice/customerprice');
            $attribute->save();
            $this->getResponse()->setBody("2");
        }
        elseif ($key==3){
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->getTable('customerprice/customer_price');
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->query("DROP TABLE IF EXISTS {$setup->getTable('customerprice/customer_price')}");
            $setup->removeAttribute('catalog_product', 'customer_price');
            $this->getResponse()->setBody("3");
        }
    }
}