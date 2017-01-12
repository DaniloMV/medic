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

class Nwdthemes_Wunderadmin_Model_Optionscss extends Nwdthemes_All_Model_Optionscss {
    
    /**
     * Css data
     * @var array
     */
    
    protected $_cssData;
    
    /**
     * Set Css data
     *
     * @param array $data Css data
     */
    
    public function setCssData($data = array()) {
        $this->_cssData = $data;
        return $this;
    }
    
    /**
     * Generate css file from data
     *
     * @param $filePath Target file path
     * @param $fileName Target file name
     */
    
    public function generate($filePath, $fileName) {
		$css = Mage::app()
			->getLayout()
			->createBlock('core/template')
			->setData('cfg', $this->_cssData)
			->setTemplate( $this->getCssTemplatePath() )
			->toHtml();

		if ( empty($css) ) {
			throw new Exception(  Mage::helper('nwdall')->__('Css generation error: using template %s', $this->getCssTemplatePath()) );
		}

		try {
			$file = new Varien_Io_File();
			$file->setAllowCreateFolders(true)
				->open(array('path' => $filePath));
			$file->streamOpen($fileName, 'w+');
			$file->streamWrite($css);
			$file->streamClose();
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nwdall')->__('Css generation error: %s', $filePath . $filename) . '<br/>' . $e->getMessage());
			Mage::logException($e);
		}
    }

}