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

class Nwdthemes_Wunderadmin_Block_Adminhtml_Wunderadmin extends Mage_Adminhtml_Block_Widget_Form_Container {
    
    /**
     * Constructor
     */
	
    public function __construct() {
		
        parent::__construct();
		
        $this->_blockGroup = 'wunderadmin';
        $this->_controller = 'adminhtml_wunderadmin';
		$this->_headerText = Mage::helper('wunderadmin')->__('Wunderadmin');
		
		$this->_updateButton('save', 'label', Mage::helper('wunderadmin')->__('Update theme settings'));
		$this->_removeButton('back');

		if (Mage::helper('wunderadmin')->getWunderStyle('theme') != 'default')
		{
			$buttonData = array(
				'label' 	=>  Mage::helper('wunderadmin')->__('Import Color Scheme'),
				'onclick'	=> 	"wunderImport.openDialog('" . $this->getUrl('*/*/importForm') . "')",
				'class'     =>  'import'
			);
			$this->_addButton('import_color_scheme', $buttonData, 0, 100, 'header');

			$buttonData = array(
				'label' 	=>  Mage::helper('wunderadmin')->__('Export Color Scheme'),
				'onclick'	=> 	"setLocation('" . $this->getUrl('*/*/export') . "')",
				'class'     =>  'export_button'
			);
			$this->_addButton('export_color_scheme', $buttonData, 0, 200, 'header');
		}

		$buttonData = array(
			'label' 	=>  Mage::helper('wunderadmin')->__('Get More Color Schemes'),
			'onclick'	=> 	"popWin('" . Mage::helper('wunderadmin')->getColorSchemesUrl() . "', '_blank')",
			'class'     =>  'get_more'
		);
		$this->_addButton('get_more_color_schemes', $buttonData, 0, 300, 'header');
    }	
}