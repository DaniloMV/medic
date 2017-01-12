<?php 
require_once('../auth_ip_check.php');
require_once('../auth.php');
define('BPmw', dirname(dirname(__FILE__)));
if(!isset($_SESSION)) 
    session_start();
$_SESSION['autoupdate'] = 0;
if (isset($_POST['autoupdate']))
    $_SESSION['autoupdate'] = 1;
else
    $_SESSION['autoupdate'] = 0;

if (isset($_POST['log_path'])) {
    $sErrorPath = $_POST['log_path'];
} else {
    $sErrorPath = ini_get('error_log');
}
?>
<html>
<head>
<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("document.getElementById('form_error_log').submit();",timeoutPeriod);
}
//   -->
</script>
</head>
<body onload="<?php if ($sErrorPath && $_SESSION['autoupdate']): ?>JavaScript:timedRefresh(5000);<?php endif; ?>">
<form method="POST" id="form_error_log">
Error log path: <input length type="text" size="50" name="log_path" value="<?php echo $sErrorPath; ?>" />
Autoupdate(5 s): <input name="autoupdate" type="checkbox" value="1" <?php if ($_SESSION['autoupdate']) echo "checked"; ?> />
<input value="START LOG ERRORS" type="submit" />
<div style="margin-left: 100px"><small><?php echo BPmw."/"; ?></small></div>
</form>
<hr />
<?php

$n = ( isset($_REQUEST['n']) == true )? $_REQUEST['n']:5;

$offset = -$n * 1024;
$sErrorPath = BPmw.$_POST['log_path'];
$sErrorPath = str_replace("mtest", "", $sErrorPath);
if ($sErrorPath)
{
    if(file_exists($sErrorPath)) {
        try {
            $rs = fopen($sErrorPath,'r');
        } catch (Exception $e) {
                die('Cannot open file :(');
        }
        if ( $rs === false )
        {
            echo "Cannot open file :(";
            die();
        }
        fseek($rs,$offset,SEEK_END);

        fgets($rs);
        $buffer = '';
        while(!feof($rs))
        {
            $buffer = fgets($rs).$buffer;
        }
        echo "Display last ".abs($offset/1024) ."KB<hr><pre>".$buffer."</pre>";
        fclose($rs);
    } else {
         echo "Cannot open file ".$sErrorPath;
    }
}
?>

</body>
</html>