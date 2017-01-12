<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Block_Multishipping_Addresses extends Mage_Checkout_Block_Multishipping_Addresses
{
    /**
     * Get customer addresses. Clean up fake seller addresses.
     *
     * @return array
     */
    public function getAddresses()
    {
        $session   = Mage::getSingleton('core/session');

        $addresses = array();
        $addressId = $session->getData('address_id');

        foreach ($this->getCustomer()->getAddresses() as $address) {
            $addressData  = $address->getData();

            $addressData['firstname'] = $this->getCustomer()->getFirstname();
            $addressData['company']   = $this->getCustomer()->getCompany();
            $address->setData($addressData);

            if ($address->getIsClone()) {
                $address->delete();
            } else {
                $addresses[$address->getId()] = $address->format('html');
            }
        }

        $session->unsetData('address_id');

        return array(
            'data'    => $addresses,
            'checked' => $addressId
        );
    }
}
