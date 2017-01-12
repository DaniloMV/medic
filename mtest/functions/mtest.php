<?php
/**
* @copyright  Copyright (c) 2010 mwOC, Inc. 
*/
require_once('auth_ip_check.php');
require_once('auth.php');
define('DSmw',DIRECTORY_SEPARATOR);
define('PSmw', PATH_SEPARATOR);
require_once(BPmw . DSmw . 'lib' . DSmw . 'Varien' . DSmw . 'Object.php');
require_once(BPmw . DSmw . 'lib' . DSmw . 'Varien' . DSmw . 'Simplexml' . DSmw . 'Config.php');
require_once(BPmw . DSmw . 'lib' . DSmw . 'Varien' . DSmw . 'Simplexml' . DSmw . 'Element.php');

require_once(BPmw . DSmw . 'app' . DSmw . 'code' . DSmw . 'core' . DSmw . 'Mage' . DSmw . 'Core' . DSmw . 'functions.php');

class mwTest
{
    protected $_codeDir     = '';
    protected $_etcDir      = '';
    protected $_mwDir       = '';
    protected $_designDir   = '';
    protected $_cacheDir    = '';

    protected $_localXml    = null;
    protected $_filePaths   = null;

    protected $_mysqlConnect = null;

    protected $_magentoVersion = null;

    protected $_messages    = null;

    public function __construct() {
        $this->_etcDir      = BPmw . DSmw . 'app' . DSmw . 'etc';
        $this->_codeDir     = BPmw . DSmw . 'app' . DSmw . 'code';
        $this->_mwDir       = $this->_codeDir . DSmw . 'local' . DSmw . 'MageWorx';
        $this->_designDir   = BPmw . DSmw . 'app' . DSmw . 'design';
        $this->_cacheDir    = BPmw . DSmw . 'var' . DSmw . 'cache';

        $this->_filePaths = array();

        $this->_messages = array();

        $this->mysqlConnect();
    }

    public function __destruct() {
        $this->mysqlDisconnect();
    }
    
    public static function run()
    {
        try {
            $testObj = new mwTest();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    protected function checkPostForm() {
        if( isset($_POST['submit']) ) {
            $actionType = $_POST['action'];

            switch($actionType) {
                case 'flush_cache' : $this->flushMagentoCache(); break;
            }
        }
    }

    protected function flushMagentoCache() {
        $this->emptyDir($this->_cacheDir);
        $this->_messages[] = 'Magento cache successfully flushed';
    }


    public function checkRewriteConflicts() {
        $conflicts = $this->_getPossibleConflictsList();

        if(FALSE !== $conflicts) {
            return $this->outConflicts($conflicts);
        }
    }

    /**
     * Retrive possible conflicts list
     *
     * @return array
     */
    protected  function _getPossibleConflictsList()
    {
        $moduleFiles = glob($this->_etcDir . DSmw . 'modules' . DSmw . '*.xml');

        if (!$moduleFiles) {
            return false;
        }
        
        // load file contents
        $unsortedConfig = new Varien_Simplexml_Config();
        $unsortedConfig->loadString('<config/>');
        $fileConfig = new Varien_Simplexml_Config();

        foreach($moduleFiles as $filePath) {
            $fileConfig->loadFile($filePath);
            $unsortedConfig->extend($fileConfig);
        }

        // create sorted config [only active modules]
        $sortedConfig = new Varien_Simplexml_Config();
        $sortedConfig->loadString('<config><modules/></config>');
        
        foreach ($unsortedConfig->getNode('modules')->children() as $moduleName => $moduleNode) {
            if('true' === (string)$moduleNode->active) {
                $sortedConfig->getNode('modules')->appendChild($moduleNode);
            }
        }

        $fileConfig = new Varien_Simplexml_Config();

        $_finalResult = array();

        foreach($sortedConfig->getNode('modules')->children() as $moduleName => $moduleNode) {
            $codePool = (string)$moduleNode->codePool;
            $configPath = $this->_codeDir . DSmw . $codePool . DSmw . uc_words($moduleName, DSmw) . DSmw . 'etc' . DSmw . 'config.xml';

            $fileConfig->loadFile($configPath);

            $rewriteBlocks = array('blocks', 'models', 'helpers');

            foreach($rewriteBlocks as $param) {
                if(!isset($_finalResult[$param])) {
                    $_finalResult[$param] = array();
                }

                if($rewrites = $fileConfig->getXpath('global/' . $param . '/*/rewrite')) {
                    foreach ($rewrites as $rewrite) {
                        $parentElement = $rewrite->xpath('../..');
                        
                        foreach($parentElement[0] as $moduleKey => $moduleItems) {
                            if($moduleItems->rewrite) {
                                $_finalResult[$param] = array_merge_recursive($_finalResult[$param], array($moduleKey => $moduleItems->asArray()));
                            }
                        }
                    }
                }
            }
        }

        foreach(array_keys($_finalResult) as $groupType) {

            foreach(array_keys($_finalResult[$groupType]) as $key) {
                // remove some repeating elements after merging all parents 
                foreach($_finalResult[$groupType][$key]['rewrite'] as $key1 => $value) {
                    if(is_array($value)) {
                        $_finalResult[$groupType][$key]['rewrite'][$key1] = array_unique($value);
                    }
                    
                    // if rewrites count < 2 - no conflicts - remove
                    if( 
                        (gettype($_finalResult[$groupType][$key]['rewrite'][$key1]) == 'array' && count($_finalResult[$groupType][$key]['rewrite'][$key1]) < 2) 
                        ||
                        gettype($_finalResult[$groupType][$key]['rewrite'][$key1]) == 'string'
                    ) {
                        unset($_finalResult[$groupType][$key]['rewrite'][$key1]);
                    }
                } 
                
                // clear empty elements
                if(count($_finalResult[$groupType][$key]['rewrite']) < 1) {
                    unset($_finalResult[$groupType][$key]);
                }
            }
            
            // clear empty elements
            if(count($_finalResult[$groupType]) < 1) {
                unset($_finalResult[$groupType]);
            }

        }

        return $_finalResult;
    }

    protected function getLocalXmlConfig() {
        if(is_null($this->_localXml)) {
            $this->_localXml = new Varien_Simplexml_Config();
            $this->_localXml->loadFile($this->_etcDir . DSmw . 'local.xml');
        }
        return $this->_localXml;
    }
    
    protected function checkDisableLocalModules() {
        $isDisabled = (string)$this->getLocalXmlConfig()->getNode('global/disable_local_modules');

        $this->outDisableLocalModules($isDisabled);
    }

    protected function checkDisableModulesOutput() {
        $query = "SELECT * FROM _TABLE_PREFIX_core_config_data WHERE path LIKE '%advanced/modules_disable_output%' AND value = 1";
        $query = str_replace('_TABLE_PREFIX_', $this->getMysqlTablePrefix(), $query);

        $result = mysql_query($query, $this->mysqlConnect()) or die (mysql_error());

        $data = array();
        if($result && mysql_num_rows($result)) {
            while($row = mysql_fetch_assoc($result)) {
                $t = explode('/', $row['path']);
                $moduleName = $t[2];
                if(stristr($moduleName, 'MageWorx')) {
                    $data[] = $moduleName;
                }
            }
        }
        
        return $this->outDisableModulesOutput($data);
    }
    
    public function removeDebugStoreInfo() {
        $query = "DELETE FROM _TABLE_PREFIX_core_config_data WHERE path LIKE '%dev/debug/template_hints%'";
        $query = str_replace('_TABLE_PREFIX_', $this->getMysqlTablePrefix(), $query);

        $result = mysql_query($query, $this->mysqlConnect()) or die (mysql_error());
        return $result;
    }
    
    public function removeLogStoreInfo() {
        $query = "DELETE FROM _TABLE_PREFIX_core_config_data WHERE path LIKE '%dev/log/%'";
        $query = str_replace('_TABLE_PREFIX_', $this->getMysqlTablePrefix(), $query);

        $result = mysql_query($query, $this->mysqlConnect()) or die (mysql_error());
        return $result;
    }

    protected function mysqlConnect() {
        if(!$this->_mysqlConnect) {
            $dbParams = $this->getLocalXmlConfig()->getNode('global/resources/default_setup/connection');

            $this->_mysqlConnect = mysql_connect(
                (string)$dbParams->host, 
                (string)$dbParams->username, 
                (string)$dbParams->password) or die(mysql_error());
            mysql_select_db((string)$dbParams->dbname);
        }
        return $this->_mysqlConnect;
    }

    protected function mysqlDisconnect() {
        if($this->_mysqlConnect) {
            mysql_close($this->_mysqlConnect);
        }
    }

    protected function mysqlPrepareQuery($query) {
        return str_replace('_TABLE_PREFIX_', $this->getMysqlTablePrefix(), $query);
    }

    protected function getMysqlTablePrefix() {
        return (string)$this->getLocalXmlConfig()->getNode('global/resources/db/table_prefix');
    }

    protected function getMagentoVersion() {
        if(is_null($this->_magentoVersion)) {
            $this->_magentoVersion = '1.3';

            if( FALSE !== strpos(file_get_contents(BPmw . DSmw . 'app' . DSmw . 'Mage.php'), 'getVersionInfo()') ) {
                $this->_magentoVersion = '1.4';
            }
        }

        return $this->_magentoVersion;
    }

    /**
    * Output functions
    */
    protected function outConflicts(&$conflicts) {
        $html = '<table class=table width="100%" border="1">';
        $html .= '<tr><th>Magento Class</th><th>Rewrite Classes</th></tr>';
        foreach($conflicts as $groupType => $modules) {
            $html .= '<tr><td colspan="2"><center><strong>' . ucwords($groupType) . '</strong></center></td></tr>';
            foreach($modules as $moduleName => $moduleRewrites) {
                foreach($moduleRewrites['rewrite'] as $moduleClass => $rewriteClasses) {
                    $html .= '<tr>';
                    $html .= '<td>' . uc_words($moduleName . '_' . $moduleClass) . '</td>';
                    $html .= '<td>' . implode('<br/>', $rewriteClasses) . '</td>';
                    $html .= '</tr>';
                }
            }
        }
        $html .= '</table>';
        
        return $html;
    }

    public function checkModulesVersion() {
        $moduleFiles = glob($this->_etcDir . DSmw . 'modules' . DSmw . '*.xml');
        
        $moduleNames = array();

        foreach ($moduleFiles as $moduleFile)
        {
            if( FALSE !== strpos($moduleFile, 'MageWorx_')) {
                $aModuleFile = explode(DSmw, $moduleFile);
                $moduleName = substr(array_pop($aModuleFile), 0, -4);

                $moduleConfig = new Varien_Simplexml_Config();
                $moduleConfig->loadFile($moduleFile);
                if((string)$moduleConfig->getNode('modules/' . $moduleName . '/active') == 'false') {
                    continue;
                }

                $moduleNames[(string)$moduleConfig->getNode('modules/' . $moduleName . '/extension_name')] = array();

                $adminhtmlPath = $this->_codeDir . DSmw . 'local' . DSmw . uc_words($moduleName, DSmw) . DSmw . 'etc' . DSmw . 'adminhtml.xml';
                $configPath = $this->_codeDir . DSmw . 'local' . DSmw . uc_words($moduleName, DSmw) . DSmw . 'etc' . DSmw . 'config.xml';
                
                if(file_exists($configPath)) {
                    $fileConfig = new Varien_Simplexml_Config();
                    $fileConfig->loadFile($configPath);

                    $version = (string)$fileConfig->getNode('modules/' . $moduleName . '/version');
                    $moduleNames[(string)$moduleConfig->getNode('modules/' . $moduleName . '/extension_name')][] = $version;
                }
                else {
                    $moduleNames[$moduleName][] = 'config.xml not found';
                }
            }
        }

        return $this->outModulesVersion($moduleNames);
    }

    protected function outModulesVersion(&$modules) {
        $html = '';
        if(count($modules)) {
            $html .= $this->outHasmwModules($modules);
        }
        else {
            $html .=$this->outNomwModules();
        }
        return $html;
    }

    protected function outNomwModules() {
        return 'No MageWorx Modules Found';
    }

    protected function outHasmwModules(&$modules) {
        $html = '';
        foreach($modules as $moduleName => $moduleVersions) {
            $html .= '<div>' . $moduleName . " is <b>" . join('', $moduleVersions) .'</b></div>';
        }
        return $html;
    }

    
    public function checkDisableModules() {
        return $this->checkDisableModulesOutput();
    }

    protected function outDisableLocalModules($isDisabled) {
        $html = '<h3>Disable Local Modules</h3>';
        $html .= $isDisabled;
        $html .='<hr/>';
        return $html;
    }

    protected function outDisableModulesOutput($data) {
        $html = '';
        if(count($data)) {
            $html .= '<ul class="list">';
            foreach($data as $moduleName) {
                $html .= '<li>' . $moduleName . '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }
    
    public function getCacheConfig() {
        $cacheData = array();
        foreach (Mage::app()->getCacheInstance()->getTypes() as $type) {
            $cacheData[] = $type->getData();
        }
     
        return $this->outCacheConfig($cacheData);
    }

    
    protected function outCacheConfig($data) {
        $html = '<table class="table">';
        $html .="<tr>";
            $html .= '<th>Cache Type</th>';
            $html .= '<th>Description</th>';
            $html .= '<th>Tags</th>';
            $html .= '<th>Status</th>';
        $html .="</tr>";
        foreach($data as $cacheTag => $cacheData) {
            $html .= "<tr>";
                $html .= '<td>' . $cacheData['cache_type'] . '</td>';
                $html .= '<td>' . $cacheData['description'] . '</td>';
                $html .= '<td>' . $cacheData['tags'] . '</td>';
                $html .= '<td>' . ($cacheData['status'] ? 'enabled' : 'disabled') . '</td>';
            $html .= "</tr>";
        }
        $html .= '</table>';
        return $html;
    }

    protected function outMessages() {
        if(count($this->_messages)) {
            echo '<ul style="border: 1px solid Black; padding-top: 10px; padding-bottom: 10px; background:#00CCCC">';
            foreach($this->_messages as $mKey => $mText) {
                echo '<li><strong>' . $mText . '</strong></li>';
                unset($this->_messages[$mKey]);
            }
            echo '</ul>';
        }
    }

    protected function emptyDir($dirname = null)
    {
        if(!is_null($dirname)) {
            if (is_dir($dirname)) {
                if ($handle = @opendir($dirname)) {
                    while (($file = readdir($handle)) !== false) {
                        if ($file != "." && $file != "..") {
                            $fullpath = $dirname . '/' . $file;
                            if (is_dir($fullpath)) {
                                $this->emptyDir($fullpath);
                                @rmdir($fullpath);
                            }
                            else {
                                @unlink($fullpath);
                            }
                        }
                    }
                    closedir($handle);
                }
            }
        }
    }

}
?>


