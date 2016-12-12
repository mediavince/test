<?php #۞ # ADMIN

if (stristr($_SERVER["HTTP_HOST"], "localhost")) {
	$redirect = 'http://www.localhost.com/mvcms/';//index.php on IIS only
} else if (stristr($_SERVER["HTTP_HOST"], "mediavince.com")) {
	$redirect = 'http://mvcms.mediavince.com/';
} else {
	$redirect = "http://".$_SERVER["HTTP_HOST"]."/";
}

if (stristr($_SERVER['PHP_SELF'], '_security.php'))
{Header("Location: $redirect");Die();}
