<?php if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if ($admin_viewing === true) {
  $_mod_content = ${$this_is."String"}.' > '.$error_accesspriv;
} else {
  if (!isset($tabbing))
  $tabbing = true;
  if (!isset($array_tabs))
  $array_tabs = array("user");//,"membre","jobs","methods","communications");
  if (!isset($default_do))
  $default_do = 'tabcontent0';
  
  $array_tabs_processed = array();
  foreach($array_tabs as $key=>$value)
  $array_tabs_processed[] = "tabcontent$key";
  
  if (!isset($do)) {
    $do = $default_do;
  } else {
  	if (isset($do)&&in_array($do,$array_tabs_processed))//
    $_SESSION['do'] = $do;
  }
  
  if (!isset($this_is) || ($this_is=='profil'))
  $this_is = 'user';
  if (!isset($user_id))
  $user_id = sql_getone($tbluser,"WHERE userutil='$user_name' ","user".(in_array($this_is."rid",$array_fields)?'r':'')."id");
  $this_id = $user_id;
  if (!isset($that_is) && isset($tblmembre))
  $that_is = 'membre';
  $membre_id = $user_id;

  if (in_array("communications",$array_tabs))
  include $getcwd.$up.$urladmin.'communicationsadmin.php';
  
  if (!isset($basic_array))
  $basic_array = array('id','date','statut','lang','rid');
  if (isset($params_array))
  $basic_array = array_unique(array_merge($basic_array,$params_array));
  else $params_array = $basic_array;

  $mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
  $longtext_array = array(); // textarea with tinyMCE: word style UI
  $enumYN_array = array(); // either Y or NO: produces selectable option code 
  $enumtype_array = array(); // int(11), produces selectable code for all possibilities taken from enum and assign string, then allows creation of new type, further options apply like - for deleting the selected item
  $int3_array = array(); // int(3), flag for fetching items from referenced table
  $datetime_array = array(); // datetime, flag for showing calendar
  $array_fields_type = array();//lists all types for a given table

  $dbtable = ${"tbl".$this_is};
  $result = mysqli_query($connection, "SHOW FIELDS FROM $dbtable");
  while($row=mysqli_fetch_array($result)) {
    $array_fields_type[$row['Field']] = $row['Type'];
    if ($row['Type'] == 'mediumtext')
    $mediumtext_array[] = $row['Field'];
    if ($row['Type'] == 'longtext')
    $longtext_array[] = $row['Field'];
    if ($row['Type'] == "enum('N','Y')")
    $enumYN_array[] = $row['Field'];
    if ($row['Type'] == 'int(11) unsigned')
    $enumtype_array[] = $row['Field'];
    if ($row['Type'] == 'int(3) unsigned')
    $int3_array[] = $row['Field'];
    if ($row['Type'] == 'datetime')
    $datetime_array[] = $row['Field'];
  }
  mysqli_free_result($result);
  $array_fields = sql_fields($dbtable,'array');
	$this_id_rid = (in_array( $this_is."rid" , $array_fields ));
  foreach($array_fields as $key) {
    // search only on this
    if (isset($q) && ($q != '')) {
      if (($key != $this_is."id") && ($key != $this_is."date") && ($key != $this_is."statut")) {
        $array_q = explode(" ",$q);
        if (!isset($array_q[1])) $array_q = ($sql_q!=''?" OR ":'').$key." LIKE '%".$q."%' ";
        else $array_q = implode("%' OR ".$key." LIKE '%",$array_q);
        if ($array_q != '') $sql_q .= $array_q;
      }
    }
    // end of search
    $empty_array_fields[] = ($key=='statut'?'Y':'');
    $list_array_fields = isset($list_array_fields)?$list_array_fields.','.$key:$key;
  }
  if (isset($that_is)) {
    $that_dbtable = ${"tbl".$that_is};
    $that_array_fields = sql_fields($that_dbtable,'array');
		$that_id_rid = (in_array( $that_is."rid" , $that_array_fields ));
    foreach($that_array_fields as $key) {
      $that_empty_array_fields[] = ($key=='statut'?'Y':'');
      $that_list_array_fields = isset($that_list_array_fields)?$that_list_array_fields.','.$key:$key;
    }
    $result = mysqli_query($connection, "SHOW FIELDS FROM $that_dbtable");
    while($row=mysqli_fetch_array($result)) {
      $array_fields_type[$row['Field']] = $row['Type'];
      if ($row['Type'] == 'mediumtext')
      $mediumtext_array[] = $row['Field'];
      if ($row['Type'] == 'longtext')
      $longtext_array[] = $row['Field'];
      if ($row['Type'] == "enum('N','Y')")
      $enumYN_array[] = $row['Field'];
      if ($row['Type'] == 'int(11) unsigned')
      $enumtype_array[] = $row['Field'];
      if ($row['Type'] == 'int(3) unsigned')
      $int3_array[] = $row['Field'];
      if ($row['Type'] == 'datetime')
      $datetime_array[] = $row['Field'];
    }
    mysqli_free_result($result);
  }
  if (isset($longtext_array[0]))
  $edit_text = true;
  
  $local_url = $local.substr($local_uri,1).'?'.(isset($q)?'&amp;q='.$q:'');//.(isset($do)&&($do!='')?"&amp;do=$do":'')
  $_mod_content = '';
  
  $max_session_mail_count = 99;
  if (!isset($max_session_mail_count)) $max_session_mail_count = '3';
  
  if (isset($_SESSION['pwdtochange']) && ($_SESSION['pwdtochange'] === true)) {
  	$pwdtochange = true;
    $_SESSION['pwdtochange'] = '';
  }
  if (!isset($pwdtochange))
  $pwdtochange = false;
  if (md5(sql_getone($tbluser,"WHERE userutil='$user_name' ","useremail")) == $pass_word) {
    $pwdtochange = true;
  //  $content = '';
    $_mod_content .= '<div class="error">'.strtoupper($modifierString.' '.$motdepasseString).'</div>';
		$do = 'tabcontent0';// resets to show user tabs
  }
  
  $editthis = sql_get($dbtable,"WHERE ".$this_is.(in_array($this_is."rid",$array_fields)?"lang='$lg' AND ".$this_is.'r':'')."id='$user_id' ",$list_array_fields);
  if (isset($that_is))
  $editthat = sql_get($that_dbtable,"WHERE ".$that_is.(in_array($that_is."rid",$that_array_fields)?"lang='$lg' AND ".$that_is.'r':'')."id='$user_id' ",$that_list_array_fields);
   
  if (isset($send) && ($send == 'delete')) {
    if (isset(${$this_is."Img"}) || isset(${$this_is."Doc"}) ||
       (isset($that_is) && (isset(${$that_is."Img"}) || isset(${$that_is."Doc"})))) {
      ${$this_is."Id"} = $membre_id;
      $old_this = $this_is;
      if (isset($that_is))
      $this_is = $that_is;
      ${$this_is."Id"} = $membre_id;
      $old_editthis = $editthis;
      $editthis = $editthat;
      $old_array_fields = $array_fields;
    	if (isset($that_is))
      $array_fields = $that_array_fields;
      $old_list_array_fields = $list_array_fields;
    	if (isset($that_is))
      $list_array_fields = $that_list_array_fields;
      $old_dbtable = $dbtable;
     	if (isset($that_is))
      $dbtable = $that_dbtable;
     	$error = '';
     	$notice = '';
     	$error_img = '';
     	$notice_img = '';
     	foreach($editthis as $key => $value) {
        if (($key != '0') && 
           (($key == $this_is."doc") || (isset($that_is) && ($key == $that_is."doc")) ||
            ($key == $this_is."img") || (isset($that_is) && ($key == $that_is."img")))
           ) {
            if ($value != "") {
              $deletequery = 0;
              $this_is = substr($key,0,-3);
              if ($old_this!=$this_is)
              ${$this_is."Id"} = ${$old_this."Id"};
              if (substr($key,-3) == "doc")
              $deletequery = sql_del($tblcontdoc,"WHERE contdoc='$value' ");
              if (substr($key,-3) == "img")
              $deletequery = sql_del($tblcontphoto,"WHERE contphotoimg='$value' ");
          	//	$deletequery = sql_del($tblcontphoto, " WHERE contphotoid='$contphotoId' ");
          		if ($deletequery > 0) {
                $error_img .= $error_img_request.': '.($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$effaceString;//.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
          		} else {
          		  $value = explode("|",$value);
                if (!isset($value[1]) || (count($value)!=$nof))
                  for($inof=0;$inof<$nof;$inof++)
                  $value[] = (isset($value[$inof])?$value[$inof]:'');
                $newvalue_imgdoc = '';
          		  for($ii=0;$ii<$nof;$ii++) {
          		    if (isset(${$this_is."Img"}) || isset(${$this_is."Doc"}) || (isset($that_is) && (isset(${$that_is."Img"}) || isset(${$that_is."Doc"})))) {
              		  if ((isset(${$this_is."Img"}) && (${$this_is."Img"} == $value[$ii])) || (isset(${$this_is."Doc"}) && (${$this_is."Doc"} == $value[$ii])) || (isset($that_is) && ((isset(${$that_is."Img"}) && (${$that_is."Img"} == $value[$ii])) || (isset(${$that_is."Doc"})) && (${$that_is."Img"} == $value[$ii])))) {
              		    if (delete_imgdoc($this_is,$value[$ii]) === true) {
              		      $notice_img .= ($key==$this_is."img"?$photoString:$documentString).' '.$effaceString.'!<br />';
              		      $newvalue_imgdoc .= ($ii>0?"|":'');
              		    } else {
              		      $error_img .= ($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$effaceString.'!<br />';
              		      $newvalue_imgdoc .= ($ii>0?"|":'').$value[$ii];
              		    }
                		} else {
              		    $newvalue_imgdoc .= ($ii>0?"|":'').$value[$ii];
                //		echo $value[$ii].' = bump<br/>';
                		  //Header("Location: $redirect");Die();
                    }
                  } else {
              		  if (delete_imgdoc($this_is,$value[$ii]) === true) {
              		    $notice_img .= ($key==$this_is."img"?$photoString:$documentString).' '.$effaceString.'!<br />';
              		    $newvalue_imgdoc .= ($ii>0?"|":'');
              		  } else {
              		    $error_img .= ($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$effaceString.'!<br />';
              		    $newvalue_imgdoc .= ($ii>0?"|":'').$value[$ii];
              		  }
                  }
                }
                $updatequery = sql_update(${"tbl".$this_is},"SET $key='$newvalue_imgdoc' ","WHERE ".$this_is.(in_array($this_is."rid",$array_fields)?'r':'')."id='".${$this_is."Id"}."' ","$key");
            		if ($updatequery[0]!='.') {
              	} else {
              		$error_img .= $error_img_request.': '.($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$modifieString.' [u]';//.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
                }
              }
            }
          }
        }
        $_SESSION['mv_error'] = $error.$error_img;
        $_SESSION['mv_notice'] = $notice.$notice_img;
        Header("Location: ".html_entity_decode($local_url));
        Die();
      }
    }
  
  #####################################
  if ( isset($send) && (($send == $sauverString) || ($send == $envoyerString)) && isset($_SESSION['mail_count']) && isset($_SESSION['profil_key']) ) {// ($send == 'delete') || ($send == $vousinscrireString) ||
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
      //echo("Unauthorized attempt to access page.");
      Header("Location: $redirect");
      Die();
    }
  	$error_report = "";
    if (($tabbing === false) || (($tabbing === true) && ($array_tabs[substr($do,-1)]=="user"))) {// && ($do == 'tabcontent0')
      $email = strtolower($_POST["email"]);
      contains_bad_str($email);
      contains_newlines($email);
      if (($email !== '') && is_email($email)) $email = strip_tags(html_encode($email)) ;
    	else $error_report .= $emailString.' > '.$error_invmiss.'<br />' ;
      $pass = strip_tags(html_encode($_POST["pass"]));
    	$md5_post_code = md5($_POST["code"]);
    	if	((($md5_post_code !== $_SESSION['profil_key']) || ($_SESSION['mail_count'] >= $max_session_mail_count)) && function_exists('imagecreatefromjpeg'))
      $error_report .= ucfirst($codeString).' anti-spam > '.$error_invmiss.'<br />';
    }
    if (($tabbing === false) || (($tabbing === true) && ($array_tabs[substr($do,-1)]=="membre"))) {// && ($do == 'tabcontent1')
      ${$this_is."Id"} = $membre_id;
      $old_this = $this_is;
      $this_is = $that_is;
      ${$this_is."Id"} = $membre_id;
      $old_editthis = $editthis;
      $editthis = $editthat;
      $old_array_fields = $array_fields;
      $array_fields = $that_array_fields;
      $old_list_array_fields = $list_array_fields;
      $list_array_fields = $that_list_array_fields;
      $old_dbtable = $dbtable;
      $dbtable = $that_dbtable;
      $array_mandatory_fields = array($this_is."gendre",$this_is."prenom",$this_is."nom");
      include $getcwd.$up.$urladmin.'function-process_post.php';
      if ($tabbing === false) {
        $this_is = $old_this;
        $editthis = $old_editthis;
        $array_fields = $old_array_fields;
        $list_array_fields = $old_list_array_fields;
        $dbtable = $old_dbtable;
      }
    }
    /*
    if (($tabbing === false) || (($tabbing === true) && ($do == 'tabcontent4'))) {
      include $getcwd.$up.$urladmin.'communicationsadmin.php';
    }
    */
  	if ($error_report == '') {
      if ($send == $sauverString) {
        if (($tabbing === false) || (($tabbing === true) && ($array_tabs[substr($do,-1)]=="membre"))) {// && ($do == 'tabcontent1')
          $membre_id = $user_id;
  		    $whereq = "WHERE membre".(in_array($that_is."rid",$that_array_fields)?"lang='$lg' AND membrer":'')."id='$membre_id' ";
          $updateq[0] = '';
  			//	$updateq = sql_update($tblmembre,"SET membreid='$membre_id' $setq ",$whereq,"membrestatut");
  			//	$notice .= $tblmembre.",SET membreid='$membre_id' $setq ,".$whereq;
  			} else {
          $updateq[0] = '';
  		  }
  			if ($updateq[0] == '.') {
  				$error .= '<font color="Red"><b>'.$erreurString.'!</b></font><br /><a href="javascript:history.back()//">'.$retourString.'</a>';
  			} else {
  				if (($tabbing === false) || (($tabbing === true) && ($array_tabs[substr($do,-1)]=="user"))) {// && ($do == 'tabcontent0')
            $_SESSION['mail_count']++;
    		    $whereq = "WHERE userutil='$user_name' ";
  				  $setq = "SET useremail='$email' ";
  				  if ($pass !== "") $setq .= ", userpass='".md5($pass)."' ";
  				  $update_user = sql_update($tbluser,$setq,$whereq,"userutil");
  				  if ($update_user[0] == '.') {
              $error .= '<font color="Red">'.$erreurString.'</font><br />'.$emailString.', '.$motdepasseString.': '.$modificationString.' '.$nonString.' '.$class_conjugaison->plural($effectueString,'F','1').' !<br /> <br /><a href="'.$mainurl.'?lg='.$lg.'&amp;x='.sql_getone($tblcont,"WHERE contlang='$lg' AND conturl='contact' ","contpg").'">'.sql_getone($tblcont,"WHERE contlang='$lg' AND conturl='contact' ","conttitle").'</a> !<br />';
            } else {
            //  if  {
            //  }
  							if (($pass == "") && ($pwdtochange === true)) {
  	              $_mod_content = '<div class="error">'.ucfirst($modifierString).' '.$motdepasseString.' '.$pouraccesprivString.' !!</div>';
  							} else {
                  $_SESSION['pwdtochange'] = true;
  	              $loginform = "";
  	              $notice = $identifierencoreString.'<div style="width:40%;text-align:right;">'.gen_form($lg,$x).$form_login.'</div>';
                  $_SESSION['mail_count'] = 0;
                  $_SESSION['mv_notice'] = $notice;
  	              user_logout();
  	            }
  				    $notice .= '<font color="Green"><b>'.$emailString.', '.$motdepasseString.': '.$modificationString.' '.$class_conjugaison->plural($effectueString,'F','1').'!</b></font>';
            }
            $_SESSION['mv_error'] = $error;
            $_SESSION['mv_notice'] = $notice;
          //  Header("Location: ".html_entity_decode($local_url));Die();
          } else
  				$notice .= '<font color="Green"><b>'.$modificationString.' '.$class_conjugaison->plural($effectueString,'F','1').'!</b></font>';
  			}
  		} else {
  			$error .= '<font color="Red">'.$erreurString.'</font>, '.$modificationString.' '.$nonString.' '.$class_conjugaison->plural($effectueString,'F','1').' !<br /> <br /><a href="'.$mainurl.'?lg='.$lg.'&x='.sql_getone($tblcont,"WHERE contlang='$lg' AND conturl='contact' ","contpg").'">'.sql_getone($tblcont,"WHERE contlang='$lg' AND conturl='contact' ","conttitle").'</a> !<br />';
  		}
        $_SESSION['mail_count'] = 0;
        $_SESSION['mv_notice'] = $notice;
      //  Header("Location: ".html_entity_decode($local_url));Die();
  	} else {
        $_SESSION['mv_error'] = $error_report;
        if ($tabbing === true)
        Header("Location: ".html_entity_decode($local_url));Die();
  	}
  }
  ###############################################################################
  //  $edit_text = false; // check compatibility with tinymce
  	if ($tabbing === true) {
      $stylesheet .= '<link rel="stylesheet" href="'.$mainurl.'lib/tabbed.css" type="text/css" />
    	<script type="text/javascript" src="'.$mainurl.'lib/tabcontent.js"></script>';
    	/* calls niftyCorners.css from niftycube.js in docroot... not needed and does not exist
    	<script src="'.$mainurl.'lib/niftycube.js" type="text/javascript">
      <script type="text/javascript"><!-- //
        window.onload=function(){
          Nifty("div#tabsidebar_ext,div#tabsidebar_int","same-height, none");
          Nifty("div#tabsidebar_search h2","top, transparent");
          Nifty("div#tabsidebar_ext h2,div#tabsidebar_int h2","top, transparent");
        }
      // --></script>';
      */
      $_mod_content .= '<div id="tabsidebars"><div id="tabsidebar_search">';
      $do = (isset($_SESSION['do'])&&($_SESSION['do']!='')?$_SESSION['do']:$default_do);
    	$_mod_content .= '<ul id="profilmaintab" class="tabshade">';
    	foreach($array_tabs as $key=>$value)
    	$_mod_content .= '<li'.($do=='tabcontent'.$key?' class="selected"':'').'><a href="#" rel="tcontent'.$key.'">'.${$value."String"}.'</a></li>';
    	/*
    	$_mod_content .= '<li'.($do=='tabcontent0'?' class="selected"':'').'><a href="#" rel="tcontent0">'.$userString.'</a></li>
    				<li'.($do=='tabcontent1'?' class="selected"':'').'><a href="#" rel="tcontent1">'.sql_getone($tblcont,"WHERE contlang='$lg' AND conttype='profil' ","conttitle").'</a></li>
    				<li'.($do=='tabcontent2'?' class="selected"':'').'><a href="#" rel="tcontent2">'.$jobsString.'</a></li>
    				<li'.($do=='tabcontent3'?' class="selected"':'').'><a href="#" rel="tcontent3">'.$methodsString.'</a></li>';
          	if (in_array("communications",$array_tabs))
    	$_mod_content .= '
    				<li'.($do=='tabcontent4'?' class="selected"':'').'><a href="#" rel="tcontent4">'.$communicationsString.'</a></li>';
    	*/
    	$_mod_content .= '</ul>';
    	$_SESSION['do'] = $default_do;//void do session for other tab choices
    	$_mod_content .= '<div class="profiltabcontentstyle">';
    	$_mod_content .= '<div id="tcontent'.array_search("user",$array_tabs).'" class="profiltabcontent">';
      $_mod_content .= gen_form($lg,$x);
    } else
    $_mod_content .= gen_form($lg,$x);
  	
    $send = 'edit'; //set to enable tinyMCE
    $md5 = md5(microtime(1) * mktime(0,0,0,0,0,0));
    $string = substr($md5,0,rand(5,8));
    $_SESSION['profil_key'] = md5($string);
    if (!isset($_SESSION['mail_count'])) $_SESSION['mail_count'] = 0;
    if (isset($that_is))
  	if ($logged_in === true) {
  		$edit_profil = sql_get($tblmembre,"WHERE membre".(in_array($that_is."rid",$that_array_fields)?"lang='$lg' AND membrer":'')."id='$membre_id' AND membrelang='$lg' ",$that_list_array_fields);
  	} else {
  		$edit_profil = $that_empty_array_fields;
  	}
  	$_mod_content .= gen_form($lg,$x);
  	
  	if ($logged_in === true) {
  	  $_mod_content .= $modificationString.' '.$pourString.' '.$user_name.' ('.$privilegeString.': ';
      foreach($user_priv as $key)
      $_mod_content .= sql_stringit('privilege',$key)." ";
      $_mod_content .= ')<br /> <br /><div style="text-align:right;width:550px;margin:0 auto;"><div style="text-align:right;">';
      if ($pwdtochange === true) {
        $_mod_content .= '<br /><label for="pass"><b>> '.ucfirst($nouveauString.' '.$motdepasseString).' ('.$obligatoireString.')</b></label> <input name="pass" type="password" value="" /><br />';
    	} else {
        $_mod_content .= '<br /><label for="pass"><b>> '.ucfirst($motdepasseString).' ('.$laisservidepourgarderString.')</b></label> <input name="pass" type="password" value="" /><br />';
    	}
      $_mod_content .= '<div class="anti-spam"><label for="code"><b>> '.$recopiercodeString.'</b> </label><br /><img src="'.$mainurl.'images/_captcha.php?string='.base64_encode($string).'" style="padding: 1px 16px 0px 16px;" /> <input name="code" type="text" /></div><br />';
      $_mod_content .= '<label for="email"><b>> '.ucfirst($emailString).'</b> </label><input name="email" value="'.sql_getone($tbluser,"WHERE userutil='$user_name' ","useremail").'" /><br />';
    }
    
  	$_mod_content .= '</div></div>';
  	
  	if ($tabbing === true) {
    	$_mod_content .= '<input name="send" type="submit" value="'.($logged_in===true?$sauverString:$vousinscrireString).'" /><input type="hidden" name="do" value="tabcontent'.array_search("user",$array_tabs).'" /></form>';
    	$_mod_content .= '</div>';
    	if (isset($that_is))
    	$_mod_content .= '<div id="tcontent'.array_search("membre",$array_tabs).'" class="profiltabcontent">'.gen_form($lg,$x);
  	} else
    $_mod_content .= '<hr />';
  	
  	$_mod_content .= '<div style="margin:0 auto;width:550px;text-align:left;">';
    $show_mod_content = '';
    if (isset($that_is) && in_array($that_is,$array_tabs)) {
    	for($i=1;$i<count($that_array_fields);$i++) {
        $key = substr($that_array_fields[$i],strlen($that_is));
        $value = (isset(${$that_is.ucfirst($key)})?${$that_is.ucfirst($key)}:$edit_profil[$i]);
        if (in_array($key,$basic_array)) {
          if ($key != 'id')
          ${"show_".$key} = '<input name="'.$that_is.ucfirst($key).'" type="hidden" value="'.$value.'" />';
        } else if ($key == 'gendre')
      	${"show_".$key} = (in_array($that_is.'nom',$that_array_fields)?'<div class="clear">&nbsp;</div><div style="float:left;width:50%;">':'<div class="clear">&nbsp;</div>').'<label for="gendre">'.ucfirst(${$key."String"}).'</label> <select name="'.$that_is.ucfirst($key).'">'.gen_selectoption($tblenum,$value,'','gendre').'</select><br />';
      	else if (($key == 'img') || ($key == 'doc')) {
          $value = $edit_profil[$i];
          for($ii=0;$ii<$nof;$ii++) {
            ${"show_".$key} = ($key=='img'?($value!=''?'<div style="float:right;"><img src="'.$mainurl.(is_int($value)?sql_getone($tblcontphoto,"WHERE contphotoid='".$value."' AND contphotolang='$lg' ","contphotoimg"):$value).'?'.$now_time.'" border="0" title="'.$photoString.'" alt="'.$photoString.'" /></div>':''):'').'<div class="clear">&nbsp;</div><label for="'.$that_is.ucfirst($key).'[]">> '.${$key."String"}.'</label> <!--<input type="hidden" name="'.$that_is."Desc".'[]" value="'.strtoupper(${$that_is."String"}).'-'.space2underscore((isset($old_that)?(in_array($that_is.'nom',$that_array_fields)?$edit_profil[4]." ".$edit_profil[5]:$edit_profil[3]):$edit_profil[3])).'" />--><input type="file" name="'.$that_is.ucfirst($key).'[]" />'.($value!=''?'<br /><a href="'.$local_url.'&amp;send=delete&amp;'.$that_is.ucfirst($key).'='.$value.'&amp;do=tabcontent2" onclick="return confirm(\''.$confirmationeffacementString.'\');" title="'.$effacerString.'" alt="'.$effacerString.'"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" border="0" title="'.$effacerString.'" alt="'.$effacerString.'" /> '.$value.'</a>':'').'<br />'.($key=='img'&&$value!=''?'':'');//&amp;'.$that_is.'Id='.$edit_profil[0]$membre_id.'
          }
        } else if (isset(${"tbl".$key}) && !in_array($key,$basic_array)) {
          if (in_array("$that_is$key",$int3_array)) {
            ${"tab_".$key} = '<div style="overflow:hidden;"><a href="'.($full_url===true?$mainurl.lgx2readable($lg,'',$key,'?send=new'):$local_url.'&amp;'.$key.'Id=new');
            /*
            if (in_array($key,$editable_by_membre)) {
              $user_id = sql_getone($tbluser,"WHERE userutil='$user_name' ","userid");
              ${"tab_".$key} .= "&amp;membreId=$user_id&amp;instituteId=".sql_getone($tblmembre,"WHERE membreid='$user_id' ","membreinstitute");
            }
            */
            $this_count_of_Y_items = sql_nrows(${"tbl".$key},"WHERE $key".$that_is."='".$membre_id."' AND ".$key."statut='Y' ".(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?" AND ".$key."lang='$lg' ":''));
            $this_count_of_N_items = sql_nrows(${"tbl".$key},"WHERE $key".$that_is."='".$membre_id."' AND ".$key."statut='N' ".(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?" AND ".$key."lang='$lg' ":''));
            ${"tab_".$key} .= '"><img src="'.$mainurl.'images/folder_add_f2.png" align="left" border="0" title="'.$ajoutString.' '.$key.'" alt="'.$ajoutString.' '.$key.'"/> &nbsp; '.$ajoutString.' '.$key.'</a> | <a href="'.($full_url===true?$mainurl.lgx2readable($lg,'',$key):$local_url).'">'.($this_count_of_Y_items>0?$this_count_of_Y_items:'').' '.$class_conjugaison->plural(${$key."String"},'',$this_count_of_Y_items).'</a>'.($this_count_of_N_items>0?" ($this_count_of_N_items $enattenteString)":'').'<hr /><br />';
            foreach(sql_array(${"tbl".$key},"WHERE $key".$that_is."='".$membre_id."' AND ".$key."statut='Y' ".(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?" AND ".$key."lang='$lg' ":'')." ORDER BY ".$key."date ASC ",$key.(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?'r':'')."id") as $keytab) {
              $get_tab = sql_get(${"tbl".$key},"WHERE ".$key.(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?"lang='$lg' AND ".$key.'r':'')."id='$keytab' ","*");
              if ($get_tab[0]!='.') {
                ${"tab_".$key} .= '<div style="border:1px solid gray;float:left;padding:5px;width:47%;margin-left:5px;"><h3>'.(isset($get_tab[$key."type"])?'<b><i>'.strtoupper(sql_stringit($key.'type',$get_tab[$key."type"])).'</i></b>: ':'').'<a href="'.lgx2readable($lg,$x,$key,$keytab).'">'.$get_tab[$key."title"].'</a></h3>';
                if (isset($get_tab[$key."doc"]) && ($get_tab[$key."doc"]!='')) {
                  $ext = explode(".",strrev($get_tab[$key."doc"]),2);
                  $ext = strtolower(strrev($ext[0]));
                  if (in_array($ext,$array_doc_ext))
                  ${"tab_".$key} .= '<a href="'.$mainurl.(stristr($get_tab[$key."doc"],$safedir)?$urlsafe.'?file='.base64_encode($get_tab[$key."doc"]):$get_tab[$key."doc"]).'" target="_new"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" align="left" border="0" title="'.$get_tab[$key."title"].'" alt="'.$get_tab[$key."title"].'" /></a>';// '.str_replace($filedir,"",substr($get_tab[$key."doc"],0,-(strlen($ext)+1)))."</a>";//'.substr($get_tab[$key."entry"],0,30).'
                }
                ${"tab_".$key} .= '<div style="float:none;">('.$enregistreString.' : '.human_date($get_tab[$key."date"]).') | <a href="'.$local_url.'&amp;send=edit&amp;'.$key.'Id='.$get_tab[$key.(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?'r':'')."id"].'">'.$modifierString.'</a> | <a href="'.$local_url.'&amp;send=delete&amp;'.$key.'Id='.$get_tab[$key.(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?'r':'')."id"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');" title="'.$effacerString.'" alt="'.$effacerString.'"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" border="0" title="'.$effacerString.'" alt="'.$effacerString.'" /></a></div><br /></div>';
              }
            }
            ${"tab_".$key} .= '</div><hr /><br />';
            ${"show_".$key} = '';// no show
          } else {
            if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
            include $getcwd.$up.$safedir.'_extra_routines.php';
            if (!isset(${"show_".$key}))
            ${"show_".$key} = '<div class="clear">&nbsp;</div><label for="'.$that_is.ucfirst($key).'">> '.${$key."String"}.'</label> <!--<br />--><select name="'.$that_is.ucfirst($key).'">'.gen_selectoption(${"tbl".$key},$value,(sql_getone(${"tbl".$key},"WHERE ".$key.(in_array($key."rid",sql_fields(${"tbl$key"},'array'))?"lang='$lg' AND ".$key.'r':'')."id='$value' ",$key."statut")=='Y'?'':" OR ".$key."statut='N' "),$key).'</select><br />';
          }
      	} else {
          if (in_array("$that_is$key",$longtext_array)) {
            ${"show_".$key} = '<div class="clear">&nbsp;</div><label for="'.$that_is.ucfirst($key).'">> '.${$key."String"}.'</label><br />'.($tinyMCE === false?$text_style_js:'').'<br /><textarea id="elm'.($i_elm>=-1?$i_elm+=1:'').'" name="'.$that_is.ucfirst($key).'" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.format_edit($value,'edit').'</textarea><br />';
          } else if (in_array("$that_is$key",$enumYN_array)) {
            ${"show_".$key} = '';//'<label for="'.$that_is.ucfirst($key).'">> '.${$key."String"}.'</label> <!--<br />--><select name="'.$that_is.ucfirst($key).'">'.gen_selectoption(array('N','Y'),$edit_profil[$i],'','').'</select><br />';
          } else if (in_array("$that_is$key",$int3_array) && !isset(${"tbl".$key})) {
          //
          } else {
            if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
            include $getcwd.$up.$safedir.'_extra_routines.php';
            if (!isset(${"show_".$key}))
            ${"show_".$key} = (in_array($key,array('address','adresse'))?'<div class="clear">&nbsp;</div><div style="float:left;width:50%;">':'').'<div style="float:left;"><label for="'.$that_is.ucfirst($key).'">'.ucfirst(${$key."String"}).'</label><br /><input class="text" name="'.$that_is.ucfirst($key).'" type="text" value="'.$value.'" /><br /></div>'.(in_array($key,array('ville'))||(($key=='nom')&&in_array($that_is.'gendre',$that_array_fields))?'</div>':'');//<div class="clear">&nbsp;
          }
        }
        
        if (!isset($array_tpl))
        $show_mod_content .= ${"show_".$key};
        
      }
      if (isset($array_tpl)) {
        foreach($array_tpl as $tpl) //  by id
        $_mod_content .= ($tpl[0]=="$"?${substr($tpl,1)}:$tpl);
      } else
        $_mod_content .= $show_mod_content;
  	}
    $_mod_content .= ' </div><br />';
  	if ($tabbing === true) {
      foreach($array_tabs as $key=>$value)
        if (!in_array($value,array("user")))
          if ($value == "membre") {
  	        $_mod_content .= '<input name="send" type="submit" value="'.($logged_in===true?$sauverString:$vousinscrireString).'" /><input type="hidden" name="do" value="tabcontent'.$key.'" /></form></div>';      
          } else if ($value == "communications") {
            $_mod_content .= '<div id="tcontent'.$key.'" class="profiltabcontent">'.$communications_content.'</div>';
          } else 
          $_mod_content .= '<div id="tcontent'.$key.'" class="profiltabcontent">'.${"tab_".$array_tabs[$key]}.'</div>';
      /*
    	if (isset($array_tabs[2])) {
      	$_mod_content .= '<div id="tcontent2" class="profiltabcontent">'.${"tab_".$array_tabs[2]}.'</div>';//profiltabcontent2
      }
    	if (isset($array_tabs[3])) {
      	$_mod_content .= '<div id="tcontent3" class="profiltabcontent">';
      	$_mod_content .= ${"tab_".$array_tabs[3]};
      //	$_mod_content .= '<input name="send" type="submit" value="'.($logged_in===true?$modifierString:$vousinscrireString).'" /><input type="hidden" name="do" value="tabcontent2" /></form>';
      	$_mod_content .= '</div>';//profiltabcontent3
    	}
    	if (isset($array_tabs[4])) {
      	if (in_array("communications",$array_tabs)) {
        }
      }
      */
    	$_mod_content .= '</div>';//profiltabcontentstyle
    	$_mod_content .= '<script type="text/javascript">
                          initializetabcontent("profilmaintab")
                        </script>';
    	$_mod_content .= '</div></div>';//tabsidebar_search,tabsidebars
    } else {
    	if ($logged_in === true) {
        foreach($array_tabs as $key)
          if (!in_array($key,array("user","membre")))
            if ($key == "communications")
          	$_mod_content .= include $getcwd.$up.$urladmin.'communicationsadmin.php';
          	else
          	$_mod_content .= ${"tab_".$key};
        /*
        if (isset($array_tabs[2]))
      	$_mod_content .= ${"tab_".$array_tabs[2]};
        if (isset($array_tabs[3]))
      	$_mod_content .= ${"tab_".$array_tabs[3]};
        if (isset($array_tabs[4]))
      	*/
    		$_mod_content .= '<input name="send" type="submit" value="'.($logged_in===true?$sauverString:$vousinscrireString).'" /></form>';
    	}
  	}
  }
####################################### CREATE NEW ENTRY WITH VALIDATION
$_mod_profil = ($logged_in===true?$_mod_content:'');
