<?PHP #۞ # ADMIN

if (!isset($urladmin))
$urladmin = 'admin/';
if (!isset($urlintro))// needs to be a dir
$urlintro = 'blog/';
// File Directory where your protected files and configs shall be uploaded
if (!isset($safedir))
$safedir = "SQL/"	;

$up = "";
if (stristr($_SERVER['REQUEST_URI'],$urladmin) || (stristr($_SERVER['REQUEST_URI'],$urlintro) && is_dir($urlintro))) {
  $up = "../"	;
}

if (@file_exists($up.$safedir.'_security.php')) {
  include $up.$safedir.'_security.php';
} else {
  if (@file_exists('defaults/_security.php'))
  include 'defaults/_security.php';
}

if (!isset($redirect))
$redirect = "http://".$_SERVER['HTTP_HOST']."/";

if (stristr($_SERVER['PHP_SELF'],'_security.php'))
{Header("Location: $redirect");Die();}

?>