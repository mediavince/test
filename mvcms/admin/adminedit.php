<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

// note to self, this is a major headache source!! even back in the days, i dreaded editing this!

if (!isset($root_writable))
	$root_writable = false;
if (!isset($htaccess4sef))
	$htaccess4sef = false;
if ($htaccess4sef === false) {
	if ($root_writable === false)
		$notice .= "Please contact the webmaster concerning htaccess and root files!";
	$root_writable = true;
}

if (!isset($array_img_ext))
	$array_img_ext = array("jpg","jpeg","gif","png");
if (!isset($array_swf_ext))
	$array_swf_ext = array("swf","flv","wmv","avi","mp3","mov","rm","mpeg","mpg","asf");
if (!isset($array_doc_ext))
	$array_doc_ext = array("doc","xls","ppt","pps","pdf");

$dbtable = $tblcont;
$dir_urlintro = $urlintro;
if (isset($send) && (strstr($send,' Intro') || ($send == 'editemintro') || ($send == 'deleteintro')))
	$dbtable = $tblintro;
else
	$dir_urlintro = "";

$array_conttypes = array_unique(array_merge($array_modules,$array_fixed_modules,array("scroller","leftlinks","toplinks","contact","profil","")));

//if (!isset($contlogo)) $contlogo = ""; // here we use that for contPriv // ???

if (!isset($contmenu))
	$contmenu = "";

/* seems useless check line 95 */
$array_linkedmenus = "";
$array_linkedmenus = array($array_linkedmenus);
$all_contmenus = mysqli_query($connection, "SELECT `contmenu` FROM $dbtable WHERE contmenu!='' ");
$nrows = mysqli_num_rows($all_contmenus);
for ($i=0;$i<$nrows;$i++) {
	$row = mysqli_fetch_array($all_contmenus);
	$this_contmenus = explode("|", $row["contmenu"]);
	if	(!is_array($this_contmenus))	$this_contmenus = array($this_contmenus);
	if	(!empty($array_linkedmenus))	$array_linkedmenus = array_merge($array_linkedmenus,$this_contmenus);
	if	(!empty($array_linkedmenus))	$array_linkedmenus = array_unique($array_linkedmenus);
}


$x_array = array($x,(($x*10)+1),(($x*100)+11),(($x*1000)+111));
$xplus1_array = array(($x+1),((($x+1)*10)+1),((($x+1)*100)+11),((($x+1)*1000)+111));

if ($logged_in === true) {
  	$error_img = '';
  	$notice_img = '';
	$contGet = sql_get($dbtable," WHERE contpg='$x' AND contlang='$lg' ","conttitle,contentry,contstatut,contlogo,contmenu,conturl,conttype,contorient,contmetadesc,contmetakeyw,contupdate,contupdateby");
	if ($contGet[0] == '.')
		$contGet["conttitle"] = '.';
	if	($x == '10777')	$contType = 'scroller'	;
	if	($x == '10888')	$contType = 'leftlinks'	;
	if	($x == '10999')	$contType = 'toplinks'	;
  	$contPriv = $contGet["contlogo"];// ??? contlogo?? see above...
	if ($contPriv == "")
		$contPriv = "1";// ???
	$y_menu = "";
	$menu1 = "";
	if (!in_array('1',$admin_priv))
		$menu1 .= '<div style="float:right;text-align:right;padding-left:3px;"><a href="'.$local.'?x=z&amp;y=1">Extra '.$adminString.' '.$optionsString.'</a></div>';
	if (($contGet["conttitle"] == '.') || isset($contTitle)) {
		if (isset($contTitle))
			$this_title = $contTitle;
		else {
			$this_title = "_NEW PAGE_";
			$title = $this_title;
		}
		if (isset($send) && (($send == 'editemintro') || strstr($send, " Intro")))// old
			$this_title = sql_getone($tblcont," WHERE contpg='$x' AND contlang='$lg' ","conttitle");// old
		$menu1 .= '<a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$modificationString.' '.$detexteString.' '.$surString.' <i>'.$this_title.'</i></a>';
	} else {
		$menu1 .= '<a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$modificationString.' '.$detexteString.' '.$surString.' <i>'.$contGet["conttitle"].'</i></a>';
	}
	if (!in_array('1',$admin_priv)) {
	  	if (isset($send) && (stristr($send, "delete") || !stristr($send, "edit"))) {
	  		// nada: no options to delete or add anything
	  	} else {
	  		if (($lg == $default_lg) && ($contGet["conttitle"] !== '.')
	  		 && !in_array($contGet["conttype"],array("toplinks","leftlinks","scroller"))
	  		) {
	  			if (is_float(($x+1)/10) || (($x1subok === false) && ($x == '9')))
	  				if (sql_nrows($dbtable,"WHERE contpg='".($x+1)."' AND contlang='$lg' ") == 0)
	          			$menu1 .= ' > <a href="'.$local.'?lg='.$lg.'&amp;x='.($x+1).'">'.$ajouterString.' 1 '.$pageString.'</a>' ;
	  		  	if ((sql_nrows($dbtable,"WHERE contpg='".(($x*10)+1)."' AND contlang='$lg' ") == 0) && ((($x1subok === false) && ($x > '1')) || ($x1subok === true)) && (strlen($x) <= 3))
	        		$menu1 .= ' >> <a href="'.$local.'?lg='.$lg.'&amp;x='.(($x*10)+1).'">'.$ajouterString.' 1 '.$menuString.'</a>' ;
	  			if ((array_intersect($x_array,$array_linkedmenus) == $x_array) || ((sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='".($x+1)."' ") == '0') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='".(($x*10)+1)."' ") == '0'))) {
	          		if ($x > '1')
	          			$menu1 .= ' | <font color="Red"> !! <a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=deletepg" onclick="return confirm(\''.$confirmationeffacementString.'\');">'.$effacerString.' '.$cettepageString.'</a> !!<!--  (plus multilingual versions) --></font>' ;
	  			}
	  		}
	  		if (isset($send) && ($send == 'editemintro') && (sql_nrows($dbtable,"WHERE contpg='$x' ") > 0))
	  			$menu1 .= ' | <font color="Red"> !! <a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=deleteintro" onclick="return confirm(\''.$confirmationeffacementString.'\');">'.$effacerString.' '.$cettepageString.' Blog</a> !!<!--  (plus multilingual versions) --></font>' ;
	  	}
	}
	$this_contmenu = explode("|", $contGet["contmenu"]); // contmenu:4
	if (!is_array($this_contmenu))
		$this_contmenu = array($this_contmenu);
	if ($x !== 'z') {
		if ($y == '1')
			$y_menu .= '<b>'.$menu1.'</b>';
		else
			$y_menu .= '<a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;y=1">'.$menu1.'</a>';
	}
  	$content = '<!-- <p> -->'.$y_menu.'<!-- </p> -->';
	$editInfo = '<div style="float:right;text-align:right;clear:both;">'.$derniereString.' '.$modificationString.' '.$parString.' <b>'.$contGet["contupdateby"].'</b> ('.$dateString.' <b>'.$contGet["contupdateby"].'</b>) <font color="Red">|</font> <a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$modifierString.' '.$cettepageString.'</a></div>';

	if (!isset($send)) {

		if ($contGet["conttitle"] == '.') {
			$reload = $local.'?lg='.$lg.'&x='.$x.'&y='.$y.'&send=edit'; // no &amp; here or in any passed url for redirect
			Header("Location: $reload");Die();
		} else {
	        $content .= $editInfo.'<br /> <br /><h3>'.$contGet["conttitle"].'</h3>';
	      	$content .= '<div class="content_body">'.str_replace("content/",$up."content/",$contGet["contentry"]).'</div><br /><hr />'.$editInfo;
		}

	} elseif (($send == "edit") || ($send == 'editemintro')) {

		if ((!is_array($contGet)) || ($contGet["conttitle"] == '.')) { // new

      		if (!in_array('1',$admin_priv)) {

  $content .= $form_method.'<input type="submit" name="send" value="'.$ajouterString;
  				if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
				if ($tinyMCE === false)
  $content .= ' | <a href="javascript: alert(\''.$helpEdit.'\')" title="Help" alt="Help">'.$aideString.'</a>';
  $content .= '<br /><label for="contStatut"> <b>'.$statutString.'</b></label> <select name="contStatut">'.gen_selectoption($tblenum,'Y','','statut').'</select> | <label for="contOrient"> <b>'.$menuString.' position </b></label> <select name="contOrient">'.gen_selectoption($array_orient,'center','','').'</select><br />';
  				if ($send == 'editemintro')
  $content .= '<label for="contType"> <b>'.$typeString.' (default replace)</b></label><select name="contType"><option value="1"> (replace) </option><option value="2"> < (before) </option><option value="3"> > (after) </option></select>';
  				else
  $content .= '<label for="contType"> <b>'.$pageString.' (empty = default '.$titreString.')</b></label> <select name="contType"'.($tinyMCE===true?' onchange="typeArray=[0,\'scroller\',1,\'leftlinks\',2,\'toplinks\'];tinyMCE.execCommand(\'mceInsertContent\',false,(typeArray.indexOf(this.options[this.selectedIndex].value)==-1?\'[\'+this.options[this.selectedIndex].value+\']\':\'\'));"':'').'>'.gen_selectoption($array_conttypes,(isset($contType)?$contType:$contGet["conttype"]),'','').'</select>';
				if ($tinyMCE === true) {
					$content .= '<br />click to insert code '.$pourString.' '.$typeString.' :<br />';
          			foreach(array_intersect($array_conttypes,array_unique(array_merge($array_modules,$array_fixed_modules))) as $key)
          				$content .= ' <a onmousedown="tinyMCE.execCommand(\'mceInsertContent\',false,\'['.$key.']\');" href="javascript:;">['.$key.']</a> ';
        		}
        		if ($mod_priv === true)
  $content .= '<br /><div><div style="float:left;"><label for="contPriv"> <b> '.$privilegeString.' : </b></label> &nbsp;</div>'.gen_inputcheck($tblenum,((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'1':sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")),((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'':"%' AND enumtitre!='1' AND enumtype LIKE '%"),'privilege')."</div><!--<div class='clear'></div>--> <br />";
  $content .= '<br /><label for="contTitle"> <b>'.$titreString.'</b> <!-- ('.$saufString.' '.$accueilString.')  --></label> <input type="text" name="contTitle" value="'.(isset($contType)?$contType:'').'" style="width: 97%" /><br />';
  $content .= '<br /><div id="metadescToggle">META DESCRIPTION & KEYWORDS (1 line) (click to edit)<hr /></div><div id="metadescZone"><label for="contMetadesc"> <b>'.$descriptionString.'</b> </label><br /><textarea name="contMetadesc" rows="3" cols="40" style="width: 97%; height: 50px; min-height: 50px;">'.($contGet["contmetadesc"]==""?$desc:$contGet["contmetadesc"]).'</textarea><br /><label for="contMetakeyw"> <b>keywords</b> </label><br /><textarea name="contMetakeyw" rows="3" cols="40" style="width: 97%; height: 50px; min-height: 50px;">'.($contGet["contmetakeyw"]==""?$keyw:$contGet["contmetakeyw"]).'</textarea><hr /></div>';
  $content .= '<label for="contEntry"> <b>'.$descriptionString.'</b> '.(isset($contType)&&($contType=='toplinks')?'<i> !! edit with HTML !! </i>':'').'</label><br />'.$text_style.'<textarea id="'.($tinyMCE===false?'uldescription':'elm1').'" name="contEntry" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.(isset($contType)&&($contType=='toplinks')?'<div style="float:left;overflow:hidden;width:110px;margin-left:-25px;"><ul><li><a title="MediaVince" href="https://www.mediavince.com/" target="_blank">MediaVince</a></li></ul></div> <ul><li><a title="MediaVince" href="https://www.mediavince.com/" target="_blank">MediaVince</a> <br /></li><li><span class="current"> '.$codename.' </span> &rsaquo;</li></ul>':'').'</textarea><p>'.$text_style.'</p>';
  				if ($tinyMCE === false)
  $content .= $text_style_js;
  $content .= '<br /> <br /><!-- <div class="clear"></div> --><input type="submit" name="send" value="'.$ajouterString;
  				if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
				if ($tinyMCE === false)
  $content .= ' | <A href="javascript: alert(\''.$helpEdit.'\')" alt="Help">'.$aideString.'</a>';
  $content .= '<br /> <br /></form>';

  			} else { // adminpriv denied
  $content = '<div style="background-color: #FA5;width: 90%;border: 1px solid red;padding: 5px;margin: 5px;text-align:center;">'.ucfirst($accesString.' '.$refuseString).' >> '.$privilegeString.' || <a href="javascript:history.back()//">'.$retourString.'</a></div>';
      		}

		} else { // update

			$contGet["conttitle"] = strip_tags($contGet["conttitle"]);

  $content .= $form_method.'<input type="submit" name="send" value="'.$sauverString;
  			if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
			if ($tinyMCE === false)
  $content .= ' | <a href="javascript: alert(\''.addslashes($helpEdit).'\')" title="Help" alt="Help">'.$aideString.'</a>';
  $content .= '<br />';

			if (in_array('1',$admin_priv)) { // only pagine
    //$content .= '<input type="hidden" name="contStatut" value="'.$contGet["contstatut"].'" /><input type="hidden" name="contOrient" value="'.$contGet["contorient"].'" /><input type="hidden" name="contType" value="'.$contGet["conttype"].'" />';
$content .= '<br /><b>'.$contGet["conttitle"].'</b><br />';

			} else { // admin can edit

$content .= '<label for="contStatut"> <b>'.$statutString.'</b></label> <select name="contStatut">'.gen_selectoption($tblenum,$contGet["contstatut"],'','statut').'</select> | <label for="contOrient"> <b>'.$menuString.' position </b></label> <select name="contOrient">'.gen_selectoption($array_orient,$contGet["contorient"],'','').'</select><br />';
  				if ($send == 'editemintro')
$content .= '<label for="contType"> <b>'.$typeString.' (default replace)</b></b></label><select name="contType"><option value="'.$contGet["conttype"].'"> >> '.$contGet["conttype"].'</option><option value="1"> (replace) </option><option value="2"> < (before) </option><option value="3"> > (after) </option></select><br />';
  				else
$content .= '<label for="contType"> <b>'.$pageString.' (empty = default '.$titreString.')</b></label><select name="contType"'.($tinyMCE===true?' onchange="typeArray=[0,\'scroller\',1,\'leftlinks\',2,\'toplinks\'];tinyMCE.execCommand(\'mceInsertContent\',false,(typeArray.indexOf(this.options[this.selectedIndex].value)==-1?\'[\'+this.options[this.selectedIndex].value+\']\':\'\'));"':'').'>'.gen_selectoption($array_conttypes,(isset($contType)?$contType:$contGet["conttype"]),'','').'</select><br />';
				if ($tinyMCE === true) {
					$content .= 'click to insert code '.$pourString.' '.$typeString.' :<br />';
          			foreach(array_intersect($array_conttypes,array_unique(array_merge($array_modules,$array_fixed_modules))) as $key)
          				$content .= ' <a onmousedown="tinyMCE.execCommand(\'mceInsertContent\',false,\'['.$key.']\');" href="javascript:;">['.$key.']</a> ';
        		}
        		$priv_param0 = ((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?$contPriv:((strlen($x)>1)&&in_array('1',explode("|",$contPriv))?sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo"):$contPriv));
        		$priv_param1 = ((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'':((strlen($x)>1)&&in_array('1',explode("|",$contPriv))?'':(strlen($x)>1?"%' AND enumtitre!='1' AND enumtype LIKE '%":'')));
        		if ($mod_priv === true)
$content .= '<div><div style="float:left;"><label for="contPriv"> <b> '.$privilegeString.' : </b></label> &nbsp;</div>'.gen_inputcheck($tblenum,$priv_param0,$priv_param1,'privilege')."</div><!--<div class='clear'></div>--><br /> <br />";
$content .= '<label for="contTitle"> <b>'.$titreString.'</b> <!-- ('.$saufString.' '.$accueilString.')  --></label><input type="text" name="contTitle" value="'.$contGet["conttitle"].'" style="width:97%;" /><br />';
$content .= '<br /><div id="metadescToggle">META DESCRIPTION & KEYWORDS (1 line) (click to edit)<hr /></div><div id="metadescZone"><label for="contMetadesc"> <b>'.$descriptionString.'</b> </label><br /><textarea name="contMetadesc" rows="3" cols="40" style="width: 97%;height:50px;min-height:50px;">'.($contGet["contmetadesc"]==""?$desc:$contGet["contmetadesc"]).'</textarea><br /><label for="contMetakeyw"> <b>keywords</b> </label><br /><textarea name="contMetakeyw" rows="3" cols="40" style="width:97%;height:50px;min-height:50px;">'.($contGet["contmetakeyw"]==""?$keyw:$contGet["contmetakeyw"]).'</textarea><hr /><br /></div>';

  			} // end admin can edit

$content .= '<label for="contEntry"> <b>'.$descriptionString.'</b> '.((isset($contType)&&($contType=='toplinks'))||($contGet["conttype"]=='toplinks')?'<i> !! edit with HTML !! </i>':'').'</label><br />'.$text_style;
			if ($tinyMCE === false)
$content .= '<div style="float:left;min-width:500px;width:80%;">';//<table width="100%" border="2" cellspacing="0" cellpadding="0"><tr><td width="100%" valign="top">';
$content .= '<textarea id="'.($tinyMCE===false?'uldescription':'elm1').'" name="contEntry" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.format_edit($contGet["contentry"],"edit").'</textarea><p>'.$text_style.'</p>';

			if ($tinyMCE === false)
$content .= $text_style_js;
$content .= '<br /> <br /><div style="float:left;width:98%;text-align:left;"><input type="submit" name="send" value="'.$sauverString;
  			if ($send == 'editemintro')
$content .= ' Intro';
$content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
			if ($tinyMCE === false)
$content .= ' | <a href="javascript: alert(\''.addslashes($helpEdit).'\')" title="Help" alt="Help">'.$aideString.'</a><br /> <br />';//</td></tr></table>';
$content .= '</div></form>';

		} // end new else update

	} elseif ( // end send not set
		(!in_array('1',$admin_priv) && (($send == $ajouterString) || ($send == $ajouterString.' Intro')))
		|| (($send == $sauverString) || ($send == $sauverString.' Intro'))
	) {
		if (($contGet["conttype"] !== 'scroller') && ($contType == 'scroller') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND conttype='scroller' ") > 0)) {
			Header("Location: ".$local."?lg=".$lg."&amp;x=10777&amp;y=".$y."&amp;send=edit&amp;redirected");
			die();
		}
		if (($contGet["conttype"] !== 'leftlinks') && ($contType == 'leftlinks') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND conttype='leftlinks' ") > 0)) {
			Header("Location: ".$local."?lg=".$lg."&amp;x=10888&amp;y=".$y."&amp;send=edit&amp;redirected");
			die();
		}
		if (($contGet["conttype"] !== 'toplinks') && ($contType == 'toplinks') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND conttype='toplinks' ") > 0)) {
			Header("Location: ".$local."?lg=".$lg."&amp;x=10999&amp;y=".$y."&amp;send=edit&amp;redirected");
			die();
		}

		if (($contEntry == "<p></p>") || ($contEntry == "<p>&nbsp;</p>"))
			$contEntry = ""	;
		//	$contEntry = html_encode($contEntry);
		if ($tinyMCE === false) {
			$contEntry = nl2br($contEntry);
			$contEntry = format_edit($contEntry,"show");
		}

		// checking module integrity
		foreach($mod_array as $key_mod_array => $value_mod_array) {
			if (strstr($contEntry,"[".$value_mod_array))
				$notice .= check_module_content($value_mod_array,$contEntry);
		}
    	if (in_array('1',$admin_priv)) {
	      	$contTitle = $contGet["conttitle"];
	  		$contStatut	= $contGet["contstatut"];
	  		$contPriv = $contGet["contlogo"];// 3
	  		$contMenu = $contGet["contmenu"];
	  		$contType	= $contGet["conttype"];
	  		$contOrient	= $contGet["contorient"];
	  		$contMetadesc = $contGet["contmetadesc"];
	  		$contMetakeyw = $contGet["contmetakeyw"];
	    } else {
  			$contTitle = strip_tags($contTitle);
  			$clean_contTitle = space2underscore($contTitle);
	  		$contTitle = html_encode($contTitle);
	  		$contMetadesc = html_encode(strip_tags($contMetadesc));
	  		if (($contMetadesc == "") && ($x == '1'))
	  			$contMetadesc = $meta_desc;
	  		$contMetakeyw = html_encode(strip_tags($contMetakeyw));
	  		if (($contMetakeyw == "") && ($x == '1'))
	  			$contMetakeyw = $meta_keyw;
	  		$contType = strip_tags($contType);
	  		$contOrient = strip_tags($contOrient);
	  		$contPriv = "";
	  		$array_priv = sql_array($tblenum,"WHERE enumwhat='privilege' AND enumstatut='Y' ".((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'':" AND enumtitre!='1' ")." ","enumtitre");
	  		if ($array_priv[0] !== "") {
	  		  	foreach($array_priv as $key)
	  		   		if (isset($_POST["privilege".$key]) && ($_POST["privilege".$key] == 'on'))
	  		      		$contPriv .= $key."|";
        		$contPriv = substr($contPriv,0,-1);
		    } else {
	        	$contPriv = "1";
	    	}

  			if (($send == $sauverString) || ($send == $sauverString.' Intro')) {
  				$contStatut = strip_tags($contStatut);
	  			if (isset($contMenu)) {
	  				$contMenu = strip_tags($contMenu);
	  				if (!isset($valid_pg))
	  					$valid_pg = false;
	  				if (sql_nrows($dbtable,"WHERE contpg='$contMenu' ") == '1')
	  					$valid_pg = true;
	  				if ($contMenu == '')
	  					$valid_pg = true;
	  			}
  				if (strtolower($contTitle) !== strtolower($contGet["conttitle"])) {
  					if (sql_nrows($dbtable,"WHERE conturl='$clean_contTitle".($htaccess4sef===true?'':"_$default_lg.php")."' AND contpg!='$x' ") > '0')
  						$valid_url = false	;// contlang='$lg' AND
	  			}
	  		} else {
	  			if (sql_nrows($dbtable,"WHERE conturl='$clean_contTitle".($htaccess4sef===true?'':"_$default_lg.php")."' ") > '0')
	  				$valid_url = false;// contlang='$lg' AND
	  		}
  			if (!isset($valid_pg))
  				$valid_pg = true; // linked to $-cont
  			if (!isset($valid_url))
  				$valid_url = true; // on hold
  		}

		if ((in_array('1',$admin_priv) && (!$contEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contEntry)))
		 || (!in_array('1',$admin_priv) && ((
		 	   !$contTitle || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contTitle)
		 	|| is_dir($getcwd.$up."$clean_contTitle") 
		 	|| !$contEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contEntry)
		 	|| !$contOrient || !in_array($contOrient, $array_orient)
		 	|| ($valid_url === false) || (!$valid_url === true)
		 	|| ((!$contStatut || ($valid_pg === false)) && (($send == $sauverString) || ($send == $sauverString.' Intro')))
		 	)))
		) {
$error .= '<b>'.$erreurString.'!</b><br />'.$listecorrectionString.'<ul>';
			if (!$contTitle || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contTitle))
$error .= '<li>'.$titreString.' > '.$error_invmiss.'</li>';
			if (is_dir($getcwd.$up."$clean_contTitle"))
$error .= '<li>'.$titreString.' > '.$error_inv.' ('.$dejaString.' '.$existantString.' <a href="'.$mainurl.$clean_contTitle.'" target="_blank">'.$clean_contTitle.'</a>)</li>';
			if (!$contEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contEntry))
$error .= '<li>'.$descriptionString.' > '.$error_invmiss.'</li>';
			if (!$contOrient || !in_array($contOrient, $array_orient))
$error .= '<li>'.$menuString.' position > '.$error_invmiss.'</li>';
			if (($valid_url === false) || (!$valid_url === true))
$error .= '<li>'.$urlString.' > '.$error_inv.' ('.$dejaString.' '.$existantString.' <a href="'.$mainurl.$clean_contTitle.($htaccess4sef===true?'':"_$default_lg.php").'" target="_blank">'.$clean_contTitle.($htaccess4sef===true?'':"_$default_lg.php").'</a>)</li>'	;
			if (!$contStatut && (($send == $sauverString) || ($send == $sauverString.' Intro')))
$error .= '<li>'.$statutString.' > '.$error_invmiss.'</li>';
			if (($valid_pg === false) && (($send == $sauverString) || ($send == $sauverString.' Intro')))
$error .= '<li>'.$menuString.' > '.$error_inv.'</li>';
$error .= '</ul><a href="javascript:history.back()//">'.$retourString.'</a>';
		} else {
			
			if	($contType == 'scroller')	$x = '10777'	;
			if	($contType == 'leftlinks')	$x = '10888'	;
			if	($contType == 'toplinks')	$x = '10999'	;
			if	($x == '10777')	$contType = 'scroller'	;
			if	($x == '10888')	$contType = 'leftlinks'	;
			if	($x == '10999')	$contType = 'toplinks'	;
			if	($x == '0')	$contTitle = $accueilString	;

			if (($send == $sauverString) || ($send == $sauverString.' Intro')) {
				if (!is_array($this_contmenu))
					$this_contmenu = array($this_contmenu);
				if (isset($contMenu)) {
					if (in_array($contMenu, $this_contmenu)) {
						$contMenu = $contGet["contmenu"];
					} else {
						if ($contGet["contmenu"] !== '') {
							if ($contMenu !== '') {
								$contMenu = $contGet["contmenu"]."|".$contMenu;
							} else {
								$contMenu = $contGet["contmenu"];
							}
						}
					}
				} else {
					$contMenu = "";
				}
			}

			$values = ""; // new
			$editrapport = "";

			include $getcwd.$up.$urladmin.'menu_pagine.php';
			include $getcwd.$up.$urladmin.'html_index.php';
			
			foreach($array_lang as $keylg) {
				if (($send == $sauverString) || ($send == $sauverString.' Intro')) {
					if ($keylg == $default_lg) {
						$default_lg_pg_htm = $clean_contTitle;
					} else {
						$default_lg_pg_htm = space2underscore(sql_getone($dbtable,"WHERE contpg='$x' AND contlang='$default_lg' ","conttitle"));
					}
					if ($send == $sauverString) {
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php'	;
						$conturlhtm = $default_lg_pg_htm.'_'.$keylg.'.htm';
					} else {
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php';
					}
					if ($lg == $default_lg) {
						if (($contTitle !== $contGet["conttitle"])) {//($x !== '1') &&
						  	if (($root_writable === true) && @file_exists($getcwd.$up.$dir_urlintro.($htaccess4sef===true?$contGet["conturl"]:substr($contGet["conturl"],0,-7)).'_'.$keylg.'.php'))
  								if	(rename($getcwd.$up.$dir_urlintro.($htaccess4sef===true?$contGet["conturl"]:substr($contGet["conturl"],0,-7)).'_'.$keylg.'.php',$getcwd.$up.$dir_urlintro.$conturl))
  									$editrapport .= '<br />'.$dir_urlintro.($htaccess4sef===true?$contGet["conturl"]:substr($contGet["conturl"],0,-7)).'_'.$keylg.'.php <font color="Red">> RENAME ></font> <a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a> ,<br /> <br />';
							if ($send == $sauverString) {
	  						    if ($root_writable === true)
	  								if	($html_site === true)
	  									if	(rename($getcwd.$up.$dir_urlintro.($htaccess4sef===true?$contGet["conturl"]:substr($contGet["conturl"],0,-7)).'_'.$keylg.'.htm',$getcwd.$up.$dir_urlintro.$conturlhtm))
	  									$editrapport .= '<br />'.$dir_urlintro.($htaccess4sef===true?$contGet["conturl"]:substr($contGet["conturl"],0,-7)).'_'.$keylg.'.htm <font color="Red">> RENAME ></font> <a href="'.$mainurl.$dir_urlintro.$conturlhtm.'" target="_blank">'.$dir_urlintro.$conturlhtm.'</a> ,<br /> <br />';
							}
						}
						$other_pgs = sql_get($dbtable,"WHERE contpg='$x' AND contlang='".$keylg."' ","conttitle,contentry,contstatut,contlogo,contmenu,conturl,conttype,contorient,contmetadesc,contmetakeyw");
						if ($other_pgs[0] == '.')
							$other_pgs["conttitle"] = '.';
						$sql_update = ""; // modifies changes on other than default pages if original title AND/OR entry matches
						if (($other_pgs["conttitle"] == $contGet["conttitle"])
						 || ($other_pgs["contentry"] == $contGet["contentry"])
						 || ($other_pgs["contstatut"] == $contGet["contstatut"])
						 || ($other_pgs["contmenu"] == $contGet["contmenu"])
						) {
							if (!isset($contMenu))
								$contMenu = "";
							if (!in_array('1',$admin_priv))
								if ($other_pgs["conttitle"] == $contGet["conttitle"])
									$sql_update .= ", conttitle='$contTitle', conturl='".($htaccess4sef===true?$clean_contTitle:$conturl)."' ";
							if ($other_pgs["contentry"] == $contGet["contentry"])
								$sql_update .= ", contentry='$contEntry' ";
							if (!in_array('1',$admin_priv)) {
								if ($other_pgs["contstatut"] == $contGet["contstatut"])
									$sql_update .= ", contstatut='$contStatut' ";
								if ($other_pgs["contmenu"] == $contGet["contmenu"])
									$sql_update .= ", contlogo='$contPriv' ";
								if ($other_pgs["contmenu"] == $contGet["contmenu"])
									$sql_update .= ", contmenu='$contMenu' ";
	  							// conturl
	  							if ($other_pgs["conttype"] == $contGet["conttype"])
	  								$sql_update .= ", conttype='$contType' ";
	  							if ($other_pgs["contorient"] == $contGet["contorient"])
	  								$sql_update .= ", contorient='$contOrient' ";
							}

							if ($send == $sauverString) {
								if ($html_site === true) {
									$Fnm = $getcwd.$up.$dir_urlintro.$conturlhtm;
									$inF = fopen($Fnm,"w+");
									fwrite($inF,html_index($keylg));
									fclose($inF);
									if (($x == '1') && ($lg == $keylg)) {
									//	unlink($getcwd.$up.$dir_urlintro.'index.htm');
										if (copy($getcwd.$up.$dir_urlintro.'index_'.$default_lg.'.htm',$getcwd.$up.$dir_urlintro.'index.htm'))
										$editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.'index.htm" target="_blank">'.$dir_urlintro.'index.htm</a> ,<br />'	;
									}
									$editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.$conturlhtm.'" target="_blank">'.$dir_urlintro.$conturlhtm.'</a> ,<br />';
								}
								if ($contType == 'scroller') { // fill scroller content
									$Fjsnmscroller = $getcwd.$up.$dir_urlintro.'-scroller_'.$keylg.'.js';
									$injsFscroller = fopen($Fjsnmscroller,"w+");
									fwrite($injsFscroller,scroller_js($keylg));
									fclose($injsFscroller);
									$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.'-scroller_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-scroller_'.$keylg.'.js</a> ,<br />';
								} else {
									if (($contGet["conttitle"] !== $contTitle) || ($contGet["contstatut"] !== $contStatut)) {
										$Fjsnm = $getcwd.$up.$dir_urlintro.'-menu_pagine_'.$keylg.'.js';
										$injsF = fopen($Fjsnm,"w+");
										fwrite($injsF,html_js($keylg));
										fclose($injsF);
										$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.'-menu_pagine_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-menu_pagine_'.$keylg.'.js</a> ,-<br />';
									}
								}
							}
						} // end all basic params check

						if ($sql_update !== "") {
							$setq = " SET ".((($other_pgs["conttitle"] == $contGet["conttitle"]) || ($other_pgs["contentry"] == $contGet["contentry"]))?" contupdate=$dbtime, contupdateby='$admin_name' ":" contpg='$x' ")." $sql_update ".($keylg==$lg?($contMetadesc==$desc?'':", contmetadesc='$contMetadesc' ").($contMetakeyw==$keyw?'':", contmetakeyw='$contMetakeyw' "):'');
          					$whereq = " WHERE contpg='$x' AND contlang='".$keylg."' ";
          					$update_one_page = sql_update($dbtable,$setq,$whereq,"conturl");
        				}

					} else { // end of lg == default_lg //

						if ($lg == $keylg) {

							$setq = " SET contupdate=$dbtime, contstatut='$contStatut', contupdateby='$admin_name', conttitle='$contTitle', contentry='$contEntry', contlogo='$contPriv', contmenu='$contMenu', conttype='$contType', conturl='".($htaccess4sef===true?$clean_contTitle:$conturl)."', contorient='$contOrient' ".($contMetadesc==$desc?'':", contmetadesc='$contMetadesc' ")." ".($contMetakeyw==$keyw?'':", contmetakeyw='$contMetakeyw' ")." ";
							$whereq = " WHERE contpg='$x' AND contlang='".$keylg."' ";
							$update_one_page = sql_update($dbtable,$setq,$whereq,"conturl");

							if ($send == $sauverString) {
                				if ($root_writable === true)
									if ($html_site === true) {
										$Fnm = $getcwd.$up.$dir_urlintro.$conturlhtm;
										$inF = fopen($Fnm,"w+");
										fwrite($inF,html_index($keylg));
										fclose($inF);
										$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.$conturlhtm.'" target="_blank">'.$dir_urlintro.$conturlhtm.'</a> ,,<br />';
									}
								$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a> ,,<br />';
								if ($contType == 'scroller') { // fill scroller content
									$Fjsnmscroller = $getcwd.$up.$dir_urlintro.'-scroller_'.$keylg.'.js';
									if (file_exists($Fjsnmscroller) || ($root_writable === true)) {
  										$injsFscroller = fopen($Fjsnmscroller,"w+");
	  									fwrite($injsFscroller,scroller_js($keylg));
	  									fclose($injsFscroller);
	  									$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.'-scroller_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-scroller_'.$keylg.'.js</a> ,,<br />';
	  								}
								} else {
									if (($contGet["conttitle"] !== $contTitle) || ($contGet["contstatut"] !== $contStatut)) {
										$Fjsnm = $getcwd.$up.$dir_urlintro.'-menu_pagine_'.$keylg.'.js';
	  									if (file_exists($Fjsnm) || ($root_writable === true)) {
	  										$injsF = fopen($Fjsnm,"w+");
	  										fwrite($injsF,html_js($keylg));
	  										fclose($injsF);
	  										$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.'-menu_pagine_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-menu_pagine_'.$keylg.'.js</a> ,,-<br />';
	  									}
									}
								}
							}

						} // end lg = keylg

					} // end lg check against default_lg

				} else { // end of save - start ajout

					if ($x == '1') {
						$default_lg_pg_htm = 'index';
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php';
            			if ($root_writable === true)
							if (copy($getcwd.$up.$urladmin.'_tpl_.php',$getcwd.$up.$dir_urlintro.$conturl)) {
								chmod($getcwd.$up.$dir_urlintro.$conturl, 0755);
              					$editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a><br />';
              					if (copy($getcwd.$up.$urladmin.'_tpl_.php',$getcwd.$up.$dir_urlintro.$clean_contTitle.'_'.$keylg.'.php'))
              						$editrapport .= '<a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a><br />';
        					}
						$values = "('', 'Y', $dbtime, '$admin_name', $dbtime, '$admin_name', '$x', '".$keylg."', '$contTitle', '$contEntry', '".($htaccess4sef===true?$clean_contTitle:$conturl)."', '$contPriv', '$contmenu', '$contType', '$contOrient', '$contMetadesc', '$contMetakeyw')";
						if (sql_nrows($dbtable,"WHERE contpg='$x' AND contlang='".$keylg."' AND conttitle='$contTitle' ") == 0) {
							$sql = "INSERT INTO $dbtable (`contid`, `contstatut`, `contdate`, `contdateby`, `contupdate`, `contupdateby`, `contpg`, `contlang`, `conttitle`, `contentry`, `conturl`, `contlogo`, `contmenu`, `conttype`, `contorient`, `contmetadesc`, `contmetakeyw`) VALUES $values ;";
							$addquery = mysqli_query($connection, $sql);
						}
						if ($send == $ajouterString) {
              				if ($root_writable === true)
								if ($html_site === true) {
									$Fnm = $getcwd.$up.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm';
                					$inF = fopen($Fnm,"w+");
  									fwrite($inF,html_index($keylg));
  									fclose($inF);
	  								if (($keylg == $default_lg) && copy($Fnm,$getcwd.$up.$dir_urlintro.'index.htm'))
                						$editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.'index.htm" target="_blank">'.$dir_urlintro.'index.htm</a><br />'	;
	  									$editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm" target="_blank">'.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm</a><br />';
								}
							$Fjsnm = $getcwd.$up.$dir_urlintro.'-menu_pagine_'.$keylg.'.js';
          					if (file_exists($Fjsnm) || ($root_writable === true)) {
	  							$injsF = fopen($Fjsnm,"w+");
	  							fwrite($injsF,html_js($keylg));
	  							fclose($injsF);
	  							$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.'-menu_pagine_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-menu_pagine_'.$keylg.'.js</a><br />';
								}
							$Fjsnmscroller = $getcwd.$up.$dir_urlintro.'-scroller_'.$keylg.'.js';
          					if (file_exists($Fjsnmscroller) || ($root_writable === true)) {
	  							$injsFscroller = fopen($Fjsnmscroller,"w+");
	  							fwrite($injsFscroller,scroller_js($keylg));
	  							fclose($injsFscroller);
	  							$editrapport .= $modifieString.' <a href="'.$mainurl.'-scroller_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-scroller_'.$keylg.'.js</a><br />';
								}
						}

					} else { // x != 1

						$default_lg_pg_htm = space2underscore(sql_getone($dbtable,"WHERE contpg='$x' AND contlang='$default_lg' ","conttitle"));
						if ($lg == $default_lg)
							$default_lg_pg_htm = $clean_contTitle;
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php';
        				if ($root_writable === true)
							if (copy($getcwd.$up.$urladmin.'_tpl_.php',$getcwd.$up.$dir_urlintro.$conturl)) {
					  			chmod($getcwd.$up.$dir_urlintro.$conturl, 0755);
          						$editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a><br />';
        					}
						$values = "('', 'Y', $dbtime, '$admin_name', $dbtime, '$admin_name', '$x', '".$keylg."', '$contTitle', '$contEntry', '".($htaccess4sef===true?$clean_contTitle:$conturl)."', '$contPriv' ,'$contmenu' ,'$contType', '$contOrient', '$contMetadesc', '$contMetakeyw')";
						if (sql_nrows($dbtable,"WHERE contpg='$x' AND contlang='".$keylg."' AND conttitle='$contTitle' ") == 0) {
							$sql = "INSERT INTO $dbtable (`contid`, `contstatut`, `contdate`, `contdateby`, `contupdate`, `contupdateby`, `contpg`, `contlang`, `conttitle`, `contentry`, `conturl`, `contlogo`, `contmenu`, `conttype`, `contorient`, `contmetadesc`, `contmetakeyw`) VALUES $values ";
							$addquery = mysqli_query($connection, $sql);
						}
						if ($send == $ajouterString) {
          					if ($root_writable === true)
								if ($html_site === true) {
									$Fnm = $getcwd.$up.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm';
									if ($lg == $default_lg)
										$Fnm = $getcwd.$up.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm';
									$inF = fopen($Fnm,"w+");
									fwrite($inF,html_index($keylg));
									fclose($inF);
									$editrapport .= '<a href="'.$mainurl.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm" target="_blank">'.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm</a><br />';
								}
							if ($contType == 'scroller') {
								$Fjsnmscroller = $getcwd.$up.$dir_urlintro.'-scroller_'.$keylg.'.js';
            					if (file_exists($Fjsnmscroller) || ($root_writable === true)) {
	  								$injsFscroller = fopen($Fjsnmscroller,"w+");
	  								fwrite($injsFscroller,scroller_js($keylg));
	  								fclose($injsFscroller);
	  								$editrapport .= '<a href="'.$mainurl.$dir_urlintro.'-scroller_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-scroller_'.$keylg.'.js</a><br />';
								}
							} else {
								$Fjsnm = $getcwd.$up.$dir_urlintro.'-menu_pagine_'.$keylg.'.js';
            					if (file_exists($Fjsnm) || ($root_writable === true)) {
	  								$injsF = fopen($Fjsnm,"w+");
	  								fwrite($injsF,html_js($keylg));
	  								fclose($injsF);
	  								$editrapport .= '<a href="'.$mainurl.$dir_urlintro.'-menu_pagine_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-menu_pagine_'.$keylg.'.js</a><br />';
								}
							}
						} // end save = add
					} // end x 1 or not
				} // end save or not
			} // end loop

			$contSaved = sql_get($dbtable," WHERE contpg='$x' AND contlang='$lg' ","conttitle,contentry,contmenu,contupdate,contupdateby");
			$title = $contSaved["conttitle"];
			$editInfo = '<!-- <div class="clear"></div> --><div style="float:right;text-align:right;clear:both;">'.$derniereString.' '.$modificationString.' '.$parString.' <b>'.$contSaved["contentry"].'</b> ('.$dateString.' <b>'.$contSaved["conttitle"].'</b>) <font color="Red">|</font> <a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=edit';
			if (strstr($send, " Intro"))
				$editInfo .= 'emintro';
			$editInfo .= '">'.$modifierString.' '.$cettepageString.'</a></div>';
  $content .= $editInfo;
			if	(!strstr($send, " Intro") && ($editrapport != ''))
  $content .= '<div style="border:1px solid green;padding:10px;text-align:left;"><h1>'.$rapportString.'</h1>'.$editrapport.'<br /></div>';
  $content .= '<br /> <br /><h3>'.$title.'</h3><div class="content_body">'.str_replace("content/",$up."content/",$contSaved["contmenu"]).'</div><br /><hr />'.$editInfo;
      		$notice .= $enregistrementString.' '.$effectueString;
		}

	} else if (!in_array('1',$admin_priv) && ($send == 'deletepg')) {
		if	(($x == '10777') || (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='$x' ") == '0'))
    		{Header("Location: $redirect");Die();}
		if	(($x == '10888') || (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='$x' ") == '0'))
    		{Header("Location: $redirect");Die();}
		if	(($x == '10999') || (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='$x' ") == '0'))
    		{Header("Location: $redirect");Die();}

		$editrapport = "";
		$read = mysqli_query($connection, "SELECT * FROM $dbtable WHERE contpg='$x' ");
		$nrows = mysqli_num_rows($read);
		
		include $getcwd.$up.$urladmin.'menu_pagine.php';
		include $getcwd.$up.$urladmin.'html_index.php';

  $content .= '<br />';
    	$default_url = sql_getone($tblcont,"WHERE contpg='$x' AND contlang='$default_lg' ","conturl");
		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($read);
			$conturl = $default_url.($htaccess4sef===true?"_".$row["contlang"].".php":'');
			$conturlhtm = substr($conturl,0,-4).'.htm';
			if (strlen($x) == '1') { // delete the pages in folder and VOID the db entry
				if ($x == '1') {
	  				if ($i == $nrows-1)
						$error .= $error_request.' you cannot erase the index...<br />'	;
  				} else {
          			$unlink_conturl = true;
          			if ($root_writable === true)
          				$unlink_conturl = unlink($getcwd.$up."$conturl");
					if ($unlink_conturl === true) {
						if (sql_nrows($dbtable,"WHERE contpg='.($x+1).' ") == '0') {
							$deleteq = sql_del($dbtable,"WHERE contid='".$row["contid"]."' ");
							if ($deleteq == '0')
								$editrapport .= '<br /><font color="Red">> '.ucfirst($class_conjugaison->plural($effaceString,'F','1')).' ></font> '.$conturl.' <br />';
          					else
          						$content .= $error_request.'<br />';
						} else {
							$updateq = sql_update($dbtable,"SET contdate='', contdateby='', contupdate='', contupdateby='', contstatut='N', conttitle='VOID".$x."', contentry='', contmenu='', contlogo='', conttype='' ","WHERE contlang='".$row["contlang"]."' AND contpg='$x' ","contpg");
	  						if ($updateq[0] == $x)
	  							$content .= '';
          					else
          						$content .= $error_request.'<br />';
						}
						if ($root_writable === true)
		  					if ($html_site === true) {
		  						unlink($getcwd.$up."$conturlhtm");
		  						$editrapport .= '<br /><font color="Red">> '.ucfirst($class_conjugaison->plural($effaceString,'F','1')).' ></font> '.$conturlhtm.' <br 	/>';
		  					}
						$Fjsnm = $getcwd.$up.'-menu_pagine_'.$row["contlang"].'.js';
        				if (file_exists($Fjsnm) || ($root_writable === true)) {
	    					$injsF = fopen($Fjsnm,"w+");
	    					fwrite($injsF,html_js($row["contlang"]));
	    					fclose($injsF);
	    					$editrapport .= '<a href="'.$mainurl.'-menu_pagine_'.$row["contlang"].'.js" target="_blank">-menu_pagine_'.$row["contlang"].'.js</a><br />';
						}
					} else {
						$error .= $error_delete.' ('.$fichierString.': '.$conturl.')<br />'	;
					}
				}
			} else { // delete the pages in folder and DELETE the db entry
		        $unlink_conturl = true;
	    	    if ($root_writable === true)
	        		unlink($getcwd.$up."$conturl");
				if ($unlink_conturl === true) {
					$deleteq = sql_del($dbtable,"WHERE contlang='".$row["contlang"]."' AND contpg='$x' ");
					if ($deleteq == '0')
						$editrapport .= '<br /><font color="Red">> '.ucfirst($class_conjugaison->plural($effaceString,'F','1')).' ></font> '.$conturl.' <br />' ;
	          		else
	          			$content .= $error_request.'<br />';
	          		if ($root_writable === true)
						if ($html_site === true) {
							unlink($getcwd.$up."$conturlhtm");
							$editrapport .= '<br /><font color="Red">> '.ucfirst($class_conjugaison->plural($effaceString,'F','1')).' ></font> '.$conturlhtm.' <br />';
						}
					$Fjsnm = $getcwd.$up.'-menu_pagine_'.$row["contlang"].'.js';
	          		if (file_exists($Fjsnm) || ($root_writable === true)) {
	  					$injsF = fopen($Fjsnm,"w+");
	  					fwrite($injsF,html_js($row["contlang"]));
	  					fclose($injsF);
	  					$editrapport .= '<a href="'.$mainurl.'-menu_pagine_'.$row["contlang"].'.js" target="_blank">-menu_pagine_'.$row["contlang"].'.js</a><br />';
	  				}
				} else {
					$error .= $error_delete.' ('.$fichierString.': '.$conturl.')<br />'	;
				}
			}
		}
		if (!strstr($content, $error_request) && !strstr($content, $error_delete)) {
			$notice .= $pageString.' '.$class_conjugaison->plural($effaceString,'F','1').' !';
		    $content = '<br /><div style="border:1px solid red;padding:10px;text-align:left;"><h1>'.$rapportString.'</h1>'.$editrapport.'<br /></div>';
		}

	} else if (!in_array('1',$admin_priv) && ($send == "delmenu")) {

		if ((!isset($menuId)) || !preg_match("/^[0-9]+\$/", $menuId) || ($menuId == ''))
			{Header("Location: $redirect");Die();}
		$menuId = strip_tags($menuId);
		$new_contmenu = "";
		if (in_array($menuId, $this_contmenu)) {
			for ($k=0;$k<count($this_contmenu);$k++) {
				if ($this_contmenu[$k] !== $menuId)
					$new_contmenu .= $this_contmenu[$k]."|";
			}
			$new_contmenu = substr($new_contmenu,0,-1);
		}
		$updatequery = sql_update($dbtable,"SET contmenu='$new_contmenu' "," WHERE contpg='$x' ","contmenu");
		$new_contmenu = explode("|", $updatequery[0]);
		if (!is_array($new_contmenu))
			$new_contmenu = array($new_contmenu);
		if (!in_array($menuId, $new_contmenu)) {
			$notice .= $menuString.' '.$effaceString.'<br /><a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$retourString.'</a><br />';
		} else {
			$error .= $error_delete.' | <a href="javascript:history.back()//">'.$retourString.'</a><br />';
		}

	} else { // send = different...
		Header("Location: $redirect");Die();
	}

} else { // logged_in = false;
	Header("Location: $redirect");Die();
}
