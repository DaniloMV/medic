<?php

class Biztech_News_Model_Newsdata extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('news/newsdata');
    }
}