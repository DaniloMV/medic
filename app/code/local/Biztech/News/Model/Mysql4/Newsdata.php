<?php

class Biztech_News_Model_Mysql4_Newsdata extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('news/newsdata', 'text_id');
    }
}