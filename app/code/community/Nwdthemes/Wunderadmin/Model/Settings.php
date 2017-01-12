<?php

/**
 * Nwdthemes Wunder Admin Extension
 *
 * @package     Wunderadmin
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2014. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

class Nwdthemes_Wunderadmin_Model_Settings extends Mage_Core_Model_Abstract {
    
    /**
     * Constants
     */ 
    
    const SETTINGS_ID = 1;
    
    /**
     * Store loaded styles
     */ 
    
    private $_styles = false;
    
    /**
     * Constructor
     */ 

    public function _construct() {
        parent::_construct();
        $this->_init('wunderadmin/settings');
    }
    
    /**
     * Set styles in JSON
     *
     * @param array $data
     * @return Nwdthemes_Wunderadmin_Model_Settings
     */
    
    public function setJsonStyles($data = array()) {
        unset($data['form_key']);
        $this->setStyles( json_encode($data) );
        return $this;
    }
    
    /**
     * Get styles in JSON
     *
     * @return array Style data
     */
    
    public function getJsonStyles() {
        return json_decode( $this->getStyles() , true);
    }
    
    /**
     * Load styles from settings
     *
     * @return array Style data
     */
    
    public function loadStyles() {
        return $this->load(self::SETTINGS_ID)->getJsonStyles();
    }
    
    /**
     * Save styles to settings
     */
    
    public function saveStyles($data = array()) {
        
        $overwriteStyles = isset($data['overwrite_styles']) ? $data['overwrite_styles'] : false;
        $resetCustomCss = isset($data['reset_custom_css']) ? $data['reset_custom_css'] : false;
        $oldData = $this->loadStyles();
        $theme = isset($data['theme']) ? $data['theme'] : (isset($oldData['theme']) ? $oldData['theme'] : 'default');

        if ( ($theme != 'default' && $theme != $oldData['theme']) && ( count($oldData) < 3 || $overwriteStyles) )
        {
            $styleData = Mage::getModel('wunderadmin/themes')->getThemeStyles($theme);
            $styleData['theme'] = $theme;
            if ( ! $resetCustomCss)
            {
                $styleData['custom_css'] = isset($data['custom_css']) ? $data['custom_css'] : '';
            }
        }
        else
        {
            unset($data['overwrite_styles'], $data['reset_custom_css']);
            $styleData = array_merge($oldData, $data);
        }
        $styleData['timestamp'] = time();
        
        $this->load(self::SETTINGS_ID)
            ->setJsonStyles($styleData)
            ->setId(self::SETTINGS_ID)
            ->save();
    }
    
	/**
	 * Get style option by name
	 *
	 * @param string $handle
	 * @param string $default
	 * @return string
	 */
	
	public function getStyleOptionValue($handle, $default = '') {
        
        if ($this->_styles === false)
        {
            $this->_styles = $this->loadStyles();
        }
		
		return isset($this->_styles[$handle]) && $this->_styles[$handle] ? $this->_styles[$handle] : $default;
	}    
    
}    