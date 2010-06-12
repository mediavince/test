<?PHP
if (stristr($_SERVER['PHP_SELF'],'_mod_contact.php')) {
	include '_security.php';
	Header("Location: $redirect");Die();
}

if ( isset($send) && in_array($send,array($envoyerString,$sauverString)) && isset($_SESSION['antispam_key']) ) {

  if($_SERVER['REQUEST_METHOD'] != "POST"){
     Header("Location: $redirect");Die();
  }
  
	$md5_post_code = md5($_POST['code']);
	
	if ($md5_post_code !== $_SESSION['antispam_key']) {
		$error .= '<ul><li>Code > '.$error_invmiss.'</li></ul>'	;
    session_unregister('antispam_key');
    $md5 = md5(microtime() * mktime());
    $string = substr($md5,0,rand(5,8));
    $_SESSION['antispam_key'] = md5($string);
    $content .= '<label for="code"><b>> Anti-Sp@m </b></label><br /><input class="text" name="code" type="text" /><br /><img src="'.$mainurl.'images/_captcha.php?string='.base64_encode($string).'" class="imgspam" /><br />';
	} else {
    if ($error != '')
    session_unregister('antispam_key');
    $md5 = md5(microtime() * mktime());
    $string = substr($md5,0,rand(5,8));
    $_SESSION['antispam_key'] = md5($string);
    $content .= '<label for="code"><b>> Anti-Sp@m </b></label><br /><input class="text" name="code" type="text" /><br /><img src="'.$mainurl.'images/_captcha.php?string='.base64_encode($string).'" class="imgspam" /><br />';
	}
} else {
  session_unregister('antispam_key');
  $md5 = md5(microtime() * mktime());
  $string = substr($md5,0,rand(5,8));
  $_SESSION['antispam_key'] = md5($string);
  $content .= '<label for="code"><b>> Anti-Sp@m </b></label><br /><input class="text" name="code" type="text" /><br /><img src="'.$mainurl.'images/_captcha.php?string='.base64_encode($string).'" class="imgspam" /><br />';
}

  
?>