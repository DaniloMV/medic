<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Model_Lock extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('loregistration/lock');
    }
}
