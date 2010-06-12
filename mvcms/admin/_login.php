<?PHP ## ADMIN
if (stristr($_SERVER['PHP_SELF'],'_login.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

$dbtable = $tbladmin;

$form_login = '<label for="adminName"> '.$nomutilString.' </label> <input class="inputtext" name="adminName" type="text" autocomplete="off" /><br /><label for="passWord"> '.$motdepasseString.' </label> <input class="inputtext" name="passWord" type="password" autocomplete="off" /><br /><input name="login" type="submit" value="'.$entrerString.'" /></form>';

$form_error = '<font color="Red"><b> * '.$accesString.' '.$refuseString.' * </b></font><br />';
$get_profil = sql_get($tblcont,"WHERE contlang='$lg' AND conttype='profil' ","conturl,conttitle");
$url_profil = $mainurl.$urladmin."?lg=$lg&amp;x=z&amp;y=2";//&amp;adminId=".sql_getone($tbladmin,"WHERE adminutil='$admin_name' ","admin".(in_array("adminrid",$admin_array_fields)?"r":'')."id")."&amp;adminUtil=$admin_name";
$form_logout = '<input name="login" type="submit" value="'.$sortirString.'" /></form>';// <a href="'.$url_profil.'">'.$get_profil[1].'</a>

$admin_array_fields = sql_fields($tbladmin,'array');

#####################################
if (!isset($login)) {
	if (isset($_COOKIE[$cookie_codename."admin"])) {
		$cookie = $_COOKIE[$cookie_codename."admin"];
		$read_cookie = explode("^",base64_decode($cookie));
		$admin_name = $read_cookie[0];
		$admin_password = $read_cookie[1];
		$admin_priv = $read_cookie[2];
		$admin_priv = explode("|",$admin_priv);
		if (!is_array($admin_priv))	{
      $admin_priv = array($admin_priv);
      $sql_priv = " AND adminpriv='$admin_priv' ";
		} else {
		  $sql_priv = "AND (";
		  foreach($admin_priv as $key)
		    $sql_priv .= " adminpriv LIKE '%$key%' OR";
		  $sql_priv = substr($sql_priv,0,-2).") ";
    }
    $read = sql_get($tbladmin,"WHERE adminutil='$admin_name' ","admin".(in_array("adminrid",$admin_array_fields)?"r":'')."id,adminemail");
    $admin_id = $read[0];
    $admin_email = $read[1];
		if (sql_nrows($dbtable," WHERE adminutil='$admin_name' AND adminpass='$admin_password' $sql_priv AND adminstatut='Y' ") > 0) {// == (in_array("adminrid",$admin_array_fields)?count($array_lang):'1')
	  	$loginform = gen_form($lg).' >> <a href="'."$mainurl$urladmin?lg=$lg&x=z&y=2&send=edit&adminId=$admin_id".'">'.$get_profil[1].'</a> | '.$admin_name.' | '.$form_logout;
	  	$logged_in = true;
      if ((md5($admin_email) == $admin_password) && ((!isset($adminId) || (isset($adminId) && ($adminId != $admin_id))) && ($y != '2') && (!isset($send) || (isset($send) && (($send != 'edit') || ($send != $sauverString)))))) {// && !stristr($_SERVER["PHP_SELF"],$get_profil[0]))
        Header("Location: $mainurl$urladmin?lg=$lg&x=z&y=2&send=edit&adminId=$admin_id");Die();
	  	}
		} else {
			unset($_COOKIE[$cookie_codename."admin"]);
			setcookie($cookie_codename."admin", false);
			$_COOKIE[$cookie_codename."admin"] = "";
			$loginform = gen_form($lg).$form_login;
			$logged_in = false;
		}
###!!! add else if session is started in case cookies don't work !!!###
	} else {
		$loginform = gen_form($lg).$form_login;
		$logged_in = false;
	}
#####################################
} else if ( ($login == $entrerString) && (isset($adminName)) && (isset($passWord)) ) {
	$admin_name = strip_tags(html_encode($adminName));
	$admin_password = strip_tags(html_encode($passWord));
	$admin_password_md5 = md5($admin_password);
	if ( (!$admin_name == '') || (!$admin_password == '')	) {// || !preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $admin_name)
		if (!$connection) {
			$loginform = gen_form($lg).$form_login.$form_error;
			$logged_in = false;
		} else if ($connection) {
			$read = sql_get($dbtable, " WHERE adminutil='$admin_name' AND adminpass='$admin_password_md5' AND adminstatut='Y' ", "adminutil,adminpass,admindate,adminpriv,adminemail,admin".(in_array("adminrid",$admin_array_fields)?"r":'')."id");
			$admin_email = $read[4];
			$admin_id = $read[5];
			$admin_priv = explode("|",$read[3]);
			if (!is_array($admin_priv)) $admin_priv = array($admin_priv);
			$countread = sql_nrows($dbtable, " WHERE adminutil='$admin_name' AND adminpass='$admin_password_md5' AND adminstatut='Y' ");
			if ( (!$read) || ($read[0] == '.') || ($countread == 0) ) {// != (in_array("adminrid",$admin_array_fields)?count($array_lang):'1') //($countread != '1') 
				$loginform = gen_form($lg).$form_login.$form_error	;
				$logged_in = false;
			} else {
			// if connection succeeded get info from DB and compare variables
				if ( !(($admin_name == $read[0]) && ($admin_password_md5 == $read[1])) ) {
					$loginform = gen_form($lg).$form_login.$form_error;
					$logged_in = false;
				} else if ( ($admin_password_md5 == $read[1]) && ($admin_name == $read[0]) ) {
					$info = base64_encode("$admin_name^$admin_password_md5^".implode("|",$admin_priv)."^");
					setcookie($cookie_codename."admin","$info",0);//,$url_mirror.$urladmin);
					$update = sql_update($dbtable,"SET admindate=$dbtime","WHERE adminutil='$admin_name' AND adminpass='$admin_password_md5' ", "admindate");
					$loginform = gen_form($lg).' >> <a href="'."$mainurl$urladmin?lg=$lg&x=z&y=2&send=edit&adminId=$admin_id".'">'.$get_profil[1].'</a> | '.$bienvenueString.' '.$admin_name.' !<br /><sup>'.$dernierString.' '.$accesString.' : "'.$read[2].'"</sup> | '.$form_logout;
					$logged_in = true;
					if ($read[1] == md5($read[4])) {
            $notice = '<div style="background-color: #FA5;width: 90%;border: 1px solid red;padding: 5px;margin: 5px;text-align:center;">'.strtoupper($modifierString.' '.$motdepasseString).' ! >> <a href="'."$mainurl$urladmin?lg=$lg&x=z&y=2&send=edit&adminId=$admin_id".'">'.$get_profil[1].'</a></div>';
          }
					if (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") == $admin_name) {
						$get_accesses = sql_array($tbladmin,"WHERE admindate>'".$read[2]."' AND adminutil!='$admin_name' ","admin".(in_array("adminrid",$admin_array_fields)?"r":'')."id");
						for ($i=0;$i<count($get_accesses);$i++) {
							$get_info = sql_get($tbladmin,"WHERE admin".(in_array("adminrid",$admin_array_fields)?"r":'')."id=".$get_accesses[$i]." ","adminutil,admindate");
							if	($get_info[0] !== '.')	
							$loginform .= $get_accesses[$i].'# '.$get_info[0].' on '.$get_info[1].'<br />'; 
						}
					}
				}
			}
		}
	} else {
		$loginform = gen_form($lg).$form_login.$form_error;
		$logged_in = false;
	}
#####################################
} else if ($login == $sortirString) {
	if (isset($_COOKIE[$cookie_codename."admin"])) {
		unset($_COOKIE[$cookie_codename."admin"]);
		setcookie($cookie_codename."admin", false);
		$_COOKIE[$cookie_codename."admin"] = "";
		$loginform = gen_form($lg).$form_login;
		$logged_in = false;
	} else {
		$loginform = gen_form($lg).$form_login;
		$logged_in = false;
	}
	$_SESSION['mv_notice'] = $sortirString.' '.$effectueString.' ! <a href="'.$mainurl.$urladmin.'" target="_self">'.$entrerString.'</a>';
	Header("Location: $redirect");
	Die();
#####################################
} else {
	Header("Location: $redirect");
	Die();
}
?>