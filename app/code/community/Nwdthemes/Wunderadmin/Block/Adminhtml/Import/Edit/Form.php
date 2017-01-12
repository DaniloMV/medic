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

class Nwdthemes_Wunderadmin_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm() {

		$form = new Varien_Data_Form(array(
	          'id' 		=> 'import_form',
	          'name' 	=> 'importForm',
	          'action' 	=> $this->getUrl('*/*/import'),
	          'method' 	=> 'post',
			  'enctype'	=> 'multipart/form-data'
	    ));
		$form->setUseContainer(true);
		$this->setForm($form);

		$fieldset = $form->addFieldset('wunderadmin_import', array('legend' => Mage::helper('wunderadmin')->__('Import Color Scheme')));
		$fieldset->addField('colorscheme', 'file', array(
			'label' 	=> Mage::helper('wunderadmin')->__('Select Color Scheme File'),
			'required' 	=> true,
			'name' 		=> 'colorscheme'
		));

		return parent::_prepareForm();
	}
}