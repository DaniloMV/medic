<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_CatalogImport_Model_Export extends Mage_Core_Model_Abstract
{
    private $_collection;
    
    /**
     * Construct model
     */
    public function __construct() {
        $collection = Mage::getResourceModel('loseller/goverment_catalog_collection');
        $collection->getSelect()->where('is_remove=0');
        $this->_collection = $collection;
        Mage::getModel('core/config')->saveConfig('lomedic/export/updated',  time());
    }
    
    /**
     * Returns indexes of the fetched array as headers for CSV
     * @param array $collection
     * @return array
     */
    protected function _getCsvHeaders()
    {
        return Lomedic_CatalogImport_Model_Import_Entity_Catalog::headerNameList();
    }
    
     /**
     * Generates CSV file with items's list according to the collection in the $this->_collection
     * @return array
     */
    private function _generateFile()
    {
        $objPHPExcel = new PHPExcel();
        $cellList = array(0=>"A",1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P");
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("MedicJoint");
        $objPHPExcel->getProperties()->setLastModifiedBy("MedicJoint");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Document");
        $objPHPExcel->getProperties()->setDescription("Document for Office 2007 XLSX.");

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $headers = $this->_getCsvHeaders();
        $i=1;
        $ii = 0;
        foreach ($this->_getCsvHeaders($items) as $head) {
            $objPHPExcel->getActiveSheet()->SetCellValue($cellList[$ii].$i, $head);
            $ii++;
        }
        $i++;
        $items = $this->_collection->getItems();
        foreach ($items as $_item) {
            $_item->setIsRemove('');
            $ii=0;
            $data =array();
            foreach ($headers as $key=>$value) {
                $objPHPExcel->getActiveSheet()->SetCellValue($cellList[$ii].$i,$_item->getData($key));
                $ii++;
            }
            $i++;
        }
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Government Catalog');

        // Save Excel 2007 file
        $path = Mage::getBaseDir('var') . DS . 'export';
        $name = md5(microtime());
        $file = $path . DS . $name . '.xlsx';
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file);
        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }
    
    /**
     * Create export file
     * 
     * @return array
     */
    public function export() {
        return $this->_generateFile();
    }
}