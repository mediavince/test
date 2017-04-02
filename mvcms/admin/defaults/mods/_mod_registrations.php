<?php if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$this_is = 'registrations';
$dbtable = ${"tbl".$this_is};

$local_url = $local.substr($local_uri,1).(isset($q)?'?q='.$q:'?');

if (in_array($this_is,$array_modules_as_form)) {

  if (isset($filter_registrationsType))
  $array_hidden[] = 'type';
  if (isset($filter_registrationsEvents))
  $array_hidden[] = 'events';
  
  if (isset($filter_registrationsType) && ($filter_registrationsType == 1)) {
    $array_hidden[] = 'roomdbl';
    $array_hidden[] = 'roomguest1';
    $array_hidden[] = 'roomextranights';
    $array_hidden[] = 'roomwhichnights';
    $array_hidden[] = 'roomnotes';
  }
  if (isset($filter_registrationsType) && ($filter_registrationsType == 2)) {
    $array_hidden[] = 'interests';
    $array_hidden[] = 'submitabstract';
    $array_hidden[] = 'publicdinner2';
    $array_hidden[] = 'publicdinner2guest';
    $array_hidden[] = 'gender';
    $array_hidden[] = 'roommate';
  }
  
  if (isset($send) && ($send == $envoyerString)) {
    $this_titre = $registrationsNom;
    $this_email = html_encode(strtolower(trim($registrationsEmail)));
    if (sql_nrows($tblregistrations,"WHERE registrationsnom='$registrationsNom' OR registrationsemail='$this_email' ") > 0) {
      $_SESSION['mv_error'] = 'Your registration is already recorded, please contact us should you need any further assistance.';
      Header("Location: ".(isset($filter_registrationsEvents)?$mainurl.lgx2readable($lg,'','events',$filter_registrationsEvents):$redirect));Die();
    } else {
      if (!isset($array_modules_as_form))
      $array_modules_as_form = array();
      if (!isset($array_preferred_fields))
      $array_preferred_fields = array('title','titre','name','nom','util');
      
      $mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
      $longtext_array = array(); // textarea with tinyMCE: word style UI
      $enumYN_array = array(); // either Y or NO: produces selectable option code 
      $enumtype_array = array(); // int(11) unsigned, produces selectable code for all possibilities taken from enum and assign string, then allows creation of new type, further options apply like - for deleting the selected item
      $int3_array = array(); // int(3) unsigned, flag for fetching items from referenced table
      $datetime_array = array(); // datetime, flag for showing calendar
      $array_fields_type = array();//lists all types for a given table
      
      $result = @mysql_query("SHOW FIELDS FROM $dbtable");
      if (!$result) {Header("Location: $redirect");Die();} // no table or no connection...
      while($row=@mysql_fetch_array($result)) {
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
      @mysql_free_result($result);
      
      if (!isset($array_mandatory_fields))
      $array_mandatory_fields = array();
      if (!isset($params_array))
      $params_array = array();
      
      if (isset(${"_".$this_is."_params_array"}))
      $params_array = array_unique(array_merge($params_array,${"_".$this_is."_params_array"}));
      if (isset(${"_".$this_is."_array_mandatory_fields"}))
      $array_mandatory_fields = array_unique(array_merge($array_mandatory_fields,${"_".$this_is."_array_mandatory_fields"}));
      
      $array_fields = sql_fields($dbtable,'array');
      if (!isset(${$this_is.'_referrerfield'})) {
        foreach($array_preferred_fields as $k)
          if (!isset(${$this_is.'_referrerfield'}))
            if (in_array($this_is.$k,$array_fields)) ${$this_is.'_referrerfield'} = $this_is.$k;
      }
      if (!isset(${$this_is.'_referrerfield'}))
      ${$this_is.'_referrerfield'} = $array_fields[3];
      if (!in_array("lang",$array_fields)) $basic_array = array_diff($basic_array,array('lang'));
      $params_array = array_merge($basic_array,$params_array);
      
      foreach($array_fields as $key) {
        $empty_array_fields[] = ($key=='statut'?'Y':(isset(${$key."Id"})&&in_array($this_is,$editable_by_membre)&&!stristr($_SERVER['PHP_SELF'],$urladmin)&&($logged_in === true)?"'".${$key."Id"}."'":''));
        $list_array_fields = isset($list_array_fields)?$list_array_fields.','.$key:$key;
      }
      include $getcwd.$up.$urladmin.'function-process_post.php';
    //  include $getcwd.$up.$urladmin.'itemadmin.php';
      if ($error == '') {
        $_SESSION['mv_notice'] = '<br /> <br />Your registration has been succesfully recorded!<br /> <br />Please contact us should you need any further assistance.<br /> <br />'.$notice;
        Header("Location: ".(isset($filter_registrationsEvents)?$mainurl.lgx2readable($lg,'','events',$filter_registrationsEvents):$redirect));Die();
      } else {
        $_SESSION['mv_error'] = $error;
     //   Header("Location: ".(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$mainurl.$_SERVER['REQUEST_URI']));Die();
        ${$this_is."Id"} = 'new';
        $send = 'new';
      }
    }
  } else {
    $send = 'new';
  }

  include $getcwd.$up.$urladmin.'itemadmin.php';
}
