<?php

require_once 'Mage/Customer/controllers/AccountController.php';
class Webkul_Marketplace_MarketplaceaccountController extends Mage_Customer_AccountController{	
    public function indexAction(){
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function newAction(){
		$set=$this->getRequest()->getParam('set');
		$type=$this->getRequest()->getParam('type');
                if(!$set || !$type) {
                    return Mage::app()->getResponse()->setRedirect(Mage::getUrl("marketplace/marketplaceaccount/mydashboard"))->sendResponse();
                }
		if(isset($set) && isset($type)){
			$allowedsets=explode(',',Mage::getStoreConfig('marketplace/marketplace_options/attributesetid'));
			$allowedtypes=explode(',',Mage::getStoreConfig('marketplace/marketplace_options/allow_for_seller'));
			if(!in_array($type,$allowedtypes) || !in_array($set,$allowedsets)){
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
		}else{
		  $this->loadLayout(array('default','marketplace_marketplaceaccount_newproduct'));     
		  $this->renderLayout();
		}
	}

    public function categorytreeAction(){
		$data = $this->getRequest()->getParams();
		$category_model = Mage::getModel("catalog/category");
		$category = $category_model->load($data["CID"]);
		$children = $category->getChildren();
		$all = explode(",",$children);$result_tree = "";$ml = $data["ML"]+20;$count = 1;$total = count($all);
		$plus = 0;
		
		foreach($all as $each){
			$count++;
			$_category = $category_model->load($each);
			if(count($category_model->getResource()->getAllChildren($category_model->load($each)))-1 > 0){
				$result[$plus]['counting']=1;  			
			}else{
				$result[$plus]['counting']=0;
			}
			$result[$plus]['id']= $_category['entity_id'];
			$result[$plus]['name']= $_category->getName();

			$categories = explode(",",$data["CATS"]);
			if($data["CATS"] && in_array($_category["entity_id"],$categories)){
				$result[$plus]['check']= 1;
			}else{
				$result[$plus]['check']= 0;
			}
			$plus++;
		}
		echo json_encode($result);
	}
	
	/*save All product*/
	public function simpleproductAction(){
		if($this->getRequest()->isPost()){
			if (!$this->_validateFormKey()) {
             $this->_redirect('marketplace/marketplaceaccount/new/');
            }
			 
			list($data, $errors) = $this->validatePost();
			$wholedata=$this->getRequest()->getParams();
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
					$emailTempVariables['myvar2'] =$categoryname;
					$emailTempVariables['myvar3'] = $adminname;
					$emailTempVariables['myvar4'] = Mage::helper('marketplace')->__('I would like to inform you that recently i have added a new product in the store.');
					$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
					$emailTemp->setSenderName($cfname);
					$emailTemp->setSenderEmail($cmail);
//					$emailTemp->send($adminEmail,$adminname,$emailTempVariables);
				}
			}else{
				foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
				$_SESSION['new_products_errors'] = $data;
			}
			if (empty($errors))
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your product was successfully Saved'));
			    $this->_redirect('marketplace/marketplaceaccount/new/');
		}
		else{
			 $this->_redirect('marketplace/marketplaceaccount/new/');
		}
	}
	public function virtualproductAction() {
		if($this->getRequest()->isPost()){
			if(!$this->_validateFormKey()) {
				$this->_redirect('marketplace/marketplaceaccount/new/');
			}
			list($data, $errors) = $this->validatePost();
			$wholedata=$this->getRequest()->getParams();
			if(empty($errors)){		
				Mage::getModel('marketplace/product')->saveVirtualNewProduct($wholedata);
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
					$emailTempVariables['myvar2'] =$categoryname;
					$emailTempVariables['myvar3'] = $adminname;
					$emailTempVariables['myvar4'] = Mage::helper('marketplace')->__('I would like to inform you that recently i have added a new product in the store.');
					$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
					$emailTemp->setSenderName($cfname);
					$emailTemp->setSenderEmail($cmail);
//					$emailTemp->send($adminEmail,$adminname,$emailTempVariables);
				}
			}else{
				foreach ($errors as $message) {$this->_getSession()->addError($message);}
				$_SESSION['new_products_errors'] = $data;
			}
			if (empty($errors))
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your product was successfully Saved'));
				 $this->_redirect('marketplace/marketplaceaccount/new/');
		}
		else{
			 $this->_redirect('marketplace/marketplaceaccount/new/');
		}
	}
	public function downloadableproductAction() {
		if($this->getRequest()->isPost()){ 
			 if (!$this->_validateFormKey()) {
				 $this->_redirect('marketplace/marketplaceaccount/new/');
             }
			list($data, $errors) = $this->validatePost();
			$wholedata=$this->getRequest()->getParams();
			if(empty($errors)){		
				Mage::getModel('marketplace/product')->saveDownloadableNewProduct($wholedata);
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
					$emailTempVariables['myvar2'] =$categoryname;
					$emailTempVariables['myvar3'] = $adminname;
					$emailTempVariables['myvar4'] = Mage::helper('marketplace')->__('I would like to inform you that recently i have added a new product in the store.');
					$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
					$emailTemp->setSenderName($cfname);
					$emailTemp->setSenderEmail($cmail);
					$emailTemp->send($adminEmail,$adminname,$emailTempVariables);
				}
			}else{
				foreach ($errors as $message) {$this->_getSession()->addError($message);}
				$_SESSION['new_products_errors'] = $data;
			}
			if (empty($errors))
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your product was successfully Saved'));
				return $this->_redirect('marketplace/marketplaceaccount/new/');
		}
		else{
			return $this->_redirect('marketplace/marketplaceaccount/new/');
		}
	}
	public function configurableproductAction() {
		$wholedata=$this->getRequest()->getParam('attribute');
		$magentoProductModel = Mage::getModel('catalog/product');
		$this->_redirect('marketplace/marketplaceaccount/addconfigurableproduct');
	}
	public function configurableproductattrAction(){
		$this->loadLayout( array('default','marketplace_account_configurableproductattr'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Configurable Product Attribute'));
    	$this->renderLayout();
	}

	public function vieworderAction(){
		$available_seller_item = 0;
		$customerid=Mage::getSingleton('customer/session')->getCustomerId();
		$seller_orderslist=Mage::getModel('marketplace/saleslist')->getCollection()
                    ->addFieldToFilter('mageproownerid',array('eq'=>$customerid))
                    ->addFieldToFilter('mageorderid',array('eq'=>$this->getRequest()->getParam('id')));
		foreach($seller_orderslist as $seller_item){
			$available_seller_item = 1;
		}
		if($available_seller_item){
                $this->loadLayout( array('default','marketplace_marketplaceaccount_vieworder'));
	        $this->_initLayoutMessages('customer/session');
	        $this->_initLayoutMessages('catalog/session');
                $this->getLayout()->getBlock('head')->setTitle($this->__('View Order Details'));
	    	$this->renderLayout();
	    }else{
	    	$this->_redirect('marketplace/marketplaceaccount/myorderhistory');
	    }
	}
	public function printorderAction(){
		$available_seller_item = 0;
		$customerid=Mage::getSingleton('customer/session')->getCustomerId();
		$seller_orderslist=Mage::getModel('marketplace/saleslist')->getCollection()
									 ->addFieldToFilter('mageproownerid',array('eq'=>$customerid))
									 ->addFieldToFilter('mageorderid',array('eq'=>$this->getRequest()->getParam('id')));
		foreach($seller_orderslist as $seller_item){
			$available_seller_item = 1;
		}
		if($available_seller_item){
		$this->loadLayout( array('default','marketplace_marketplaceaccount_printorder'));
	        $this->_initLayoutMessages('customer/session');
	        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Print Order Details'));
	    	$this->renderLayout();
    	}else{
	    	$this->_redirect('marketplace/marketplaceaccount/myorderhistory');
	    }
	}
	public function viewbuyerorderAction(){
            $available_seller_item = 0;
            $customerid=Mage::getSingleton('customer/session')->getCustomerId();
            $orderId = $this->getRequest()->getParam('id');
            $order = Mage::getModel('sales/order')->load($orderId);    
            if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerid)) {
		$this->loadLayout( array('default','marketplace_marketplaceaccount_vieworder'));
	        $this->_initLayoutMessages('customer/session');
	        $this->_initLayoutMessages('catalog/session');
                $this->getLayout()->getBlock('head')->setTitle($this->__('View Order Details'));
	    	$this->renderLayout();
	    }else{
	    	$this->_redirect('marketplace/marketplaceaccount/myorderhistory');
	    }
	}
        public function emailbuyerorderAction()
        {
            $customerid=Mage::getSingleton('customer/session')->getCustomerId();
            $orderId = $this->getRequest()->getParam('id');
            $order = Mage::getModel('sales/order')->load($orderId);    
            if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerid)) {
                try {
                    $order->sendNewOrderEmail();
                    $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                        ->getUnnotifiedForInstance($order, Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);
                    if ($historyItem) {
                        $historyItem->setIsCustomerNotified(1);
                        $historyItem->save();
                    }

                    $this->_getSession()->addSuccess($this->__('The order email has been sent.'));
                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Failed to send the order email.'));
                    Mage::logException($e);
                }

                $this->_redirect('marketplace/marketplaceaccount/viewbuyerorder', array('id' => $order->getId()));
                return false;
            }

            $this->_redirect('marketplace/marketplaceaccount/myorderhistory');
        }
	public function printbuyerorderAction(){
            $available_seller_item = 0;
            $customerid=Mage::getSingleton('customer/session')->getCustomerId();
            $orderId = $this->getRequest()->getParam('id');
            $order = Mage::getModel('sales/order')->load($orderId);    
            if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerid)) {
		$this->loadLayout( array('default','marketplace_marketplaceaccount_printorder'));
	        $this->_initLayoutMessages('customer/session');
	        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Print Order Details'));
	    	$this->renderLayout();
            }else{
                    $this->_redirect('marketplace/marketplaceaccount/myorderhistory');
                }
	}
	
	public function addconfigurableproductAction(){
		return $this->_redirect('marketplace/marketplaceaccount/new/');
		/*$this->loadLayout( array('default','marketplace_account_configurableproduct'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Add Configurable Product'));
    	$this->renderLayout();*/
	}

	public function newattributeAction(){
		$this->loadLayout( array('default','marketplace_account_newattribute'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__(' Manage Attribute'));
    	$this->renderLayout();
	}
	
	public function createattributeAction() {
		if($this->getRequest()->isPost()){
			if (!$this->_validateFormKey()) {
              return $this->_redirect('marketplace/marketplaceaccount/newattribute/');
             }
			
			$wholedata=$this->getRequest()->getParams();
			$attributes = Mage::getModel('catalog/product')->getAttributes();

		    foreach($attributes as $a){
		            $allattrcodes = $a->getEntityType()->getAttributeCodes();
		    }
		    if(in_array($wholedata['attribute_code'], $allattrcodes)){
		    	Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('Attribute Code already exists'));
				$this->_redirect('marketplace/marketplaceaccount/newattribute/');
		    }else{
				list($data, $errors) = $this->validatePost();
				if(array_key_exists('attroptions', $wholedata)){
					foreach( $wholedata['attroptions'] as $c){
						$data1['.'.$c['admin'].'.'] = array($c['admin'],$c['store']);	
					}
				}else{
					$data1=array();
				}
				
				$_attribute_data = array(
									'attribute_code' => $wholedata['attribute_code'],
									'is_global' => '1',
									'frontend_input' => $wholedata['frontend_input'],
									'default_value_text' => '',
									'default_value_yesno' => '0',
									'default_value_date' => '',
									'default_value_textarea' => '',
									'is_unique' => '0',
									'is_required' => '0',
									'apply_to' => '0',
									'is_configurable' => '1',
									'is_searchable' => '0',
									'is_visible_in_advanced_search' => '1',
									'is_comparable' => '0',
									'is_used_for_price_rules' => '0',
									'is_wysiwyg_enabled' => '0',
									'is_html_allowed_on_front' => '1',
									'is_visible_on_front' => '0',
									'used_in_product_listing' => '0',
									'used_for_sort_by' => '0',
									'frontend_label' => $wholedata['attribute_code']
								);
				$model = Mage::getModel('catalog/resource_eav_attribute');
				if (!isset($_attribute_data['is_configurable'])) {
					$_attribute_data['is_configurable'] = 0;
				}
				if (!isset($_attribute_data['is_filterable'])) {
					$_attribute_data['is_filterable'] = 0;
				}
				if (!isset($_attribute_data['is_filterable_in_search'])) {
					$_attribute_data['is_filterable_in_search'] = 0;
				}
				if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
					$_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
				}
				$defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
				if ($defaultValueField) {
					$_attribute_data['default_value'] = $this->getRequest()->getParam($defaultValueField);
				}
				$model->addData($_attribute_data);
				$data['option']['value'] = $data1;
				if($wholedata['frontend_input'] == 'select' || $wholedata['frontend_input'] == 'multiselect')
					$model->addData($data);
				$model->setAttributeSetId($wholedata['attribute_set_id']);
				$model->setAttributeGroupId($wholedata['AttributeGroupId']);
				$entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
				$model->setEntityTypeId($entityTypeID);
				$model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
				$model->setIsUserDefined(1);
				$model->save();
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Attribute Created Successfully'));
				$this->_redirect('marketplace/marketplaceaccount/newattribute/');
			}
		}
	}

	public function quickcreateAction() {
		 if (!$this->_validateFormKey()) {
           return $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
         }
		$wholedata=$this->getRequest()->getParams();
		$id = $wholedata['mainid'];
	    Mage::getModel('marketplace/product')->quickcreate($wholedata);
		$this->_redirect('marketplace/marketplaceaccount/configurableassociate',array('id'=>$id));
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Associate Product created Successfully'));
	}
	public function assignassociateAction() {
		$wholedata=$this->getRequest()->getParams();		
	    Mage::getModel('marketplace/product')->editassociate($wholedata);
	    Mage::getModel('marketplace/product')->saveassociate($wholedata);
	    $id = $wholedata['mainid'];
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Product has been assigned successfully'));
		$this->_redirect('marketplace/marketplaceaccount/configurableassociate',array('id'=>$id));
	}

	public function configproductAction() {
		if($this->getRequest()->isPost()){
			 if (!$this->_validateFormKey()) {
              return $this->_redirect('marketplace/marketplaceaccount/new/');
             }
			
			list($data, $errors) = $this->validatePost();
			$wholedata=$this->getRequest()->getParams();
			if(empty($errors)){
			$id =  Mage::getModel('marketplace/product')->saveConfigNewProduct($wholedata);
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
				$emailTempVariables['myvar2'] =$categoryname;
				$emailTempVariables['myvar3'] =$adminname;
				$emailTempVariables['myvar4'] = Mage::helper('marketplace')->__('I would like to inform you that recently i have added a new product in the store.');
				$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
				$emailTemp->setSenderName($cfname);
				$emailTemp->setSenderEmail($cmail);
//				$emailTemp->send($adminEmail,$adminname,$emailTempVariables);
			}
			}else{
				foreach ($errors as $message) {$this->_getSession()->addError($message);}
				$_SESSION['new_products_errors'] = $data;
			}
			$attr = $wholedata['attrdata'];
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Product has been Created Successfully'));
			$this->_redirect('marketplace/marketplaceaccount/configurableassociate',array('attr'=>$attr,'id'=>$id));
		}
		else{
			return $this->_redirect('marketplace/marketplaceaccount/new/');
		}
	}
	
	public function configurableassociateAction(){
		$this->loadLayout( array('default','marketplace_account_configurableassociate'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Add Associate Product'));
    	$this->renderLayout();
	}
	
	public function myproductslistAction(){
		$this->loadLayout( array('default','marketplace_account_productlist'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('My Product List'));
    	$this->renderLayout();
	}
	public function becomepartnerAction(){
		if($this->getRequest()->isPost()){ 
			 if (!$this->_validateFormKey()) {
              return $this->_redirect('marketplace/marketplaceaccount/becomepartner/');
             }
			
			$wholedata=$this->getRequest()->getParams();
			Mage::getModel('marketplace/product')->saveBecomePartnerStatus($wholedata);
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your request to become seller was successfully send to admin'));
			$this->_redirect('marketplace/marketplaceaccount/becomepartner/');
		}
		else{
			$this->loadLayout( array('default','marketplace_account_becomepartner'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('Seller Request Panel'));
			$this->renderLayout();
		}
	}
	
	public function myorderhistoryAction(){
		$this->loadLayout( array('default','marketplace_account_orderhistory'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('My Order History'));
    	$this->renderLayout();
	}
	
	public function editapprovedsimpleAction() {
		if($this->getRequest()->isPost()){
			 if (!$this->_validateFormKey()) {
              return $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
             }
			
			list($data, $errors) = $this->validatePost();
			$id= $this->getRequest()->getParam('productid');
			$customerid=Mage::getSingleton('customer/session')->getCustomerId();
			$collection_product = Mage::getModel('marketplace/product')->getCollection()->addFieldToFilter('mageproductid',array('eq'=>$id))->addFieldToFilter('userid',array('eq'=>$customerid));
            if(count($collection_product))
            {
				if(empty($errors)){	
					Mage::getSingleton('core/session')->setEditProductId($id);
					Mage::getModel('marketplace/product')->editProduct($id,$this->getRequest()->getParams());
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your Product Is Been Sucessfully Updated'));
					$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
				}else{
					foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
					$_SESSION['new_products_errors'] = $data;
					$this->_redirect('marketplace/marketplaceaccount/editapprovedsimple',array('id'=>$id));
				}
		    }
		    else
		    {
				$this->_redirect('marketplace/marketplaceaccount/editapprovedsimple',array('id'=>$id));
			}	
		}
		else{
			$urlid=$this->getRequest()->getParam('id');
			$loadpro =Mage::getModel('catalog/product')->load($urlid);
			if($loadpro ->getTypeId()!='simple'){
				$type_id = $loadpro ->getTypeId();
				if($type_id=='virtual')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedvirtual',array('id'=>$urlid));
				if($type_id=='downloadable')
					$this->_redirect('marketplace/marketplaceaccount/editapproveddownloadable',array('id'=>$urlid));	
				if($type_id=='configurable')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedconfigurable',array('id'=>$urlid));
			}
			$this->loadLayout( array('default','marketplace_account_simpleproductedit'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('MarketPlace: Edit Simple Magento Product'));
			$this->renderLayout();
		}
	}
	public function editapprovedvirtualAction() {
		if($this->getRequest()->isPost()){
			if (!$this->_validateFormKey()) {
				return $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
			}
			list($data, $errors) = $this->validatePost();
			$id= $this->getRequest()->getParam('productid');
			$customerid=Mage::getSingleton('customer/session')->getCustomerId();
			$collection_product = Mage::getModel('marketplace/product')->getCollection()->addFieldToFilter('mageproductid',array('eq'=>$id))->addFieldToFilter('userid',array('eq'=>$customerid));
			if(count($collection_product)){
				if(empty($errors)){     
					Mage::getSingleton('core/session')->setEditProductId($id);
					Mage::getModel('marketplace/product')->editVirtualProduct($id,$this->getRequest()->getParams());
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your Product Is Been Sucessfully Updated'));
					$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
				}else{
					foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
					$_SESSION['new_products_errors'] = $data;
					$this->_redirect('marketplace/marketplaceaccount/editapprovedvirtual',array('id'=>$id));
				}
			}else{
				$this->_redirect('marketplace/marketplaceaccount/editapprovedvirtual',array('id'=>$id));
			}					
		}else{
			$urlid=$this->getRequest()->getParam('id');
			$loadpro =Mage::getModel('catalog/product')->load($urlid);
			if($loadpro ->getTypeId()!='virtual'){
				$type_id = $loadpro ->getTypeId();
				if($type_id=='simple')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedsimple',array('id'=>$urlid));
				if($type_id=='downloadable')
					$this->_redirect('marketplace/marketplaceaccount/editapproveddownloadable',array('id'=>$urlid));	
				if($type_id=='configurable')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedconfigurable',array('id'=>$urlid));
			}
			$this->loadLayout( array('default','marketplace_account_virtualproductedit'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('MarketPlace: Edit Virtual Magento Product'));
			$this->renderLayout();
			}
        }
	public function editapproveddownloadableAction() {
		if($this->getRequest()->isPost()){
			if (!$this->_validateFormKey()) {
				$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
			}
			list($data, $errors) = $this->validatePost();
			$id= $this->getRequest()->getParam('productid');
			$customerid=Mage::getSingleton('customer/session')->getCustomerId();
			$collection_product = Mage::getModel('marketplace/product')->getCollection()->addFieldToFilter('mageproductid',array('eq'=>$id))->addFieldToFilter('userid',array('eq'=>$customerid));
			if(count($collection_product)){
				if(empty($errors)){     
					Mage::getSingleton('core/session')->setEditProductId($id);
					Mage::getModel('marketplace/product')->editDownloadableProduct($id,$this->getRequest()->getParams());
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your Product Is Been Sucessfully Updated'));
					$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
				}else{
					foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
					$_SESSION['new_products_errors'] = $data;
					$this->_redirect('marketplace/marketplaceaccount/editapproveddownloadable',array('id'=>$id));
				}
			}else{
				$this->_redirect('marketplace/marketplaceaccount/editapproveddownloadable',array('id'=>$id));
			}	        
		}else{
			$urlid=$this->getRequest()->getParam('id');
			$loadpro =Mage::getModel('catalog/product')->load($urlid);
			if($loadpro ->getTypeId()!='downloadable'){
				$type_id = $loadpro ->getTypeId();
				if($type_id=='simple')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedsimple',array('id'=>$urlid));
				if($type_id=='virtual')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedvirtual',array('id'=>$urlid));
				if($type_id=='configurable')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedconfigurable',array('id'=>$urlid));
			}
			$this->loadLayout( array('default','marketplace_account_downloadableproductedit'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('MarketPlace: Edit Downloadable Magento Product'));
			$this->renderLayout();
		}
	}
	public function editapprovedconfigurableAction() {
		if($this->getRequest()->isPost()){
			if(!$this->_validateFormKey()){
				 $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
			}
			list($data, $errors) = $this->validatePost();
			$id= $this->getRequest()->getParam('productid');
			$customerid=Mage::getSingleton('customer/session')->getCustomerId();
			$collection_product = Mage::getModel('marketplace/product')->getCollection()->addFieldToFilter('mageproductid',array('eq'=>$id))->addFieldToFilter('userid',array('eq'=>$customerid));
			if(count($collection_product)){
				if(empty($errors)){	
					Mage::getSingleton('core/session')->setEditProductId($id);
					Mage::getModel('marketplace/product')->editProduct($id,$this->getRequest()->getParams());
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Your Product Is Been Sucessfully Updated'));
					$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
				}else{
					foreach ($errors as $message) {Mage::getSingleton('core/session')->addError($message);}
					$_SESSION['new_products_errors'] = $data;
					$this->_redirect('marketplace/marketplaceaccount/editapprovedconfigurable',array('id'=>$id));
				}
			}else{
				$this->_redirect('marketplace/marketplaceaccount/editapprovedconfigurable',array('id'=>$id));
			}				
		}else{
			$urlid=$this->getRequest()->getParam('id');
			$loadpro =Mage::getModel('catalog/product')->load($urlid);
			if($loadpro ->getTypeId()!='configurable'){
				$type_id = $loadpro ->getTypeId();
				if($type_id=='simple')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedsimple',array('id'=>$urlid));
				if($type_id=='virtual')
					$this->_redirect('marketplace/marketplaceaccount/editapprovedvirtual',array('id'=>$urlid));
				if($type_id=='downloadable')
					$this->_redirect('marketplace/marketplaceaccount/editapproveddownloadable',array('id'=>$urlid));
			}
			$this->loadLayout( array('default','marketplace_account_configurableproductedit'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('MarketPlace: Edit Configurable Magento Product'));
			$this->renderLayout();
		}
	}
	
	public function deleteAction(){
		$urlapp=$_SERVER['REQUEST_URI'];
		$record=Mage::getModel('marketplace/product')->deleteProduct($urlapp);
		if($record==1){
			Mage::getSingleton('core/session')->addError( Mage::helper('marketplace')->__('YOU ARE NOT AUTHORIZE TO DELETE THIS PRODUCT..'));	
		}else{
			Mage::getSingleton('core/session')->addSuccess( Mage::helper('marketplace')->__('Your Product Has Been Sucessfully Deleted From Your Account'));
		}  
		$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
	}

	public function massdeletesellerproAction(){
		if($this->getRequest()->isPost()){
			if(!$this->_validateFormKey()){
				 $this->_redirect('marketplace/marketplaceaccount/myproductslist/');
			}
			$ids= $this->getRequest()->getParam('product_mass_delete');
			$customerid=Mage::getSingleton('customer/session')->getCustomerId();
			$unauth_ids = array();
			Mage::register("isSecureArea", 1);
			Mage :: app("default") -> setCurrentStore( Mage_Core_Model_App :: ADMIN_STORE_ID );
			foreach ($ids as $id){		
				$data['id']=$id;			
				Mage::dispatchEvent('mp_delete_product', $data);		
			    $collection_product = Mage::getModel('marketplace/product')->getCollection()
			    							->addFieldToFilter('mageproductid',array('eq'=>$id))
				    						->addFieldToFilter('userid',array('eq'=>$customerid));
				if(count($collection_product)) {					
					Mage::getModel('catalog/product')->load($id)->delete();
					$collection=Mage::getModel('marketplace/product')->getCollection()
									->addFieldToFilter('mageproductid',array('eq'=>$id));
					foreach($collection as $row){
						$row->delete();
					}
				}else{
					array_push($unauth_ids, $id);
				}
			}
		}
		if(count($unauth_ids)){
			Mage::getSingleton('core/session')->addError( Mage::helper('marketplace')->__('You are not authorized to delete products with id '.implode(",", $unauth_ids)));	
		}else{
			Mage::getSingleton('core/session')->addSuccess( Mage::helper('marketplace')->__('Products has been sucessfully deleted from your account'));
		}  
		$this->_redirect('marketplace/marketplaceaccount/myproductslist/');
	}
	
	public function mydashboardAction(){
		$this->loadLayout( array('default','marketplace_account_dashboard'));
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('My Dashboard'));
    	$this->renderLayout();
	}
	
	public function verifyskuAction(){
		$sku=$this->getRequest()->getParam('sku');
		$id = Mage::getModel('catalog/product')->getIdBySku($sku);
		if ($id){ $avl=0; }
		else{ $avl=1; } 
		echo json_encode(array("avl"=>$avl));
	}
	public function deleteimageAction(){
		$data= $this->getRequest()->getParams();
		$_product = Mage::getModel('catalog/product')->load($data['pid'])->getMediaGalleryImages();
		$main = explode('/',$data['file']);
		foreach($_product as $_image) { 
			$arr = explode('/',$_image['path']);
			if(array_pop($arr) != array_pop($main)){
				$newimage = $_image['file'];
				$id = $_image['value_id'];
				break;
			}		
		}
		$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
		$mediaApi->remove($data['pid'], $data['file']);
		if($newimage){
			$objprod=Mage::getModel('catalog/product')->load($data['pid']);
			$objprod->setSmallImage($newimage);
			$objprod->setImage($newimage);
			$objprod->setThumbnail($newimage);
			$objprod->save();	
		}
	}
	
	private function validatePost(){
		$errors = array();
		$data = array();
		foreach( $this->getRequest()->getParams() as $code => $value){
			switch ($code) :
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
			endswitch;
		}
		return array($data, $errors);
	}
	public function paymentAction(){
		$wholedata=$this->getRequest()->getParams();
		$customerid=Mage::getSingleton('customer/session')->getCustomerId();
		$collection = Mage::getModel('marketplace/userprofile')->getCollection();
		$collection->addFieldToFilter('mageuserid',array('eq'=>$customerid));
		foreach($collection as $row){
			$id=$row->getAutoid();
		}
		$collectionload = Mage::getModel('marketplace/userprofile')->load($id);
		$collectionload->setpaymentsource($wholedata['paymentsource']);
		$collectionload->save();
		Mage::getSingleton('core/session')->addSuccess( Mage::helper('marketplace')->__('Your Payment Information Is Sucessfully Saved.'));
		$this->_redirect('marketplace/marketplaceaccount/editProfile');
	 }
	public function askquestionAction(){
		$customerid=Mage::getSingleton('customer/session')->getCustomerId();
		$seller = Mage::getModel('customer/customer')->load($customerid);
		$email = $seller->getEmail();
		$name = $seller->getFirstname()." ".$seller->getLastname();
		$adminname = 'Administrators';
		$admin_storemail = Mage::getStoreConfig('marketplace/marketplace_options/adminemail');
		$adminEmail=$admin_storemail? $admin_storemail:Mage::getStoreConfig('trans_email/ident_general/email');
		$emailTemp = Mage::getModel('core/email_template')->loadDefault('queryadminemail');
		$emailTempVariables = array();
		$emailTempVariables['myvar1'] = $_POST['subject'];
		$emailTempVariables['myvar2'] =$name;
		$emailTempVariables['myvar3'] = $_POST['ask'];
		$processedTemplate = $emailTemp->getProcessedTemplate($emailTempVariables);
		$emailTemp->setSenderName($name);
		$emailTemp->setSenderEmail($email);
		$emailTemp->send($adminEmail,'Administrators',$emailTempVariables);
	}
	public function deleteprofileimageAction(){
		$collection = Mage::getModel('marketplace/userprofile')->getCollection();
		$collection->addFieldToFilter('mageuserid',array('eq'=>$this->_getSession()->getCustomerId()));
		foreach($collection as  $value){ 
			$data = $value; 
			$id = $value->getAutoid(); 
		}
		Mage::getModel('marketplace/userprofile')->load($id)->setBannerpic('')->save();
		echo "true";
	}
	public function deletelogoimageAction(){
		$collection = Mage::getModel('marketplace/userprofile')->getCollection();
		$collection->addFieldToFilter('mageuserid',array('eq'=>$this->_getSession()->getCustomerId()));
		foreach($collection as  $value){ 
			$data = $value; 
			$id = $value->getAutoid(); 
		}
		Mage::getModel('marketplace/userprofile')->load($id)->setLogopic('')->save();
		echo "true";
	}
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
	
	public function mytransactionAction(){
	   $this->loadLayout( array('default','marketplace_transaction_info'));
	   $this->_initLayoutMessages('customer/session');
       $this->_initLayoutMessages('catalog/session');
	   $this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('Transactions'));
	   $this->renderLayout();  
	}

	public function viewtransdetailsAction(){
	   $this->loadLayout( array('default','marketplace_marketplaceaccount_viewtransdetails'));
	   $this->_initLayoutMessages('customer/session');
       $this->_initLayoutMessages('catalog/session');
	   $this->getLayout()->getBlock('head')->setTitle( Mage::helper('marketplace')->__('Transaction Details'));
	   $this->renderLayout();  
	}

	public function downloadtranscsvAction(){
		$id = Mage::getSingleton('customer/session')->getId();
        $transid=$this->getRequest()->getParam('transid')!=""?$this->getRequest()->getParam('transid'):"";
        $filter_data_frm=$this->getRequest()->getParam('from_date')!=""?$this->getRequest()->getParam('from_date'):"";
        $filter_data_to=$this->getRequest()->getParam('to_date')!=""?$this->getRequest()->getParam('to_date'):"";
        if($filter_data_to){
            $todate = date_create($filter_data_to);
            $to = date_format($todate, 'Y-m-d 23:59:59');
        }
        if($filter_data_frm){
            $fromdate = date_create($filter_data_frm);
           $from = date_format($fromdate, 'Y-m-d H:i:s');
        }
        $collection = Mage::getModel('marketplace/sellertransaction')->getCollection();
        $collection->addFieldToFilter('sellerid',array('eq'=>$id));
        if($transid){
            $collection->addFieldToFilter('transactionid', array('eq' => $transid));
        }
        if($from || $to){
            $collection->addFieldToFilter('created_at', array('datetime' => true,'from' => $from,'to' =>  $to));
        }
        $collection->setOrder('transid');

        $data = array();
        foreach ($collection as $transactioncoll) {
        	$data1 =array();
        	$data1['Date'] = Mage::helper('core')->formatDate($transactioncoll->getCreatedAt(), 'medium', false);
        	$data1['Transaction Id'] = Mage::helper('core')->formatDate($transactioncoll->getCreatedAt(), 'medium', false);
        	if($transactioncoll->getCustomnote()) {
				$data1['Comment Message'] = $transactioncoll->getCustomnote(); 
			}else {
		 		$data1['Comment Message'] = Mage::helper('marketplace')->__('None');
			}
        	$data1['Transaction Amount'] = Mage::helper('core')->currency($transactioncoll->getTransactionamount(), true, false);
			$data[] = $data1;
        }

	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename=transactionlist.csv');
	    header('Pragma: no-cache');
	    header("Expires: 0");

	    $outstream = fopen("php://output", "w");    
	    fputcsv($outstream, array_keys($data[0]));

	    foreach($data as $result)
	    {
	        fputcsv($outstream, $result);
	    }

	    fclose($outstream);
	}
	
	public function deletelinkAction(){
		$data= $this->getRequest()->getParams();
		$_product = Mage::getModel('downloadable/link')->load($data['id'])->delete();
	}
	
	public function deletesampleAction(){
		$data= $this->getRequest()->getParams();
		$_product = Mage::getModel('downloadable/sample')->load($data['id'])->delete();
	}

	public function nicuploadscriptAction(){
		$data= $this->getRequest()->getParams();
		if(isset($_FILES['image'])){
	        $img = $_FILES['image'];
	        $imagename = rand().$img["name"];
	        $path = "nicimages/".$imagename;
	        if(!is_dir(Mage::getBaseDir().'/media/marketplace/nicimages')){
				mkdir(Mage::getBaseDir().'/media/marketplace/nicimages', 0755);
			}
			$target =Mage::getBaseDir().'/media/marketplace/nicimages/';
			$targetpath = $target.$imagename;
	        move_uploaded_file($img['tmp_name'],$targetpath);
	        $data = getimagesize($targetpath);
	        $link = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'marketplace/'.$path;
	        $res = array("upload" => array(
                        "links" => array("original" => $link),
                        "image" => array("width" => $data[0],
                                                 "height" => $data[1]
                                                )                              
                    ));
       	}
        echo json_encode($res);
	}

    public function emailorderAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->sendNewOrderEmail();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($order, Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }

                $this->_getSession()->addSuccess($this->__('The order email has been sent.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Failed to send the order email.'));
                Mage::logException($e);
            }

            $this->_redirect('marketplace/marketplaceaccount/vieworder', array('id' => $order->getId()));
            return false;
        }

        $this->_redirect('marketplace/marketplaceaccount/myorderhistory');
    }

    protected function _initOrder()
    {
        $id         = $this->getRequest()->getParam('id');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();

        $orderList  = Mage::getModel('marketplace/saleslist')->getCollection()
            ->addFieldToFilter('mageproownerid', array('eq' => $customerId))
            ->addFieldToFilter('mageorderid', array('eq' => $id));

        if ($orderList->getSize()) {
            $order = Mage::getModel('sales/order')->load($id);

            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('This order no longer exists.'));
                $this->_redirect('marketplace/marketplaceaccount/myorderhistory');
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);

                return false;
            }

            return $order;
        }
    }

    /**
     * Create order shipment
     *
     * @return void
     */
    public function shipAction()
    {
        $tracks = $this->getRequest()->getPost('tracking');
        foreach ($tracks as $data) {
            if (empty($data['number'])) {
                $this->_getSession()->addError($this->__('Enter track number.'));
                $this->_redirect('*/*/vieworder', array('id' => $this->getRequest()->getParam('order_id')));
                return;
            }
        }

        try {
            $shipment = $this->_initShipment();

            if (!$shipment) {
                $this->_forward('noRoute');
                return;
            }

            $shipment->register();
            $shipment->getOrder()->setCustomerNoteNotify(true);

            $this->_saveShipment($shipment);
            $this->_getSession()->addSuccess($this->__('The shipment has been created.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/vieworder', array('id' => $this->getRequest()->getParam('order_id')));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot save shipment.'));
            $this->_redirect('*/*/vieworder', array('id' => $this->getRequest()->getParam('order_id')));
        }

        $this->_redirect('*/*/vieworder', array('id' => $shipment->getOrderId()));
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment|bool
     */
    protected function _initShipment()
    {
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $shipment   = false;
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $orderId    = $this->getRequest()->getParam('order_id');

        if ($shipmentId) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        } elseif ($orderId) {
            $order    = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }

            /**
             * Check shipment is available to create separate from invoice
             */
            if ($order->getForcedDoShipmentWithInvoice()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order separately from invoice.'));
                return false;
            }

            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                $this->_getSession()->addError($this->__('Cannot do shipment for the order.'));
                return false;
            }

            $savedQtys = $this->_getItemQtys();
            $shipment  = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

            $tracks    = $this->getRequest()->getPost('tracking');

            if ($tracks) {
                foreach ($tracks as $data) {
                    if (empty($data['number'])) {
                        Mage::throwException($this->__('Tracking number cannot be empty.'));
                    }
                    $track = Mage::getModel('sales/order_shipment_track')->addData($data);
                    $shipment->addTrack($track);
                }
            }
        }

        Mage::register('current_shipment', $shipment);
        return $shipment;
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param  Mage_Sales_Model_Order_Shipment $shipment
     * @return Webkul_Marketplace_MarketplaceaccountController
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        return $this;
    }

    /**
     * Initialize shipment items QTY
     *
     * @return array
     */
    protected function _getItemQtys()
    {
        $data = $this->getRequest()->getParam('shipment');

        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }

        return $qtys;
    }
}
