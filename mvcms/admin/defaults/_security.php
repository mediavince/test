<?php #۞ # ADMIN

if (stristr($_SERVER["HTTP_HOST"], "localhost")) {
	$redirect = 'http://localhost/mvcms/';//index.php on IIS only
} else if (stristr($_SERVER["HTTP_HOST"], "mediavince.com")) {
	$redirect = 'https://mvcms.mediavince.com/';
} else {
	$redirect = "https://".$_SERVER["HTTP_HOST"]."/";
}

if (stristr($_SERVER['PHP_SELF'], '_security.php'))
{Header("Location: $redirect");Die();}
