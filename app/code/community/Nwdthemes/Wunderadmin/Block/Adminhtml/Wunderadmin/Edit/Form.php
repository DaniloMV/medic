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

class Nwdthemes_Wunderadmin_Block_Adminhtml_Wunderadmin_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$data = array();
		if (Mage::getSingleton('adminhtml/session')->getWunderadminData())
		{
			$data = Mage::getSingleton('adminhtml/session')->getWunderadminData();
		}
		elseif (Mage::registry('wunderadmin_data'))
		{
			$data = Mage::registry('wunderadmin_data');
		}

		$form = new Varien_Data_Form(array(
	          'id' 		=> 'edit_form',
	          'action' 	=> $this->getUrl('*/*/save'),
	          'method' 	=> 'post',
			  'enctype'	=> 'multipart/form-data'
	    ));
		$form->setUseContainer(true);
		$this->setForm($form);

		$theme_fieldset = $form->addFieldset('wunderadmin_theme', array('legend' => Mage::helper('wunderadmin')->__('Wunder Admin Theme')));
		$themeField = $theme_fieldset->addField('theme', 'select', array(
			'label' 	=> Mage::helper('wunderadmin')->__('Select Theme'),
			'required' 	=> true,
			'name' 		=> 'theme',
			'values' 	=> Mage::getModel('wunderadmin/themes')->getThemesOptions(),
		));

		if (count($data) > 2)
		{
			$overwriteStylesField = $theme_fieldset->addField('overwrite_styles', 'select', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Overwrite Current Styles?'),
				'name' 		=> 'overwrite_styles',
				'required' 	=> true,
				'values' 	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
				'note'		=> Mage::helper('wunderadmin')->__('If selected yes current styles will be replaced with theme built in styles on theme change.'),
			));

			$resetCustomCssField = $theme_fieldset->addField('reset_custom_css', 'select', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Reset Custom CSS?'),
				'name' 		=> 'reset_custom_css',
				'required' 	=> true,
				'values' 	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
				'note'		=> Mage::helper('wunderadmin')->__('If selected yes current Custom CSS styles will be removed on theme change.'),
			));

			$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
				->addFieldMap($themeField->getHtmlId(), $themeField->getName())
				->addFieldMap($resetCustomCssField->getHtmlId(), $resetCustomCssField->getName())
				->addFieldDependence($resetCustomCssField->getName(), $themeField->getName(), Mage::getModel('wunderadmin/themes')->getOtherThemes($data['theme']) )
				->addFieldMap($overwriteStylesField->getHtmlId(), $overwriteStylesField->getName())
				->addFieldDependence($overwriteStylesField->getName(), $themeField->getName(), Mage::getModel('wunderadmin/themes')->getOtherThemes($data['theme']) ));
		}

		if ($data['theme'] != 'default')
		{
			$logo_fieldset = $form->addFieldset('wunderadmin_logo', array('legend' => Mage::helper('wunderadmin')->__('Custom Logo')));
			$logo_fieldset->addField('logo', 'image', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Admin Logo'),
				'required' 	=> false,
				'name' 		=> 'logo',
			));

			$font_fieldset = $form->addFieldset('wunderadmin_font', array('legend' => Mage::helper('wunderadmin')->__('Fonts')));
			$font_fieldset->addType('google_font','Nwdthemes_All_Lib_Varien_Data_Form_Element_Googlefont');
			$font_fieldset->addField('main_font', 'google_font', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Main Font'),
				'required' 	=> false,
				'name' 		=> 'main_font',
				'values'	=> Mage::getModel('nwdall/system_config_googlefonts')->toOptionArray()
			));
			$font_fieldset->addField('navigation_font', 'google_font', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Navigation Font'),
				'required' 	=> false,
				'name' 		=> 'navigation_font',
				'values'	=> Mage::getModel('nwdall/system_config_googlefonts')->toOptionArray()
			));
			$font_fieldset->addField('heading_font', 'google_font', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Heading Font'),
				'required' 	=> false,
				'name' 		=> 'heading_font',
				'values'	=> Mage::getModel('nwdall/system_config_googlefonts')->toOptionArray()
			));
			$font_fieldset->addField('button_font', 'google_font', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Button Font'),
				'required' 	=> false,
				'name' 		=> 'button_font',
				'values'	=> Mage::getModel('nwdall/system_config_googlefonts')->toOptionArray()
			));

			$layout_fieldset = $form->addFieldset('wunderadmin_layout', array('legend' => Mage::helper('wunderadmin')->__('Layout Colors')));
			$this->_addColorField($layout_fieldset, 'body_background', Mage::helper('wunderadmin')->__('Body Background'));
			$this->_addColorField($layout_fieldset, 'heading_color', Mage::helper('wunderadmin')->__('Heading Color'));
			$this->_addColorField($layout_fieldset, 'heading_icon_color', Mage::helper('wunderadmin')->__('Heading Icon'));
			$this->_addColorField($layout_fieldset, 'column_border', Mage::helper('wunderadmin')->__('Column Border'));
			$this->_addColorField($layout_fieldset, 'separator', Mage::helper('wunderadmin')->__('Separator Color'));

			$global_fieldset = $form->addFieldset('wunderadmin_global', array('legend' => Mage::helper('wunderadmin')->__('Global Elements Colors')));
			$this->_addColorField($global_fieldset, 'text_color', Mage::helper('wunderadmin')->__('Text Color'));
			$this->_addColorField($global_fieldset, 'link_color', Mage::helper('wunderadmin')->__('Link Color'));
			$this->_addColorField($global_fieldset, 'input_text', Mage::helper('wunderadmin')->__('Input Text'));
			$this->_addColorField($global_fieldset, 'input_background', Mage::helper('wunderadmin')->__('Input Background'));
			$this->_addColorField($global_fieldset, 'input_border', Mage::helper('wunderadmin')->__('Input Border'));
			$this->_addColorField($global_fieldset, 'checkbox_bg', Mage::helper('wunderadmin')->__('Checkbox Background'));
			$this->_addColorField($global_fieldset, 'checkbox_icon', Mage::helper('wunderadmin')->__('Checkbox Icon'));
			$this->_addColorField($global_fieldset, 'checkbox_border', Mage::helper('wunderadmin')->__('Checkbox Border'));
			$this->_addColorField($global_fieldset, 'checkbox_disabled', Mage::helper('wunderadmin')->__('Checkbox Disabled'));

			$header_fieldset = $form->addFieldset('wunderadmin_header', array('legend' => Mage::helper('wunderadmin')->__('Page Header Colors')));
			$this->_addColorField($header_fieldset, 'header_background', Mage::helper('wunderadmin')->__('Header Background'));
			$this->_addColorField($header_fieldset, 'header_logo_bg', Mage::helper('wunderadmin')->__('Header Logo Background'));
			$this->_addColorField($header_fieldset, 'header_search_text', Mage::helper('wunderadmin')->__('Header Search Text'));
			$this->_addColorField($header_fieldset, 'header_serach_icon', Mage::helper('wunderadmin')->__('Header Search Icon'));
			$this->_addColorField($header_fieldset, 'header_links', Mage::helper('wunderadmin')->__('Header Links Color'));
			$this->_addColorField($header_fieldset, 'header_text', Mage::helper('wunderadmin')->__('Header Text Color'));
			$this->_addColorField($header_fieldset, 'header_gotry_button', Mage::helper('wunderadmin')->__('Header Go Try Button'));
			$this->_addColorField($header_fieldset, 'header_gotry_text', Mage::helper('wunderadmin')->__('Header Go Try Button Text'));
			$this->_addColorField($header_fieldset, 'header_help_text', Mage::helper('wunderadmin')->__('Header Help Link Text'));
			$this->_addColorField($header_fieldset, 'header_help_border', Mage::helper('wunderadmin')->__('Header Help Link Border'));
			$this->_addColorField($header_fieldset, 'header_help_background', Mage::helper('wunderadmin')->__('Header Help Link Background'));

			$menu_fieldset = $form->addFieldset('wunderadmin_menu', array('legend' => Mage::helper('wunderadmin')->__('Navigation Colors')));
			$this->_addColorField($menu_fieldset, 'navigation_text', Mage::helper('wunderadmin')->__('Navigation Text Color'));
			$this->_addColorField($menu_fieldset, 'navigation_background', Mage::helper('wunderadmin')->__('Navigation Background'));
			$this->_addColorField($menu_fieldset, 'navigation_vertical_separators', Mage::helper('wunderadmin')->__('Navigation Vertical Separators'));
			$this->_addColorField($menu_fieldset, 'navigation_horizontal_separators', Mage::helper('wunderadmin')->__('Navigation Horizontal Separators'));
			$this->_addColorField($menu_fieldset, 'navigation_active', Mage::helper('wunderadmin')->__('Navigation Active Item Background'));
			$this->_addColorField($menu_fieldset, 'navigation_active_text', Mage::helper('wunderadmin')->__('Navigation Active Item Text'));
			$this->_addColorField($menu_fieldset, 'navigation_hover', Mage::helper('wunderadmin')->__('Navigation Hover Item Background'));
			$this->_addColorField($menu_fieldset, 'navigation_hover_text', Mage::helper('wunderadmin')->__('Navigation Hover Item Text'));
			$this->_addColorField($menu_fieldset, 'navigation_item_level_1_text', Mage::helper('wunderadmin')->__('Dropdown Level 1 Text'));
			$this->_addColorField($menu_fieldset, 'navigation_item_level_1', Mage::helper('wunderadmin')->__('Dropdown Level 1 Background'));
			$this->_addColorField($menu_fieldset, 'navigation_item_level_2_text', Mage::helper('wunderadmin')->__('Dropdown Level 2 Text'));
			$this->_addColorField($menu_fieldset, 'navigation_item_level_2', Mage::helper('wunderadmin')->__('Dropdown Level 2 Background '));
			$this->_addColorField($menu_fieldset, 'navigation_item_hover', Mage::helper('wunderadmin')->__('Dropdown Hover Background '));
			$this->_addColorField($menu_fieldset, 'navigation_item_hover_text', Mage::helper('wunderadmin')->__('Dropdown Item Hover Text'));

			$notify_fieldset = $form->addFieldset('wunderadmin_notify', array('legend' => Mage::helper('wunderadmin')->__('Notifications Colors')));
			$this->_addColorField($notify_fieldset, 'notify_highlight', Mage::helper('wunderadmin')->__('Notification Highlight Color'));
			$this->_addColorField($notify_fieldset, 'notify_button_bg', Mage::helper('wunderadmin')->__('Notification Button Background'));
			$this->_addColorField($notify_fieldset, 'notify_button_text', Mage::helper('wunderadmin')->__('Notification Button Color'));
			$this->_addColorField($notify_fieldset, 'notify_border', Mage::helper('wunderadmin')->__('Notification Border Color'));
			$this->_addColorField($notify_fieldset, 'notify_icon', Mage::helper('wunderadmin')->__('Notification Icon Color'));
			$this->_addColorField($notify_fieldset, 'notify_notice_icon', Mage::helper('wunderadmin')->__('Notification Notice Icon Color '));
			$this->_addColorField($notify_fieldset, 'notify_success_icon', Mage::helper('wunderadmin')->__('Notification Success Icon Color'));

			$footer_fieldset = $form->addFieldset('wunderadmin_footer', array('legend' => Mage::helper('wunderadmin')->__('Footer Colors')));
			$this->_addColorField($footer_fieldset, 'footer_bg', Mage::helper('wunderadmin')->__('Footer Background'));
			$this->_addColorField($footer_fieldset, 'footer_text', Mage::helper('wunderadmin')->__('Footer Text'));
			$this->_addColorField($footer_fieldset, 'footer_border', Mage::helper('wunderadmin')->__('Footer Border'));

			$buttons_fieldset = $form->addFieldset('wunderadmin_button', array('legend' => Mage::helper('wunderadmin')->__('Button Colors')));
			$this->_addColorField($buttons_fieldset, 'button_bg', Mage::helper('wunderadmin')->__('Button'));
			$this->_addColorField($buttons_fieldset, 'button_hover_bg', Mage::helper('wunderadmin')->__('Button Hover'));
			$this->_addColorField($buttons_fieldset, 'button_text', Mage::helper('wunderadmin')->__('Button Text'));
			$this->_addColorField($buttons_fieldset, 'button_icon', Mage::helper('wunderadmin')->__('Button Icon'));
			$this->_addColorField($buttons_fieldset, 'button_icon_bg', Mage::helper('wunderadmin')->__('Button Icon Background'));
			$this->_addColorField($buttons_fieldset, 'button_icon_hover_bg', Mage::helper('wunderadmin')->__('Button Icon Hover Background'));
			$this->_addColorField($buttons_fieldset, 'button_delete_bg', Mage::helper('wunderadmin')->__('Delete Button'));
			$this->_addColorField($buttons_fieldset, 'button_back_bg', Mage::helper('wunderadmin')->__('Back Button'));
			$this->_addColorField($buttons_fieldset, 'button_side_bg', Mage::helper('wunderadmin')->__('Side Column Button'));
			$this->_addColorField($buttons_fieldset, 'button_side_text', Mage::helper('wunderadmin')->__('Side Column Button Text'));
			$this->_addColorField($buttons_fieldset, 'button_side_border', Mage::helper('wunderadmin')->__('Side Column Button Border'));
			$this->_addColorField($buttons_fieldset, 'button_side_icon', Mage::helper('wunderadmin')->__('Side Column Button Icon'));

			$scope_fieldset = $form->addFieldset('wunderadmin_scope', array('legend' => Mage::helper('wunderadmin')->__('Scope Switcher Colors')));
			$this->_addColorField($scope_fieldset, 'scope_switcher_bg', Mage::helper('wunderadmin')->__('Scope Switcher Background'));
			$this->_addColorField($scope_fieldset, 'scope_switcher_border', Mage::helper('wunderadmin')->__('Scope Switcher Border'));
			$this->_addColorField($scope_fieldset, 'scope_switcher_help', Mage::helper('wunderadmin')->__('Scope Switcher Help Icon'));

			$tree_fieldset = $form->addFieldset('wunderadmin_tree', array('legend' => Mage::helper('wunderadmin')->__('Tree Colors')));
			$this->_addColorField($tree_fieldset, 'tree_actions_icon', Mage::helper('wunderadmin')->__('Tree Actions Icon'));
			$this->_addColorField($tree_fieldset, 'tree_item_color', Mage::helper('wunderadmin')->__('Tree Item Color'));

			$tabs_fieldset = $form->addFieldset('wunderadmin_tabs', array('legend' => Mage::helper('wunderadmin')->__('Tabs Colors')));
			$this->_addColorField($tabs_fieldset, 'tabs_bg', Mage::helper('wunderadmin')->__('Tabs Background'));
			$this->_addColorField($tabs_fieldset, 'tabs_border', Mage::helper('wunderadmin')->__('Tabs Border'));
			$this->_addColorField($tabs_fieldset, 'tabs_active_border', Mage::helper('wunderadmin')->__('Tabs Active Border'));
			$this->_addColorField($tabs_fieldset, 'tabs_item_text', Mage::helper('wunderadmin')->__('Tabs Item Text'));
			$this->_addColorField($tabs_fieldset, 'tabs_item_bg', Mage::helper('wunderadmin')->__('Tabs Item Background'));
			$this->_addColorField($tabs_fieldset, 'tabs_active_item_bg', Mage::helper('wunderadmin')->__('Tabs Active Item Background'));
			$this->_addColorField($tabs_fieldset, 'tabs_active_item_text', Mage::helper('wunderadmin')->__('Tabs Active Text'));

			$form_fieldset = $form->addFieldset('wunderadmin_form', array('legend' => Mage::helper('wunderadmin')->__('Form Colors')));
			$this->_addColorField($form_fieldset, 'form_heading_bg', Mage::helper('wunderadmin')->__('Form Heading Background'));
			$this->_addColorField($form_fieldset, 'form_heading_text', Mage::helper('wunderadmin')->__('Form Heading Text'));
			$this->_addColorField($form_fieldset, 'form_label_text', Mage::helper('wunderadmin')->__('Form Label Text'));
			$this->_addColorField($form_fieldset, 'form_bg', Mage::helper('wunderadmin')->__('Form Background'));
			$this->_addColorField($form_fieldset, 'form_border', Mage::helper('wunderadmin')->__('Form Border'));
			$this->_addColorField($form_fieldset, 'form_scope', Mage::helper('wunderadmin')->__('Form Scope Text'));
			$this->_addColorField($form_fieldset, 'form_scope_border', Mage::helper('wunderadmin')->__('Form Scope Border'));
			$this->_addColorField($form_fieldset, 'form_scope_hover', Mage::helper('wunderadmin')->__('Form Scope Hover'));

			$config_fieldset = $form->addFieldset('wunderadmin_config', array('legend' => Mage::helper('wunderadmin')->__('Config Tabs Colors')));
			$this->_addColorField($config_fieldset, 'config_tab_border', Mage::helper('wunderadmin')->__('Tab Border'));
			$this->_addColorField($config_fieldset, 'config_tab_section_head_bg', Mage::helper('wunderadmin')->__('Tab Section Heading Background'));
			$this->_addColorField($config_fieldset, 'config_tab_section_head_text', Mage::helper('wunderadmin')->__('Tab Section Heading Text'));
			$this->_addColorField($config_fieldset, 'config_tab_section_head_separator', Mage::helper('wunderadmin')->__('Tab Section Heading Separator'));
			$this->_addColorField($config_fieldset, 'config_tab_section_head_icon', Mage::helper('wunderadmin')->__('Tab Section Heading Icon'));
			$this->_addColorField($config_fieldset, 'config_tab_section_head_icon_collapsed', Mage::helper('wunderadmin')->__('Tab Section Heading Collapsed Icon'));
			$this->_addColorField($config_fieldset, 'config_tab_bg', Mage::helper('wunderadmin')->__('Tab Background'));
			$this->_addColorField($config_fieldset, 'config_tab_text', Mage::helper('wunderadmin')->__('Tab Text'));
			$this->_addColorField($config_fieldset, 'config_tab_active_bg', Mage::helper('wunderadmin')->__('Active Tab Background'));
			$this->_addColorField($config_fieldset, 'config_tab_active_text', Mage::helper('wunderadmin')->__('Active Tab Text'));
			$this->_addColorField($config_fieldset, 'config_tab_separator', Mage::helper('wunderadmin')->__('Tab Separator'));

			$system_fieldset = $form->addFieldset('wunderadmin_system', array('legend' => Mage::helper('wunderadmin')->__('Config Forms Colors')));
			$this->_addColorField($system_fieldset, 'system_handle_bg', Mage::helper('wunderadmin')->__('Section Handle Icon Background'));
			$this->_addColorField($system_fieldset, 'system_handle_icon', Mage::helper('wunderadmin')->__('Section Handle Icon'));
			$this->_addColorField($system_fieldset, 'system_handle_active_bg', Mage::helper('wunderadmin')->__('Section Handle Active Icon Background'));
			$this->_addColorField($system_fieldset, 'system_handle_active_icon', Mage::helper('wunderadmin')->__('Section Handle Active Icon'));

			$grid_fieldset = $form->addFieldset('wunderadmin_grid', array('legend' => Mage::helper('wunderadmin')->__('Grid Colors')));
			$this->_addColorField($grid_fieldset, 'grid_massactions_bg', Mage::helper('wunderadmin')->__('Grid Mass Actions Background'));
			$this->_addColorField($grid_fieldset, 'grid_massactions_text', Mage::helper('wunderadmin')->__('Grid Mass Actions Text'));
			$this->_addColorField($grid_fieldset, 'grid_massactions_border', Mage::helper('wunderadmin')->__('Grid Mass Actions Border'));
			$this->_addColorField($grid_fieldset, 'grid_heading_bg', Mage::helper('wunderadmin')->__('Grid Heading Background'));
			$this->_addColorField($grid_fieldset, 'grid_heading_border', Mage::helper('wunderadmin')->__('Grid Heading Border'));
			$this->_addColorField($grid_fieldset, 'grid_heading_hover_bg', Mage::helper('wunderadmin')->__('Grid Heading Hover Background'));
			$this->_addColorField($grid_fieldset, 'grid_heading_hover_text', Mage::helper('wunderadmin')->__('Grid Heading Hover Text'));
			$this->_addColorField($grid_fieldset, 'grid_heading_hover_border', Mage::helper('wunderadmin')->__('Grid Heading Hover Border'));
			$this->_addColorField($grid_fieldset, 'grid_sort_bg', Mage::helper('wunderadmin')->__('Grid Column Sort Background'));
			$this->_addColorField($grid_fieldset, 'grid_sort_border', Mage::helper('wunderadmin')->__('Grid Column Sort Border'));
			$this->_addColorField($grid_fieldset, 'grid_sort_icon', Mage::helper('wunderadmin')->__('Grid Column Sort Icon'));
			$this->_addColorField($grid_fieldset, 'grid_filter_bg', Mage::helper('wunderadmin')->__('Grid Filter Background'));
			$this->_addColorField($grid_fieldset, 'grid_filter_border', Mage::helper('wunderadmin')->__('Grid Filter Border'));
			$this->_addColorField($grid_fieldset, 'grid_data_bg', Mage::helper('wunderadmin')->__('Grid Data Background'));
			$this->_addColorField($grid_fieldset, 'grid_data_border', Mage::helper('wunderadmin')->__('Grid Data Border'));
			$this->_addColorField($grid_fieldset, 'grid_data_hover_row', Mage::helper('wunderadmin')->__('Grid Data Row Hover'));

			$popup_fieldset = $form->addFieldset('wunderadmin_popup', array('legend' => Mage::helper('wunderadmin')->__('Popup Colors')));
			$this->_addColorField($popup_fieldset, 'popup_heading_bg', Mage::helper('wunderadmin')->__('Popup Heading Background'));
			$this->_addColorField($popup_fieldset, 'popup_heading_text', Mage::helper('wunderadmin')->__('Popup Heading Text'));
			$this->_addColorField($popup_fieldset, 'popup_bg', Mage::helper('wunderadmin')->__('Popup Background'));
			$this->_addColorField($popup_fieldset, 'popup_close_icon', Mage::helper('wunderadmin')->__('Popup Close Icon'));

			$other_fieldset = $form->addFieldset('wunderadmin_other', array('legend' => Mage::helper('wunderadmin')->__('Other Colors')));
			$this->_addColorField($other_fieldset, 'login_form_bg', Mage::helper('wunderadmin')->__('Login Form Background'));
			$this->_addColorField($other_fieldset, 'legal_text', Mage::helper('wunderadmin')->__('Login Legal Text'));
			$this->_addColorField($other_fieldset, 'tab_error_icon', Mage::helper('wunderadmin')->__('Tab Error Icon'));
			$this->_addColorField($other_fieldset, 'tab_changed_icon', Mage::helper('wunderadmin')->__('Tab Changed Icon'));
			$this->_addColorField($other_fieldset, 'loader_color', Mage::helper('wunderadmin')->__('Loader Color'));
			$this->_addColorField($other_fieldset, 'loader_background', Mage::helper('wunderadmin')->__('Loader Background'));
			$this->_addColorField($other_fieldset, 'loader_border', Mage::helper('wunderadmin')->__('Loader Border'));

			$custom_fieldset = $form->addFieldset('wunderadmin_custom', array('legend' => Mage::helper('wunderadmin')->__('Custom CSS')));
			$custom_fieldset->addField('custom_css', 'textarea', array(
				'label' 	=> Mage::helper('wunderadmin')->__('Custom CSS'),
				'required' 	=> false,
				'name' 		=> 'custom_css'
			));
		}

        $data['overwrite_styles'] = true;
		if (isset($data['logo_type']) && $data['logo_type'] == 'skin' && isset($data['logo']) && $data['logo'])
		{
			$data['logo'] = Mage::getDesign()->getSkinUrl('images/' . $data['logo']);
		}

		$form->setValues($data);

		return parent::_prepareForm();
	}

	/**
	 * Generate params for color field
	 *
	 * @param Varien_Data_Form_Element_Fieldset $fieldset Fieldset object
	 * @param string $name Field name
	 * @param string $label Field label
	 */

	private function _addColorField(Varien_Data_Form_Element_Fieldset &$fieldset, $name, $label) {
        $fieldset->addField($name, 'text', array(
			'label' 	=> $label,
			'required' 	=> false,
			'name' 		=> $name,
			'class'		=> 'color {required:true, adjust:true, hash:true}'
		));
	}

}
