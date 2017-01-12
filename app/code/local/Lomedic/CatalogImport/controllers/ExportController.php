<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_CatalogImport_ExportController extends Lomedic_CatalogImport_Controller_Abstract
{
    /**
     * Inicialize export action
     */
    public function indexAction()
    {
        $content = Mage::getModel('loimport/export')->export();
        $filename = 'export_goverment_catalog-'.date('Y-m-d H:i:s').'.xlsx';
        $this->_prepareDownloadResponse($filename, $content);
    }
}