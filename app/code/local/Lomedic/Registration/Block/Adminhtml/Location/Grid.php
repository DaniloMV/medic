<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Demac_MultiLocationInventory_Block_Adminhtml_Location_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Init - prepare widget grid.
     */
    public function __construct() {
        parent::__construct();

        // Set some defaults for our grid
        // Set some defaults for our grid

        $this->setDefaultSort('id');
        $this->setId('LocationGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Set this widget grid's collection to be a collection of locations then let parent prepare grid object collection.
     *
     * @return Demac_MultiLocationInventory_Block_Adminhtml_Location_Grid
     */
    protected function _prepareCollection() {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel('demac_multilocationinventory/location_collection');

        if ($this->isDealer()) {
            $website = Mage::helper('dealeraccount')->getUserWebsite();
            $stores = Mage::getModel('core/website')->load($website['website_id'])
                    ->getStoreIds();

            if (count($stores)) {
                $collection->addStoreFilter($stores, false);
            }

            $address = $this->getAddress();

            foreach ($collection as $item) {
                if ('Not assigned' == $item->getAddress()) {
                    $item->setAddress($address['street'])
                            ->setZipcode($address['postcode'])
                            ->setCity($address['city'])
                            ->setCountryId($address['country_id'])
                            ->setRegionId($address['region_id'])
                            ->setTelephone($address['telephone'])
                            ->setFax($address['fax'])
                            ->setAddressEmail($address['address_email'])
                            ->save();
                }
            }
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Add all necessary columns then prepare for rendering.
     *
     * @return Demac_MultiLocationInventory_Block_Adminhtml_Location_Grid
     */
    protected function _prepareColumns() {
        // Add the columns that should appear in the grid
        $this->addColumn('name', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('Name'),
            'index' => 'name',
                )
        );

        $this->addColumn('address', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('Address'),
            'index' => 'address',
                )
        );

        $this->addColumn('zipcode', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('Postal Code'),
            'index' => 'zipcode',
                )
        );

        $this->addColumn('city', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('City'),
            'index' => 'city',
                )
        );

        $this->addColumn('region_id', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('Region'),
            'index' => 'region_id',
                )
        );

        $this->addColumn('country_id', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'country_id',
        ));


        $this->addColumn('status', array(
            'header' => Mage::helper('demac_multilocationinventory')->__('Status'),
            'index' => Mage::helper('demac_multilocationinventory')->__('status'),
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('demac_multilocationinventory')->__('Disabled'),
                1 => Mage::helper('demac_multilocationinventory')->__('Enabled'),
            ),
                )
        );


        return parent::_prepareColumns();
    }

    /**
     * Define identifier field and options for mass actions.
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('demac_multilocationinventory');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('demac_multilocationinventory')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('demac_multilocationinventory')->__('Are you sure?')
        ));

        $statuses = array(
            1 => Mage::helper('demac_multilocationinventory')->__('Enabled'),
            0 => Mage::helper('demac_multilocationinventory')->__('Disabled')
        );
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('demac_multilocationinventory')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('demac_multilocationinventory')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        return $this;
    }

    /**
     * Get edit URL for clicking on a row.
     *
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * After the collection is loaded call it's afterLoad method on each item.
     *
     * @return Demac_MultiLocationInventory_Block_Adminhtml_Location_Grid
     */
    protected function _afterLoadCollection() {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Check if dealer
     *
     * @return bool
     */
    private function isDealer() {
        $session = Mage::getSingleton('admin/session');
        $user = $session->getUser();

        return Mage::helper('dealeraccount')->isDealerRole($user);
    }

    private function getAddress() {
        $customerId = Mage::helper('dealeraccount')->getCurentCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $addressId = $customer->getDefaultShipping();

        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);
            $address = $address->getData();
        }

        return $address;
    }

}