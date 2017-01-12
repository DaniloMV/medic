<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_BuyerCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get product seller id
     *
     * @param  Mage_Sales_Model_Quote_Item $item
     * @return int Customer id
     */
    public function getCustomerIdFromQuoteItem($item)
    {
        $product = Mage::getModel('marketplace/product')->getCollection()
            ->addFieldToFilter('mageproductid', array('eq' => $item->getProductId()))
            ->getFirstItem();

        return $product->getUserid();
    }

    /**
     * Get seller company name
     *
     * @param  int $customerId Customer id
     * @return void|string Company name
     */
    public function getCompanyName($customerId)
    {
        if ($customer = Mage::getModel('customer/customer')->load($customerId)) {
            return $customer->getCompany();
        }
    }

    /**
     * Get seller id from sales quote address
     *
     * @param  Mage_Sales_Model_Quote_Address $address
     * @return void|string Company name
     */
    public function getCompanyFromAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $addressId = $address->getCustomerAddressId();
        $address   = Mage::getModel('customer/address')->load($addressId);

        if ($address->getId()) {
            return $this->getCompanyName($address->getSeller());
        }
    }
	
	function utf8ToCode($utf8Char) {
	
		$utf8Char = (string) $utf8Char;

		if ("" == $utf8Char) {
			return 0;
		}

		# [a, b, c, d, e, f]
		$bytes = array_map('ord', str_split(substr($utf8Char, 0, 6), 1));
	 
		# a, [b, c, d, e, f]
		$first = array_shift($bytes);
	 
		# 0-------
		if ($first <= 0x7F) {
			return 0;
		}
		# 110----- 10------
		elseif ($first >= 0xC0 && $first <= 0xDF) {
			$tail = 1;
		}
		# 1110---- 10------ 10------
		elseif ($first >= 0xE0 && $first <= 0xEF) {
			$tail = 2;
		}
		# 11110--- 10------ 10------ 10------
		elseif ($first >= 0xF0 && $first <= 0xF7) {
			$tail = 3;
		}
		# 111110-- 10------ 10------ 10------ 10------
		elseif ($first >= 0xF8 && $first <= 0xFB) {
			$tail = 4;
		}
		# 1111110- 10------ 10------ 10------ 10------ 10------
		elseif ($first >= 0xFC && $first <= 0xFD) {
			$tail = 5;
		}
		
		if (count($bytes) < $tail) {
		  return 1; 
		}
	 
		return 0;
	}
	
	public function cutString($string,$len) {
		if(strlen($string)>$len) {
			for($i=0;$i<$len;$i++) {
				$len= $len+$this->utf8ToCode($string[$i]);
			}
			return substr($string,0,$len)."...";
		}
		return $string;
	}
}

