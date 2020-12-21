<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$this_is = 'string';
$dbtable = '_'.$this_is;

$sql_query = " WHERE ".fieldis('lang')."='$lg' ";
$sql_query_default_lg = " WHERE ".fieldis('lang')."='$default_lg' ";
$sql_orderq = "ORDER BY ".fieldis('type').", ".fieldis('title')." ASC ";
$return = fieldis('type');

if (!isset($array_stringtypes)) $array_stringtypes = array('general','help');
$array_stringtypes = array_unique(array_merge($array_stringtypes,sql_array($tblenum,"","enumwhat"),sql_array($dbtable,"","stringtype")));
//sort($array_stringtypes);

$content .= $admin_menu;

// lists all the texts strings appearing on site
if (!isset($send)) {
  $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br />';
  if (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") == $admin_name)
  $content .= '<br />NEW<br /><label for="'.fieldis('type').'"> > '.$typeString.': </label>  '.gen_fullselectoption($array_stringtypes,'','',fieldis('type')).'<label for="new'.fieldis('title').'"> > '.$titleString.': </label> <input type="text" name="new'.fieldis('title').'" value="" /><br /><label for="new'.fieldis('entry').'"> > '.$entryString.': </label> <input name="new'.fieldis('entry').'" type="text" style="width: 70%" value="" /><br />';
	$read = @mysqli_query(" SELECT * FROM $dbtable $sql_query $sql_orderq ");
  if ((@mysqli_num_rows($read) == 0) && ($lg != $default_lg)) {
  	$read = @mysqli_query(" SELECT * FROM $dbtable $sql_query_default_lg $sql_orderq ");
  	$content .= '<span style="font-weight:bold;color:red;">Version in '.sql_getone($tblstring,"WHERE stringlang='$default_lg' AND stringtype='lang' ","stringentry").' !!</span>';
  	$content .= '<input name="import" type="hidden" value="" />';
  	$import = true;
  }
	$nRows = @mysqli_num_rows($read);
	for ($i=1;$i<=$nRows;$i++) {
		$iminus = $i-1;
		if	($iminus == '0')	$iminus = '1'	;
		$row = mysqli_fetch_array($read);
		$row_id = $row["".fieldis('id').""];
		$sql_q = (isset($import)&&$import===true?$sql_query_default_lg:$sql_query)."AND ".fieldis('id')."='$row_id' ";
		$edit[$i] = sql_get($dbtable,$sql_q,$return);
		if	(($i == '1') || ($edit[$iminus][0] != $edit[$i][0])) 
    $content .= (($i=='1')?'':'</div>').'<hr /><b style="float:left;">'.$row["".fieldis('type').""].'</b><br /><div style="text-align:right">';
    $content .= '<br /><label for="'.$row["".fieldis('id').""].'">> '.$row["".fieldis('title').""].' </label> <input name="'.$row["".fieldis('id').""].'" type="text" style="width: 70%" value="'.$row["".fieldis('entry').""].'" />';
	}
  $content .= (($nRows>'0')?'</div>':'').'<br /> <br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
// update the strings
} else if ($send == $envoyerString) {
	$update_rapport = '';
	$insertq = '';
	$read = @mysqli_query(" SELECT * FROM $dbtable $sql_query $sql_orderq ");
  if ((@mysqli_num_rows($read) == 0) && ($lg != $default_lg))
  if (isset($_POST['import']))
  $read = @mysqli_query(" SELECT * FROM $dbtable $sql_query_default_lg $sql_orderq ");
	$nRows = @mysqli_num_rows($read);
	$full_strings = "";
	for ($i=1;$i<=$nRows;$i++) {
		$row = mysqli_fetch_array($read);
		$row_id = $row["".fieldis('id').""];
		$row_type = $row["".fieldis('type').""];
		$row_title = $row["".fieldis('title').""];
		$row_entry = $row["".fieldis('entry').""];
		if (isset($_POST[$row_id])) {
      $_POST[$row_id] = strip_tags(stripslashes(str_replace("'","&acute;",html_encode($_POST[$row_id]))),"<a><i><b><u><br><h2><span>");//´
			// $_POST[$row_id] = html_encode(strip_tags(stripslashes(str_replace("'","&acute;",$_POST[$row_id])),"<a><i><b><u><br><h2><span>"));//´
		//	if (in_array($row_type,array("general","jour","mois")))
			if ($row_type == "general")
			if (isset($_POST['import'])) {
        $full_strings .= "$".$row_title."String = '".$_POST[$row_id]."';
      "; // JUMPED TO LINE
      } else {
        $full_strings .= "$".$row_title."String = '".$_POST[$row_id]."';
      "; // JUMPED TO LINE
      }
			if ($row_type == "help")
			if (isset($_POST['import'])) {
        $full_strings .= "$".$row_title."HelpString = '".$_POST[$row_id]."';
      "; // JUMPED TO LINE
      } else {
        $full_strings .= "$".$row_title."HelpString = '".$_POST[$row_id]."';
      "; // JUMPED TO LINE
      }
			if (isset($_POST['import'])) {
			  // insert
			  $insertq .= ($insertq==''?'':',')."('', '', '$lg', '$row_type', '$row_title', '".$_POST[$row_id]."')\n\r";
      } else {
  			if ($_POST[$row_id] == $row_entry) {
  				$update_rapport .= '';
  			} else {
  				$updated = fieldis('entry');
  				$setq = " SET ".fieldis('entry')."='".$_POST[$row_id]."' ";
  				$whereq = " WHERE ".fieldis('id')."='$row_id' ";
  				$setq = " SET ".fieldis('entry')."='".$_POST[$row_id]."' ";
    			$update = sql_update($dbtable,$setq,$whereq,$updated);
    			$update_rapport .= '<b>'.$update[0].'</b> : '.$enregistrementString.' '.$effectueString.' <br />';
  			}
  		}
		}
	}
	if (isset($_POST['import']) && ($insertq != '')) {
          $insertquery = @mysqli_query("
  									INSERT INTO $dbtable 
  									(`stringid`, `stringpg`, `stringlang`, `stringtype`, `stringtitle`, `stringentry`)
  									VALUES 
  									$insertq
  									");
  				if ($insertquery) {
  				  $notice .= "added<hr /><br />";
  				} else
  				$error .= "not inserted<hr /><br />$insertq";
	}
	if (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") == $admin_name) {
    if (isset($_POST['new'.fieldis('title')]) && ($_POST['new'.fieldis('title')] != '') && isset($_POST[fieldis('type')]) && in_array($_POST[fieldis('type')],$array_stringtypes) && isset($_POST['new'.fieldis('entry')]) && ($_POST['new'.fieldis('entry')] != '')) {
      $stringTitle = strip_tags(html_encode($_POST['new'.fieldis('title')]));
      $stringType = strip_tags($_POST[fieldis('type')]);
      if (sql_nrows($dbtable,"WHERE ".fieldis('title')."='$stringTitle' AND ".fieldis('type')."='$stringType' ")==0) {
        $stringDesc = strip_tags(stripslashes(str_replace("'","&acute;",html_encode($_POST['new'.fieldis('entry')]))),"<a><i><b><u><br><h2><span>");//´
        // $stringDesc = html_encode(strip_tags(stripslashes(str_replace("'","&acute;",$_POST['new'.fieldis('entry')])),"<a><i><b><u><br><h2><span>"));//´
        foreach($array_lang as $k) {
          $insertquery = @mysqli_query("
  									INSERT INTO $dbtable 
  									(`stringid`, `stringpg`, `stringlang`, `stringtype`, `stringtitle`, `stringentry`)
  									VALUES 
  									('', '', '$k', '$stringType', '$stringTitle', '$stringDesc')
  									");
  				if ($insertquery) {
  				  $notice .= "added > '$k', '$stringType', '$stringTitle':<br />'$stringDesc'<hr /><br />";
  				  if ($row_type == "general")
      			$full_strings .= "$".$stringTitle."String = '$stringDesc';
      "; // JUMPED TO LINE
      			if ($row_type == "help")
      			$full_strings .= "$".$stringTitle."HelpString = '$stringDesc';
      "; // JUMPED TO LINE
  				} else
  				$error .= "not inserted > '$k', '$stringType', '$stringTitle':<br />'$stringDesc'<hr /><br />";
        }
      }
    }
  }
	if (!isset($_POST['import']) && ($update_rapport == '')) {
    $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$nonString.' '.$modifieString.'</b></font><br /> <br /><a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a></p>';
	} else {
    $first_line = '<'.'?php if (stristr($_SERVER[\'PHP_SELF\'],\'_full_strings_'.$lg.'.php\')){include\'_security.php\';Header("Location: $redirect");Die();}';
    $last_line = PHP_EOL;//'?'.'>';
	  $Fnm = $getcwd.$up.$safedir.'_full_strings_'.$lg.'.php';
		$inF = fopen($Fnm,"w+");
		$full_strings = $first_line.
                stripslashes($full_strings).
                $last_line;
		fwrite($inF,$full_strings);
		fclose($inF);
    $content .= (isset($_POST['import'])?'':'<br />'.$update_rapport.'<br />').'<p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effectueString.'</b></font><br /> <br /><a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a></p>';
	}
} else {
	Header("Location: $redirect");Die();
}
