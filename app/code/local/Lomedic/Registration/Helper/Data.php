<?php

/**
 * Copyright (c) 2015, Мedic Joint.
 * Developed by Softeq Development Corp. for Lomedic S.A..
 */
class Lomedic_Registration_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Get User role id
     * 
     * @return int
     */
    public function getManagerType() {
        $session = Mage::getSingleton('admin/session');
        if(!$session->getUser()->getRoleId()) {
            $user = $this->getManager($session->getUser()->getUserId());
            $role = $user->getRole();
            $roleId = $role->getRoleId();
            $session->getUser()->setRoleId($roleId);
        }

       return $session->getUser()->getRoleId();
    }

    /**
     * Check if user admin
     * 
     * @return boolean
     */
    public function getHeadAdminType() {
        $session = Mage::getSingleton('admin/session');
        $user = $this->getManager($session->getUser()->getUserId());
        $role = $user->getRole();

        if($role->getData("role_id") == Mage::getStoreConfig('softeq/managers/admin')) {
            return true;
        }
        return false;
    }

    /**
     * Get Manager by id
     * 
     * @param int $managerId
     * @return \Mage_Admin_Model_User
     */
    public function getManager($managerId) {
        $user = Mage::getModel('admin/user')->load($managerId);
        return $user;
   }
   
   /**
    * Get current admin
    * 
    * @return \Mage_Admin_Model_User
    */
   public function getCurrentUser() {
       $session = Mage::getSingleton('admin/session');
       return $session->getUser();
   }


   public function getRecruter($recruterId) {
       return $this->getManager($recruterId);
   }

   /**
    * Get classification list
    * 
    * @return array
    */
    public function getClassificationArr() {

        return array(
            1=> 'I',
            2=> 'II',
            3=> 'III',
            4=> 'IV',
            5=> 'V',
            6=> 'VI'
        );
    }
    
    /**
     * Get all values from zip-state-colonia table sort by params
     * 
     * @param array $selectRestrict
     * @return \Lomedic_Registration_Model_Resource_Zip_Collection
     * 
     */
    public function getZipCollection($selectRestrict=array()) {
        $state          = Mage::app()->getRequest()->getPost('state',false);
        $municipality   = Mage::app()->getRequest()->getPost('municipality',false);
        $colonia        = Mage::app()->getRequest()->getPost('colonia',false);
        $zipcode        = Mage::app()->getRequest()->getPost('zipcode',false);
        $collection = Mage::getModel('loregistration/zip')
                ->getCollection();
        foreach ($selectRestrict as $restrict) {
            $collection->addFieldToSelect($restrict);
            $collection->getSelect()->order($restrict." ASC");
        }
        $collection->getSelect()->distinct(true);
        if($state) {
                $collection->getSelect()->where('state=?',$state);
        }
        if($municipality) {
                $collection->getSelect()->where('municipality=?',$municipality);
        }
        if($colonia) {
                $collection->getSelect()->where('colonia=?',$colonia);
        }
        if($zipcode) {
                $collection->getSelect()->where('zip_code=?',zipcode);
        }

        return $collection;
    }
}
