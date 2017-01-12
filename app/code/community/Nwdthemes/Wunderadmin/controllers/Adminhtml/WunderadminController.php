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

class Nwdthemes_Wunderadmin_Adminhtml_WunderadminController extends Mage_Adminhtml_Controller_Action {

	const CSS_PATH = '/adminhtml/default/wunderadmin/css';

	/**
	 * Check permissions
	 */

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('nwdthemes/wunderadmin');
    }

	/**
	 * Init action
	 */

	protected function _initAction() {
		return $this;
	}

	/**
	 * Init page
	 */

	protected function _initPage() {

		$this->_initAction()
			->loadLayout()
			->_setActiveMenu('nwdthemes/wunderadmin/wunderadmin')
			->_addBreadcrumb( Mage::helper('wunderadmin')->__('Wunder Admin'), Mage::helper('wunderadmin')->__('Wunder Admin') );
			
		$this->getLayout()
			->getBlock('head')
			->setTitle( Mage::helper('wunderadmin')->__('Wunder Admin') );
			
		return $this;
	}

	/**
	 * Default page
	 */

	public function indexAction() {

		$_cssPath = Mage::getBaseDir('skin') . self::CSS_PATH;
		if (! $this->_checkCssWritable($_cssPath) )
		{
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper('wunderadmin')->__('Not able to write CSS file. Please make this folder writable: ') . $_cssPath);
		}

		$this->_initPage();
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (empty($data))
		{
			$data = Mage::getModel('wunderadmin/settings')->loadStyles();
		}
		else
		{
			$data = array_merge(Mage::getModel('wunderadmin/settings')->loadStyles(), $data);
		}
		Mage::register('wunderadmin_data', $data);
			
		$this->renderLayout();
	}
	
	/**
	 * Save action
	 */
	
	public function saveAction() {
		
		if ($data = $this->getRequest()->getPost())
		{
			if ( ! $this->_checkCssWritable(Mage::getBaseDir('skin') . self::CSS_PATH) )
			{
				Mage::getSingleton('adminhtml/session')->addError( Mage::helper('wunderadmin')->__('Theme update error: Not able to write CSS file.') );
				$this->_redirect('*/*/');
				return;
			}

			$_files = array('logo');
			foreach ($_files as $_file) {
				if(isset($_FILES[$_file]['name']) and (file_exists($_FILES[$_file]['tmp_name'])))
				{
					try {
						$uploader = new Varien_File_Uploader($_file);
						$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(false);
						$path = Mage::getBaseDir('media') .DS.'nwdthemes'.DS.'wunderadmin'.DS ;
						$result = $uploader->save($path, $_FILES[$_file]['name'] );
					}
					catch(Exception $e) {
						unset($data[$_file]);
						unset($data[$_file . '_type']);
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage() . '  '. $path);
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						$this->_redirect('*/*/');
						return;
					}
					$data[$_file] = 'nwdthemes/wunderadmin/' . $result['file'];
					$data[$_file . '_type'] = 'media';
				}
				else
				{
					if(isset($data[$_file]['delete']) && $data[$_file]['delete'] == 1)
					{
						$data[$_file] = '';
					}
					else
					{
						unset($data[$_file]);
					}
				}
			}
			
			try {
				$this->_saveStyles($data);
				
				Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('wunderadmin')->__('Theme settings was successfully saved') );
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				Mage::register('wunderadmin_data', false);
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
                Mage::getSingleton('adminhtml/session')->setFormData( $data );
				$this->_redirect('*/*/');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Unable to find item to save') );
        $this->_redirect('*/*/');
	}

	/**
	 * Export action
	 */

	public function exportAction() {

		$data = Mage::getModel('wunderadmin/settings')->loadStyles();
		$theme = $data['theme'];
		unset($data['logo'], $data['logo_type'], $data['theme'], $data['timestamp']);
		$jsonData = json_encode($data);

		header('Content-Type: application/json');
		header('Content-Length: ' . strlen($jsonData));
		header('Content-Disposition: attachment; filename="wundeadmin_scheme_' . $theme . '.wcs"');
		echo $jsonData;
		exit();
	}

	/**
	 * Import form action
	 */

	public function importFormAction() {
        $importBlock = $this->getLayout()->createBlock('wunderadmin/adminhtml_import');
        $this->getResponse()->setBody($importBlock->toHtml());
	}

	/**
	 * Import action
	 */

	public function importAction() {
		$_file = 'colorscheme';
		if(isset($_FILES[$_file]['name']) and (file_exists($_FILES[$_file]['tmp_name'])))
		{
			try {
				$fileData = file_get_contents($_FILES[$_file]['tmp_name']);
				$colorData = json_decode($fileData, true);
				if (is_array($colorData) && $colorData)
				{
					$this->_saveStyles($colorData);

					Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('wunderadmin')->__('Color scheme imported successfully') );
					Mage::getSingleton('adminhtml/session')->setFormData(false);
					Mage::register('wunderadmin_data', false);
					$this->_redirect('*/*/');
					return;
				}
				else
				{
					Mage::getSingleton('adminhtml/session')->addError( Mage::helper('wunderadmin')->__('Incorrect import file format') );
					$this->_redirect('*/*/index');
					return;
				}
			}
			catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/');
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError( Mage::helper('wunderadmin')->__('No file selected to import') );
		$this->_redirect('*/*/');
	}

	/**
	 * Save styles data
	 *
	 * @param array $data
	 */

	private function _saveStyles($data = array()) {
		$oldStyles = Mage::getModel('wunderadmin/settings')->loadStyles();
		Mage::getModel('wunderadmin/settings')->saveStyles($data);
		$newStyles = Mage::getModel('wunderadmin/settings')->loadStyles();

		if (isset($oldStyles['timestamp']))
		{
			$file = new Varien_Io_File();
			$file->rm(Mage::getBaseDir('skin') . self::CSS_PATH . '/settings_' . $oldStyles['timestamp'] . '.css');
		}

		if ($newStyles['theme'] != 'default')
		{
			Mage::getSingleton('wunderadmin/optionscss')
				->setCssData($newStyles)
				->setCssTemplatePath('nwdthemes/wunderadmin/css.phtml')
				->generate(Mage::getBaseDir('skin') . self::CSS_PATH, 'settings_' . $newStyles['timestamp'] . '.css');
		}
	}

	/**
	 * Check if Css folder is writable
	 *
	 * @return boolean
	 */

	private function _checkCssWritable($cssPath) {

		$_isWrtiable = true;
		$ioFile = new Varien_Io_File();

		try {
			if ( ! ( $ioFile->checkandcreatefolder($cssPath) && $ioFile->isWriteable($cssPath) ) )
			{
				$_isWrtiable = false;
			}
		} catch (Exception $e) {
			$_isWrtiable = false;
			Mage::logException($e);
		}

		return $_isWrtiable;
	}

}
