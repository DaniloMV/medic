<?php 
ini_set('display_errors',1);
require_once('auth_ip_check.php');
require_once('auth.php');
define('BPmw', dirname(dirname(__FILE__)));
require_once 'functions/mtest.php';
require_once 'functions/magento-check.php';
$mageFilename = BPmw.'/app/Mage.php';
if (!file_exists($mageFilename)) {
        return $mageFilename." was not found";
}
require_once $mageFilename;
// Initialize Magento
Mage::app();
if(isset($_GET['action']) && isset($_GET['params'])) {
    if(function_exists($_GET['action'])) {
        $result = $_GET['action']($_GET['cmd']);
        if (!is_array($result)) {
            $return = array('type'=>'success','message'=>$result);
        } else {
            $return = $result;
        }
    }
    else {
        $return = array('type'=>'error','message'=>'Fuction not Loaded');
    }
    echo json_encode($return);
    exit;
}
$testObj = new mwTest();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="body">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Mageworx Dashboard Test Page</title>
    <link rel="stylesheet" type="text/css" href="style_index.css" />
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.loadmask.js"></script>
    <script src="js/notice.js"></script>
    <script language="javascript" type="text/javascript">
        function anichange (objName) {
            if ( $(objName).css('display') == 'none' ) {
              $(objName).animate({height: 'show'}, 400);
            } else {
              $(objName).animate({height: 'hide'}, 200);
            }
          }
        function button_action(elId) {
            var action = $("#"+elId).val();
            $("#body").mask("");
            $.ajax({
              dataType: "json",
              url: 'index.php?action='+action+"&params=1",
               error: function (msg) {
                   $("#body").unmask();
                   // Error occurred in sending request
                   showNotification({
                        message: msg.toSource(),
                        type: "error", // type of notification is error
                        autoClose: false, // auto close to true
                        duration: 5 // display duration
                   });
               },
               success: function (msg) {
                     $("#body").unmask();
                     var message=msg.message;//.replace(/\s/g, '');
                     if(message!='') {
                         showNotification({
                            message: message,
                            type: msg.type, // type of notification is error/success/warning/information,
                            autoClose: true, // auto close to true
                            duration: 2 // message display duration
                         });
                     }
                     else {
                         showNotification({
                            message: "Oops! an error occurred.",
                            type: "error", // type of notification is error
                            autoClose: true, // auto close to true
                            duration: 5 // display duration
                         });
                     }

               }
            });
        }
    </script>
</head>
<body class="workspace">
    <form name="shell" class="logout_form" action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
        <input type="submit" name="logout" class="button" value="Logout">
    </form>
<h1 align="center">Mageworx Dashboard Test Page</h1>
<table border="1" class="main_table">
    <tr class="params actions">
        <td id="actions" valign="top"><h2 align="center">Actions</h2>
            <ul align="center">
                <li><button class="button" id="clear_cache" onclick="button_action(this.id)" value="clear_cache">Clear All Caches</button></li>
                <li><button class="button" id="clear_apc" onclick="button_action(this.id)" value="clear_apc">Clear APC Cache</button></li>
                <li><button class="button" id="disable_cache" onclick="button_action(this.id)" value="disable_cache">Disable Magento Cachses</button></li>
                <li><button class="button" id="create_admin" onclick="button_action(this.id)" value="create_admin">Create Admin</button></li>
                <li><button class="button" id="enable_hints" onclick="button_action(this.id)" value="enable_hints">Enable/Disable Hints</button></li>
                <li><button class="button" id="enable_log" onclick="button_action(this.id)" value="enable_log">Enable/Disable Log</button></li>
            </ul>
           
            <div class="divDropdown">
                <h2 align="center" class="dropdown" onclick="anichange('#divCaches'); return false;">Magento Cache Config</h2>
                <div id="divCaches" style="display: none"><?php echo $testObj->getCacheConfig(); ?></div>
            </div>
            <div class="divDropdown">
                <h2 align="center" class="dropdown" onclick="anichange('#divVersions'); return false;">Modules Version</h2>
                <div id="divVersions" style="display: none"><?php echo $testObj->checkModulesVersion(); ?></div>
            </div>
            <?php if($testObj->checkRewriteConflicts()): ?>
            <div class="divDropdown">
                <h2 align="center" class="dropdown red" onclick="anichange('#divRewrites'); return false;">Rewrite Conflicts</h2>
                <div id="divRewrites" style=""><?php echo $testObj->checkRewriteConflicts(); ?></div>
            </div>
            <?php endif; ?>
            <?php if($testObj->checkDisableModules()): ?>
            <div class="divDropdown">
                <h2 align="center" class="dropdown red" onclick="anichange('#divAdvanced'); return false;">Advanced Disable Extensions</h2>
                <div id="divAdvanced" style=""><?php echo $testObj->checkDisableModules(); ?></div>
            </div>
            <?php endif; ?>
            
        </td>
        <td valign="top" id="params"><h2 align="center">System Variables</h2><?php echo get_params();?></td>
    </tr>
</table>
<ul align="center" class="special_navigation_bottom">
<li><a href="functions/apc.php">apc.php</a></li>
<?php
    $config = Mage::getConfig()->getResourceConnectionConfig("core_setup");
?>
<li><a target="_blank" href="functions/adminer.php?username=<?php echo $config->username; ?>&server=<?php echo $config->host;?>&password=<?php echo $config->password; ?>&db=<?php echo $config->dbname; ?>">adminer.php</a></li>
<li><a target="_blank" href="functions/mtest.php">mtest.php</a></li>
<li><a target="_blank" href="functions/errorlogger.php">errorlogger.php</a></li>
<li><a target="_blank" href="functions/magento-check.php">magento-check.php</a></li>
<li><a target="_blank" href="functions/phpinfo.php">phpinfo.php</a></li>
<li><a target="_blank" href="functions/phpminiadmin.php">phpminiadmin.php</a></li>
<li><a target="_blank" href="functions/phpshell.php">phpshell.php</a></li>
<li><a target="_blank" href="functions/pwhash.php">pwhash.php</a></li>
</ul>
</body>
</html>

<?php
function get_params() {
    $params = array('max_execution_time'=>600,'memory_limit'=>'256M','disable_functions'=>'');
    $html = '<ul class="list">';
    $i=0;
    $style = '';
    $html .= '<li class="param '.$style.'"><div class="title">PHP Version</div><div class="value">'.phpversion().'</div></li>';
    foreach ($params as $param=>$val) {
        $style = '';
        if($value = ini_get($param)) {
            if($param =='memory_limit') {
                $val = str_replace("M","",$val);
                $value = str_replace("M","",$value);
            }
            if($val>$value) {
                $style = 'red';
            }
            $html .= '<li class="param '.$style.'"><div class="title">'.  str_replace('_', ' ', $param).'</div><div class="value">'.$value.'</div></li>';
        }
    }
    // default cache mode
    $cache_mode='opcode';
    $apc = "<font color='white'>Enable</font>";
    $apc_flag = true;
    if(!function_exists('apc_cache_info') || !(@apc_cache_info($cache_mode))) {
            $apc = 'Disable';
            $apc_flag = false;
    }
    if($apc_flag) {
        $style = 'red';
    }
    $html .= '<li class="param '.$style.'"><div class="title">APC Cache</div><div class="value">'.$apc.'</div></li>';
    
    $memcache = "<font color='white'>Enable</font>";
    $memcache_flag = true;
    if(!function_exists('memcached_set')) {
            $memcache = 'Disable';
            $memcache_flag = false;
    }
    if($memcache_flag) {
        $style = 'red';
    }
    $html .= '<li class="param '.$style.'"><div class="title">Memcache</div><div class="value">'.$memcache.'</div></li>';
    
    $exec = "Enable";
    $exec_flag = true;
    if(!function_exists('exec')) {
            $exec = "<font color='white'>Disable</font>";
            $exec_flag = false;
    }
    if(!$exec_flag) {
        $style = 'red';
    }
    $html .= '<li class="param '.$style.'"><div class="title">Exec</div><div class="value">'.$exec.'</div></li>';
    $html .='</ul>';
    $html .= check_magento_requirements();
    return $html;
}

function cmd($params) {
    if($result = system($ff)) {
       print_r($result);
       exit;
    }
    echo "Command '$ff' not found.";
    exit;
}

function enable_hints($params=true) {
    $model = Mage::getModel('core/config');
    $ips = Mage::getStoreConfig('dev/restrict/allow_ips');
    $ips .=",".getClientIp();
    
    $v1 = Mage::getStoreConfig("dev/debug/template_hints",0);
    $v2 = Mage::getStoreConfig("dev/debug/template_hints_blocks",0);
    $testObj = new mwTest();
    $testObj->removeDebugStoreInfo();
   
    if($v1 || $v2) {
        $model->saveConfig('dev/debug/template_hints', "0", 'default', 0);
        $model->saveConfig('dev/debug/template_hints_blocks', "0", 'default', 0);
        $ips = str_replace(",".getClientIp(),"", $ips);
        $model->saveConfig('dev/restrict/allow_ips', $ips, 'default', 0);
        $message = "Magento Hints Disabled";
    } else {
        $model->saveConfig('dev/debug/template_hints', "1", 'default', 0);
        $model->saveConfig('dev/debug/template_hints_blocks', "1", 'default', 0);
        $message = "Magento Hints Enabled";
    }
    $model->saveConfig('dev/debug/template_hints_blocks', "1", 'default', 0);
    return $message;
}

function enable_log($params=true) {
    $model = Mage::getModel('core/config');
    $v1 = Mage::getStoreConfig("dev/log/active",0);
    $testObj = new mwTest();
    $testObj->removeDebugStoreInfo();
   
    if($v1) {
        $model->saveConfig('dev/log/active', "0", 'default', 0);
        $message = "Magento Log Disabled";
    } else {
        $model->saveConfig('dev/log/active', "1", 'default', 0);
        $message = "Magento Log Enabled";
    }
    return $message;
}

function disable_cache($params=true) {
    //load Magento
    
    $model = Mage::getModel('core/cache');
    $options = $model->canUse();
    foreach($options as $option=>$value) {
        $options[$option] = 0;
    }
    $model->saveOptions($options); 
    return "Caches disabled";
}

function clear_cache($params=true) {
    $start = (float) array_sum(explode(' ',microtime()));
    $html= "<br/>****************** CLEARING CACHE ******************<br/>";
    
    Mage::app()->cleanCache();
    $html .= 'Refresh Magento Caches<br/>';
    if (file_exists(BPmw."/var/cache")) {
        $html.= "Clearing var/cache<br/>";
        cleandir(BPmw."/var/cache");
    }

    if (file_exists(BPmw."/var/session")) {
        $html.= "Clearing var/session<br/>";
        cleandir(BPmw."/var/session");
    }

    if (file_exists(BPmw."/var/minifycache")) {
        $html.= "Clearing var/minifycache<br/>";
        cleandir(BPmw."/var/minifycache");
    }

    if (file_exists(BPmw."/downloader/pearlib/cache")) {
        $html.= "Clearing downloader/pearlib/cache<br/>";
        cleandir(BPmw."/downloader/pearlib/cache");
    }

    if (file_exists(BPmw."/downloader/pearlib/download")) {
        $html.= "Clearing downloader/pearlib/download<br/>";
        cleandir(BPmw."/downloader/pearlib/download");
    }

    if (file_exists(BPmw."/downloader/pearlib/pear.ini")) {
        $html.= "Removing downloader/pearlib/pear.ini<br/>";
        unlink (BPmw."/downloader/pearlib/pear.ini");
    }

    $end = (float) array_sum(explode(' ',microtime()));
    $html.= "<br/>------------------- CLEANUP COMPLETED in:". sprintf("%.4f", ($end-$start))." seconds ------------------<br/>";
    return $html;
}

function clear_apc($params=true) {
    $cache_mode='user';
    if(function_exists('apc_clear_cache')) {
    $result = apc_clear_cache($cache_mode);
        if($result) {
            return "APC cache cleared";
        }
        return array('type'=>'error','message'=>'Can\'t clear APC cache');
    }
    return array('type'=>'error','message'=>'APC cache disabled');
    
}

## Function to clean out the contents of specified directory

function cleandir($dirname) {

    if(!is_null($dirname)) {
        if (is_dir($dirname)) {
            if ($handle = @opendir($dirname)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {
                        $fullpath = $dirname . '/' . $file;
                        if (is_dir($fullpath)) {
                            cleandir($fullpath);
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

function create_admin($params=true) {
    define('USERNAME','mageworx2');
    define('EMAIL','mage@worx.com');
    define('PASSWORD','1qqqqqq');
    if(!defined('USERNAME') || !defined('EMAIL') || !defined('PASSWORD')){
            return 'Edit this file and define USERNAME, EMAIL and PASSWORD.';
    }
    
    try {
            //create new user
            $user = Mage::getModel('admin/user')
                    ->setData(array(
                            'username'  => USERNAME,
                            'firstname' => 'MageWorx',
                            'lastname'	=> 'Support',
                            'email'     => EMAIL,
                            'password'  => PASSWORD,
                            'is_active' => 1
                    ))->save();

    } catch (Exception $e) {
            return $e->getMessage();
    }

    try {
            //create new role
            $role = Mage::getModel("admin/roles")
                            ->setName('MageWorx')
                            ->setRoleType('G')
                            ->save();

            //give "all" privileges to role
            Mage::getModel("admin/rules")
                            ->setRoleId($role->getId())
                            ->setResources(array("all"))
                            ->saveRel();

    } catch (Mage_Core_Exception $e) {
        return $e->getMessage();
            
    } catch (Exception $e) {
            return 'Error while saving role.';
    }

    try {
            //assign user to role
            $user->setRoleIds(array($role->getId()))
                    ->setRoleUserId($user->getUserId())
                    ->saveRelations();

    } catch (Exception $e) {
            return $e->getMessage();
    }
    return "Superadmin was created!";
}