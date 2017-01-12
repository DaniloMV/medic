<?php
/**
 * Checkout billing block
 *
 * @category Lomedic
 * @package  Lomedic_BuyerCheckout
 */
class Lomedic_BuyerCheckout_Block_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    /**
     * Get customer addresses
     *
     * @return array
     */
    public function getAddresses()
    {
        $addresses = array();

        if ($this->isCustomerLoggedIn()) {
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $addresses[$address->getId()] = $address->format('html');
            }
        }

        return $addresses;
    }
}
