<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_CatalogImport_Model_Import_Entity_Catalog extends Mage_ImportExport_Model_Import_Entity_Abstract
{
    protected $_processedDeletedCount = 0;
    protected $_processedAddedCount = 0;
    protected $_processedUpdatedCount = 0;
     
    /**
     * Size of bunch - part of entities to save in one step.
     */
    const BUNCH_SIZE = 20;
    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COL_ID            = 'Id';
    const COL_NAME          = 'Nombre Genérico';
    const COL_DESCRIPTION   = 'Descripción';
    const COL_CATEGORY      = 'Especialidad';
    const COL_CODE          = 'Clave';
    const COL_QTY           = 'Cantidad';
    const COL_PRESENTATION  = 'Presentación';
    const COL_GROUP_PRESENT = 'Grupo de especialidad';
    const COL_LEVEL         = 'Nivel';
    const COL_DELETE        = 'Borrar';
    const COL_UPDATE        = 'Last Update';

    protected $_permanentAttributes = array(
        self::COL_ID, self::COL_NAME, self::COL_DESCRIPTION, self::COL_CATEGORY, self::COL_CODE,
        self::COL_QTY, self::COL_PRESENTATION, self::COL_GROUP_PRESENT, self::COL_LEVEL
    );
    protected $_particularAttributes = array(
        self::COL_ID, self::COL_NAME, self::COL_DESCRIPTION, self::COL_CATEGORY, self::COL_CODE,
        self::COL_QTY, self::COL_PRESENTATION, self::COL_GROUP_PRESENT, self::COL_LEVEL,self::COL_UPDATE,self::COL_DELETE
    );
    
    public static $colHeaderList = array(
            'entity_id'          => self::COL_ID,
            'code'               => self::COL_CODE,
            'generic_name'       => self::COL_NAME,
            'description'        => self::COL_DESCRIPTION,
            'qty'                => self::COL_QTY,
            'presentation'       => self::COL_PRESENTATION,
            'group_presentation' => self::COL_GROUP_PRESENT,
            'category'           => self::COL_CATEGORY,
            'level'              => self::COL_LEVEL,
            'is_remove'          => self::COL_DELETE
        );
    
    /**
     * Error codes.
     */
    const ERROR_INVALID_WEBSITE      = 'invalidWebsite';
    const ERROR_INVALID_EMAIL        = 'invalidEmail';
    const ERROR_DUPLICATE_EMAIL_SITE = 'duplicateEmailSite';
    const ERROR_EMAIL_IS_EMPTY       = 'emailIsEmpty';
    const ERROR_ROW_IS_ORPHAN        = 'rowIsOrphan';
    const ERROR_VALUE_IS_REQUIRED    = 'valueIsRequired';
    const ERROR_INVALID_STORE        = 'invalidStore';
    const ERROR_EMAIL_SITE_NOT_FOUND = 'emailSiteNotFound';
    const ERROR_PASSWORD_LENGTH      = 'passwordLength';

   
    /**
     * Options entity DB table name.
     *
     * @var string
     */
    protected $_entityTable;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR_INVALID_WEBSITE      => 'Invalid value in Website column (website does not exists?)',
        self::ERROR_INVALID_EMAIL        => 'E-mail is invalid',
        self::ERROR_DUPLICATE_EMAIL_SITE => 'E-mail is duplicated in import file',
        self::ERROR_EMAIL_IS_EMPTY       => 'E-mail is not specified',
        self::ERROR_ROW_IS_ORPHAN        => 'Orphan rows that will be skipped due default row errors',
        self::ERROR_VALUE_IS_REQUIRED    => "Required attribute '%s' has an empty value",
        self::ERROR_INVALID_STORE        => 'Invalid value in Store column (store does not exists?)',
        self::ERROR_EMAIL_SITE_NOT_FOUND => 'E-mail and website combination is not found',
        self::ERROR_PASSWORD_LENGTH      => 'Invalid password length'
    );

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_dataSourceModel = Mage_ImportExport_Model_Import::getDataSourceModel();
        $this->_connection      = Mage::getSingleton('core/resource')->getConnection('write');
        $this->_entityTable   = Mage::getSingleton('core/resource')->getTableName('loseller/goverment_catalog');
    }
    
    public static function headerNameList() {
        return static::$colHeaderList;
    }

    /**
     * Delete entities.
     *
     * @return Lomedic_CatalogImport_Model_Import_Entity_Catalog
     */
    protected function _deleteEntities()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $idToDelete = array();

            foreach ($bunch as $rowNum => $rowData) {
                if (isset($rowData[self::COL_DELETE]) && $rowData[self::COL_DELETE] && isset($rowData[self::COL_ID])) {
                    $idToDelete[] = $rowData[self::COL_ID];
                }
            }
            if ($idToDelete) {
                $this->_connection->query(
                    $this->_connection->quoteInto(
                        "UPDATE `{$this->_entityTable}` SET `is_remove`=1 WHERE `entity_id` IN (?)", $idToDelete
                    )
                );
            }
        }
        return $this;
    }

    /**
     * Save customer data to DB.
     *
     * @throws Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
            $this->_deleteEntities();
            $this->_saveEntities();
        return true;
    }
    

    /**
     * Gather and save information about entities.
     *
     * @return Lomedic_CatalogImport_Model_Import_Entity_Catalog
     */
    protected function _saveEntities()
    {
        $updateTime = date('Y-m-d h:i:s');
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRowsIn = array();
            $entityRowsUp = array();

            foreach ($bunch as $rowNum => $rowData) {
                if(isset($rowData[self::COL_DELETE])) {
                    continue;
                }
                $entityValues = array(
                    'entity_id'          => (isset($rowData[self::COL_ID]) && $rowData[self::COL_ID])?$rowData[self::COL_ID]:'',
                    'generic_name'       => (isset($rowData[self::COL_NAME]) && $rowData[self::COL_NAME])?$rowData[self::COL_NAME]:'',
                    'description'        => (isset($rowData[self::COL_DESCRIPTION]) && $rowData[self::COL_DESCRIPTION])?$rowData[self::COL_DESCRIPTION]:'',
                    'category'           => (isset($rowData[self::COL_CATEGORY]) && $rowData[self::COL_CATEGORY])?$rowData[self::COL_CATEGORY]:'',
                    'code'               => (isset($rowData[self::COL_CODE]) && $rowData[self::COL_CODE])?$rowData[self::COL_CODE]:'',
                    'qty'                => (isset($rowData[self::COL_QTY]) && $rowData[self::COL_QTY])?$rowData[self::COL_QTY]:'',
                    'presentation'       => (isset($rowData[self::COL_PRESENTATION]) && $rowData[self::COL_PRESENTATION])?$rowData[self::COL_PRESENTATION]:'',
                    'group_presentation' => (isset($rowData[self::COL_GROUP_PRESENT]) && $rowData[self::COL_GROUP_PRESENT])?$rowData[self::COL_GROUP_PRESENT]:'',
                    'level'              => (isset($rowData[self::COL_LEVEL]) && $rowData[self::COL_LEVEL])?$rowData[self::COL_LEVEL]:'',
                    'updated_date'       => $updateTime
                );
               
                if($entityValues['entity_id']) {
                    $entityRowsUp[] = $entityValues;
                } else {
                    $entityRowsIn[] = $entityValues;
                }
            }
            $this->_saveEntity($entityRowsIn, $entityRowsUp);
        }
        return $this;
    }
    
    /**
     * Update and insert data in entity table.
     *
     * @param array $entityRowsIn Row for insert
     * @param array $entityRowsUp Row for update
     * @return Lomedic_CatalogImport_Model_Import_Entity_Catalog
     */
    protected function _saveEntity(array $entityRowsIn, array $entityRowsUp)
    {
        if ($entityRowsIn) {
            $this->_connection->insertMultiple($this->_entityTable, $entityRowsIn);
        }
        if ($entityRowsUp) {
            $this->_connection->insertOnDuplicate(
                $this->_entityTable,
                $entityRowsUp,
                array_keys(reset($entityRowsUp))
            );
        }
        return $this;
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $this->_processedEntitiesCount ++;
        if($rowData[self::COL_ID] && !$rowData[self::COL_DELETE]) {
            $this->_processedUpdatedCount++;
        }
        if($rowData[self::COL_ID] && $rowData[self::COL_DELETE]) {
            $this->_processedDeletedCount++;
        }
        if(!$rowData[self::COL_ID]) {
            $this->_processedAddedCount++;
        }
        return !isset($this->_invalidRows[$rowNum]);
    }
    
    /**
     * Validate data
     * 
     * @return \Mage_ImportExport_Model_Import_Entity_Abstract
     */
    public function validateData() {
        return parent::validateData();
    }
    
    /**
     * Return deleted rows
     * 
     * @return decimal
     */
    public function getDeletedRows() {
        return $this->_processedDeletedCount;
    }
    
    /**
     * Return added rows
     * 
     * @return decimal
     */
    public function getAddedRows() {
        return $this->_processedAddedCount;
    }
    
    /**
     * Return updated rows
     * 
     * @return decimal
     */
    public function getUpdatedRows() {
        return $this->_processedUpdatedCount;
    }
    
    /**
     * Return entity type code
     * 
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'goverment_catalog';
    }
}
