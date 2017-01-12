<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Model_Marketplace_Product extends Webkul_Marketplace_Model_Product
{
    private $_product;

    /**
     * Save product
     * @param array $wholedata
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    public function saveBatchProduct($wholedata,$product)
    {
        $this->_product = $product;
        $this->_product->setCategoryIds(array(Mage::app()->getStore()->getRootCategoryId()));
        $cats=array();
        foreach($wholedata['category'] as $keycat) {
            array_push($cats,$keycat);
        }
        if(isset($wholedata['status']) && isset($wholedata['wstoreids']) ){
            $status=1;
            $stores=$wholedata['wstoreids'];
        } else {
            $status=Mage::getStoreConfig('marketplace/marketplace_options/product_approval')? 2:1;
            $stores=Mage::app()->getStore()->getStoreId();
        }
        $wholedata['customer_price'] = array();
        unset($wholedata['stock_data']);
        foreach ($wholedata['price'] as $key=>$price) {
            if($price<0 || $price=='') continue;

            $priceData = array('website_id'=>0,'cust_group'=>0,'price'=>$price);
            if($key=='private' && isset($wholedata['private_price'])) {
                $priceData['cust_group'] = Mage::getStoreConfig('softeq/loregistration/privatebuyer');
                $wholedata['customer_price'][] = $priceData;
            }
            if($key=='government' && isset($wholedata['government_price'])) {
                $priceData['cust_group'] = Mage::getStoreConfig('softeq/loregistration/govbuyer');
                $wholedata['customer_price'][] = $priceData;
            }
        }
        $prices = Mage::getResourceModel('loseller/customerprice_collection');
        $prices->getSelect()
            ->where('main_table.entity_id=?',$product->getId())
            ->where('main_table.customer_group_id IN(?)',array(Mage::getStoreConfig('softeq/loregistration/privatebuyer'),Mage::getStoreConfig('softeq/loregistration/govbuyer')));
        foreach ($prices as $_price) {
            $_price->delete();
        }

        $wholedata['price'] = 1;
        $wholedata['batch_weight'] = $wholedata['weight'];

        $lastId = $product->getId();
        Mage::dispatchEvent('mp_customoption_setdata', $wholedata);

        if(!is_dir(Mage::getBaseDir().'/media/catalog/marketplace/')) {
            mkdir(Mage::getBaseDir().'/media/catalog/marketplace/', 0755);
        }
        if(!is_dir(Mage::getBaseDir().'/media/catalog/marketplace/'.$lastId.'/')) {
            mkdir(Mage::getBaseDir().'/media/catalog/marketplace/'.$lastId.'/', 0755);
        }
        $target = Mage::getBaseDir().'/media/marketplace/'.$lastId.'/';
        $imageObjects = $product->getMediaGalleryImages()->getItems();
        $product->setData($wholedata)->save();
        $imageFileExists = array();
        // add new files and remove exists

        if(isset($_FILES) && count($_FILES)>0 && count(array_filter($_FILES['image']['name']))>0) {
            $imageObjects = array_slice($imageObjects,0,7);
			foreach ($imageObjects as $_image) {
                $fileNameList = explode('/',$_image->getFile());
                $filename2 = strtolower(array_pop($fileNameList));
                list($k,$v) = explode("-",$filename2);

                $imageFileExists[$k]= $filename2;
            //    var_dump(file_exists($_image->getPath()));
                copy($_image->getPath(),$target.$filename2);
            }

            foreach($_FILES as $_image){
                if(is_array($_image['tmp_name'])) {
                    $images = $_image['tmp_name'];
                    $i= -1;

                    foreach ($images as $key=>$image) {
                        $i++;
                        if(!$image) continue;

                        if(isset($imageFileExists[$key])) {
                            unlink($target.$imageFileExists[$key]);
                        }
                        $splitname = explode('.', $_image['name'][$key]);
                        if(count($splitname)>2) {
                            $extension = strtolower(array_pop($splitname));
                            $name = strtolower(join('.',$splitname));
                            $splitname = array($name,$extension);
                        }
                        $splitname[0] = str_replace('-', '', $splitname[0]);
                        $splitname[0] = str_replace('.', '', $splitname[0]);
                        $image_name = preg_replace('/[^A-Za-z0-9\-]/', '', $splitname[0]);
                        $target1 = $target.$key."-".$image_name.".".strtolower($splitname[1]);
                        $oImage = $imageObjects[$i];
                        if($oImage) {
                            $fileNameList = explode('/',$oImage->getFile());
                        }
						//echo "<pre>".$target1; print_r($image); exit;
                        move_uploaded_file($image,$target1);
                        if($key=='1') {
                            $wholedata['defaultimage'] = $key."-".$image_name.".".strtolower($splitname[1]);
                        }
                    }
                }
            }

            $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
            $items = $mediaApi->items($product->getId());
            foreach($items as $item) {
                $mediaApi->remove($product->getId(), $item['file']);
            }
            $this->_addImages($lastId,$wholedata['defaultimage']);
        }

        return $product->getId();
    }


    /**
     * Save new product
     *
     * @param array $wholedata
     * @return int
     */
    public function saveBatchNewProduct($wholedata)
    {
        $cats=array();
        $date = explode('/',$wholedata['expiration_date']);
        $wholedata['expiration_date'] = $date[2]."-".$date[1]."-".$date[0];

        foreach($wholedata['category'] as $keycat){
            array_push($cats,$keycat);
        }
        if(isset($wholedata['status']) && isset($wholedata['wstoreids']) ){
            $status=1;
            $stores=$wholedata['wstoreids'];
        }
        else{
            $status=Mage::getStoreConfig('marketplace/marketplace_options/product_approval')? 2:1;
            $stores=Mage::app()->getStore()->getStoreId();
        }
        $wholedata['customer_price'] = array();

        foreach ($wholedata['price'] as $key=>$price) {
            if($price<0.0001) continue;

            $priceData = array('website_id'=>0,'cust_group'=>0,'price'=>$price);
            if($key=='private') {
                $priceData['cust_group'] = Mage::getStoreConfig('softeq/loregistration/privatebuyer');
            }
            if($key=='government') {
                $priceData['cust_group'] = Mage::getStoreConfig('softeq/loregistration/govbuyer');
            }
            $wholedata['customer_price'][] = $priceData;
        }
        $wholedata['price'] = 1;
        $wholedata['batch_weight'] = $wholedata['weight'];
        $magentoProductModel = Mage::getModel('catalog/product');
        if(isset($_FILES['batch_certificate'])) {
            $path = Mage::getBaseDir().'/media/marketplace/certificate/'.$wholedata['batch_seller'];
            if(!is_dir($path)){
                mkdir($path, 0755);
            }
            $value = $_FILES['batch_certificate'];
            $uploader = new Varien_File_Uploader($value);
            $uploader->setFilesDispersion(true);
            $uploader->setFilenamesCaseSensitivity(false);
            $uploader->setAllowRenameFiles(true);
            $uploader->save($path, $value['name']);
            $fileName = Mage::getUrl('media/marketplace/certificate/').$wholedata['batch_seller'].$uploader->getUploadedFileName();
            $wholedata['batch_certificate'] = $fileName;
            unset($_FILES['batch_certificate']);
        }
        $magentoProductModel->setData($wholedata);
        $magentoProductModel->setCategoryIds(array(Mage::app()->getStore()->getRootCategoryId()));
        $saved = $magentoProductModel->save();
        $lastId = $saved->getId();
        $wholedata['id'] = $lastId;
        Mage::dispatchEvent('mp_customoption_setdata', $wholedata);
        $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection=Mage::getModel('marketplace/product');
        $collection->setmageproductid($lastId);
        $collection->setuserid($vendorId);
        $collection->setstatus($status);
        $collection->save();
        if(!is_dir(Mage::getBaseDir().'/media/marketplace/')){
            mkdir(Mage::getBaseDir().'/media/marketplace/', 0755);
        }
        if(!is_dir(Mage::getBaseDir().'/media/marketplace/'.$lastId.'/')){
            mkdir(Mage::getBaseDir().'/media/marketplace/'.$lastId.'/', 0755);
        }
        $target =Mage::getBaseDir().'/media/marketplace/'.$lastId.'/';
        if(isset($_FILES) && count($_FILES) > 0){
            foreach($_FILES as $_image){
                if(is_array($_image['tmp_name'])) {
                    $images = $_image['tmp_name'];
                    foreach ($images as $key=>$image) {
                        $splitname = explode('.', $_image['name'][$key]);
                        if(count($splitname)>2) {
                            $extension = strtolower(array_pop($splitname));
                            $name = strtolower(join('.',$splitname));
                            $splitname = array($name,$extension);
                        }
                        $splitname[0] = str_replace('-', '', $splitname[0]);
                        $splitname[0] = str_replace('.', '', $splitname[0]);
                        $image_name = preg_replace('/[^A-Za-z0-9\-]/', '', $splitname[0]);
                        $target1 = $target.$key.'-'.$image_name.".".strtolower($splitname[1]);
                        move_uploaded_file($image,$target1);
                        if($key=='1') {
                            $wholedata['defaultimage'] = $key."-".$image_name.".".strtolower($splitname[1]);
                        }
                    }
                }
            }
        }
        if($wholedata['defaultimage']){
            $this->_addImages($lastId,$wholedata['defaultimage']);
        }
        Mage::dispatchEvent('mp_customattribute_settierpricedata', $wholedata);

        return $lastId;
    }

    /**
     * Add images to product
     *
     * @param int $objProduct
     * @param array $defaultimage
     * @return boolean|void
     */
    private function _addImages($objProduct,$defaultimage)
    {
        $mediDir = Mage::getBaseDir('media');
        $imagesdir = $mediDir . '/marketplace/' . $objProduct . '/';
        if(!file_exists($imagesdir)){
            return false;
        }
        $objprod=Mage::getModel('catalog/product')->load($objProduct);
        foreach (new DirectoryIterator($imagesdir) as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->isDir()) continue;
            if($fileInfo->isFile()) {
                $file_extension = pathinfo($fileInfo->getPathname(), PATHINFO_EXTENSION);
                $allow_extension = array('png','gif','jpg','jpeg');
                if(in_array(strtolower($file_extension),$allow_extension)) {
                    $fileinfo=explode('@',$fileInfo->getPathname());
                    $options = array ('image','small_image','thumbnail');
                    $objprod->addImageToMediaGallery($fileInfo->getPathname(), $options, true, false);
                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                }
            }
        }
        $objprod->save();
        $_product = Mage::getModel('catalog/product')->load($objProduct)->getMediaGalleryImages();
        if (strpos($defaultimage, '.') !== FALSE) {
            $defimage =  explode('.',$defaultimage);
            foreach ($_product as $value) {
                $image = explode($defimage[0],$value->getFile());
                if (strpos($value->getFile(), $defimage[0]) !== FALSE) {
                    $newimage = $value->getFile();
                }
            }
        } else {
            foreach ($_product as $value) {
                if($value->getValueId()==$defaultimage) {
                    $newimage = $value->getFile();
                }
            }
        }
        $objprod=Mage::getModel('catalog/product')->load($objProduct);
        $objprod->setSmallImage($newimage);
        $objprod->setImage($newimage);
        $objprod->setThumbnail($newimage);
        $objprod->save();
        var_dump($a);
    }
}
