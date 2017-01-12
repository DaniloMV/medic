<?php

    class Biztech_News_Block_Product_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
    {
        protected function _construct()
        {
            
            $this->setTemplate('news/toolbar.phtml');
            
        }

        public function setCollection($collection)
        {
            parent::setCollection($collection);
            if ($this->getCurrentOrder() && $this->getCurrentDirection()) {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }

            return $this;
        }

        public function getCurrentOrder()
        {
            $order = $this->getRequest()->getParam($this->getOrderVarName());

            if(!$order) {
                return $this->_orderField;
            }

            if(array_key_exists($order, $this->getAvailableOrders())) {
                return $order;
            }

            return $this->_orderField;

        }

        public function getCurrentMode()
        {
            return null;
        }

        public function getAvailableLimit()
        {
            return $this->getPost()->getAvailLimits();
        }

        public function getCurrentDirection()
        {
            $dir = $this->getRequest()->getParam($this->getDirectionVarName());

            if(in_array($dir, array('asc', 'desc'))) {
                return $dir;
            }

            return Mage::helper('news')->defaultPostSort(Mage::app()->getStore()->getId());
        }

        public function setDefaultOrder($field)
        {
            $this->_orderField = $field;
        }


        public function getLimit()
        {
            return $this->getRequest()->getParam($this->getLimitVarName());
        }

    }
