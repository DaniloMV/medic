<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */

$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'date_of_creation', array(
    'group'         => 'General',
    'label'         => 'Date of creation',
    'input'         => 'datetime',
    'type'          => 'datetime',
    'time'          => 1,
    'visible'       => 1,
    'input_renderer'=> 'loseller/adminhtml_widget_grid_column_renderer_datefrom',//definition of renderer
    'format'        => 'yyyy-MM-dd HH:mm',
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1,
    'value'         => 1,
    'class'         => 'validate-date date-range-custom_theme-from validate-date-range input-text required-entry datepicker',
));

$installer->addAttribute('catalog_product', 'expiration_date_p', array(
    'group'         => 'General',
    'label'         => 'Expiration date',
    'input'         => 'datetime',
    'type'          => 'datetime',
    'time'          => 1,
    'input_renderer'=> 'loseller/adminhtml_widget_grid_column_renderer_dateto',//definition of renderer
    'visible'       => 1,
    'format'        => 'yyyy-MM-dd HH:mm',
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => 1,
    'value'         => 1,
    'class'         => 'validate-date date-range-custom_theme-to validate-date-range input-text required-entry datepicker',
));

$installer->endSetup();