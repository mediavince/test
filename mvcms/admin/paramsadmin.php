<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
$content .= $admin_menu;

$first_line_old = '<'.'?php if (stristr($_SERVER[\'PHP_SELF\'],\'_params.php\')){include\'_security.php\';Header("Location: $redirect");Die();}';
$first_line = '<'.'?php if (stristr($_SERVER[\'PHP_SELF\'], basename(__FILE__))){include\'_security.php\';Header("Location: $redirect");Die();}'
	.PHP_EOL.PHP_EOL;
$last_line = PHP_EOL.'?'.'>';

$update_rapport = '';

$array_params_form = array(
    array("name"=>"domain",
		"value"=>(isset($deduced_urlclient)&&$deduced_urlclient==""?"https://localhost.lan/mvcms/":'https://'.$_SERVER["HTTP_HOST"].$deduced_urlclient."/"),
		"help"=>"Indicate the url of the domain with sub folder if any (correct it, if the deduced one is wrong)"),
    array("name"=>"url_mirror",
		"value"=>(isset($deduced_urlclient)&&$deduced_urlclient!==""?"$deduced_urlclient/":'/'),
		"help"=>"Deduced automatically: it is the folder with slashes or just a slash whether the site is at the root or not"),
    array("name"=>"dbhost",
		"value"=>"localhost",
		"help"=>"Indicate the url provided by the MySQL admin, in some situation, localhost is valid"),
    array("name"=>"dbname",
		"value"=>"dbname",
		"help"=>"Indicate here the name for the database to use that is set up on the server"),
    array("name"=>"dbuser",
		"value"=>"dbuser",
		"help"=>"Username for the database connection"),
    array("name"=>"dbpass",
		"value"=>"dbpass",
		"help"=>"Password for the database connection"),
    array("name"=>"dbtime",
		"value"=>"NOW()",
		"help"=>"Time for the database dynamic stamp, Default is NOW() but you may also use DATE_ADD(NOW(), INTERVAL 6 HOUR) if the server is located in a 6 hours away timezone"),
    array("name"=>"cosite",
		"value"=>(isset($deduced_urlclient)&&$deduced_urlclient==""?"www.localhost.com/mvcms":$_SERVER["HTTP_HOST"].$deduced_urlclient),
		"help"=>"URL the way you wish to present it to your vistors or members, e.g. url without https:// "),
    array("name"=>"comail",
		"value"=>"@mvcms.tld",
		"help"=>"@ and domain with tld like @mediavince.com "),
    array("name"=>"coinfo",
		"value"=>"info@mvcms.tld",
		"help"=>"Email for general info"),
    array("name"=>"clientemail",
		"value"=>"dev@mvcms.tld",
		"help"=>"Email for the admin of the website that is not published and likely used for trace and troubleshoot purposes"),
    array("name"=>"max_sqrt",
		"value"=>"2020",
		"help"=>"Use with care, update it according to the memory and max size of pictures for the media manager, this represents the maximum square root of the image sides product before the server produces an Internal Server Error upon uploading an image"),
    array("name"=>"max_session_mail_count",
		"value"=>"3",
		"help"=>"Number of time before a form will no longer work after this number of attempts, Default is 3"),
    array("name"=>"now_time",
		"value"=>"time()",
		"help"=>"PHP Time for comparaison in the login procedure, Default is time() but you may also use time()+n seconds if the server is located in another timezone"),
    array("name"=>"getcwd",
		"value"=>"",
		"help"=>"On some server, this might be needed, it is the absolute path of the root of the website, Default however is void")
);

// lists all the texts strings appearing on site
if (!isset($send)) {
  if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
  $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br /> <br />';
//  $content .= '<label for="params">> '.$paramsString.' </label> <textarea name="params" rows="30" cols="30" style="width:98%;height:100%;">'.(file_exists($getcwd.$up.$safedir.'_params.php')?str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.'_params.php',NULL,NULL,strlen($first_line))):str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/_params.php',NULL,NULL,strlen($first_line)))).'</textarea><br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';

  $content .= '<label for="params">> '.$paramsString.' </label><br />'; 

	if (isset($array_params_form)) {
	  	foreach($array_params_form as $apf) {
	  		if (isset($_POST[$apf["name"]]))
	  			${$apf["name"]} = $_POST[$apf["name"]];
	    	if (isset(${$apf["name"]}) && ($apf["name"] == 'now_time')) {
		        $this_value = ceil(${$apf["name"]}-time());
		        ${$apf["name"]} = "time()";
		        if ($this_value <= -360)
		        	${$apf["name"]} .= $this_value;
		        else if ($this_value >= 360)
		        	${$apf["name"]} .= "+".$this_value;
	      	}
	    	$content .= '<div class="box">'.$apf["help"].'<br /><input style="width:90%;min-width:180px;" type="text" name="'.$apf["name"].'" value="'.(isset(${$apf["name"]})?${$apf["name"]}:$apf["value"]).'" /><br /></div>';
	    }
	} else {
    	$content .= '<textarea name="params" rows="30" cols="30" style="width:98%;height:100%;">'.(@file_exists($getcwd.$up.$safedir.'_params.php')?substr(str_replace($last_line,"",@file_get_contents($getcwd.$up.$safedir.'_params.php')),strlen($first_line)):substr(str_replace($last_line,"",@file_get_contents($getcwd.$up.$urladmin.'defaults/_params.php')),strlen($first_line))).'</textarea><br />';
  	}

  	if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
	  	$content .= '<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
  	else
	  	$content .= '<div style="clear:both;"><br /></div>'; 

// update the strings
} else if ($send == $envoyerString) {
    if (!isset($_POST) or isset($_POST["params"])) {
        Header("Location: $redirect");Die();
    } else {
        $params = $_POST["params"];
        $update_rapport = '';
        if (strstr($params,"<"."?") || strstr($params,"?".">"))
            $update_rapport = 'no php tags allowed!';
		if (!isset($params)) $params = "";
		if (isset($array_params_form))
		foreach($array_params_form as $apf)
            if ($apf["name"]=="now_time")
                $params .= "$".$apf["name"].' = '.(stristr(addslashes($_POST[$apf["name"]]),'time()')?'':'time()').addslashes($_POST[$apf["name"]]).";".PHP_EOL;
            else
                $params .= "$".$apf["name"].' = \"'.addslashes($_POST[$apf["name"]])."\";".PHP_EOL;
        $connection = connect();
        if (!$connection) {
            $update_rapport .= 'connection failed: check the credentials';
            unset($send);
        }
	}
	if ($update_rapport == '') {
		if (!is_dir($getcwd.$up.$safedir))
			mkdir($getcwd.$up.$safedir);
	  	$Fnm = $getcwd.$up.$safedir.'_params.php';
		$inF = fopen($Fnm,"w+");
		$params = $first_line.stripslashes($params).$last_line;
		fwrite($inF,$params);
		fclose($inF);
  		$content .= '<br /><p style="text-align: center"><font color="Green"><b>params : '.$enregistrementString.' '.$effectueString.'</b></font></p>';
	}
} else {
	Header("Location: $redirect");Die();
}
