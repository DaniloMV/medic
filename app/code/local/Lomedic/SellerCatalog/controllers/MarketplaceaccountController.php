<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
require_once 'Webkul/Marketplace/controllers/MarketplaceaccountController.php';

class Lomedic_SellerCatalog_MarketplaceaccountController extends Webkul_Marketplace_MarketplaceaccountController
{
    /**
     * Edit batch product
     */
    public function editapprovedbatchAction() 
    {
        Mage::registry('addBatchProductToSystem',true,true);
        if($this->getRequest()->isPost()){
            $productId = $this->getRequest()->getParam('productid');
            if (!$this->_validateFormKey() || !$productId) {
                $this->_redirect('loseller/marketplaceaccount/batches/');
            }
            $data = $this->getRequest()->getPost();
            $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $product = Mage::getModel('catalog/product')->load($productId);
            $product->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($product);
            if($product->getBatchSeller()!==$vendorId) {
                Mage::getSingleton('core/session')->addError(Mage::helper('loseller')->__('Product not exist'));
            }
            
            Mage::getModel('loseller/marketplace_product')->saveBatchProduct($data,$product);
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('loseller')->__('Your product was successfully Saved'));
        }
        $this->_redirect('loseller/marketplaceaccount/batchedit/id/'.$product->getId());
    }
    
    /**
     * |Filter product list by AJAX
     */
    public function filterProductListAjaxAction() 
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Batch grid action
     */
    public function batchesAction() 
    {

        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Save batch product
     */
    public function batchproductAction() 
    {
        if($this->getRequest()->isPost()) {
            if (!$this->_validateFormKey()) {
                $this->_redirect('marketplace/marketplaceaccount/new/');
            }
            $data = $this->getRequest()->getPost();
            Mage::getModel('loseller/marketplace_product')->saveBatchNewProduct($data);
            $status=Mage::getStoreConfig('marketplace/marketplace_options/partner_approval');
            if($status==1){
                $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                $customer = Mage::getModel('customer/customer')->load($vendorId);
                $cfname=$customer->getFirstname()." ".$customer->getLastname();
                $cmail=$customer->getEmail();
                $catagory_model = Mage::getModel('catalog/category');
                $categoriesy = $catagory_model->load($wholedata['category'][0]);
                $categoryname=$categoriesy->getName();
                $emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
                $emailTempVariables = array();
                $adminname = 'Administrators';
                $admin_storemail = Mage::getStoreConfig('marketplace/marketplace_options/adminemail');
                $adminEmail=$admin_storemail? $admin_storemail:Mage::getStoreConfig('trans_email/ident_general/email');
                $emailTempVariables['myvar1'] = $wholedata['name'];
                $emailTempVariables['myvar2'] = $categoryname;
                $emailTempVariables['myvar3'] = $adminname;
                $emailTempVariables['myvar4'] = Mage::helper('marketplace')->__('I would like to inform you that recently i have added a new product in the store.');
                $processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
                $emailTemp->setSenderName($cfname);
                $emailTemp->setSenderEmail($cmail);
            }
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your batch was successfully saved'));
        }
        $this->_redirect('loseller/marketplaceaccount/batches/');
    }

    /**
     * View or edit batch product
     */
    public function batcheditAction() 
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     *  Delete batch product
     */
    public function deletebatchAction() 
    {
        if(!$id = $this->getRequest()->getParam('id',false)) {
            return Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Please specify batch'));
        }
        $product = Mage::getModel('catalog/product')->load($id);
        $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        if($product->getBatchSeller()!==$vendorId) {
           return Mage::getSingleton('core/session')->addError(Mage::helper('loseller')->__('Batch not exist'));
        }
        try {
            Mage::register('isSecureArea',true,true);
            $product->delete();
            Mage::unregister('isSecureArea');
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your batch was successfully deleted'));
        } catch(Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('loseller')->__('Batch not exist'));
        }
        
        $this->_redirect('*/*/batches');
    }

    /**
     *  Delete batch product image
     */
    public function deleteimageAction() 
    {
        $data = $this->getRequest()->getPost();
        if($data){
            if($data["id"] == Mage::getSingleton('customer/session')->getCustomer()->getId()){
                $product = Mage::getModel("catalog/product")->load($data["product_id"]);
                $mediaApi = Mage::getModel("catalog/product_attribute_media_api");

                $items = $mediaApi->items($product->getId());
                foreach($items as $item) {
                    $one = str_replace("/","_",$data["path"]);
                    $one = str_replace("\\","_",$one);
                    $two = str_replace("/","_",$item["file"]);
                    $two = str_replace("\\","_",$two);
                    $res = strpos($one,$two);
                    if ((int)$res > 0) {
                        $mediaApi->remove($product->getId(), $item['file']);
                    }
                }
            }
        }
    }

    /**
     * View product
     */
    public function viewproductAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create new product
     */
    public function newAction()
    {
            $set=$this->getRequest()->getParam('set');
            $type=$this->getRequest()->getParam('type');
            if(!$set || !$type) {
                return Mage::app()->getResponse()->setRedirect(Mage::getUrl("marketplace/marketplaceaccount/mydashboard"))->sendResponse();
            }
            if(isset($set) && isset($type)) {
                $allowedsets=explode(',',Mage::getStoreConfig('marketplace/marketplace_options/attributesetid'));
                $allowedtypes=explode(',',Mage::getStoreConfig('marketplace/marketplace_options/allow_for_seller'));

                if(!in_array($type,$allowedtypes) || !in_array($set,$allowedsets)) {
                    Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Product Type Invalide Or Not Allowed'));
                    return Mage::app()->getResponse()->setRedirect(Mage::getUrl("marketplace/marketplaceaccount/mydashboard"))->sendResponse();
                }
            Mage::getSingleton('core/session')->setAttributeSet($set);
            switch($type){
                case "simple":
                    $this->loadLayout(array('default','marketplace_account_simpleproduct'));
                    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('marketplace')->__('MarketPlace Product Type: Simple Product'));
                    break;
                case "downloadable":
                    $this->loadLayout(array('default','marketplace_account_downloadableproduct'));
                    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('marketplace')->__('MarketPlace Product Type: Downloabable Product'));
                    break;
                case "virtual":
                    $this->loadLayout(array('default','marketplace_account_virtualproduct'));
                    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('marketplace')->__('MarketPlace Product Type: Virtual Product'));
                    break;
                case "configurable":
                    $this->loadLayout(array('default','marketplace_account_configurableproduct'));
                    $this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('MarketPlace Product Type: Configurable Product'));
                    break;
            }
            Mage::dispatchEvent('mp_bundalproduct',array('layout'=>$this,'type'=>$type));
            Mage::dispatchEvent('mp_groupedproduct',array('layout'=>$this,'type'=>$type));

            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
        } else {
            $this->loadLayout(array('default','marketplace_marketplaceaccount_newproduct'));
            $this->renderLayout();
        }
    }

    /**
     * Save product
     */
    public function simpleproductAction()
    {
        if($this->getRequest()->isPost()){
            list($data, $errors) = $this->validatePost();
            $wholedata=$this->getRequest()->getParams();
            $wholedata['status'] = '1';

            if(empty($errors)){
                Mage::getModel('marketplace/product')->saveSimpleNewProduct($wholedata);
                $status=Mage::getStoreConfig('marketplace/marketplace_options/partner_approval');
                if($status==1){
                    $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                    $customer = Mage::getModel('customer/customer')->load($vendorId);
                    $cfname=$customer->getFirstname()." ".$customer->getLastname();
                    $cmail=$customer->getEmail();
                    $catagory_model = Mage::getModel('catalog/category');
                    $categoriesy = $catagory_model->load($wholedata['category'][0]);
                    $categoryname=$categoriesy->getName();
                    $emailTemp = Mage::getModel('core/email_template')->loadDefault('approveproduct');
                    $emailTempVariables = array();
                    $adminname = 'Administrators';
                    $admin_storemail = Mage::getStoreConfig('marketplace/marketplace_options/adminemail');
                    $adminEmail=$admin_storemail? $admin_storemail:Mage::getStoreConfig('trans_email/ident_general/email');
                    $emailTempVariables['myvar1'] = $wholedata['name'];
                    $emailTempVariables['myvar2'] = $categoryname;
                    $emailTempVariables['myvar3'] = $adminname;
                    $emailTempVariables['myvar4'] = Mage::helper('marketplace')->__('I would like to inform you that recently i have added a new product in the store.');
                    $processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
                    $emailTemp->setSenderName($cfname);
                    $emailTemp->setSenderEmail($cmail);
                }
            } else {
                foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
                $_SESSION['new_products_errors'] = $data;
            }
            if (empty($errors)) {
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your product was successfully Saved'));
            }
            $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
        } else{
            $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
        }
    }

    /**
     * Validate data from post
     * @return array
     */
    private function validatePost()
    {
        $errors = array();
        $data = array();
        foreach( $this->getRequest()->getParams() as $code => $value) {
            switch ($code) {
                case 'name':
                    if(trim($value) == '' ){$errors[] = Mage::helper('marketplace')->__('Name has to be completed');}
                    else{$data[$code] = $value;}
                    break;
                case 'description':
                    if(trim($value) == '' ){$errors[] = Mage::helper('marketplace')->__('Description has to be completed');}
                    else{$data[$code] = $value;}
                    break;
                case 'short_description':
                    if(trim($value) == ''){$errors[] = Mage::helper('marketplace')->__('Short description has to be completed');}
                    else{$data[$code] = $value;}
                    break;
                case 'price':
                    if(!preg_match("/^([0-9])+?[0-9.]*$/",$value)){
                        $errors[] = Mage::helper('marketplace')->__('Price should contain only decimal numbers');
                    }else{$data[$code] = $value;}
                    break;
                case 'weight':
                    if(!preg_match("/^([0-9])+?[0-9.]*$/",$value)){
                        $errors[] = Mage::helper('marketplace')->__('Weight should contain only decimal numbers');
                    }else{$data[$code] = $value;}
                    break;
                case 'stock':
                    if(!preg_match("/^([0-9])+?[0-9.]*$/",$value)){
                        $errors[] = Mage::helper('marketplace')->__('Product stock should contain only an integer number');
                    }else{$data[$code] = $value;}
                    break;
                case 'sku_type':
                    if(trim($value) == '' ){$errors[] = Mage::helper('marketplace')->__('Sku Type has to be selected');}
                    else{$data[$code] = $value;}
                    break;
                case 'price_type':
                    if(trim($value) == '' ){$errors[] = Mage::helper('marketplace')->__('Price Type has to be selected');}
                    else{$data[$code] = $value;}
                    break;
                case 'weight_type':
                    if(trim($value) == ''){$errors[] = Mage::helper('marketplace')->__('Weight Type has to be selected');}
                    else{$data[$code] = $value;}
                    break;
                case 'bundle_options':
                    if(trim($value) == ''){$errors[] = Mage::helper('marketplace')->__('Default Title has to be completed');}
                    else{$data[$code] = $value;}
                    break;
            }
        }
        return array($data, $errors);
    }

    /**
     * Delete product
     */
    public function deleteAction()
    {
        $params = $this->getRequest()->getParams();
        $urlapp=$_SERVER['REQUEST_URI'];

        if(isset($params["id"]) && !empty($params["id"])) {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('batch_number')
                ->addAttributeToSelect('expiration_date')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('batch_parent_product');
            $collection->addAttributeToFilter('batch_parent_product',array('eq'=>$params["id"]));

            foreach($collection as $row) {
                Mage::register('isSecureArea', true);
                $row->delete();
                Mage::unregister('isSecureArea');
            }
        }

        $record=Mage::getModel('marketplace/product')->deleteProduct($urlapp);

        if($record==1) {
            Mage::getSingleton('core/session')->addSuccess( Mage::helper('marketplace')->__('Your Product Has Been Sucessfully Deleted From Your Account'));
        } else {
            Mage::getSingleton('core/session')->addError( Mage::helper('marketplace')->__('YOU ARE NOT AUTHORIZE TO DELETE THIS PRODUCT.'));
        }
        $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
    }

    /**
     * Products grid action
     */
    public function myproductslistAction()
    {
        $this->loadLayout( array('default','marketplace_account_productlist'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('My Product List'));
        $this->renderLayout();
    }

    /**
     * Edit profile action
     * @return void
     */
    public function editprofileAction(){
        if($this->getRequest()->isPost()){
            if (!$this->_validateFormKey()) {
                return $this->_redirect('marketplace/marketplaceaccount/editProfile');
            }
            list($data, $errors) = $this->validateprofiledata();
            $fields = $this->getRequest()->getParams();
            $loid=$this->_getSession()->getCustomerId();
            $img1='';
            $img2='';
            if(empty($errors)){
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $collection = Mage::getModel('marketplace/userprofile')->getCollection();
                $collection->addFieldToFilter('mageuserid',array('eq'=>$this->_getSession()->getCustomerId()));
                foreach($collection as  $value){ $data = $value; }
                $value->settwitterid($fields['twitterid']);
                $value->setfacebookid($fields['facebookid']);
                $value->setcontactnumber($fields['contactnumber']);
                $value->setbackgroundth($fields['backgroundth']);
                $value->setshoptitle($fields['shoptitle']);
                $value->setcomplocality($fields['complocality']);
                $value->setMetaKeyword($fields['meta_keyword']);

                if($fields['compdesi']){
                    $fields['compdesi'] = str_replace('script', '', $fields['compdesi']);
                }
                $value->setcompdesi($fields['compdesi']);

                if(isset($fields['returnpolicy'])){
                    $fields['returnpolicy'] = str_replace('script', '', $fields['returnpolicy']);
                    $value->setReturnpolicy($fields['returnpolicy']);
                }

                if(isset($fields['shippingpolicy'])){
                    $fields['shippingpolicy'] = str_replace('script', '', $fields['shippingpolicy']);
                    $value->setShippingpolicy($fields['shippingpolicy']);
                }

                $value->setMetaDescription($fields['meta_description']);
                if(strlen($_FILES['bannerpic']['name'])>0){
                    $extension = pathinfo($_FILES["bannerpic"]["name"], PATHINFO_EXTENSION);
                    $temp = explode(".",$_FILES["bannerpic"]["name"]);
                    $img1 = $temp[0].rand(1,99999).$loid.'.'.$extension;
                    $value->setbannerpic($img1);
                }
                if(strlen($_FILES['logopic']['name'])>0){
                    $extension = pathinfo($_FILES["logopic"]["name"], PATHINFO_EXTENSION);
                    $temp1 = explode(".",$_FILES["logopic"]["name"]);
                    $img2 = $temp1[0].rand(1,99999).$loid.'.'.$extension;
                    $value->setlogopic($img2);
                }
                if (array_key_exists('countrypic', $fields)) {
                    $value->setcountrypic($fields['countrypic']);
                }
                $value->save();
                $target =Mage::getBaseDir().'/media/avatar/';
                $targetb = $target.$img1;

                move_uploaded_file($_FILES['bannerpic']['tmp_name'],$targetb);
                $targetl = $target.$img2;
                move_uploaded_file($_FILES['logopic']['tmp_name'],$targetl);
                try{
                    if(!empty($errors)){
                        foreach ($errors as $message){$this->_getSession()->addError($message);}
                    }else{Mage::getSingleton('core/session')->addSuccess( Mage::helper('marketplace')->__('Profile information was successfully saved'));}
                    $this->_redirect('marketplace/marketplaceaccount/editProfile');
                    return;
                }catch (Mage_Core_Exception $e){
                    $this->_getSession()->addError($e->getMessage());
                }catch (Exception $e){
                    $this->_getSession()->addException($e,  Mage::helper('marketplace')->__('Cannot save the customer.'));
                }
                $this->_redirect('customer/*/*');
            }else{
                foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
                $_SESSION['new_products_errors'] = $data;
                $this->_redirect('marketplace/marketplaceaccount/editProfile');
            }
        }
        else{
            $this->loadLayout( array('default','marketplace_account_editaccount'));
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');
            $this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('Profile Information'));
            $this->renderLayout();
        }
    }

    /**
     * Validate profile data
     * @return array
     */
    private function validateprofiledata(){
        $errors = array();
        $data = array();
        foreach( $this->getRequest()->getParams() as $code => $value){
            switch ($code) :
                case 'twitterid':
                    if(trim($value) != '' && preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $value)){$errors[] = Mage::helper('marketplace')->__('Twitterid cannot contain space and special charecters');}
                    else{$data[$code] = $value;}
                    break;
                case 'facebookid':
                    if(trim($value) != '' &&  preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $value)){$errors[] = Mage::helper('marketplace')->__('Facebookid cannot contain space and special charecters');}
                    else{$data[$code] = $value;}
                    break;
                case 'backgroundth':
                    if(trim($value) != '' && strlen($value)!=6 && substr($value, 0, 1) != "#"){$errors[] = Mage::helper('marketplace')->__('Invalid Background Color');}
                    else{$data[$code] = $value;}
                    break;
            endswitch;
        }
        return array($data, $errors);
    }
}