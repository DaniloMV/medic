<?php

class Biztech_News_Model_Mysql4_News extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the news_id refers to the key field in your database table.
        $this->_init('news/news', 'news_id');
    }
}