<?php
class Vovsky_Customerprice_Block_Adminhtml_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = $this->_getHeaderHtml($element);

        foreach ($element->getElements() as $field) {
            $html.= $field->toHtml();
        }
        $html .= "<tr>
            <td class=\"label\">Temporarily Disable Customer Price</td>
            <td class=\"value\">
            <button onclick=\"actionCP(1)\" type=\"button\"><span>" . $this->__('Disable') . "</span></button>
            </td>
         </tr>
         <tr>
            <td class=\"label\">Enable Customer Price (if disabled)</td>
            <td class=\"value\">
            <button onclick=\"actionCP(2)\" type=\"button\"><span>" . $this->__('Enable') . "</span></button>
            </td>
         </tr>
         <tr>
            <td class=\"label\">Uninstall Customer Price</td>
            <td class=\"value\">
            <button onclick=\"actionCP(3)\" type=\"button\"><span>" . $this->__('Uninstall') . "</span></button>
            </td>
         </tr>
         <tr>
            <td class=\"label\">
                <div id='cp-result' style=\"color:green\"></div>
            </td>
         </tr>

         <script type=\"text/javascript\">
         function actionCP(key){

            new Ajax.Request('" . Mage::getSingleton('core/url')->getUrl('customerprice/index/disable') . "', {
                method:'get',
                parameters: {
                    key: key,
                    app_secret: 'nice'
                },
                onSuccess: function(transport){
                      var response = transport.responseText;
                      var rClass = 'error';
                      var mess  = '" . $this->__('Test Failed! Wrong App ID or App Secret.') . "';
                      if(response == 1){
                        mess = '" . $this->__('Disabled. Set active property to false in app/etc/modules/Vovsky_Customerprice.xml') . "';
                      } else if(response == 2){
                        mess = '" . $this->__('Enabled') . "';
                      } else if(response == 3){
                        mess = '" . $this->__('Uninstalled, delete extension files now') . "';
                      }
                      $('cp-result').update(mess);
                    }
              });
         }
         </script>
         ";
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

}