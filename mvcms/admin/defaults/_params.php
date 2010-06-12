<?PHP if (stristr($_SERVER['PHP_SELF'],'_params.php')){include'_security.php';Header("Location: $redirect");Die();}
  $deduced_urlclient = explode("/",$_SERVER['REQUEST_URI']);// http_host + rqsturi = www + /client/admin/script.php
  $deduced_urlclient = ($deduced_urlclient[1]=='admin'?$deduced_urlclient[0]:$deduced_urlclient[1]);
	$domain = "http://".$_SERVER['HTTP_HOST']."/".($deduced_urlclient==''?'':$deduced_urlclient."/");
	$url_mirror = "/".($deduced_urlclient==''?'':$deduced_urlclient."/");
	$dbhost = "dbhost";
	$dbuser = "dbuser";
	$dbpass = "dbpass";
	$dbname = "dbname";
	$dbtime = "NOW()";
	$cosite = $_SERVER['HTTP_HOST']."/".($deduced_urlclient==''?'':$deduced_urlclient."/");
	$comail = "@mvcms.tld";
	$coinfo = "info".$comail;
	$clientemail = "dev@mvcms.tld";
	$max_sqrt = 2020;
	$max_session_mail_count = "3";
	$now_time = time();
	$getcwd = "";
?>