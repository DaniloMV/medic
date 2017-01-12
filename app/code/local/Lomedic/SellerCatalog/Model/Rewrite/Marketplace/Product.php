<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_Rewrite_Marketplace_Product extends Webkul_Marketplace_Model_Product
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marketplace/product');
    }
    
    /**
     * Create and save product
     * 
     * @param array $wholedata
     * @return int
     */
    public function saveSimpleNewProduct($wholedata)
    {
        if(isset($wholedata['generic_name_n']) && !empty($wholedata['generic_name_n'])){
            $wholedata['status'] = 2;
            $status = 2;
        }else{
            $wholedata['status'] = 1;
            $status = 1;
        }

        if(isset($status) && $status == 2) {

            $attribute= Mage::getModel('loseller/goverment_catalog');
            $attribute->setData(array(
                'code'          => $wholedata['code_n'],
                'description'   => $wholedata['description_a_n'],
                'presentation'  => $wholedata['presentation_n'],
                'level'         => $wholedata['level_n'],
                'generic_name'  => $wholedata['generic_name_n'],
                'qty'           => $wholedata['qty_n'],
                'category'      => $wholedata['category_n'],
                'is_remove'     => 0
            ));
            $attribute->save();

            $code = '';
            if($wholedata['code_n']){
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('code');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('code');

                foreach($coll as $val){
                    $tmp = $val->getData();
                    if($tmp["code"] == $wholedata['code_n']){
                        $code = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['code'] = $code; unset($wholedata['code_n']);

            $description_a = '';
            if($wholedata['description_a_n']){
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('description');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('description');

                foreach($coll as $val){
                    $tmp = $val->getData();
                    if($tmp["description"] == $wholedata['description_a_n']){
                        $description_a = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['description_a'] = $description_a;

            $presentation = '';
            if($wholedata['presentation_n']){
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('presentation');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('presentation');

                foreach($coll as $val){
                    $tmp = $val->getData();
                    if($tmp["presentation"] == $wholedata['presentation_n']){
                        $presentation = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['presentation'] = $presentation;

            $level = '';
            if($wholedata['level_n']) {
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('level');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('level');

                foreach($coll as $val){
                    $tmp = $val->getData();
                    if($tmp["level"] == $wholedata['level_n']){
                        $level = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['level'] = $level; unset($wholedata['level_n']);

            $generic_name = '';
            if($wholedata['generic_name_n']) {
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('generic_name');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('generic_name');

                foreach($coll as $val) {
                    $tmp = $val->getData();
                    if($tmp["generic_name"] == $wholedata['generic_name_n']){
                        $generic_name = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['generic_name'] = $generic_name;

            $qty = '';
            if($wholedata['qty_n']){
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('qty');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('qty');

                foreach($coll as $val){
                    $tmp = $val->getData();
                    if($tmp["qty"] == $wholedata['qty_n']){
                        $qty = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['qty'] = $qty;

            $category = '';
            if($wholedata['category_n']){
                $coll= Mage::getModel('loseller/goverment_catalog')->getCollection();
                $coll->addFieldToSelect('category');
                $coll->addFieldToSelect('entity_id');
                $coll->addFieldToFilter('is_remove',array('eq'=>0));
                $coll->getSelect()->group('category');

                foreach($coll as $val){
                    $tmp = $val->getData();
                    if($tmp["category"] == $wholedata['category_n']){
                        $category = $tmp["entity_id"];
                        break;
                    }
                }
            }
            $wholedata['category'] = $category;
        }

        $cats=array();
        foreach($wholedata['category'] as $keycat) {
            array_push($cats,$keycat);
        }
        if(isset($wholedata['status']) && isset($wholedata['wstoreids']) ) {
            $stores=$wholedata['wstoreids'];
        } else {
            $stores=Mage::app()->getStore()->getStoreId();
        }
        $magentoProductModel = Mage::getModel('catalog/product');
        $magentoProductModel->setData($wholedata);
        $saved=$magentoProductModel->save();
        $magentoProductModel = Mage::getModel('catalog/product')->load($saved->getId());
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
        $rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
        if(!$rates[$currentCurrencyCode]){
            $rates[$currentCurrencyCode] = 1;
        }
        if($wholedata['special_price']){
            $special_price = $wholedata['special_price']/$rates[$currentCurrencyCode];
            $magentoProductModel->setSpecialPrice($special_price);
        }
        $price = $wholedata['price']/$rates[$currentCurrencyCode];
        $magentoProductModel->setPrice($price);
        $magentoProductModel->setWeight($wholedata['weight']);
        $magentoProductModel->setStoresIds(array($stores));
        $storeId = Mage::app()->getStore()->getId();
        $magentoProductModel->setWebsiteIds(array(Mage::getModel('core/store')->load( $storeId )->getWebsiteId()));
        $magentoProductModel->setCategoryIds($cats);
        $magentoProductModel->setStatus($status);
        $saved=$magentoProductModel->save();
        $lastId = $saved->getId();
        $this->_saveStock($lastId,$wholedata['stock'],$wholedata['is_in_stock']);
        $wholedata['id'] = $lastId;
        Mage::dispatchEvent('mp_customoption_setdata', $wholedata);
        $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection1=Mage::getModel('marketplace/product');
        $collection1->setmageproductid($lastId);
        $collection1->setuserid($vendorId);
        $collection1->setstatus($status);
        $collection1->save();
        if(!is_dir(Mage::getBaseDir().'/media/marketplace/')){
            mkdir(Mage::getBaseDir().'/media/marketplace/', 0755);
        }
        if(!is_dir(Mage::getBaseDir().'/media/marketplace/'.$lastId.'/')){
            mkdir(Mage::getBaseDir().'/media/marketplace/'.$lastId.'/', 0755);
        }
        $target =Mage::getBaseDir().'/media/marketplace/'.$lastId.'/';
        if(isset($_FILES) && count($_FILES) > 0){
            if($wholedata['type_id']=='simple' || $wholedata['type_id']=='bundle'){
                foreach($_FILES as $image ){
                    if($image['tmp_name'] != ''){
                        $splitname = explode('.', $image['name']);
                        $splitname[0] = str_replace('-', '', $splitname[0]);
                        $image_name = preg_replace('/[^A-Za-z0-9\-]/', '', $splitname[0]);
                        $target1 = $target.$image_name.".".$splitname[1];
                        move_uploaded_file($image['tmp_name'],$target1);
                    }
                }
            }
        }

        $drugRegistration = "";
        if(isset($_FILES["drug_registration"]) && count($_FILES["drug_registration"]) > 0){
            if($_FILES["drug_registration"]['tmp_name'] != ''){
                $splitname = explode('.', $_FILES["drug_registration"]['name']);
                $splitname[0] = str_replace('-', '', $splitname[0]);
                $image_name = preg_replace('/[^A-Za-z0-9\-]/', '', $splitname[0]);
                $target1 = $target.$image_name.".".$splitname[1];
                move_uploaded_file($_FILES["drug_registration"]['tmp_name'],$target1);
                $drugRegistration = '/media/marketplace/'.$lastId.'/'.$image_name.".".$splitname[1];
            }
        }

        $collect =Mage::getModel('catalog/product')->load($lastId);
        $collect->setDrugRegistration($drugRegistration);
        $collect->setSeller(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $collect->save();

        if($wholedata['defaultimage']){
            $splitname = explode('.', $wholedata['defaultimage']);
            if($splitname[1]){
                $splitname[0] = str_replace('-', '', $splitname[0]);
                $image_name = preg_replace('/[^A-Za-z0-9\-]/', '', $splitname[0]);
                $wholedata['defaultimage'] = $image_name.".".$splitname[1];
            }
        }
        if($wholedata['defaultimage']){
            $this->_addImages($lastId,$wholedata['defaultimage']);
        }
        Mage::dispatchEvent('mp_customattribute_settierpricedata', $wholedata);

        $magentoProductModel = Mage::getModel('catalog/product')->load($lastId);
        $magentoProductModel->setShortDescription('1111');
        $magentoProductModel->setSku(sha1(date("Y-m-d H:i").microtime()));
        $magentoProductModel->setWeight('1111');
        $magentoProductModel->setDescription('1111');
        $magentoProductModel->setTaxClassId(0);
        $magentoProductModel->setBatchNumber(1);
        $magentoProductModel->setBatchWarehouse(1);
        $magentoProductModel->setExpirationDate('06/18/2015 08:04 AM');
        $magentoProductModel->setBatchParentProduct($lastId);
        $magentoProductModel->setBatchSeller($vendorId);
        $magentoProductModel->setBatchLength(111);
        $magentoProductModel->setBatchHeight(111);
        $magentoProductModel->setBatchWidth(111);
        $magentoProductModel->setBatchWeight(111);
        $magentoProductModel->setSeller(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $magentoProductModel->save();

        return $lastId;
    }

    /**
     * Save product stock
     * 
     * @param int $lastId
     * @param int $stock
     * @param bool $isstock
     */
    private function _saveStock($lastId,$stock,$isstock)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($lastId);
        if(!$stockItem->getId()){$stockItem->setProductId($lastId)->setStockId(1);}
        $stockItem->setProductId($lastId)->setStockId(1);
        $stockItem->setData('is_in_stock', $isstock);
        $savedStock = $stockItem->save();
        $stockItem->load($savedStock->getId())->setQty($stock)->save();
        $stockItem->setData('is_in_stock', $isstock);
        $savedStock = $stockItem->save();
    }
    
    /**
     * Add images to product
     * 
     * @param int $objProduct
     * @param array $defaultimage
     * @return boolean
     */
    private function _addImages($objProduct,$defaultimage)
    {
        $mediDir = Mage::getBaseDir('media');
        $imagesdir = $mediDir . '/marketplace/' . $objProduct . '/';
        if(!file_exists($imagesdir)){return false;}
        foreach (new DirectoryIterator($imagesdir) as $fileInfo){
            if($fileInfo->isDot() || $fileInfo->isDir()) continue;
            if($fileInfo->isFile()){
                $file_extension = pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION);
                $allow_extension = array('png','gif','jpg','jpeg');
                if(in_array($file_extension,$allow_extension)){
                    $fileinfo=explode('@',$fileInfo->getPathname());
                    $objprod=Mage::getModel('catalog/product')->load($objProduct);
                    $objprod->addImageToMediaGallery($fileInfo->getPathname(), array ('image','small_image','thumbnail'), true, false);
                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $objprod->save();
                }
            }
        }
        $_product = Mage::getModel('catalog/product')->load($objProduct)->getMediaGalleryImages();
        if (strpos($defaultimage, '.') !== FALSE){
            $defimage =  explode('.',$defaultimage);
            $defimage[0] = str_replace('-', '_', $defimage[0]);
            foreach ($_product as $value) {
                $image = explode($defimage[0],$value->getFile());
                if (strpos($value->getFile(), $defimage[0]) !== FALSE){
                    $newimage = $value->getFile();
                }
            }
        }else{
            foreach ($_product as $value) {
                if($value->getValueId()==$defaultimage){
                    $newimage = $value->getFile();
                }
            }
        }
        $objprod=Mage::getModel('catalog/product')->load($objProduct);
        $objprod->setSmallImage($newimage);
        $objprod->setImage($newimage);
        $objprod->setThumbnail($newimage);
        $objprod->save();
    }
}
