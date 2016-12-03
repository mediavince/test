<?php #۞ # VISITEURS
if	(stristr($_SERVER["PHP_SELF"],"_mod_communications.php"))	{
	include '_security.php';
	Header("Location: $redirect");Die();
}

if ($admin_viewing === true) {
  $_mod_content = ${$this_is."String"}.' > '.$error_accesspriv;
} else {
  include $getcwd.$up.$urladmin.'communicationsadmin.php';
  $_mod_content = $communications_content;
}
$_mod_communications = ($logged_in===true?$_mod_content:'');
