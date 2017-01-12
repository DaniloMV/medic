<?php
class Biztech_News_Block_Adminhtml_News extends Mage_Adminhtml_Block_Widget_Grid_Container
{
      public function __construct()
        {
            $this->_controller = 'adminhtml_news';
            $this->_blockGroup = 'news';
            $this->_headerText = Mage::helper('news')->__('News Manager');
            $this->_addButtonLabel = Mage::helper('news')->__('Add News');
            parent::__construct();
        }
        public function _prepareLayout()
        {
            return parent::_prepareLayout();
        }

        public function getNews()     
        { 
            if (!$this->hasData('news')) {
                $this->setData('news', Mage::registry('news'));
            }
            return $this->getData('news');

        }

        protected function _beforeToHtml()
        {
            Mage::helper('news/toolbar')->create($this, array(
                    'default_order' => 'created_time',
                    'dir' => 'asc',
                    'limits' => Mage::helper('news')->commentsPerPage(),
                )
            );

            return $this;
        }
       
        public function getPreparedCollection()
        {
            return $this->_prepareCollection();
        }
        protected function _prepareCollection()
        {
            $collection=Mage::getModel('news/news')->getCollection()->addFieldToFilter("status",array("eq"=>1));

            $collection->setPageSize(10);

            if ($this->getRequest()->getParam('p') > 0)
                $collection->setCurPage($this->getRequest()->getParam('p'));

            $this->setData('cached_collection', $collection);

            return $this->getData('cached_collection');
        }
}