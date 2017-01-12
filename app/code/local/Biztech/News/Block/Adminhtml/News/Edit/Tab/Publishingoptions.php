<?php

    class Biztech_News_Block_Adminhtml_News_Edit_Tab_Publishingoptions extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('news_form', array('legend'=>Mage::helper('news')->__('News information')));

            $fieldset->addField('date_to_publish', 'date', array(
                    'label'     => Mage::helper('news')->__('Start publishing on'),
                    'tabindex' => 1,
                    'after_element_html' =>"<button type='button' class='scalable' onclick=\"$('date_to_publish').setValue('')\">Clear</button>",
                    'image' => $this->getSkinUrl('images/grid-cal.gif'),
                    'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    'required'  => true,
                    'name'      => 'date_to_publish',
                ));

            $fieldset->addField('date_to_unpublish', 'date', array(
                    'label'     => Mage::helper('news')->__('End publishing on'),
                    'tabindex' => 1,
                    'after_element_html' =>"<button type='button' class='scalable' onclick=\"$('date_to_unpublish').setValue('')\">Clear</button>",
                    'image' => $this->getSkinUrl('images/grid-cal.gif'),
                    'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    'required'  => true,
                    'name'      => 'date_to_unpublish',
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