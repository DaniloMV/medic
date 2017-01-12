<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_CatalogImport_Model_Import extends Mage_ImportExport_Model_Import
{
    const CONFIG_KEY_ENTITIES  = 'global/loimport/import_entities';
    
    const BEHAVIOR_UPDATE  = 'update';
    
    /**
     * Search and returl file source adapter
     * 
     * @param file $sourceFile
     * @return \Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    protected function _getSourceAdapter($sourceFile)
    {
        return Lomedic_CatalogImport_Model_Import_Adapter::findAdapterFor($sourceFile);
    }
    
    /**
     * Validates source file and returns validation result.
     *
     * @param string $sourceFile Full path to source file
     * @return bool
     */
    public function validateSource($sourceFile)
    {
        $this->addLogComment(Mage::helper('importexport')->__('Begin data validation'));
        $result = $this->_getEntityAdapter()
            ->setSource($this->_getSourceAdapter($sourceFile))
            ->isDataValid();

        $messages = $this->getOperationResultMessages($result);
        $this->addLogComment($messages);
        if ($result) {
            $this->addLogComment(Mage::helper('importexport')->__('Done import data validation'));
        }
        return $result;
    }
    /**
     * Create instance of entity adapter and returns it.
     *
     * @throws Mage_Core_Exception
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $validTypes = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_ENTITIES);
            if (isset($validTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = Mage::getModel($validTypes[$this->getEntity()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('importexport')->__('Invalid entity model')
                    );
                }
                if (!($this->_entityAdapter instanceof Mage_ImportExport_Model_Import_Entity_Abstract)) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('Entity adapter object must be an instance of Mage_ImportExport_Model_Import_Entity_Abstract')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('importexport')->__('Invalid entity'));
            }
            // check for entity codes integrity
            if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                Mage::throwException(
                    Mage::helper('importexport')->__('Input entity code is not equal to entity adapter code')
                );
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }
    
    /**
     * Return number of deleted rows
     * 
     * @return decimal
     */
    public function getDeletedCount() {
        return $this->_getEntityAdapter()->getDeletedRows();
    }
    
    /**
     * Return number of added rows
     * 
     * @return decimal
     */
    public function getAddedCount() {
        return $this->_getEntityAdapter()->getAddedRows();
    }
    
    /**
     * Return number of updated rows
     * 
     * @return decimal
     */
    public function getUpdatedCount() {
        return $this->_getEntityAdapter()->getUpdatedRows();
    }

}