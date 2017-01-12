<?php
    class Biztech_News_IndexController extends Mage_Core_Controller_Front_Action
    {
        public function indexAction()
        {
            $this->loadLayout(); 
            
            $root     = $this->getLayout()->getBlock('root');
            $template = Mage::getStoreConfig('news/news_general/news_list_page_layout');
            $root->setTemplate($template);  
           
            $this->renderLayout();
        }

        public function viewAction()
        { 
            $id = $this->getRequest()->getParam("id");
            if($id)
            {
                $this->getLayout()->createBlock('news/news')->setData(array("id"=>$id))->setTemplate('news/news.phtml')->toHtml();
            } 
            $this->loadLayout(); 

            $root = $this->getLayout()->getBlock('root');
            $template = Mage::getStoreConfig('news/news_general/news_detail_page_layout');
            $root->setTemplate($template); 
            
            $store  = Mage::app()->getStore()->getId();
             $news  = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('store_id',$store)->addFieldToFilter('news_id',$id)->getData();
              
            if($news[0]['browser_title'] != "")
                $this->getLayout()->getBlock('head')->setTitle($news[0]['browser_title']);
            if($news[0]['seo_keywords'] != "")
                $this->getLayout()->getBlock('head')->setKeywords($news[0]['seo_keywords']);
            if($news[0]['seo_description'] != "")
                $this->getLayout()->getBlock('head')->setDescription($news[0]['seo_description']);
            $this->renderLayout();
        }
}