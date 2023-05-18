<?php #۞ #

include '_incdb.php';

if (isset($connection) && !(!$connection))
{Header("Location: $redirect");Die();}

//include '_strings.php';
$content = '<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="../lib/main.css" />
<style type="text/css">
.notice{}.box{width:32%;min-width:200px;float:left;padding:2px;}br{height:1px;}
</style>
</head>
<body>
<div style="margin:0 auto;width:90%;height:100%;min-width:650px;text-align:center;">
';

if (!isset($envoyerString)) $envoyerString = 'send';
if (!isset($upldbString)) $upldbString = "SQL";
if (!isset($paramsString)) $paramsString = "params";
if (!isset($enregistrementString)) $enregistrementString = "record";
if (!isset($effectueString)) $effectueString = "done";
if (!isset($error_inv)) $error_inv = "error";

$update_rapport = '';

// http_host + rqsturi = www + /client/admin/script.php
//$deduced_urlclient = explode("/",$_SERVER['REQUEST_URI']);
//$deduced_urlclient = ($deduced_urlclient[1]=='admin'?$deduced_urlclient[0]:$deduced_urlclient[1]);

$deduced_urlclient = str_replace(
	"/".$urladmin.(stristr($_SERVER['REQUEST_URI'],"_install.php")?"_install.php":''),
	"",
	$_SERVER['REQUEST_URI']
);

if (isset($_POST['send']))
$send = $envoyerString;

//if (($_REQUEST['adminName'] == 'admin') 
//&& (md5($_REQUEST['passWord']) == '21232f297a57a5a743894a0e4a801fc3')) {
if (!isset($_POST['send']) 
&& !isset($_POST['config']) 
&& !isset($_POST['params']) 
&& !isset($_POST[$upldbString])
) {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		Die("Unauthorized attempt to access page.");
	}
    $content .= '
<h1>
	1. Select a new SQL file if you wish to add new tables directly to the template<br />
	2. Fill in the information<br />
	3. Click the install button...
</h1>
<form method="POST" action="" enctype="multipart/form-data">
	<input type="hidden" name="'.$upldbString.'" value="'.$upldbString.'" />
	<input type="file" name="sqlupload" /><br />';
    // lang manager
	$content .= '
<br />Select the languages you wish to have available in the system,
 make sure to enable the array_lang accordingly...<br />';
	foreach($array_supported_lg as $keylg => $lgname)
	//	if (@file_exists('../images/tab_inverted_'.$k.'.gif'))
	if (@file_exists($up.'images/'.$keylg.'.gif'))
	$content .= ($keylg==$default_lg?'':' | ')
			.' <strong>'.$lgname.'</strong> <input type="checkbox" name="lang_'.$keylg.'" '
			.($keylg==$default_lg?$inputchecked:'').' /> <img src="'.$up.'images/'
			.$keylg.'.gif" title="'.$lgname.'" alt="'.$lgname
			.'" style="height:24px;width:24px;" /> ';//tab_inverted_'.$k.'.gif" />'
	$content .= '<br /> <br />';
    
	include 'paramsadmin.php';
	include 'configadmin.php';
    
	$content .= '<br /><input type="submit" name="send" value="install" /><br /></form>';
    
} else {
	/*
          foreach($GLOBALS as $k => $v)
          echo "$k => ".(is_array($v)?"<pre>".print_r($v)."</pre>":$v)."<br />";
          echo $_FILES["sql"]["tmp_name"]."<br />";
	*/
//	die(isset($_FILES['sqlupload']['tmp_name'])?$_FILES['sqlupload']['tmp_name']:'no');
  
  
//	if (isset($_POST['params']))
	include 'paramsadmin.php';

	if ($update_rapport == '') {
		if (isset($_POST['config']))
		include 'configadmin.php';
		if ($update_rapport == '') {
			if (@file_exists($up.$safedir.'_params.php'))
				include $getcwd.$up.$safedir.'_params.php';
			else die('params not written');

			$connection = connect();
			if (!$connection) {
			    die('connection failed');
			}
			$db_exists = mysqli_query($connection, "USE $dbname;");

			if ($connection && $db_exists) {
				$content .= "<h1>connected</h1>";
			} else {
				$do = 'mysqladmin '.($dbhost==''?'':"-h$dbhost")
					." -u$dbuser -p$dbpass CREATE $dbname --default-character-set=utf8 ";
        	
	        	$content .= exec($do,$output,$result);
			//	echo "$do<br />".exec($do,$output,$result)."<br />$result = result<br /><br />";
			//	die($content);
	        	if ($result == 0) {
	        		$content .= '<div class="notice">Database creation '
	        				.$class_conjugaison->plural($effectueString,'F','1').'</div>';
				} else {
					$content .= '
<h1 style="text-align:center">NOT connected: the database could not be created !!</h1>'.$do;
					$content .= '<div class="error" style="text-align:center;">';
					if (@unlink($up.$safedir.'_params.php'))
					$content .= 'params deleted<br />';
					if (@unlink($up.$safedir.'_config.php'))
					$content .= 'config deleted<br />';
					Die("$content</div>");
				}
	        }
			//	install default mysql
			//$_SERVER['DOCUMENT_ROOT']."/".$deduced_urlclient.$urladmin.
			$movelocation = 'defaults/_fullinstall.sql';
			$do = 'mysql '.($dbhost==''?'':"-h$dbhost")." -u$dbuser -p$dbpass $dbname < $movelocation";
			$content .= "<br />".exec($do,$output,$result).($result=='0'?
				'<div class="notice">'.$class_conjugaison->plural($effectueString,'F','1').'</div>'
			:
				'<div class="error"> '.$error_inv.' full install<br />'.$do.'</div>');
			//.' copier et envoyer au webmaster ce qui suit :<br />=> '.$result.' == '.$do.'</div>');

			mysqli_close($connection);
			$connection = connect();
			$tbls_exist = mysqli_query($connection, "DESCRIBE _admin;");
			if (!$tbls_exist) {
				$content .=  "error fullinstall : tables not installed<br />";
				if (@unlink($up.$safedir.'_params.php'))
				$content .= 'params deleted<br />';
				if (@unlink($up.$safedir.'_config.php'))
				$content .= 'config deleted<br />';
				Die("$content</div>");
			}
			//	if error then database name needs to be created
			
			if (isset($_POST[$upldbString]) && isset($_FILES)) {
				if (isset($_FILES["sqlupload"]) && ($_FILES["sqlupload"]["name"] != '')) {
					$userfile_name = $_FILES["sqlupload"]["name"];
					$movelocation = $getcwd.$up.$safedir.'uploaded_'.date('YmdHis')."_"
									.$userfile_name."_.sql";
					$content .= $movelocation."<br />";
					$userfile_tmp = $_FILES["sqlupload"]["tmp_name"];
					$content .= (move_uploaded_file($userfile_tmp,$movelocation)?'moved':'not moved');
					$do = 'mysql -s '.($dbhost==''?'':"-h$dbhost")
						." -u$dbuser -p$dbpass $dbname < $movelocation";
					$content .= "<br />".exec($do,$output,$result).($result=='0'?
				'<div class="notice">'.$class_conjugaison->plural($effectueString,'F','1').'</div>'
					:
				'<div class="error">'.$error_inv.' sql upload</div>');
					// copier et envoyer au webmaster ce qui suit :<br />=> '.$result.' == '.$do.'
				}
				$content .= "<br />Langs<br />";
				$chosen_lang = array();
				$sql_updates = array();  
				foreach($array_supported_lg as $keylg) {
					if (isset($_POST['lang_'.$keylg]) && ($_POST['lang_'.$keylg] == 'on')) {
					//	install lang strings in mysql
						$movelocation = 'defaults/_insert_'.$keylg.'.sql';
						//$_SERVER['DOCUMENT_ROOT']."/".$deduced_urlclient.$urladmin.
						$do = 'mysql -s '.($dbhost==''?'':"-h$dbhost")
							." -u$dbuser -p$dbpass $dbname < $movelocation";
						$content .= "<br />".exec($do,$output,$result).($result=='0'?
		'<div class="notice">'.$keylg.' '.$class_conjugaison->plural($effectueString,'F','1').'</div>'
						:
		'<div class="error">'.$keylg.' '.$error_inv.'</div>');
						if ($result == '0') {
							$chosen_lang[] = $keylg;
							$sql_updates[] = array(
								"SET enumstatut='Y' ",
								"WHERE enumwhat='lang' AND enumtype='$keylg' ",
								$keylg);
						} else {
							$sql_updates[] = array(
								"SET enumstatut='N' ",
								"WHERE enumwhat='lang' AND enumtype='$keylg' ",
								$keylg);
						}
					} else {
						$sql_updates[] = array(
							"SET enumstatut='N' ",
							"WHERE enumwhat='lang' AND enumtype='$keylg' ",
							$keylg);
					}
				}
				if (count($chosen_lang)>0) {
					connect();// needs to connect again after using the command line
					foreach($chosen_lang as $keylg) {
						$insert_cont = mysqli_query($connection, "INSERT INTO `_cont` VALUES 
							(NULL,'Y',NOW(),'admin',NOW(),'admin',1,'$keylg','MVCMS',"
							."'<p>The website is ready to be used. Go to the admin directory.</p>',"
							."'mvcms','1','','','center','','')");
						if ($root_writable === true)
						copy('_tpl_.php',$up.'index_'.$keylg.'.php');
					}
					$content .= "<br /><h1>default content in all languages</h1>| ";
					/*
            $sql = "UPDATE $tblenum SET enumstatut='Y' 
            		WHERE enumwhat='lang' AND enumtype IN ('".implode("','",$chosen_lang)."')";
            $update_langs = mysqli_query($connection, $sql);
            $content .= "<br />".$sql;
					*/
					foreach($sql_updates as $s) {
						$content .= '<span style="'.(in_array($s[2],$chosen_lang)?
							'color:green;font-weight:bold;text-decoration:underline;':'color:red;')
							.'">'.$s[2]." > ".sql_updateone($tblenum,$s[0],$s[1],'enumstatut')
							."</span> | ";
					}
				}
				$content .= "<br /> <br />";
				//	write _security.php in SQL (safe dir)
				$Fnm = $getcwd.$up.$safedir.'_security.php';
				$inF = fopen($Fnm,"w+");
				$security = '<'.'?php #۞ # ADMIN
$redirect = \"http:\/\/\".$_SERVER[\"HTTP_HOST\"].\"'.$deduced_urlclient.'\";
if (stristr($_SERVER[\'PHP_SELF\'],\'_security.php\'))
{Header(\"Location: $redirect\");Die();}
?'.'>';
				fwrite($inF,stripslashes($security));
				fclose($inF);
				$content .= '<br /><p style="text-align:center;color:green;font-weight:bold;">'
						.'security: '.$enregistrementString.' '.$effectueString.'</p>';
				//	write .htaccess to root
				$Fnm = $getcwd.$up.'.htaccess';
				$inF = fopen($Fnm,"w+");
				$htaccess = '
#Options +FollowSymLinks

RewriteEngine On

############### localhost/folder
# RewriteBase /mvcms

############### localhost
RewriteBase '.($deduced_urlclient==''||($deduced_urlclient[0]!="/")?"/":'').$deduced_urlclient.'

## ENABLE ONE OR DISABLE TWO OR ALL ACCORDING TO SERVER SETTINGS, PHP5 HAS TO BE INSTALLED TO WORK
# AddHandler php5-script .php
# AddHandler application/x-httpd-php5 .php
AddType x-mapp-php5 .php

############### prevents recursive access to .svn folders,, 
############### replacing "index.php" with "- [F]" will act as forbidden
RewriteRule ^.svn index.php

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/index.php
RewriteCond %{REQUEST_URI} (/|\.php|\.html|\.htm|\.feed|\.pdf|\.raw|/[^.]*)$  [NC]
RewriteRule (.*) index.php

RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]

RewriteCond %{QUERY_STRING} .
RewriteRule index.php(.*) /index.php? [L] 

########## Begin - Rewrite rules to block out some common exploits
## If you experience problems on your site block out the operations listed below
## This attempts to block the most common types of exploit
#
# Block out any script trying to set a mosConfig value through the URL
RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|\%3D) [OR]
# Block out any script trying to base64_encode crap to send via URL
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [OR]
# Block out any script that includes a <script> tag in URL
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
# Block out any script trying to set a PHP GLOBALS variable via URL
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
# Block out any script trying to modify a _REQUEST variable via URL
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
# Send all blocked request to homepage with 403 Forbidden error!
RewriteRule ^(.*)$ index.php [F,L]
#
########## End - Rewrite rules to block out some common exploits
';
				fwrite($inF,stripslashes($htaccess));
				fclose($inF);
				$content .= '<br /><p style="text-align:center;color:green;font-weight:bold;">'
						.'htaccess: '.$enregistrementString.' '.$effectueString.'</p>';
			}
		} else $content .= '<br />config: '.$update_rapport;
	} else $content .= '<br />params: '.$update_rapport;
}
$content .= '</div></body></html>';

echo $content;
