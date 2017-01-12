<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Widget Form File Element Block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Lomedic_Registration_Block_Rewrite_Adminhtml_Customer_Form_Element_File extends Mage_Adminhtml_Block_Customer_Form_Element_File
{
    /**
     * Initialize Form Element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        $this->setType('file');
    }

    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-file');
        if ($this->getRequired()) {
            $this->removeClass('required-entry');
            $this->addClass('required-file');
        }

        $element = sprintf('<div class="mb10">'
                . '<span class="file-wrapper">'
                . '<input id="%s" name="%s" %s /><span class="button">upload document</span>'
                . '<span class="button">View</span><span class="button">Download</span>'
                . '</span>'
                . '</div>'
                . '<div>%s%s',
            $this->getHtmlId(),
            $this->getName(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getAfterElementHtml(),
            $this->_getHiddenInput()
        );

        if($this->getData('name') !== "contract" && Mage::registry('current_customer')){
            return $element.$this->_getPreviewHtml().$this->_getApproveRadioHtml()."</div>";
        }else{
            return $element.$this->_getPreviewHtml().$this->_getDeleteCheckboxHtml()."</div>";
        }
    }

    /**
     * Return Delete File CheckBox HTML
     *
     * @return string
     */
    protected function _getDeleteCheckboxHtml()
    {
        $html = '';
        if ($this->getValue() && !$this->getRequired() && !is_array($this->getValue())) {
            $checkboxId = sprintf('%s_delete', $this->getHtmlId());
            $checkbox   = array(
                'type'  => 'checkbox',
                'name'  => sprintf('%s[delete]', $this->getName()),
                'value' => '1',
                'class' => 'checkbox',
                'id'    => $checkboxId
            );
            $label      = array(
                'for'   => $checkboxId
            );
            if ($this->getDisabled()) {
                $checkbox['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }

            $html .= '<span class="' . $this->_getDeleteCheckboxSpanClass() . '">';
            $html .= $this->_drawElementHtml('input', $checkbox) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false) . $this->_getDeleteCheckboxLabel() . '</label>';
            $html .= '</span>';
        }
        return $html;
    }


    /**
     * Return Delete File CheckBox HTML
     *
     * @return string
     */
    protected function _getApproveRadioHtml()
    {
        $customer = Mage::registry('current_customer');

        $customerFiles = Mage::getModel('loregistration/files')->getCollection();
        $customerFiles->addFieldToFilter('customer_id',$customer->getId());
        $customerFiles->addFieldToFilter('attribute',$this->getData('name'));
        $customerData = $customerFiles->getData();
        if($customerData){
            $customerData = $customerData[0];
        }

        $html = '';
        //if ($this->getValue() && !$this->getRequired() && !is_array($this->getValue())) {
            $radioId = sprintf('%s_approve', $this->getHtmlId()."1");
            $radio   = array(
                'type'  => 'radio',
                'name'  => 'approve['.$this->getData('name').']',
                'value' => '1',
                'sel' =>  $this->getData('name'),
                'class' => 'radio file-approve',
                'id'    => $radioId,
                'approve' => '1',
                'cus' => $customer->getId()
            );
            $label      = array(
                'for'   => $radioId
            );

            if(isset($customerData["approve"]) && $customerData["approve"] == 1){
                $radio["checked"] = "checked";
            }

            if ($this->getDisabled()) {
                $radio['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }

            $html .= '<span class="'.$this->_getApproveSpanClass().'">';
            $html .= $this->_drawElementHtml('input', $radio) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false) . Mage::helper('adminhtml')->__('Approve file').' </label>';
            $html .= '</span>';

            $radioId = sprintf('%s_approve', $this->getHtmlId()."2");
            $radio   = array(
                'type'  => 'radio',
                'name'  => 'approve['.$this->getData('name').']',
                'value' => '0',
                'sel' =>  $this->getData('name'),
                'class' => 'radio file-approve',
                'id'    => $radioId,
                'approve' => '0',
                'cus' => $customer->getId()
            );
            $label      = array(
                'for'   => $radioId
            );

            if(isset($customerData["approve"]) && $customerData["approve"] == 0){
                $radio["checked"] = "checked";
            }

            if ($this->getDisabled()) {
                $radio['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }

            $html .= '<span class="'.$this->_getApproveSpanClass().'">';
            $html .= $this->_drawElementHtml('input', $radio) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false).Mage::helper('adminhtml')->__('Not approve file').'</label>';
            $html .= '</span>';

        $customerFiles = Mage::getModel('loregistration/files')->getCollection();
        $customerFiles->addFieldToFilter('customer_id',$customer->getId());

        $fileComment = "";
        $fileApprove = 2;

        foreach($customerFiles as $collection) {
            $tmpArr = $collection->getData();

            if($tmpArr["attribute"] == $this->getData('name')){
                $fileComment = $tmpArr["comment"];
                $fileApprove = $tmpArr["approve"];
                break;
            }
        }

        $hiden = $fileApprove==1 || $fileApprove==2?'style="display:none;"':"";

        $html .= '<span id="comment_'.$this->getData('name').'" '.$hiden.'><input class="comment-text" type="text" name="'.'comment['.$this->getData('name').']'.'" value="'.$fileComment.'"> - comment</span>';
        //}
        return $html;
    }

    /**
     * Return Approve SPAN Class name
     *
     * @return string
     */
    protected function _getApproveSpanClass()
    {
        return 'approve-file';
    }

    /**
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-file';
    }

    /**
     * Return Delete CheckBox Label
     *
     * @return string
     */
    protected function _getDeleteCheckboxLabel()
    {
        return Mage::helper('adminhtml')->__('Delete File');
    }

    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $image = array(
                'alt'   => Mage::helper('adminhtml')->__('Download'),
                'title' => Mage::helper('adminhtml')->__('Download'),
                'src'   => Mage::getDesign()->getSkinUrl('images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            );
            $url = $this->_getPreviewUrl();
            $html .= '<span class="download-file">';
            $html .= '<a href="' . $url . '">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $html .= '<a href="' . $url . '">' . Mage::helper('adminhtml')->__('Download') . '</a>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Hidden element with current value
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return $this->_drawElementHtml('input', array(
            'type'  => 'hidden',
            'name'  => sprintf('%s[value]', $this->getName()),
            'id'    => sprintf('%s_value', $this->getHtmlId()),
            'value' => $this->getEscapedValue()
        ));
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/customer/viewfile', array(
            'file'      => Mage::helper('core')->urlEncode($this->getValue()),
        ));
    }

    /**
     * Return Element HTML
     *
     * @param string $element
     * @param array $attributes
     * @param boolean $closed
     * @return string
     */
    protected function _drawElementHtml($element, array $attributes, $closed = true)
    {
        $parts = array();
        foreach ($attributes as $k => $v) {
            $parts[] = sprintf('%s="%s"', $k, $v);
        }

        return sprintf('<%s %s%s>', $element, implode(' ', $parts), $closed ? ' /' : '');
    }

    /**
     * Return escaped value
     *
     * @param int $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        if (is_array($this->getValue())) {
            return false;
        }
        $value = $this->getValue();
        if (is_array($value) && is_null($index)) {
            $index = 'value';
        }

        return parent::getEscapedValue($index);
    }
}
