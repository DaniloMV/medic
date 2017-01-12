<?php

class Biztech_News_Block_Adminhtml_News_Edit_Tab_Seo extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('news_form', array('legend'=>Mage::helper('news')->__('News information')));
     
   
      $fieldset->addField('browser_title', 'text', array(
              'label'     => Mage::helper('news')->__('Page Title'),
              'required'  => false,
              'name'      => 'browser_title',
          ));
      $fieldset->addField('seo_keywords', 'textarea', array(
              'label'     => Mage::helper('news')->__('Keywords'),
              'required'  => false,
              'name'      => 'seo_keywords',
          ));
      $fieldset->addField('seo_description', 'textarea', array(
              'label'     => Mage::helper('news')->__('Description'),
              'required'  => false,
              'name'      => 'seo_description',
          ));
            
     
      if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
          Mage::getSingleton('adminhtml/session')->setNewsData(null);
      } elseif ( Mage::registry('news_data') ) {
          $form->setValues(Mage::registry('news_data')->getData());
      }
      return parent::_prepareForm();
  }
}