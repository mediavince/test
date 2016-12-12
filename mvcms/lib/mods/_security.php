<?php #۞ # LIB/

if (!isset($urladmin))
	$urladmin = 'admin/';
if (!isset($urlintro))// needs to be a dir
	$urlintro = 'blog/';
// File Directory where your protected files and configs shall be uploaded
if (!isset($safedir))
	$safedir = 'SQL/';

$up = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

if (@file_exists($up.$safedir.'_security.php')) {
  	include $up.$safedir.'_security.php';
} else {
  	if (@file_exists($up.$urladmin.'defaults/_security.php'))
  		include $up.$urladmin.'defaults/_security.php';
}

if (!isset($redirect))
	$redirect = 'http://'.$_SERVER['HTTP_HOST'].'/';

if (stristr($_SERVER['PHP_SELF'], '_security.php'))
{
	Header("Location: $redirect");Die();
}
