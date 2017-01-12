<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Catalog_Block_Adminhtml_Catalog_Product_Grid_Renderer_Name  extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  \Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
     */
    public function render(Varien_Object $row)
    {
        if($row->getTypeId()==Lomedic_SellerCatalog_Model_Product_Type::TYPE_BATCH_PRODUCT) {
            $row->setName($row->getName()."(".Mage::helper('core')->__('Batch number').": #".$row->getBatchNumber().")");
        }
        return parent::render($row); 
    }
}
