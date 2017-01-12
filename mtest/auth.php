<?php
function logout() {
    /* Empty the session data, except for the 'authenticated' entry which the
     * rest of the code needs to be able to check. */
    $_SESSION = array('authenticated' => false);
    unset($_SESSION['XSS']);
    

    /* Unset the client's cookie, if it has one. */
//    if (isset($_COOKIE[session_name()]))
//        setcookie(session_name(), '', time()-42000, '/');

    /* Destroy the session data on the server.  This prevents the simple
     * replay attach where one uses the back button to re-authenticate using
     * the old POST data since the server wont know the session then.*/
//    session_destroy();
}
 
function stripslashes_deep($value) {
    if (is_array($value))
        return array_map('stripslashes_deep', $value);
    else
        return stripslashes($value);
}

if (get_magic_quotes_gpc())
    $_POST = stripslashes_deep($_POST);
    
/* Initialize some variables we need again and again. */
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$nounce   = isset($_POST['nounce'])   ? $_POST['nounce']   : '';

/* Load the configuration. */

$con = file_get_contents(dirname(dirname(__FILE__)).'/mtest/config.php');
$ini = parse_ini_string($con, true);
if (empty($ini['settings']))
    $ini['settings'] = array();

session_start();

/* Delete the session data if the user requested a logout.  This leaves the
 * session cookie at the user, but this is not important since we
 * authenticates on $_SESSION['authenticated']. */
if (isset($_POST['logout']))
    logout();

/* Attempt authentication. */
if (isset($_SESSION['nounce']) && $nounce == $_SESSION['nounce'] && 
    isset($ini['users'][$username]) && isset($ini['mw_allowed_ips'])) 
{
    if (strchr($ini['users'][$username], ':') === false) 
    {
        // No seperator found, assume this is a password in clear text.
        $_SESSION['authenticated'] = ($ini['users'][$username] == $password);
    }
    else 
    {
        list($fkt, $salt, $hash) = explode(':', $ini['users'][$username]);
        $_SESSION['authenticated'] = ($fkt($salt . $password) == $hash);
    }
}


/* Enforce default non-authenticated state if the above code didn't set it
 * already. */
if (!isset($_SESSION['authenticated']))
    $_SESSION['authenticated'] = false;

if (!$_SESSION['authenticated']) 
    {
    /* Genereate a new nounce every time we preent the login page.  This binds
     * each login to a unique hit on the server and prevents the simple replay
     * attack where one uses the back button in the browser to replay the POST
     * data from a login. */
    $_SESSION['nounce'] = mt_rand();
?>
<link rel="stylesheet" type="text/css" href="style_index.css" />
<form name="shell" action="<?php print($_SERVER['PHP_SELF']) ?>" method="post" class="login_form">
<fieldset>
  <legend>Authentication</legend>
  <?php
  if (!empty($username))
      echo "  <p class=\"error\">Login failed, please try again:</p>\n";
  else
      echo "  <p>Please login:</p>\n";
  ?>

  <label for="username">Username:</label>
  <input name="username" id="username" type="text" value="<?php echo $username
  ?>"><br>
  <label for="password">Password:&nbsp;</label>
  <input name="password" id="password" type="password">
  <p align="center"><input type="submit" class="button" value="Login"></p>
  <input name="nounce" type="hidden" value="<?php echo $_SESSION['nounce']; ?>">
</fieldset>
</form>
<?php die();
} 
?>