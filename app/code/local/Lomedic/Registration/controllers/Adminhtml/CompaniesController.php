<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Adminhtml_CompaniesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('visits')
            ->_title($this->__('Companies Action'));

        $this->_addContent($this->getLayout()->createBlock('loregistration/adminhtml_companies'));

        $this->renderLayout();
    }

    /**
     * Get grid html for AJAX requests
     */
    public function gridAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('visits');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('loregistration/adminhtml_companies/grid')->toHtml()
        );
    }

    /**
     * Block company
     */
    public function blockAction()
    {
        $id = $this->getRequest()->getParam("id");
        $active = $this->getRequest()->getParam("active");

        if(!isset($active) || empty($active)){
            $active = 1;
            $status = 1;
        }else{
            $active = 0;
            $status = 2;
        }

        if($id){
            $customer = Mage::getModel('customer/customer')->load($id);
            $customer->setIsActive($active);
            $customer->save();
            $products = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToFilter('batch_seller',array('eq'=>$customer->getId()));
            $productIds = array();
            foreach($products as $product) {
                $productIds[] = $product->getId();
            }
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('status' => $status), Mage::app()->getStore()->getId());
            echo $this->getLayout()->createBlock('loregistration/adminhtml_companies/grid')->toHtml();
        }else{
            echo 0;
        }
    }

    /**
     * Delete company
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam("id");

        if($id){

            $collection = Mage::getModel('marketplace/product')->getCollection()->addFieldToFilter('userid',array('eq'=>$id));

            foreach($collection as $market){
                $productCollection=Mage::getModel('catalog/product')->load($market->getMageproductid());

                $productCollection->delete();
                $market->delete();
            }

            $customer = Mage::getModel('customer/customer')->load($id);
            $customer->delete();

            echo $this->getLayout()->createBlock('loregistration/adminhtml_companies/grid')->toHtml();
        }else{
            echo 0;
        }
    }
}
