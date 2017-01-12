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

class Nwdthemes_Wunderadmin_Helper_Data extends Mage_Core_Helper_Abstract {

	const COLOR_SCHEMES_URL = 'http://nwdthemes.com/2014/11/28/wunderadmin-color-schemes-downloads/';
	
	/**
	 * Get theme css file name depending of style settings
	 */ 

	public function getThemeCss() {
		return 'css/themes/' . $this->getWunderStyle('theme') . '.css';
	}

	/**
	 * Get settings css file
	 */

	public function getSettingsCss() {
		return 'css/settings_' . $this->getWunderStyle('timestamp') . '.css';
	}

	/**
	 * Get style option by name
	 *
	 * @param string $handle
	 * @param string $default
	 * @return string
	 */
	
	public function getWunderStyle($handle, $default = '') {
		return Mage::getSingleton('wunderadmin/settings')->getStyleOptionValue($handle, $default);
	}

	/**
	 * Get Logo image
	 *
	 * @return string
	 */

	public function getWunderLogo() {
		$logo = $this->getWunderStyle('logo');
		if ($logo)
		{
			switch ($this->getWunderStyle('logo_type'))
			{
				case 'skin' :
					$logoUrl = Mage::getDesign()->getSkinUrl('images/' . $logo);
				break;
				case 'media' :
					$logoUrl = Mage::getBaseUrl('media') . $logo;
				break;
			}
		}
		else
		{
			$logoUrl = Mage::getDesign()->getSkinUrl('images/logo.gif');
		}
		return $logoUrl;
	}

	/**
	 * Get color scheme url
	 */

	public function getColorSchemesUrl() {
		return self::COLOR_SCHEMES_URL;
	}

	/**
	 * Get Google Fonts include link
	 *
	 * @return string
	 */

	public function getGoogleFonts() {
		$linkRel = '';
		if ($this->getWunderStyle('theme') != 'default')
		{
			$arrFonts = array();
			$arrFontEntries = array('main_font', 'navigation_font', 'heading_font', 'button_font');
			foreach ($arrFontEntries as $_fontEntry) {
				if ( $this->getWunderStyle($_fontEntry) )
				{
					$_font = str_replace(' ', '+', $this->getWunderStyle($_fontEntry) ) . ':300,400,600,700,800';
					if ($_font && ! in_array($_font, $arrFonts))
					{
						$arrFonts[] = $_font;
					}
				}
			}
			$linkRel = $arrFonts ? '//fonts.googleapis.com/css?family=' . implode('|', $arrFonts) : '';
		}
		return $linkRel;
	}

}
