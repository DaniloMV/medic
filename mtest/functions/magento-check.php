<?php
require_once('auth_ip_check.php');
require_once('auth.php');
function check_magento_requirements() {
    return extension_check(array( 
	'curl',
	'dom', 
	'gd', 
	'hash',
	'iconv',
	'mcrypt',
	'pcre', 
	'pdo', 
	'pdo_mysql', 
	'simplexml'
));
}


function extension_check($extensions) {
	$fail = '';
	$pass = '';
	
	if(version_compare(phpversion(), '5.2.0', '<')) {
            $fail .= '<li>You need<strong> PHP 5.2.0</strong> (or greater)</li>';
	}
	else {
            $pass .='<li>You have<strong> PHP 5.2.0</strong> (or greater)</li>';
	}

	if(!ini_get('safe_mode')) {
            $pass .='<li>Safe Mode is <strong>off</strong></li>';
            preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $version);

            if(version_compare($version[0], '4.1.20', '<')) {
                    $fail .= '<li>You need<strong> MySQL 4.1.20</strong> (or greater)</li>';
            }
            else {
                    $pass .='<li>You have<strong> MySQL 4.1.20</strong> (or greater)</li>';
            }
	}
	else { $fail .= '<li>Safe Mode is <strong>on</strong></li>';  }

	foreach($extensions as $extension) {
            if(!extension_loaded($extension)) {
                    $fail .= '<li> You are missing the <strong>'.$extension.'</strong> extension</li>';
            }
            else{	$pass .= '<li>You have the <strong>'.$extension.'</strong> extension</li>';
            }
	}
	$html = '<h2 align="center">Magento System Requirements</h2>';
	if($fail) {
            $html .= '<p><strong>Your server does not meet the following requirements in order to install Magento.</strong>';
            $html .= '<ul>'.$fail.'</ul></p>';
            $html .= 'The following requirements were successfully met:';
            $html .= '<ul>'.$pass.'</ul>';
	} else {
            $html .= '<ul>'.$pass.'</ul>';
	}
    return $html;
}
?>