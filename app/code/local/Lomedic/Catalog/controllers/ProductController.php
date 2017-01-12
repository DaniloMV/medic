<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
require_once 'Mage/Catalog/controllers/ProductController.php';

class Lomedic_Catalog_ProductController extends Mage_Catalog_ProductController
{
    /** 
     * List action
     */
    public function listAction() 
    {
      $this->loadLayout();
      $this->renderLayout();
    }
}
