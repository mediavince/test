<?php #۞ #
/**
 * redirects to root if called directly
 * FIRST LEVEL
 */

if (!isset($urladmin))
$urladmin = 'admin/';
// File Directory where your protected files and configs shall be uploaded
if (!isset($safedir))
$safedir = "SQL/"	;

  $up = "../"	;

if (@file_exists($up.$safedir.'_security.php')) {
  include $up.$safedir.'_security.php';
} else {
  if (@file_exists($up.$urladmin.'defaults/_security.php'))
  include $up.$urladmin.'defaults/_security.php';
}

if (!isset($redirect))
$redirect = "https://".$_SERVER['HTTP_HOST']."/";

Header("Location: $redirect");Die();
