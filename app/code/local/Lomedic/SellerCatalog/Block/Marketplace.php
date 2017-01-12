<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_SellerCatalog_Block_Marketplace extends Webkul_Marketplace_Block_Marketplace
{
    protected $_productsCollection = null;
    
    /**
     * Construct object
     */
    public function __construct()
    {
        parent::__construct();
        $userId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('marketplace/product')->getCollection()->addFieldToFilter('userid', array('eq' => $userId));
        $products = array();
        foreach ($collection as $data) {
            array_push($products, $data->getMageproductid());
        }
        $filter = $this->getRequest()->getParam('s') != "" ? $this->getRequest()->getParam('s') : "";

        $filter_prostatus = $this->getRequest()->getParam('prostatus') != "" ? $this->getRequest()->getParam('prostatus') : "";

        $filter_data_frm = $this->getRequest()->getParam('from_date') != "" ? $this->getRequest()->getParam('from_date') : "";
        $filter_data_to = $this->getRequest()->getParam('to_date') != "" ? $this->getRequest()->getParam('to_date') : "";
        if ($filter_data_to) {
            $todate = date_create($filter_data_to);
            $to = date_format($todate, 'Y-m-d H:i:s');
        }
        if ($filter_data_frm) {
            $fromdate = date_create($filter_data_frm);
            $from = date_format($fromdate, 'Y-m-d H:i:s');
        }


        $pageSize = 5;
        $currPage = 1;
        $params = $this->getRequest()->getParams();

        $countCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('type_id', array('eq' => 'simple'))
            ->addFieldToFilter('name', array('like' => "%" . $filter . "%"))
            ->addFieldToFilter('status', array('like' => "%" . $filter_prostatus . "%"))
            ->addFieldToFilter('created_at', array('datetime' => true, 'from' => $from, 'to' => $to))
            ->addFieldToFilter('entity_id', array('in' => $products))
            ->setOrder('entity_id', 'AESC')->count();

        $count = 1;
        if ($countCollection && $countCollection > 5) {
            $count = ceil($countCollection / 5);
        }

        if (isset($params["page"]) && !empty($params["page"]) && $params["page"] <= $count) {
            $currPage = $params["page"];
        }

        $arrGenericName = array();
        $arrDescription = array();
        $arrQty = array();
        $arrPresentation = array();
        $arrCode = array();
        $post = $this->getRequest()->getPost();

        if (isset($post["search"]) && !empty($post["search"])) {

            $collect = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect('generic_name')
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('is_remove', array('eq' => 0))
                ->addFieldToFilter('generic_name', array('like' => "%" . trim($post["search"]) . "%"))
                ->getSelect()->group('generic_name');

            foreach ($collect as $coll) {
                $data = $coll->getData();
                $arrGenericName[] = $data["entity_id"];
            }

            $collect = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect('description')
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('is_remove', array('eq' => 0))
                ->addFieldToFilter('description', array('like' => "%" . trim($post["search"]) . "%"))
                ->getSelect()->group('description');

            foreach ($collect as $coll) {
                $data = $coll->getData();
                $arrDescription[] = $data["entity_id"];
            }

            $collect = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect('qty')
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('is_remove', array('eq' => 0))
                ->addFieldToFilter('qty', array('like' => "%" . trim($post["search"]) . "%"))
                ->getSelect()->group('qty');

            foreach ($collect as $coll) {
                $data = $coll->getData();
                $arrQty[] = $data["entity_id"];
            }

            $collect = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect('presentation')
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('is_remove', array('eq' => 0))
                ->addFieldToFilter('presentation', array('like' => "%" . trim($post["search"]) . "%"))
                ->getSelect()->group('presentation');

            foreach ($collect as $coll) {
                $data = $coll->getData();
                $arrPresentation[] = $data["entity_id"];
            }

            $collect = Mage::getModel('loseller/goverment_catalog')->getCollection()
                ->addFieldToSelect('code')
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('is_remove', array('eq' => 0))
                ->addFieldToFilter('code', array('like' => "%" . trim($post["search"]) . "%"))
                ->getSelect()->group('code');

            foreach ($collect as $coll) {
                $data = $coll->getData();
                $arrCode[] = $data["entity_id"];
            }
        }

        if (isset($post) && !empty($post)) {
            $arrSearch = array();
            $arrSpecSearch = array();
            $arrSearchSearch = array();
            $arrSearchSector = array();
            $arrPostSectors = array();

            foreach ($post as $key => $val) {
                if ($key != "search" && $key != "public_sector" && $key != "private_sector") {
                    $arrSpecSearch[] = array('attribute' => 'category', 'eq' => $key);
                    $arrSearch[] = array('attribute' => 'generic_name', 'in' => array_keys($val));
                }

                if ($key == "public_sector" || $key == "private_sector") {
                    if ($val == "on") {
                        $arrPostSectors[] = $key;
                    }
                }

                if ($key == "search" && !empty($val)) {
                    $arrSearchSearch[] = array('attribute' => 'name', 'like' => "%" . $val . "%");
                    $arrSearchSearch[] = array('attribute' => 'generic_name', 'in' => $arrGenericName);
                    $arrSearchSearch[] = array('attribute' => 'description_a', 'in' => $arrDescription);
                    $arrSearchSearch[] = array('attribute' => 'qty', 'in' => $arrQty);
                    $arrSearchSearch[] = array('attribute' => 'presentation', 'in' => $arrPresentation);
                    $arrSearchSearch[] = array('attribute' => 'code', 'in' => $arrCode);
                }
            }


            if(!empty($arrPostSectors)) {

                $tmpArr = array();
                $count = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('type_id', array('eq' => 'batch'))
                    ->addFieldToFilter('entity_id', array('in' => $products))
                    ->setOrder('entity_id', 'AESC');

                foreach ($count as $kkkk => $val) {
                    $prices = array();

                    if (array_search("private_sector",$arrPostSectors) !== false && array_search("public_sector",$arrPostSectors) !== false) {
                        $pricesT = Mage::getResourceModel('loseller/customerprice_collection');
                        $pricesT->getSelect()
                            ->where('main_table.entity_id=?', $val->getId())
                            ->where('main_table.customer_group_id IN(?)', array(Mage::getStoreConfig('softeq/loregistration/privatebuyer'),Mage::getStoreConfig('softeq/loregistration/govbuyer')));

                        if(count($pricesT) == 2) {
                            $prices = $pricesT;
                        }

                    }elseif(array_search("private_sector",$arrPostSectors) !== false && array_search("public_sector",$arrPostSectors) === false){
                        $pricesT = Mage::getResourceModel('loseller/customerprice_collection');
                        $pricesT->getSelect()
                            ->where('main_table.entity_id=?', $val->getId())
                            ->where('main_table.customer_group_id = ?', Mage::getStoreConfig('softeq/loregistration/govbuyer'));

                        if(count($pricesT) == 0){
                            $prices = Mage::getResourceModel('loseller/customerprice_collection');
                            $prices->getSelect()
                                ->where('main_table.entity_id=?', $val->getId())
                                ->where('main_table.customer_group_id = ?', Mage::getStoreConfig('softeq/loregistration/privatebuyer'));
                        }
                    }elseif(array_search("private_sector",$arrPostSectors) === false && array_search("public_sector",$arrPostSectors) !== false){
                        $pricesT = Mage::getResourceModel('loseller/customerprice_collection');
                        $pricesT->getSelect()
                            ->where('main_table.entity_id=?', $val->getId())
                            ->where('main_table.customer_group_id = ?', Mage::getStoreConfig('softeq/loregistration/privatebuyer'));

                        if(count($pricesT) == 0){
                            $prices = Mage::getResourceModel('loseller/customerprice_collection');
                            $prices->getSelect()
                                ->where('main_table.entity_id=?', $val->getId())
                                ->where('main_table.customer_group_id = ?', Mage::getStoreConfig('softeq/loregistration/govbuyer'));
                        }
                    }

                    if (count($prices) > 0) {
                        $arrSearchSector[] = $val->getData("batch_parent_product");
                    }
                }
            }

            $pageSize = 5;
            $currPage = 1;
            $params = $this->getRequest()->getParams();

            if (!empty($arrPostSectors)) {
                $newProducts = array();
                if (!empty($arrPostSectors)) {
                    foreach ($arrSearchSector as $vvv) {
                        if (array_search($vvv, $products) !== false) {
                            $newProducts[] = $vvv;
                        }
                    }
                }
            }

            if(isset($newProducts)){
                if(!empty($newProducts)){
                    $products = array_unique($newProducts);
                }else{
                    $products = array();
                }
            }

            $countCollection = Mage::getModel('catalog/product')->getCollection();

            if (!empty($arrSearchSearch) && !empty($arrSearch)) {
                $countCollection->addAttributeToFilter($arrSpecSearch)
                    ->addAttributeToFilter($arrSearch)
                    ->addAttributeToFilter($arrSearchSearch);
            } elseif (!empty($arrSearchSearch) && empty($arrSearch)) {
                $countCollection->addAttributeToFilter($arrSearchSearch);
            } elseif (!empty($arrSearch) && empty($arrSearchSearch)) {
                $countCollection->addAttributeToFilter($arrSpecSearch)
                    ->addAttributeToFilter($arrSearch);
            }

            $countCollection->addFieldToFilter('type_id', array('eq' => 'simple'))
                ->addFieldToFilter('name', array('like' => "%" . $filter . "%"))
                ->addFieldToFilter('status', array('like' => "%" . $filter_prostatus . "%"))
                ->addFieldToFilter('created_at', array('datetime' => true, 'from' => $from, 'to' => $to))
                ->addFieldToFilter('entity_id', array('in' => $products))
                ->setOrder('entity_id', 'DESC');

            $countCollection = count($countCollection);

            $count = 1;
            if ($countCollection && $countCollection > 5) {
                $count = ceil($countCollection / 5);
            }

            if (isset($params["page"]) && !empty($params["page"]) && $params["page"] <= $count) {
                $currPage = $params["page"];
            }

            $collection = Mage::getModel('catalog/product')->getCollection()->setPageSize($pageSize)->setCurPage($currPage);
            $collection->addAttributeToSelect('*');

            if (!empty($arrSearchSearch) && !empty($arrSearch)) {
                $collection->addAttributeToFilter($arrSpecSearch)
                    ->addAttributeToFilter($arrSearch)
                    ->addAttributeToFilter($arrSearchSearch);
            } elseif (!empty($arrSearchSearch) && empty($arrSearch)) {
                $collection->addAttributeToFilter($arrSearchSearch);
            } elseif (!empty($arrSearch) && empty($arrSearchSearch)) {
                $collection->addAttributeToFilter($arrSpecSearch)
                    ->addAttributeToFilter($arrSearch);
            }

            $collection->addFieldToFilter('type_id', array('eq' => 'simple'))
                ->addFieldToFilter('name', array('like' => "%" . $filter . "%"))
                ->addFieldToFilter('status', array('like' => "%" . $filter_prostatus . "%"))
                ->addFieldToFilter('created_at', array('datetime' => true, 'from' => $from, 'to' => $to))
                ->addFieldToFilter('entity_id', array('in' => $products))
                ->setOrder('entity_id', 'DESC');

            $collection->getFirstItem()->setCountAll($count);
            $collection->getFirstItem()->setCurrPage($currPage);

        } else {

            $collection = Mage::getModel('catalog/product')->getCollection()->setPageSize($pageSize)->setCurPage($currPage)
                ->addAttributeToSelect('*')
                ->addFieldToFilter('type_id', array('eq' => 'simple'))
                ->addFieldToFilter('name', array('like' => "%" . $filter . "%"))
                ->addFieldToFilter('status', array('like' => "%" . $filter_prostatus . "%"))
                ->addFieldToFilter('created_at', array('datetime' => true, 'from' => $from, 'to' => $to))
                ->addFieldToFilter('entity_id', array('in' => $products))
                ->setOrder('entity_id', 'DESC');

            $collection->getFirstItem()->setCountAll($count);
            $collection->getFirstItem()->setCurrPage($currPage);
        }

        $this->setCollection($collection);
    }

    /**
     * Prepare layout
     * @return \Lomedic_SellerCatalog_Block_Marketplace
     */
    protected function _prepareLayout() 
    {
        parent::_prepareLayout(); 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $grid_per_page_values = explode(",",Mage::getStoreConfig('catalog/frontend/grid_per_page_values'));
        $arr_perpage = array();
        foreach ($grid_per_page_values as $value) {
                $arr_perpage[$value] = $value;
        }
        $pager->setAvailableLimit($arr_perpage);
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    } 

    public function getPagerHtml() 
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Get product
     * 
     * @return \Mage_Catalog_Model_Product
     */
    public function getProduct() 
    {
        $id = $this->getRequest()->getParam('id');
        $products = Mage::getModel('catalog/product')->load($id);
        return $products;
    }
}
