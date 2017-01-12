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

class Nwdthemes_Wunderadmin_Model_Themes extends Mage_Core_Model_Abstract {
    
    /**
     * Constructor
     */ 

    public function _construct() {
        parent::_construct();
        $this->_init('wunderadmin/themes');
    }
    
    /**
     * Get theme options list
     */ 

	public function getThemesOptions() {
		$options = array();
		foreach($this->getCollection() as $_theme) {
			$options[$_theme->getAlias()] = $_theme->getName();
		}
		return $options;
	}

	/**
	 * Get theme styles
	 *
	 * @param string $themeAlias Theme alias
	 * @return array Style data
	 */

	public function getThemeStyles($themeAlias) {
		$theme = $this->getCollection()
			->addFieldToFilter('alias', $themeAlias)
			->getFirstItem();
		return $theme ? json_decode( $theme->getStyles() , true) : array();
	}

	/**
	 * Get other themes list
	 *
	 * @param string $excludeAlias Theme alias to exclude fomr list
	 * @return array List of other non default themes
	 */

	public function getOtherThemes($excludeAlias = '') {
		$themes = array();
		foreach($this->getCollection() as $_theme) {
			if ( ! in_array($_theme->getAlias(), array('default', $excludeAlias)))
			{
				$themes[] = $_theme->getAlias();
			}
		}
		return $themes;

	}
    
}    