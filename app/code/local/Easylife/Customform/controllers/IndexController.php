<?php
//Mage::log('HEADERS ALREADY SENT: '.mageDebugBacktrace(true, true, true));
class Easylife_Customform_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){ //this will display the form
        $this->loadLayout();
        $this->_initLayoutMessages('core/session'); //this will allow flash messages
        $this->renderLayout();
    }
    
    public function sendAction(){ //handles the form submit
     global $datos;
    
        if(isset($_FILES['docname']['name']) && $_FILES['docname']['name'] != '')
        {
            try
            {  
                
                $path = Mage::getBaseDir().DS.'customer_documents'.DS;  //desitnation directory     
                
                $fname = $_FILES['docname']['name']; //file name                        
                $uploader = new Varien_File_Uploader('docname'); //load class
                $uploader->setAllowedExtensions(array('xls')); //Allowed extension for file     'doc','pdf','txt','docx','xlsx'
                $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                $uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
                $uploader->setFilesDispersion(false);
                $uploader->save($path,$fname); //save the file on the specified path
                $datos=$this->loadXML($path.$fname);
                
                Mage::register('variable', $datos);
                    
                
                
                
                
//$this->_redirect('customreport', $datos);
                sleep(3);
                
                //var_dump($datos);
                //$this->_redirect('customreport', array('datos', $datos));
                $this->_redirect('form.html');//will redirect to form page
        
        
                
         
            }
            catch (Exception $e)
            {
                Mage::getSingleton('core/session')->addNotice($this->__('No se recibio ningun archivo'));//add success message.
                $this->_redirect('*/*');//will redirect to form page
            }
        }
   }
   

    public function loadXML($path_to_XML)
    {
        
        $include_path = dirname(__FILE__);
        $path_to_PHP_ExcelReader = $include_path . "/Excel/reader.php";
        require_once $path_to_PHP_ExcelReader;
         
        // ExcelFile($filename, $encoding);
        $data = new Spreadsheet_Excel_Reader();
         
        // Set output Encoding.
        $data->setOutputEncoding('utf-8');
        /* if you want you can change 'iconv' to mb_convert_encoding:*/
         
        $data->setUTFEncoder('mb');
         
        /*
        * By default rows & cols indeces start with 1
        * For change initial index use:
        */
        $index = 0;
         
        $data->setRowColOffset($index);
        /* setDefaultFormat - set format for columns with unknown formatting*/
         
        $data->setDefaultFormat('%.2f');
         
        /* setColumnFormat - set format for column (apply only to number fields)*/
        $data->setColumnFormat(4, '%.3f');
        /*Do the actual reading of file*/
         
        $data->read($path_to_XML);
        //return $data;
        
        
        $alldata = $data->sheets[0]['cells'];
        return $alldata;

  
         
        
    }
   
   
public function getCsvData($file){
    $csvObject = new Varien_File_Csv();
    try {
        return $csvObject->getData($file);
    } catch (Exception $e) {
        Mage::log('Csv: ' . $file . ' - getCsvData() error - '. $e->getMessage(), Zend_Log::ERR, 'exception.log', true);
        return false;
    }

}
    
    
}