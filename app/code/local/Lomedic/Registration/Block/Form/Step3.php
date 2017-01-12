<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Block_Form_Step3 extends Lomedic_Registration_Block_Form_Abstract
{
    /**
     * Retrieve form posting url
     * @return string
     */
    public function getPostActionUrl() 
    {
        return $this->getUrl('*/*/step3Post',array('_secure'=>true));
    }
}