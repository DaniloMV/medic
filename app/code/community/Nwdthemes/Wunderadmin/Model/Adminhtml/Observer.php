<?php

/**
 * Nwdthemes Testimonials Extension
 *
 * @package     Testimonials
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2014. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

class Nwdthemes_Wunderadmin_Model_Adminhtml_Observer {
    
    /**
     * Set current admin theme
     */ 
    
    public function setTheme() {
        if ( Mage::helper('nwdall')->getCfg('general/enabled', 'wunderadmin_config') )
        {
            if (Mage::helper('wunderadmin')->getWunderStyle('theme') != 'default')
            {
                $_theme = 'wunderadmin';
                Mage::getDesign()->setTheme($_theme);
                foreach (array('layout', 'template', 'skin', 'locale') as $type) {
                    Mage::getDesign()->setTheme($type, $_theme);
                }
            }
        }
    }
}