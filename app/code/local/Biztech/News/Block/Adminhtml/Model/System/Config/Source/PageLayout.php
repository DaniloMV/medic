<?php
    class Biztech_News_Block_Adminhtml_Model_System_Config_Source_PageLayout extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
    {
        public function toOptionArray()
        {
            return array(
                array('value' => 'page/1column.phtml', 'label'=>Mage::helper('adminhtml')->__('1column')),
                array('value' => 'page/2columns-right.phtml', 'label'=>Mage::helper('adminhtml')->__('2columns-right')),
                array('value' => 'page/2columns-left.phtml', 'label'=>Mage::helper('adminhtml')->__('2columns-left')),
                array('value' => 'page/3columns.phtml', 'label'=>Mage::helper('adminhtml')->__('3columns')),
            );
        }

}