<?php if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

 // $deduced_urlclient = explode("/",$_SERVER['REQUEST_URI']);// http_host + rqsturi = www + /client/admin/script.php
 // $deduced_urlclient = ($deduced_urlclient[1]=='admin'?$deduced_urlclient[0]:$deduced_urlclient[1]);
$deduced_urlclient = str_replace("/".$urladmin.(stristr($_SERVER['REQUEST_URI'],"_install.php")?"_install.php":''),"",$_SERVER['REQUEST_URI']);
$domain = "https://".$_SERVER['HTTP_HOST'].($deduced_urlclient==''?'/':$deduced_urlclient."/");
$url_mirror = ($deduced_urlclient==''?'/':$deduced_urlclient."/");
$dbhost = "dbhost";
$dbuser = "dbuser";
$dbpass = "dbpass";
$dbname = "dbname";
$dbtime = "NOW()";
$cosite = $_SERVER['HTTP_HOST'].($deduced_urlclient==''?'/':$deduced_urlclient."/");
$comail = "@mvcms.tld";
$coinfo = "info".$comail;
$clientemail = "dev@mvcms.tld";
$max_sqrt = 2020;
$max_session_mail_count = "3";
$now_time = time();
$getcwd = "";
