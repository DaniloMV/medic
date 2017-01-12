<?php

class Vovsky_Customerprice_Model_Customerprice extends Mage_Catalog_Model_Product_Attribute_Backend_Price
{
    private $_productPrices = array();
    protected $_rates;
    protected function _getResource()
    {
        return Mage::getResourceSingleton('customerprice/customerprice');
    }
    public function _getWebsiteRates()
    {
        if (is_null($this->_rates)) {
            $this->_rates = array();
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Mage_Core_Model_Website */
                if ($website->getBaseCurrencyCode() != $baseCurrency) {
                    $rate = Mage::getModel('directory/currency')
                        ->load($baseCurrency)
                        ->getRate($website->getBaseCurrencyCode());
                    if (!$rate) {
                        $rate = 1;
                    }
                    $this->_rates[$website->getId()] = array(
                        'code' => $website->getBaseCurrencyCode(),
                        'rate' => $rate
                    );
                } else {
                    $this->_rates[$website->getId()] = array(
                        'code' => $baseCurrency,
                        'rate' => 1
                    );
                }
            }
        }
        return $this->_rates;
    }

    public function validate($object)
    {
        $attribute = $this->getAttribute();
        $customers = $object->getData($attribute->getName());
        if (empty($customers)) {
            return true;
        }

        // validate per website
        $duplicates = array();
        foreach ($customers as $customer) {
            if (!empty($customer['delete'])) {
                continue;
            }
            $compare = join('-', array($customer['website_id'], $customer['cust_group']));
            if (isset($duplicates[$compare])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website group price customer group and quantity.')
                );
            }
            $duplicates[$compare] = true;
        }
        if (!$attribute->isScopeGlobal() && $object->getStoreId()) {
            $origCustomerPrices = $object->getOrigData($attribute->getName());
            foreach ($origCustomerPrices as $customer) {
                if ($customer['website_id'] == 0) {
                    $compare = join('-', array($customer['website_id'], $customer['cust_group']));
                    $duplicates[$compare] = true;
                }
            }
        }

        // validate currency
        $baseCurrency = Mage::app()->getBaseCurrencyCode();
        $rates = $this->_getWebsiteRates();
        foreach ($customers as $customer) {
            if (!empty($customer['delete'])) {
                continue;
            }
            if ($customer['website_id'] == 0) {
                continue;
            }

            $compare = join('-', array($customer['website_id'], $customer['cust_group']));
            $globalCompare = join('-', array(0, $customer['cust_group']));
            $websiteCurrency = $rates[$customer['website_id']]['code'];

            if ($baseCurrency == $websiteCurrency && isset($duplicates[$globalCompare])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website group price customer group and quantity.')
                );
            }
        }

        return true;
    }

    public function preparePriceData(array $priceData, $productTypeId, $websiteId)
    {
        $rates = $this->_getWebsiteRates();
        $data = array();
        $price = Mage::getSingleton('catalog/product_type')->priceFactory($productTypeId);
        foreach ($priceData as $v) {
            $key = join('-', array($v['cust_group']));
            if ($v['website_id'] == $websiteId) {
                $data[$key] = $v;
                $data[$key]['website_price'] = $v['price'];
            } else if ($v['website_id'] == 0 && !isset($data[$key])) {
                $data[$key] = $v;
                $data[$key]['website_id'] = $websiteId;
                if ($price->isTierPriceFixed()) {
                    $data[$key]['price'] = $v['price'] * $rates[$websiteId]['rate'];
                    $data[$key]['website_price'] = $v['price'] * $rates[$websiteId]['rate'];
                }
            }
        }

        $cId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $customerprice = 0;
        if (count($data)) {
            foreach ($data as $item) {
                if ($item['cust_group'] == $cId) {
                    $customerprice = $item['website_price'];
                }
            }
        }

        return $customerprice;
    }
    
    public function addCustomerPrice($product) {
        if(!isset($this->_productPrices[$product->getId()])) {
            $productId = $product->getId();
            $collection = Mage::getResourceModel('locatalog/catalog_product_price_collection');
            $collection->getSelect()
                    ->where('entity_id=?',$productId);
            $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if($customerGroup) {
                if($customerGroup == Mage::getStoreConfig('softeq/loregistration/privateseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/privatebuyer')) {
                    $collection->getSelect()->where('customer_group_id=?',Mage::getStoreConfig('softeq/loregistration/privatebuyer'));
                }
                if($customerGroup == Mage::getStoreConfig('softeq/loregistration/govseller') || $customerGroup == Mage::getStoreConfig('softeq/loregistration/govbuyer')) {
                    $collection->getSelect()->where('customer_group_id=?',Mage::getStoreConfig('softeq/loregistration/govbuyer'));
                }
            }
            $customerPriceModel = $collection->getFirstItem();
            $this->_productPrices[$product->getId()] = $customerPriceModel->getValue();
        }
        $product->setCustomerPrice($this->_productPrices[$product->getId()]);
        return $product;
    }
    

    public function afterLoad($object)
    {
        if(Mage::registry('addBatchProductToSystem')) return $this;
        $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if($customerGroup) {
            $object = $this->addCustomerPrice($object);
        } else {
            $this->afterLoadAdmin($object);
        }
        return $this;
    }
    
    public function afterLoadAdmin($object)
    {
        if(Mage::registry('addBatchProductToSystem')) return $this;
        
        $storeId = $object->getStoreId();
        $websiteId = null;
        if ($this->getAttribute()->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($storeId) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        }

        $data = $this->_getResource()->loadPriceData($object->getId(), $websiteId);
        foreach ($data as $k => $v) {
            $data[$k]['website_price'] = $v['price'];
            if ($v['all_groups']) {
                $data[$k]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }
        }
        if (!$object->getData('_edit_mode')){
            $cId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $customerprice = 0;
            if (count($data)) {
                foreach ($data as $item) {
                    if ($item['cust_group'] == $cId) {
                        $customerprice = $item['website_price'];
                    }
                }
            }
        }

        if (!$object->getData('_edit_mode') && $websiteId) {
            $data = $this->preparePriceData($data, $object->getTypeId(), $websiteId);
            $customerprice = $data;
        }


        if (!$object->getData('_edit_mode')){
            $object->setData($this->getAttribute()->getName(), $customerprice);
        } else{
            $object->setData($this->getAttribute()->getName(), $data);
        }

        $object->setOrigData($this->getAttribute()->getName(), $customerprice);

        $valueChangedKey = $this->getAttribute()->getName() . '_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);

        return $this;
    }

    public function afterSave($object)
    {
        $websiteId = Mage::app()->getStore($object->getStoreId())->getWebsiteId();
        $isGlobal = $this->getAttribute()->isScopeGlobal() || $websiteId == 0;

        $customerPrices = $object->getData($this->getAttribute()->getName());
        if (empty($customerPrices)) {
            if ($isGlobal) {
                return $this;
                $this->_getResource()->deletePriceData($object->getId());
            } else {
                $this->_getResource()->deletePriceData($object->getId(), $websiteId);
            }
            return $this;
        }

        $old = array();
        $new = array();

        // prepare original data for compare
        $origCustomerPrices = $object->getOrigData($this->getAttribute()->getName());
        if (!is_array($origCustomerPrices)) {
            $origCustomerPrices = array();
        }
        foreach ($origCustomerPrices as $data) {
            if ($data['website_id'] > 0 || ($data['website_id'] == '0' && $isGlobal)) {
                $key = join('-', array($data['website_id'], $data['cust_group']));
                $old[$key] = $data;
            }
        }

        // prepare data for save
        foreach ($customerPrices as $data) {
            if (!isset($data['cust_group']) || !empty($data['delete'])) {
                continue;
            }
            if ($this->getAttribute()->isScopeGlobal() && $data['website_id'] > 0) {
                continue;
            }
            if (!$isGlobal && (int)$data['website_id'] == 0) {
                continue;
            }

            $key = join('-', array($data['website_id'], $data['cust_group']));

            $useForAllGroups = $data['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;
            $customerGroupId = !$useForAllGroups ? $data['cust_group'] : 0;

            $new[$key] = array(
                'website_id' => $data['website_id'],
                'all_groups' => $useForAllGroups ? 1 : 0,
                'customer_group_id' => $customerGroupId,
                'value' => $data['price'],
            );
        }

        $delete = array_diff_key($old, $new);
        $insert = array_diff_key($new, $old);
        $update = array_intersect_key($new, $old);

        $isChanged = false;
        $productId = $object->getId();

        if (!empty($delete)) {
            foreach ($delete as $data) {
                $this->_getResource()->deletePriceData($productId, null, $data['price_id']);
                $isChanged = true;
            }
        }

        if (!empty($insert)) {
            foreach ($insert as $data) {
                $price = new Varien_Object($data);
                $price->setEntityId($productId);
                $this->_getResource()->savePriceData($price);

                $isChanged = true;
            }
        }

        if (!empty($update)) {
            foreach ($update as $k => $v) {
                if ($old[$k]['price'] != $v['value']) {
                    $price = new Varien_Object(array(
                        'value_id' => $old[$k]['price_id'],
                        'value' => $v['value']
                    ));
                    $this->_getResource()->savePriceData($price);

                    $isChanged = true;
                }
            }
        }

        if ($isChanged) {
            $valueChangedKey = $this->getAttribute()->getName() . '_changed';
            $object->setData($valueChangedKey, 1);
        }

        return $this;
    }
}
