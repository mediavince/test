<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

// note to self, this is a major headache source!! even back in the days, i dreaded editing this!

//          $notice .= mvtrace(__FILE__,__LINE__)." $x<br />";

if (!isset($root_writable))
$root_writable = false;
if (!isset($htaccess4sef))
$htaccess4sef = false;
if ($htaccess4sef === false) {
  if ($root_writable === false)
  echo "Please contact the webmaster!";
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
if	(isset($send) && (strstr($send,' Intro') || ($send == 'editemintro') || ($send == 'deleteintro')))	$dbtable = $tblintro	;
else	$dir_urlintro = ""	;

$array_conttypes = array_unique(array_merge($array_modules,$array_fixed_modules,array("scroller","leftlinks","toplinks","contact","profil","")));

//if (!isset($contlogo)) $contlogo = ""; // here we use that for contPriv

if (!isset($contmenu)) $contmenu = "";

$array_linkedmenus = "";
$array_linkedmenus = array($array_linkedmenus);
$all_contmenus = @mysql_query("SELECT `contmenu` FROM $dbtable WHERE contmenu!='' ");
$nrows = @mysql_num_rows($all_contmenus);
for ($i=0;$i<$nrows;$i++) {
	$row = mysql_fetch_array($all_contmenus);
	$this_contmenus = explode("|", $row["contmenu"]);
	if	(!is_array($this_contmenus))	$this_contmenus = array($this_contmenus)	;
	if	(!empty($array_linkedmenus))	$array_linkedmenus = array_merge($array_linkedmenus,$this_contmenus)	;
	if	(!empty($array_linkedmenus))	$array_linkedmenus = array_unique($array_linkedmenus)	;
}
$x_array = array($x,(($x*10)+1),(($x*100)+11),(($x*1000)+111));
$xplus1_array = array(($x+1),((($x+1)*10)+1),((($x+1)*100)+11),((($x+1)*1000)+111));

if ($logged_in === true) {
  $error_img = '';
  $notice_img = '';
	$contGet = sql_get($dbtable," WHERE contpg='$x' AND contlang='$lg' ","conttitle,contentry,contstatut,contlogo,contmenu,conturl,conttype,contorient,contmetadesc,contmetakeyw");
	if	($x == '10777')	$contType = 'scroller'	;
	if	($x == '10888')	$contType = 'leftlinks'	;
	if	($x == '10999')	$contType = 'toplinks'	;
  $contPriv = $contGet[3];
	if ($contPriv == "") $contPriv = "1";
	$contUpdate = sql_get($dbtable," WHERE contpg='$x' AND contlang='$lg' ","contupdate");
	$contUpdateby = sql_get($dbtable," WHERE contpg='$x' AND contlang='$lg' ","contupdateby");
	$y_menu = "";
	$menu1 = "";
	if (!in_array('1',$admin_priv))
	$menu1 .= '<div style="float:right;text-align:right;padding-left:3px;"><a href="?x=z&amp;y=1">Extra '.$adminString.' '.$optionsString.'</a></div>';
	if (($contGet[0] == '.') || isset($contTitle)) {
		if	(isset($contTitle))	$this_title = $contTitle	;
		else {
			$this_title = "_NEW PAGE_";
			$title = $this_title;
		}
		if (isset($send) && (($send == 'editemintro') || strstr($send, " Intro")))
		$this_title = sql_getone($tblcont," WHERE contpg='$x' AND contlang='$lg' ","conttitle");
		$menu1 .= '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$modificationString.' '.$detexteString.' '.$surString.' <i>'.$this_title.'</i></a>';
	} else {
		$menu1 .= '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$modificationString.' '.$detexteString.' '.$surString.' <i>'.sql_getone($tblcont," WHERE contpg='$x' AND contlang='$lg' ","conttitle").'</i></a>';
	}
  if (!in_array('1',$admin_priv)) {
  	if (isset($send) && (stristr($send, "delete") || !stristr($send, "edit"))) {
  	} else {
  		if (($lg == $default_lg) && !in_array($contGet[6],array("toplinks","leftlinks","scroller")) && ($contGet[0] !== '.')) {
  			if (is_float(($x+1)/10) || (($x1subok === false) && ($x == '9')))
  				if (sql_nrows($dbtable,"WHERE contpg='".($x+1)."' AND contlang='$lg' ") == 0)
          $menu1 .= ' > <a href="?lg='.$lg.'&amp;x='.($x+1).'">'.$ajouterString.' 1 '.$pageString.'</a>' ;
  		  if ((sql_nrows($dbtable,"WHERE contpg='".(($x*10)+1)."' AND contlang='$lg' ") == 0) && ((($x1subok === false) && ($x > '1')) || ($x1subok === true)) && (strlen($x) <= 3))
        $menu1 .= ' >> <a href="?lg='.$lg.'&amp;x='.(($x*10)+1).'">'.$ajouterString.' 1 '.$menuString.'</a>' ;
  			if ((array_intersect($x_array,$array_linkedmenus) == $x_array) || ((sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='".($x+1)."' ") == '0') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='".(($x*10)+1)."' ") == '0'))) {
          if ($x > '1')
          $menu1 .= ' | <font color="Red"> !! <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=deletepg" onclick="return confirm(\''.$confirmationeffacementString.'\');">'.$effacerString.' '.$cettepageString.'</a> !!<!--  (plus multilingual versions) --></font>' ;
  			}
  		}
  		if (isset($send) && ($send == 'editemintro') && (sql_nrows($dbtable,"WHERE contpg='$x' ") > 0))
  		$menu1 .= ' | <font color="Red"> !! <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=deleteintro" onclick="return confirm(\''.$confirmationeffacementString.'\');">'.$effacerString.' '.$cettepageString.' Blog</a> !!<!--  (plus multilingual versions) --></font>' ;
  	}
  }
	$inst_contGet4 = $contGet[4]; // contmenu
	$this_contmenu = explode("|", $inst_contGet4);
	if	(!is_array($this_contmenu))	$this_contmenu = array($this_contmenu)	;
	if ($x !== 'z') {
		if  ($y == '1') $y_menu .= '<b>'.$menu1.'</b>'  ;
		else  $y_menu .= '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;y=1">'.$menu1.'</a>'  ;
	}
  $content = '<!-- <p> -->'.$y_menu.'<!-- </p> -->';
	$editInfo = '<div style="float:right;text-align:right;">'.$derniereString.' '.$modificationString.' '.$parString.' <b>'.$contUpdateby[0].'</b> ('.$dateString.' <b>'.$contUpdate[0].'</b>) <font color="Red">|</font> <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$modifierString.' '.$cettepageString.'</a></div>';
#######################################################
#######################################################
//if ((isset($send) && ($send == 'edit')) || ($y == 'media'))
//if (($y == 'media'))
//include 'mediaadmin.php';
#######################################################
#######################################################
	if (!isset($send)) {
		if ($contGet[0] == '.') {
			$reload = $local.'?lg='.$lg.'&x='.$x.'&y='.$y.'&send=edit'; // no &amp; here or in any passed url for redirect
			Header("Location: $reload");Die();
		} else {
      $content .= $editInfo.'<br /> <br /><h3>'.$contGet[0].'</h3>';
      if ($mod_priv === false) {
  			if ($contGet[3] == '') {
  			} else {
          $content .= '<img '.show_img_attr(sql_getone($tblcontphoto,"WHERE contphotoid='$contGet[3]' ","contphotoimg")).' src="'.$up.sql_getone($tblcontphoto,"WHERE contphotoid='$contGet[3]' ","contphotoimg").'" align="right" hspace="5" vspace="5" alt="'.$contGet[0].'" border="0" />';
  			}
  		}
      $content .= '<div class="content_body">'.str_replace("content/",$up."content/",$contGet[1]).'</div><br /><hr />'.$editInfo;
		}
#######################################################
#######################################################
	} else if (($send == "edit") || ($send == 'editemintro')) {


	/*
		$delete_media = "";
// ########################## PICS ###############################
		$contPhotocount = sql_nrows($tblcontphoto, ""); // WHERE contphotocontid='$x'
		if ($tinyMCE === false) {
			if ($contPhotocount > '0') {
				$photoListe = '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=upldphoto">'.$ajouterString.' 1 '.$photoString.'</a><hr />';
				$photoListe .= $contPhotocount.' '.$class_conjugaison->plural($photodispoString,'F',$contPhotocount).'.<br />';
				$read = @mysql_query("SELECT * FROM $tblcontphoto ");
				if	($contPhotocount > 3)	$photoListe .= '<div style="overflow:auto;height:170px;width:100px;">';
				for ($i=0;$i<$contPhotocount;$i++) {
					$row = @mysql_fetch_array($read);
  				$ext = explode('.',strrev($row["contphotoimg"]));
  				$ext = strtolower(strrev($ext[0]));
  				if (in_array($ext,$array_swf_ext)) {
						$photoListe .= '<a href="#" onclick="insertFlash(\''.$row["contphotoimg"].'\');return(false)"><sup>'.str_replace('_',' ',$row["contphotoimg"]).'</sup></a> | <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=delphoto&amp;contphotoId='.$row["contphotoid"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a><br />';
					} else { // ($ext == "gif") || ($ext == "jpg") || ($ext == "jpeg") || ($ext == "png") || //
						$photoListe .= '<a href="#" onclick="insertImage(\''.$row["contphotoimg"].'\');return(false)"><img src="'.$mainurl.$row["contphotoimg"].'" width="50" height="50" hspace="5" vspace="5" border="0" alt="'.$row["contphotodesc"].'" /></a> <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=delphoto&amp;contphotoId='.$row["contphotoid"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a><br />';
					}
				}
				if	($contPhotocount > 3)	$photoListe .= '</div>';
			} else {
				$photoListe = '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=upldphoto">'.$ajouterString.' 1 '.$photoString.'</a><hr />'.$pasdeString.' '.$photodispoString.' !<br />';
			}
		} else { // $tinyMCE === true
			$photoListe = '<div><div style="float:left;width:50%;text-align:right;"><a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=upldphoto">'.$ajouterString.' 1 '.$photoString.'</a> |</div>'; // write javascript for import in tinymce.
			$tinyMCE_photos = '<div style="float:left;overflow:auto;height:110px;width:49%;min-width:300px;">';
			$tinyMCE_photos .= '<table cellspacing="0" cellpadding="2" border="0" style="border:none;width: 98%;"><tr><td align="center" style="border:none;">'; // '<div style="float:right;text-align:center;">';//
			$tinyMCE_flashs = '<div style="float:left;overflow:auto;height:100px;width:49%;padding-left:30px;">'.$nonString.' '.$photoString.'<br />';
			$read = @mysql_query("SELECT * FROM $tblcontphoto ");
			$loop_tinyMCE_photos = "";
			$loop_tinyMCE_flashs = "";
			for ($i=0;$i<$contPhotocount;$i++) {
				$row = @mysql_fetch_array($read);
				$ext = explode('.',strrev($row["contphotoimg"]));
				$ext = strtolower(strrev($ext[0]));
				if (in_array($ext,$array_swf_ext)) {
					$loop_tinyMCE_flashs .= '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=delphoto&amp;contphotoId='.$row["contphotoid"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a> | '.str_replace("_", " ", $row["contphotodesc"]).' ('.$ext.')<br />';
				} else { // ($ext == "gif") || ($ext == "jpg") || ($ext == "jpeg") || ($ext == "png") || //
					if ($loop_tinyMCE_photos !== "")	$loop_tinyMCE_photos .= '</td><td align="center" style="border:none;">'; // '</div><div style="float:right;text-align:center;">'; //
					$loop_tinyMCE_photos .= '<img src="'.$mainurl.$row["contphotoimg"].'" width="50" height="50" hspace="5" vspace="5" border="0" alt="'.$row["contphotodesc"].'" /><br /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=delphoto&amp;contphotoId='.$row["contphotoid"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a>';
				}
			}
			if ($loop_tinyMCE_photos != '')
			$tinyMCE_photos .= $loop_tinyMCE_photos.'</td><tr></table></div>'; //'</div></div>'; //
			else
			$tinyMCE_photos = '';
			if ($loop_tinyMCE_flashs != '')
			$tinyMCE_flashs .= $loop_tinyMCE_flashs.'</div>';
			else
			$tinyMCE_flashs = '';
			$delete_media .= $tinyMCE_photos.$tinyMCE_flashs;
		}
// end photo list
// ########################## DOC ###############################
		$contDoccount = sql_nrows($tblcontdoc, ""); // WHERE contdoccontid='$x'
		if ($tinyMCE === false) {
			$docListe = '<hr /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=uplddoc">'.$ajouterString.' 1 '.$docString.'</a>';
	// List docs from other pages only
			$contDoccount = sql_nrows($tblcontdoc,"");
			if ($contDoccount > '0') {
				$docListe .= '<hr />'.$contDoccount.' '.$class_conjugaison->plural($docdispoString,'M',$contDoccount).'.<br />';
				$read = @mysql_query("SELECT * FROM $tblcontdoc ");
				for ($i=0;$i<$contDoccount;$i++) {
					$row = @mysql_fetch_array($read);
  				$ext = explode('.',strrev($row["contdoc"]),2);
  				$ext = strtolower(strrev($ext[0]));
					$docListe .= '<a href="'.$mainurl.$row["contdoc"].'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" border="0" alt="'.$ouvrirString.'" /></a> | <a href="#" onclick="insertDoc(\''.$row["contdoc"].'\');return(false)">'.str_replace("_", " ", $row["contdocdesc"]).'</a> | <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=deldoc&amp;contdocId='.$row["contdocid"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a><br />';
				}
			} else {
				$docListe .= '<hr />'.$pasdeString.' '.$docdispoString.' !';
			}
			$docListe .= '<hr />';
		} else { // $tinyMCE === true
			$docListe = '<div style="float:left;width:50%;text-align:left;">| <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=uplddoc">'.$ajouterString.' 1 '.$docString.'</a></div></div>'; // write javascript for import in tinymce.
			$tinyMCE_docs = '<div style="float:right;overflow:auto;height:110px;min-width:300px;width:49%;text-align:left;">';
			$read = @mysql_query("SELECT * FROM $tblcontdoc ");
			for ($i=0;$i<$contDoccount;$i++) {
				$row = @mysql_fetch_array($read);
				$ext = explode('.',strrev($row["contdoc"]),2);
				$ext = strtolower(strrev($ext[0]));
				if	($tinyMCE_docs !== "")	$tinyMCE_docs .= "<br />"	;
				$tinyMCE_docs .= '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=deldoc&amp;contdocId='.$row["contdocid"].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a> | <a href="'.$mainurl.$row["contdoc"].'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" border="0" alt="'.$ouvrirString.' '.$ext.'" /> '.str_replace("_", " ", $row["contdocdesc"]).'</a>';
			}
			$tinyMCE_docs .= '</div>';
			$delete_media = $tinyMCE_docs.$delete_media;
		}
// end doc list
	//	if ($send == 'edit') {

*/


		if ((!is_array($contGet)) || ($contGet[0] == '.')) { // new

      if (!in_array('1',$admin_priv)) {
  $content .= $form_method.'<input type="submit" name="send" value="'.$ajouterString;
  			if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
				if ($tinyMCE === false)
  $content .= ' | <a href="javascript: alert(\''.$helpEdit.'\')" title="Help" alt="Help">'.$aideString.'</a>';
  $content .= '<br /><label for="contStatut"> <b>'.$statutString.'</b></label> <select name="contStatut">'.gen_selectoption($tblenum,$contGet[2],'','statut').'</select> | <label for="contOrient"> <b>'.$menuString.' position </b></label> <select name="contOrient">'.gen_selectoption($array_orient,'center','','').'</select><br />';
  			if ($send == 'editemintro')
  $content .= '<label for="contType"> <b>'.$typeString.' (default replace)</b></label><select name="contType"><option value="1"> (replace) </option><option value="2"> < (before) </option><option value="3"> > (after) </option></select>';
  			else
  $content .= '<label for="contType"> <b>'.$pageString.' (empty = default '.$titreString.')</b></label> <select name="contType"'.($tinyMCE===true?' onchange="typeArray=[0,\'scroller\',1,\'leftlinks\',2,\'toplinks\'];tinyMCE.execCommand(\'mceInsertContent\',false,(typeArray.indexOf(this.options[this.selectedIndex].value)==-1?\'[\'+this.options[this.selectedIndex].value+\']\':\'\'));"':'').'>'.gen_selectoption($array_conttypes,(isset($contType)?$contType:$contGet[6]),'','').'</select>';//<!-- <option value=""></option><option value="">>> '.$achoisirString.' </option><option value="scroller">scroller</option><option value="leftlinks">leftlinks</option><option value="toplinks">toplinks</option><option value="profil">profil</option> <option value="contact">contact</option><option value="login">login</option><option value="map">map</option><option value="partner">partner</option><option value="search">search</option> -->
				if ($tinyMCE === true) {
				  $content .= '<br />click to insert code '.$pourString.' '.$typeString.' :<br />';
          foreach(array_intersect($array_conttypes,array_unique(array_merge($array_modules,$array_fixed_modules))) as $key)
          $content .= ' <a onmousedown="tinyMCE.execCommand(\'mceInsertContent\',false,\'['.$key.']\');" href="javascript:;">['.$key.']</a> ';
        }
        if ($mod_priv === true)
  $content .= '<br /><div><div style="float:left;"><label for="contPriv"> <b> '.$privilegeString.' : </b></label> &nbsp;</div>'.gen_inputcheck($tblenum,((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'1':sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")),((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'':"%' AND enumtitre!='1' AND enumtype LIKE '%"),'privilege')."</div><!--<div class='clear'></div>--> <br />";
  $content .= '<br /><label for="contTitle"> <b>'.$titreString.'</b> <!-- ('.$saufString.' '.$accueilString.')  --></label> <input type="text" name="contTitle" value="'.(isset($contType)?$contType:'').'" style="width: 97%" /><br />';
  $content .= '<hr />META DESCRIPTION & KEYWORDS (1 line)<br /><label for="contMetadesc"> <b>'.$descriptionString.'</b> </label><br /><textarea name="contMetadesc" rows="3" cols="40" style="width: 97%; height: 50px; min-height: 50px;">'.($contGet[8]==""?$desc:$contGet[8]).'</textarea><br /><label for="contMetakeyw"> <b>keywords</b> </label><br /><textarea name="contMetakeyw" rows="3" cols="40" style="width: 97%; height: 50px; min-height: 50px;">'.($contGet[9]==""?$keyw:$contGet[9]).'</textarea><hr />';
  $content .= '<label for="contEntry"> <b>'.$descriptionString.'</b> '.(isset($contType)&&($contType=='toplinks')?'<i> !! edit with HTML !! </i>':'').'</label><br />'.$text_style.'<textarea id="'.($tinyMCE===false?'uldescription':'elm1').'" name="contEntry" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.(isset($contType)&&($contType=='toplinks')?'<div style="float:left;overflow:hidden;width:110px;margin-left:-25px;"><ul><li><a title="MediaVince" href="http://www.mediavince.com/" target="_blank">MediaVince</a></li></ul></div> <ul><li><a title="MediaVince" href="http://www.mediavince.com/" target="_blank">MediaVince</a> <br /></li><li><span class="current"> '.$codename.' </span> &rsaquo;</li></ul>':'').'</textarea><p>'.$text_style.'</p>';
  			if ($tinyMCE === false)
  $content .= $text_style_js;
  $content .= '<br /> <br /><!-- <div class="clear"></div> --><input type="submit" name="send" value="'.$ajouterString;
  			if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
				if ($tinyMCE === false)
  $content .= ' | <A href="javascript: alert(\''.$helpEdit.'\')" alt="Help">'.$aideString.'</a>';
  $content .= '<br /> <br /></form>';
  		} else {
  $content = '<div style="background-color: #FA5;width: 90%;border: 1px solid red;padding: 5px;margin: 5px;text-align:center;">'.ucfirst($accesString.' '.$refuseString).' >> '.$privilegeString.' || <a href="javascript:history.back()//">'.$retourString.'</a></div>';
      }





		} else { // update





				$contGet[0] = strip_tags($contGet[0]);
  $content .= $form_method.'<input type="submit" name="send" value="'.$sauverString;
  			if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
				if ($tinyMCE === false)
  $content .= ' | <a href="javascript: alert(\''.addslashes($helpEdit).'\')" title="Help" alt="Help">'.$aideString.'</a>';
  $content .= '<br />';
/************************************ // DISABLED FOR THIS SITE
				if ($contGet[3] == '') {
  $content .= '<a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=upldphoto&logo">'.$ajouterString.' 1 '.$logoString.'</a><br /> <br />';
				} else {
  $content .= '<img '.show_img_attr(sql_getone($tblcontphoto,"WHERE contphotoid='$contGet[3]' ","contphotoimg")).' src="'.$mainurl.sql_getone($tblcontphoto,"WHERE contphotoid='$contGet[3]' ","contphotoimg").'" align="right" hspace="5" vspace="5" border="0" alt="'.$contGet[0].'" /><p style="text-align:right"><a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=delphoto&contphotoId='.$contGet[3].'&contphotoDesc='.sql_getone($tblcontphoto,"WHERE contphotoid='$contGet[3]' ","contphotodesc").'&logo" onclick="return confirm(\''.$confirmationeffacementString.'\');">'.$effacerString.' '.$logoString.'</a></p><br /> <br />';
				}
************************************/
	if (in_array('1',$admin_priv)) { // only pagine
    //$content .= '<input type="hidden" name="contStatut" value="'.$contGet[2].'" /><input type="hidden" name="contOrient" value="'.$contGet[7].'" /><input type="hidden" name="contType" value="'.$contGet[6].'" />';
    $content .= '<br /><b>'.$contGet[0].'</b><br />';
	} else {
    $content .= '<label for="contStatut"> <b>'.$statutString.'</b></label> <select name="contStatut">'.gen_selectoption($tblenum,$contGet[2],'','statut').'</select> | <label for="contOrient"> <b>'.$menuString.' position </b></label> <select name="contOrient">'.gen_selectoption($array_orient,$contGet[7],'','').'</select><br />';
  			if ($send == 'editemintro')
    $content .= '<label for="contType"> <b>'.$typeString.' (default replace)</b></b></label><select name="contType"><option value="'.$contGet[6].'"> >> '.$contGet[6].'</option><option value="1"> (replace) </option><option value="2"> < (before) </option><option value="3"> > (after) </option></select><br />';
  			else
    $content .= '<label for="contType"> <b>'.$pageString.' (empty = default '.$titreString.')</b></label><select name="contType"'.($tinyMCE===true?' onchange="typeArray=[0,\'scroller\',1,\'leftlinks\',2,\'toplinks\'];tinyMCE.execCommand(\'mceInsertContent\',false,(typeArray.indexOf(this.options[this.selectedIndex].value)==-1?\'[\'+this.options[this.selectedIndex].value+\']\':\'\'));"':'').'>'.gen_selectoption($array_conttypes,(isset($contType)?$contType:$contGet[6]),'','').'</select><br />';//<!-- <option value="'.$contGet[6].'"> >> '.$contGet[6].'</option><option value="scroller">scroller</option><option value="leftlinks">leftlinks</option><option value="toplinks">toplinks</option><option value="contact">contact</option><option value="profil">profil</option><option value="login">login</option><option value="map">map</option><option value="partner">partner</option><option value="search">search</option><option value=""></option> -->
				if ($tinyMCE === true) {
				  $content .= 'click to insert code '.$pourString.' '.$typeString.' :<br />';
          foreach(array_intersect($array_conttypes,array_unique(array_merge($array_modules,$array_fixed_modules))) as $key)
          $content .= ' <a onmousedown="tinyMCE.execCommand(\'mceInsertContent\',false,\'['.$key.']\');" href="javascript:;">['.$key.']</a> ';
        }
        $priv_param0 = ((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?$contPriv:((strlen($x)>1)&&in_array('1',explode("|",$contPriv))?sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo"):$contPriv));
        $priv_param1 = ((strlen($x)>1)&&in_array('1',explode("|",sql_getone($tblcont,"WHERE contpg='".substr($x,0,-1)."' ","contlogo")))?'':((strlen($x)>1)&&in_array('1',explode("|",$contPriv))?'':(strlen($x)>1?"%' AND enumtitre!='1' AND enumtype LIKE '%":'')));
        if ($mod_priv === true)
    $content .= '<div><div style="float:left;"><label for="contPriv"> <b> '.$privilegeString.' : </b></label> &nbsp;</div>'.gen_inputcheck($tblenum,$priv_param0,$priv_param1,'privilege')."</div><!--<div class='clear'></div>--><br /> <br />";
    $content .= '<label for="contTitle"> <b>'.$titreString.'</b> <!-- ('.$saufString.' '.$accueilString.')  --></label><input type="text" name="contTitle" value="'.$contGet[0].'" style="width:97%;" /><br />';
    $content .= '<hr />META DESCRIPTION & KEYWORDS (1 line)<br /><label for="contMetadesc"> <b>'.$descriptionString.'</b> </label><br /><textarea name="contMetadesc" rows="3" cols="40" style="width: 97%;height:50px;min-height:50px;">'.($contGet[8]==""?$desc:$contGet[8]).'</textarea><br /><label for="contMetakeyw"> <b>keywords</b> </label><br /><textarea name="contMetakeyw" rows="3" cols="40" style="width:97%;height:50px;min-height:50px;">'.($contGet[9]==""?$keyw:$contGet[9]).'</textarea><hr /><br />';
  }
  $content .= '<label for="contEntry"> <b>'.$descriptionString.'</b> '.((isset($contType)&&($contType=='toplinks'))||($contGet[6]=='toplinks')?'<i> !! edit with HTML !! </i>':'').'</label><br />'.$text_style;
				if ($tinyMCE === false)
  $content .= '<div style="float:left;min-width:500px;width:80%;">';//<table width="100%" border="2" cellspacing="0" cellpadding="0"><tr><td width="100%" valign="top">';
  $content .= '<textarea id="'.($tinyMCE===false?'uldescription':'elm1').'" name="contEntry" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.format_edit($contGet[1],"edit").'</textarea><p>'.$text_style.'</p>';



/*
				if ($tinyMCE === false)
  $content .= '</div><div style="float:left;min-width:100px;width:18%;" class="admineditnotiny">'.$photoListe.''.$docListe.'&nbsp;</div>';
//  $content .= '</td><td rowspan="2" align="center" valign="top">'.$photoListe.''.$docListe.'&nbsp;</td></tr><tr><td valign="top">';

				if ($tinyMCE === true)
  $content .= $photoListe.$docListe.$delete_media.'<!-- <div class="clear"></div> -->';
*/



				if ($tinyMCE === false)
  $content .= $text_style_js;
  $content .= '<br /> <br /><div style="float:left;width:98%;text-align:left;"><input type="submit" name="send" value="'.$sauverString;
  			if ($send == 'editemintro')
  $content .= ' Intro';
  $content .= '" onclick="ajaxSave();" /> | <a href="javascript:history.back()//">'.$retourString.'</a>';
				if ($tinyMCE === false)
  $content .= ' | <a href="javascript: alert(\''.addslashes($helpEdit).'\')" title="Help" alt="Help">'.$aideString.'</a><br /> <br />';//</td></tr></table>';
  $content .= '</div></form>';
		}



#######################################################
#######################################################
	} else if (	(!in_array('1',$admin_priv) && (($send == $ajouterString) || ($send == $ajouterString.' Intro'))) ||
				      (($send == $sauverString) || ($send == $sauverString.' Intro'))
			) {
		if (($contGet[6] !== 'scroller') && ($contType == 'scroller') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND conttype='scroller' ") > 0)) {
			Header("Location: ".$local."?lg=".$lg."&amp;x=10777&amp;y=".$y."&amp;send=edit&amp;redirected");
			die();
		}
		if (($contGet[6] !== 'leftlinks') && ($contType == 'leftlinks') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND conttype='leftlinks' ") > 0)) {
			Header("Location: ".$local."?lg=".$lg."&amp;x=10888&amp;y=".$y."&amp;send=edit&amp;redirected");
			die();
		}
		if (($contGet[6] !== 'toplinks') && ($contType == 'toplinks') && (sql_nrows($dbtable,"WHERE contlang='$lg' AND conttype='toplinks' ") > 0)) {
			Header("Location: ".$local."?lg=".$lg."&amp;x=10999&amp;y=".$y."&amp;send=edit&amp;redirected");
			die();
		}

		if	($contEntry == "<p></p>")	$contEntry = ""	;
		if	($contEntry == "<p>&nbsp;</p>")	$contEntry = ""	;
	//	$contEntry = html_encode($contEntry);
		if ($tinyMCE === false) {
			$contEntry = nl2br($contEntry);
			$contEntry = format_edit($contEntry,"show");
		}
		//checking module integrity
		foreach($mod_array as $key_mod_array => $value_mod_array) {
			if (strstr($contEntry,"[".$value_mod_array))
			$notice .= check_module_content($value_mod_array,$contEntry);
		}
    if (in_array('1',$admin_priv)) {
      $contTitle = $contGet[0];
  		$contStatut	= $contGet[2];
  		$contPriv = $contGet[3];
  		$cont	= $contGet[4];
  		$contType	= $contGet[6];
  		$contOrient	= $contGet[7];
  		$contMetadesc = $contGet[8];
  		$contMetakeyw = $contGet[9];
    } else {
  		$contTitle = strip_tags($contTitle);

  		$clean_contTitle = space2underscore($contTitle);

  		$contTitle = html_encode($contTitle);
  		$contMetadesc = html_encode(strip_tags($contMetadesc));
  		if (($contMetadesc == "") && ($x == '1')) $contMetadesc = $meta_desc;
  		$contMetakeyw = html_encode(strip_tags($contMetakeyw));
  		if (($contMetakeyw == "") && ($x == '1')) $contMetakeyw = $meta_keyw;
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
  			if (isset($cont)) {
  				$cont = strip_tags($cont);
  				if	(!isset($valid_pg))	$valid_pg = false	;
  				if	(sql_nrows($dbtable,"WHERE contpg='$cont' ") == '1')	$valid_pg = true	;
  				if	($cont == '')	$valid_pg = true	;
  			}
  			if (strtolower($contTitle) !== strtolower($contGet[0])) {
  				if	(sql_nrows($dbtable,"WHERE conturl='$clean_contTitle".($htaccess4sef===true?'':"_$default_lg.php")."' AND contpg!='$x' ") > '0')	$valid_url = false	;// contlang='$lg' AND
  			}
  		} else {
  			if	(sql_nrows($dbtable,"WHERE conturl='$clean_contTitle".($htaccess4sef===true?'':"_$default_lg.php")."' ") > '0')	$valid_url = false	;// contlang='$lg' AND
  		}
  		if	(!isset($valid_pg))	$valid_pg = true	; // linked to $-cont
  		if	(!isset($valid_url))	$valid_url = true	; // on hold
  	}
		if ((in_array('1',$admin_priv) && (!$contEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contEntry))) || (!in_array('1',$admin_priv) && (
      (!$contTitle || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contTitle) ||
		  is_dir($getcwd.$up."$clean_contTitle") ||
			!$contEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contEntry) ||
			!$contOrient || !in_array($contOrient, $array_orient) ||
			($valid_url === false) || (!$valid_url === true) ||
			((!$contStatut || ($valid_pg === false)) && (($send == $sauverString) || ($send == $sauverString.' Intro'))))) )
			) {
  $error .= '<b>'.$erreurString.'!</b><br />'.$listecorrectionString.'<ul>';
			if ( !$contTitle || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contTitle) )
  $error .= '<li>'.$titreString.' > '.$error_invmiss.'</li>'	;
			if ( is_dir($getcwd.$up."$clean_contTitle") )
  $error .= '<li>'.$titreString.' > '.$error_inv.' ('.$dejaString.' '.$existantString.' <a href="'.$mainurl.$clean_contTitle.'" target="_blank">'.$clean_contTitle.'</a>)</li>'	;
			if ( !$contEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $contEntry) )
  $error .= '<li>'.$descriptionString.' > '.$error_invmiss.'</li>'	;
			if ( !$contOrient || !in_array($contOrient, $array_orient) )
  $error .= '<li>'.$menuString.' position > '.$error_invmiss.'</li>'	;
			if ( ($valid_url === false) || (!$valid_url === true) )
  $error .= '<li>'.$urlString.' > '.$error_inv.' ('.$dejaString.' '.$existantString.' <a href="'.$mainurl.$clean_contTitle.($htaccess4sef===true?'':"_$default_lg.php").'" target="_blank">'.$clean_contTitle.($htaccess4sef===true?'':"_$default_lg.php").'</a>)</li>'	;
			if (!$contStatut && (($send == $sauverString) || ($send == $sauverString.' Intro')))
  $error .= '<li>'.$statutString.' > '.$error_invmiss.'</li>'	;
			if (($valid_pg === false) && (($send == $sauverString) || ($send == $sauverString.' Intro')))
  $error .= '<li>'.$menuString.' > '.$error_inv.'</li>'	;
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
				if (!is_array($this_contmenu)) $this_contmenu = array($this_contmenu);
				if (isset($cont)) {
					if (in_array($cont, $this_contmenu)) {
						$cont = $contGet[4];
					} else {
						if ($contGet[4] !== '') {
							if ($cont !== '') {
								$cont = $contGet[4]."|".$cont;
							} else {
								$cont = $contGet[4];
							}
						}
					}
				} else $cont = "";
			}
			$values = ""; // new
			$editrapport = "";

			include $getcwd.$up.$urladmin.'menu_pagine.php';
			include $getcwd.$up.$urladmin.'html_index.php';
			foreach($array_lang as $keylg) {
				if (($send == $sauverString) || ($send == $sauverString.' Intro')) {
				  /*
					if ($x == '1') {
						$default_lg_pg_htm = 'index';
					} else {
					*/
						if ($keylg == $default_lg) {
							$default_lg_pg_htm = $clean_contTitle;
						} else {
							$default_lg_pg_htm = space2underscore(sql_getone($dbtable,"WHERE contpg='$x' AND contlang='$default_lg' ","conttitle"));
						}
					//}
					if ($send == $sauverString) {
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php'	;
						$conturlhtm = $default_lg_pg_htm.'_'.$keylg.'.htm'	;
					} else {
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php'	;
					}
					if ($lg == $default_lg) {
						if (($contTitle !== $contGet[0])) {//($x !== '1') &&
						  if (($root_writable === true) && @file_exists($getcwd.$up.$dir_urlintro.($htaccess4sef===true?$contGet[5]:substr($contGet[5],0,-7)).'_'.$keylg.'.php'))
  							if	(rename($getcwd.$up.$dir_urlintro.($htaccess4sef===true?$contGet[5]:substr($contGet[5],0,-7)).'_'.$keylg.'.php',$getcwd.$up.$dir_urlintro.$conturl))
  							$editrapport .= '<br />'.$dir_urlintro.($htaccess4sef===true?$contGet[5]:substr($contGet[5],0,-7)).'_'.$keylg.'.php <font color="Red">> RENAME ></font> <a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a> ,<br /> <br />'	;
							if ($send == $sauverString) {
  						  if ($root_writable === true)
  								if	($html_site === true)
  									if	(rename($getcwd.$up.$dir_urlintro.($htaccess4sef===true?$contGet[5]:substr($contGet[5],0,-7)).'_'.$keylg.'.htm',$getcwd.$up.$dir_urlintro.$conturlhtm))
  									$editrapport .= '<br />'.$dir_urlintro.($htaccess4sef===true?$contGet[5]:substr($contGet[5],0,-7)).'_'.$keylg.'.htm <font color="Red">> RENAME ></font> <a href="'.$mainurl.$dir_urlintro.$conturlhtm.'" target="_blank">'.$dir_urlintro.$conturlhtm.'</a> ,<br /> <br />'	;
							}
						}
						$other_pgs = sql_get($dbtable,"WHERE contpg='$x' AND contlang='".$keylg."' ","conttitle,contentry,contstatut,contlogo,contmenu,conturl,conttype,contorient,contmetadesc,contmetakeyw");
						//conttitle,contentry,contstatut,contlogo,contmenu,conturl,conttype,contorient,contmetadesc,contmetakeyw
						$sql_update = ""; // modifies changes on other than default pages if original title AND/OR entry matches
						if (($other_pgs[0] == $contGet[0]) || ($other_pgs[1] == $contGet[1]) || ($other_pgs[2] == $contGet[2]) || ($other_pgs[3] == $contGet[3])) {
							if	(!isset($cont))	$cont = ""	;
							if (!in_array('1',$admin_priv))
  							if	($other_pgs[0] == $contGet[0])	$sql_update .= ", conttitle='$contTitle', conturl='".($htaccess4sef===true?$clean_contTitle:$conturl)."' ";
  						if	($other_pgs[1] == $contGet[1])	$sql_update .= ", contentry='$contEntry' ";
							if (!in_array('1',$admin_priv)) {
  							if	($other_pgs[2] == $contGet[2])	$sql_update .= ", contstatut='$contStatut' ";
  							if	($other_pgs[3] == $contGet[3])	$sql_update .= ", contlogo='$contPriv' ";
  							if	($other_pgs[4] == $contGet[4])	$sql_update .= ", contmenu='$cont' ";
  							// conturl
  							if	($other_pgs[6] == $contGet[6])	$sql_update .= ", conttype='$contType' ";
  							if	($other_pgs[7] == $contGet[7])	$sql_update .= ", contorient='$contOrient' ";
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
									if (($contGet[0] !== $contTitle) || ($contGet[2] !== $contStatut)) {
										$Fjsnm = $getcwd.$up.$dir_urlintro.'-menu_pagine_'.$keylg.'.js';
										$injsF = fopen($Fjsnm,"w+");
										fwrite($injsF,html_js($keylg));
										fclose($injsF);
										$editrapport .= $modifieString.' <a href="'.$mainurl.$dir_urlintro.'-menu_pagine_'.$keylg.'.js" target="_blank">'.$dir_urlintro.'-menu_pagine_'.$keylg.'.js</a> ,-<br />';
									}
								}
							}
						}
						if ($sql_update !== "") {
  						$setq = " SET ".((($other_pgs[0] == $contGet[0]) || ($other_pgs[1] == $contGet[1]))?" contupdate=$dbtime, contupdateby='$admin_name' ":" contpg='$x' ")." $sql_update ".($keylg==$lg?($contMetadesc==$desc?'':", contmetadesc='$contMetadesc' ").($contMetakeyw==$keyw?'':", contmetakeyw='$contMetakeyw' "):'');
              $whereq = " WHERE contpg='$x' AND contlang='".$keylg."' ";
              $update_all_pages = sql_update($dbtable,$setq,$whereq,"conturl");
            }
					} else { // end of lg == default_lg //
						if ($lg == $keylg) {
							$update_all_pages = sql_update($dbtable," SET contupdate=$dbtime, contstatut='$contStatut', contupdateby='$admin_name', conttitle='$contTitle', contentry='$contEntry', contlogo='$contPriv', contmenu='$cont', conttype='$contType', conturl='".($htaccess4sef===true?$clean_contTitle:$conturl)."', contorient='$contOrient' ".($contMetadesc==$desc?'':", contmetadesc='$contMetadesc' ")." ".($contMetakeyw==$keyw?'':", contmetakeyw='$contMetakeyw' ")." "," WHERE contpg='$x' AND contlang='".$keylg."' ","conturl");
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
									if (($contGet[0] !== $contTitle) || ($contGet[2] !== $contStatut)) {
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
						}
					}
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
							$addquery = @mysql_query("
                												INSERT INTO $dbtable
                												(`contid`, `contstatut`, `contdate`, `contdateby`, `contupdate`, `contupdateby`, `contpg`, `contlang`, `conttitle`, `contentry`, `conturl`, `contlogo`, `contmenu`, `conttype`, `contorient`, `contmetadesc`, `contmetakeyw`)
                												VALUES
                												$values
                												");
						}
						if ($send == $ajouterString) {
              if ($root_writable === true)
							if ($html_site === true) {
								$Fnm = $getcwd.$up.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm';
                $inF = fopen($Fnm,"w+");
  							fwrite($inF,html_index($keylg));
  							fclose($inF);
	  						if	(($keylg == $default_lg) && copy($Fnm,$getcwd.$up.$dir_urlintro.'index.htm'))
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
					} else {
						$default_lg_pg_htm = space2underscore(sql_getone($dbtable,"WHERE contpg='$x' AND contlang='$default_lg' ","conttitle"));
						if	($lg == $default_lg)	$default_lg_pg_htm = $clean_contTitle	;
						$conturl = $default_lg_pg_htm.'_'.$keylg.'.php';
            if ($root_writable === true)
						if (copy($getcwd.$up.$urladmin.'_tpl_.php',$getcwd.$up.$dir_urlintro.$conturl)) {
						  chmod($getcwd.$up.$dir_urlintro.$conturl, 0755);
              $editrapport .= $ajoutString.' <a href="'.$mainurl.$dir_urlintro.$conturl.'" target="_blank">'.$dir_urlintro.$conturl.'</a><br />';
            }
						$values = "('', 'Y', $dbtime, '$admin_name', $dbtime, '$admin_name', '$x', '".$keylg."', '$contTitle', '$contEntry', '".($htaccess4sef===true?$clean_contTitle:$conturl)."', '$contPriv' ,'$contmenu' ,'$contType', '$contOrient', '$contMetadesc', '$contMetakeyw')";
						if (sql_nrows($dbtable,"WHERE contpg='$x' AND contlang='".$keylg."' AND conttitle='$contTitle' ") == 0) {
							$addquery = @mysql_query("
                												INSERT INTO $dbtable
                												(`contid`, `contstatut`, `contdate`, `contdateby`, `contupdate`, `contupdateby`, `contpg`, `contlang`, `conttitle`, `contentry`, `conturl`, `contlogo`, `contmenu`, `conttype`, `contorient`, `contmetadesc`, `contmetakeyw`)
                												VALUES
                												$values
                												");
						}
						if ($send == $ajouterString) {
              if ($root_writable === true)
							if ($html_site === true) {
								$Fnm = $getcwd.$up.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm';
								if	($lg == $default_lg)	$Fnm = $getcwd.$up.$dir_urlintro.$default_lg_pg_htm.'_'.$keylg.'.htm';
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
						}
					}
				}
			} // end loop
			$contGet = sql_get($dbtable," WHERE contpg='$x' AND contlang='$lg' ","contupdate,contupdateby,conttitle,contentry");
			$title = $contGet[2];
			$editInfo = '<!-- <div class="clear"></div> --><div style="float:right;text-align:right;">'.$derniereString.' '.$modificationString.' '.$parString.' <b>'.$contGet[1].'</b> ('.$dateString.' <b>'.$contGet[0].'</b>) <font color="Red">|</font> <a href="?lg='.$lg.'&amp;x='.$x.'&amp;send=edit';
			if	(strstr($send, " Intro"))
			$editInfo .= 'emintro'	;
			$editInfo .= '">'.$modifierString.' '.$cettepageString.'</a></div>';
  $content .= $editInfo;
			if	(!strstr($send, " Intro") && ($editrapport != ''))
  $content .= '<div style="border:1px solid green;padding:10px;text-align:left;"><h1>'.$rapportString.'</h1>'.$editrapport.'<br /></div>';
  $content .= '<br /> <br /><h3>'.$title.'</h3><div class="content_body">'.str_replace("content/",$up."content/",$contGet[3]).'</div><br /><hr />'.$editInfo;
      $notice .= $enregistrementString.' '.$effectueString;
		}
#######################################################
#######################################################
	} else if (!in_array('1',$admin_priv) && ($send == 'deletepg')) {
		if	(($x == '10777') || (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='$x' ") == '0'))
    {Header("Location: $redirect");Die();}
		if	(($x == '10888') || (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='$x' ") == '0'))
    {Header("Location: $redirect");Die();}
		if	(($x == '10999') || (sql_nrows($dbtable,"WHERE contlang='$lg' AND contpg='$x' ") == '0'))
    {Header("Location: $redirect");Die();}
		$editrapport = "";
		$read = @mysql_query("SELECT * FROM $dbtable WHERE contpg='$x' ");
		$nrows = @mysql_num_rows($read);
		include $getcwd.$up.$urladmin.'menu_pagine.php';
		include $getcwd.$up.$urladmin.'html_index.php';
  $content .= '<br />';
    $default_url = sql_getone($tblcont,"WHERE contpg='$x' AND contlang='$default_lg' ","conturl");
		for ($i=0;$i<$nrows;$i++) {
			$row = mysql_fetch_array($read);
			$conturl = $default_url.($htaccess4sef===true?"_".$row["contlang"].".php":'');
			$conturlhtm = substr($conturl,0,-4).'.htm';
			if (strlen($x) == '1') {		// delete the pages in folder and VOID the db entry
				if ($x == '1') {
  				//	$updateq = sql_update($dbtable,"SET contdate='', contdateby='', contupdate='', contupdateby='', contstatut='Y', conttitle='VOID".$x."', contentry='in construction/en construction/in costruzione', contmenu='', contlogo='', conttype='' ","WHERE contlang='".$row["contlang"]."' AND contpg='$x' ","contpg");
  				//	if	($updateq[0] == $x)	$content .= ''	;	else
  				if ($i == $nrows-1)
  $error .= $error_request.' you cannot erase the index...<br />'	;
  			} else {
          $unlink_conturl = true;
          if ($root_writable === true)
          $unlink_conturl = unlink($getcwd.$up."$conturl");
  				if ($unlink_conturl === true) {
  					if (sql_nrows($dbtable,"WHERE contpg='.($x+1).' ") == '0') {
  						$deleteq = sql_del($dbtable,"WHERE contid='".$row["contid"]."' ");
  						if	($deleteq == '0')	$editrapport .= '<br /><font color="Red">> '.ucfirst($class_conjugaison->plural($effaceString,'F','1')).' ></font> '.$conturl.' <br />'  ;
              else	$content .= $error_request.'<br />'	;
  					} else {
  						$updateq = sql_update($dbtable,"SET contdate='', contdateby='', contupdate='', contupdateby='', contstatut='N', conttitle='VOID".$x."', contentry='', contmenu='', contlogo='', conttype='' ","WHERE contlang='".$row["contlang"]."' AND contpg='$x' ","contpg");
  						if	($updateq[0] == $x)	$content .= ''	;
              else	$content .= $error_request.'<br />'	;
  					}
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
			} else {		// delete the pages in folder and DELETE the db entry
        $unlink_conturl = true;
        if ($root_writable === true)
        unlink($getcwd.$up."$conturl");
				if ($unlink_conturl === true) {
					$deleteq = sql_del($dbtable,"WHERE contlang='".$row["contlang"]."' AND contpg='$x' ");
					if	($deleteq == '0')	$editrapport .= '<br /><font color="Red">> '.ucfirst($class_conjugaison->plural($effaceString,'F','1')).' ></font> '.$conturl.' <br />'  ;
          else	$content .= $error_request.'<br />'	;
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
		if	(!strstr($content, $error_request) && !strstr($content, $error_delete))	{
		  $notice .= $pageString.' '.$class_conjugaison->plural($effaceString,'F','1').' !';
		  $content = '<br /><div style="border:1px solid red;padding:10px;text-align:left;"><h1>'.$rapportString.'</h1>'.$editrapport.'<br /></div>';
		}
	//	{Header("Location: $redirect");Die();}
	//cut mediaadmin in media_temp.php under SQL
#######################################################EFFACER menu
#######################################################DELETE menu
	} else if (!in_array('1',$admin_priv) && ($send == "delmenu")) {
		if	((!isset($menuId)) || !preg_match("/^[0-9]+\$/", $menuId) || ($menuId == ''))
		  {Header("Location: $redirect");Die();}
		$menuId = strip_tags($menuId);
		$new_contmenu = "";
		if (in_array($menuId, $this_contmenu)) {
			for ($k=0;$k<count($this_contmenu);$k++) {
				if ($this_contmenu[$k] !== $menuId) $new_contmenu .= $this_contmenu[$k]."|";
			}
			$new_contmenu = substr($new_contmenu,0,-1);
		}
		$updatequery = sql_update($dbtable,"SET contmenu='$new_contmenu' "," WHERE contpg='$x' ","contmenu");
		$new_contmenu = explode("|", $updatequery[0]);
		if	(!is_array($new_contmenu))	$new_contmenu = array($new_contmenu)	;
		if (!in_array($menuId, $new_contmenu)) {
  $notice .= $menuString.' '.$effaceString.'<br /><a href="'.$local.'?lg='.$lg.'&amp;x='.$x.'&amp;send=edit">'.$retourString.'</a><br />';
		} else {
  $error .= $error_delete.' | <a href="javascript:history.back()//">'.$retourString.'</a><br />';
		}
#######################################################
#######################################################
	} else { // send = different...
		Header("Location: $redirect");Die();
	}
} else { // logged_in = false;
	Header("Location: $redirect");Die();
}

