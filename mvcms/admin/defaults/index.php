<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))) {
	include dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'_security.php';
	Header("Location: $redirect");Die();
}
