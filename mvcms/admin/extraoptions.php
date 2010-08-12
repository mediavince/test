<?PHP #۞ # ADMIN
if (stristr($_SERVER["PHP_SELF"],'extraoptions.php')) {
	include '_security.php';
	Header("Location: $redirect");Die();
}

if (!isset($bakdbString)) $bakdbString = "Faire une sauvegarde de la base de donn&eacute;es.";
if (!isset($upldbString)) $upldbString = "Envoyer un fichier SQL.";
if (!isset($optdbString)) $optdbString = "Optimiser la base de donn&eacute;es.";
if (!isset($syncString)) $syncString = "Synchroniser le site";
if (!isset($svnupString)) $svnupString = "Mettre &agrave; jour avec svn";

$content = $admin_menu;
$content .= '<div style="float:left;width:100%;">';
$content .= '<div style="min-height:50px;">';
$content .= gen_form($lg,$x,$y).'<img src="'.$mainurl.'images/backup.png" align="left" title="'.$bakdbString.'" alt="'.$bakdbString.'" width="48" height="48px" border="0" /><input type="hidden" name="x" value="'.$x.'" /><input type="submit" name="bakdb" value="'.$bakdbString.'" /></form>';
  
  if (isset($_POST['bakdb'])) include '_bakdb.php';
  
  $content .= '</div><hr /><br /><div style="min-height:50px;">';
  if (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") == $admin_name) {
    $content .= gen_form($lg,$x,$y).'<img src="'.$mainurl.'images/dbrestore.png" align="left" title="'.$upldbString.'" alt="'.$upldbString.'" width="48" height="48px" border="0" /><input type="file" name="sql" /><br /><input type="submit" name="send" value="'.$upldbString.'" /></form>';
    if (isset($send) && ($send == $upldbString) && ($_FILES)) {
      $userfile_name = $_FILES["sql"]["name"];
      $movelocation = $getcwd.$up.$safedir.'uploaded_'.date('YmdHis')."_".$userfile_name."_.sql";
      $userfile_tmp = $_FILES["sql"]["tmp_name"];
      move_uploaded_file($userfile_tmp,$movelocation);
      $do = "mysql ".($dbhost==''?'':"-h$dbhost")." -u$dbuser -p$dbpass $dbname < $movelocation";
  $content .= "<hr />".exec($do,$output,$result).($result=='0'?'<div class="notice" style="text-align:left;">'.$class_conjugaison->plural($effectueString,'F','1').'</div>':'<div class="error" style="text-align:left;"> '.$error_inv.'<br />=> '.$result.' == '.$do.'</div>');
    }
    if (@file_exists('.svn')||@file_exists('../.git')||@file_exists('../../.git')) {
    	if (@file_exists('../../.git')) $up_instruction = "git pull"; // git in root or in subsub
    	else $up_instruction = "svn up";
    	$content .= '</div><hr /><br /><div style="min-height:50px;">';
      $content .= gen_form($lg,$x,$y).'<img src="'.$mainurl.'images/reload_f2.png" align="left" title="'.$svnupString.'" alt="'.$svnupString.'" width="32" height="32px" border="0" style="padding:8px;" /><input type="submit" name="send" value="'.$svnupString.'" /></form>';
      if (isset($send) && ($send == $svnupString)) {
        $do = "$up_instruction ../";
        $content .= "<hr />$up_instruction: ".exec($do,$output,$result).($result!=='0'?'<div class="notice" style="text-align:left;">'.$class_conjugaison->plural($effectueString,'F','1').': '.$result.'</div>':'<div class="error" style="text-align:left;"> '.$error_inv.'<br />=> '.$result.' == '.$do.'</div>');
      }
    }
  $content .= '</div><hr /><br /><div style="min-height:50px;">';
    $content .= gen_form($lg,$x,$y).'<img src="'.$mainurl.'images/checkin.png" align="left" title="'.$optdbString.'" alt="'.$optdbString.'" width="48" height="48px" border="0" /><input type="submit" name="send" value="'.$optdbString.'" /></form>';
    if (isset($send) && ($send == $optdbString)) {
      $do = "mysqlcheck -ao --auto-repair --silent ".($dbhost==''?'':"-h$dbhost")." -u$dbuser -p$dbpass $dbname";
      $content .= "<hr />REPAIR: ".exec(escapeshellcmd($do),$output,$result).($result=='0'?'<div class="notice" style="text-align:left;">'.$class_conjugaison->plural($effectueString,'F','1').'</div>':'<div class="error" style="text-align:left;"> '.$error_inv.'<br />=> '.$result.' == '.$do.'</div>');
    }
  $content .= '</div><hr /><br /><div style="min-height:50px;">';
  $content .= gen_form($lg,$x,$y).'<img src="'.$mainurl.'images/menu.png" align="left" title="'.$syncString.'" alt="'.$syncString.'" width="48" height="48px" border="0" /><input type="submit" name="send" value="'.$syncString.'" /></form>';
    if (isset($send) && ($send == $syncString)) {// && !in_array('1',$admin_priv))
      $editrapport = '<div class="clear"><br /></div>';
      sql_updateone($tblcont,"SET contlogo='1' ","WHERE contlogo='' ","contlogo");// set to 1 as default
  		foreach($array_lang as $keylg) {
  		  $default_metas = sql_get($tblcont,"WHERE contpg='1' AND contlang='$keylg' ","contmetadesc,contmetakeyw");
  		  $lg_desc = $default_metas[0];
  		  $lg_keyw = $default_metas[1];
        $editrapport .= "<div style=\"float:left;border:0 1px solid gray;width:32%;\">".(@file_exists($up.'images/'.$keylg.'.gif')?'<img src="'.$mainurl.'images/'.$keylg.'.gif" title="'.sql_stringit('lang',$keylg).'" alt="'.sql_stringit('lang',$keylg).'" style="float:left;height:24px;width:24px;" /> ':'')."<h3> Cleaning metas for ".sql_stringit("lang",$keylg)." matching :</h3>".(sql_updateone($tblcont,"SET contmetadesc='' ","WHERE contpg!='1' AND contlang='$keylg' AND contmetadesc='$lg_desc' ","conttitle")=='.'?'<img src="'.$mainurl.'images/success.png" width="14" height="14" title="done" alt="done" border="0" >':$errorString)." metadesc: $lg_desc<br />".(sql_updateone($tblcont,"SET contmetakeyw='' ","WHERE contpg!='1' AND contlang='$keylg' AND contmetakeyw='$lg_keyw' ","conttitle")=='.'?'<img src="'.$mainurl.'images/success.png" width="14" height="14" title="done" alt="done" border="0" >':$errorString)." metakeyw: $lg_keyw<br />";
        $editrapport .= "</div>";
      }
      $editrapport .= '<div class="clear"></div>';
      if ($root_writable === true) { #### IF NOT WRITABLE ROOT
  			include'zip.lib.php';
  			include'menu_pagine.php';
  			include'html_index.php';
  			$values = "";
        $pgext = ($html_site === true?'.htm':'.php');
        $editrapport .= '<h1 class="clear">'.strtoupper($pgext).'</h1>';
    		$zipfiles = $getcwd.$up."index$pgext|";
  			$read = @mysql_query("SELECT * FROM $tblcont ORDER BY contpg ASC ");
  			while($row = @mysql_fetch_array($read)) {
  				$contPg = $row["contpg"];
					$contTitle = $row["conttitle"];
  				//		$clean_contTitle = ($htaccess4sef===true?space2underscore($contTitle):space2underscore(sql_getone($tblcont,"WHERE contpg='$contPg' AND contlang='$default_lg' ","conttitle")));
					$clean_contTitle = space2underscore(sql_getone($tblcont,"WHERE contpg='$contPg' AND contlang='$default_lg' ","conttitle"));
					$contPg = $row["contpg"];
					$contEntry = $row["contentry"];
					$contType = $row["conttype"];
					$contLang = $row["contlang"];
					$contUrl = $row["conturl"];
  			//	if (strstr($contUrl,".php"))
          $editrapport .= "synchronised conturl: ".sql_updateone($tblcont,"SET conturl='".space2underscore($contTitle).($htaccess4sef===true?'':'_'.$contLang.$pgext)."' ","WHERE contpg='$contPg' AND contlang='$contLang' ","conturl")." | ";
        //  $editrapport .= "updated conturl: ".sql_updateone($tblcont,"SET conturl='".$clean_contTitle."_".$contLang.".php' ","WHERE contpg='".$row["contpg"]."' AND contlang='$contLang' ","conturl")." | ";
  				if	($contType == 'scroller')	$x = '10777'	;
  				if	($contType == 'leftlinks')	$x = '10888'	;
  				if	($contType == 'toplinks')	$x = '10999'	;
  				if	($contPg == '0')	$contTitle = $accueilString	;
  				if ($contPg == '1') {
  					if ($html_site === false) 
            $default_lg_pg_htm = 'index';
  					else
            $default_lg_pg_htm = $clean_contTitle;
  					$Fnm = $getcwd.$up.$default_lg_pg_htm.'_'.$contLang.$pgext;
  					if ($html_site === false) {
              $editrapport .= "index.php".(@file_exists($getcwd.$up.'index'.$pgext)?' exists ':(copy($getcwd.'_tpl_'.$pgext,$getcwd.$up.'index'.$pgext)?' copied ':' not copied! '));
              $editrapport .= $Fnm.(@file_exists($Fnm)?' exists ':(copy($getcwd.'_tpl_'.$pgext,$Fnm)?' copied ':' not copied! '));
              $editrapport .= $getcwd.$up.$clean_contTitle.'_'.$contLang.$pgext.(@file_exists($getcwd.$up.$clean_contTitle.'_'.$contLang.$pgext)?' exists ':(copy($getcwd.'_tpl_'.$pgext,$getcwd.$up.$clean_contTitle.'_'.$contLang.$pgext)?' copied ':' not copied! '));
            } else {
    					$inF = fopen($Fnm,"w+");
    					fwrite($inF,html_index($contLang));
    					fclose($inF);
    					if	(($contLang == $default_lg) && copy($Fnm,$getcwd.$up.'index'.$pgext))	
              $editrapport .= '<a href="'.$mainurl.'index'.$pgext.'" target="_blank">index'.$pgext.'</a><br />';
    				}
  					$editrapport .= '<a href="'.$mainurl.$default_lg_pg_htm.'_'.$contLang.$pgext.'" target="_blank">'.$default_lg_pg_htm.'_'.$contLang.$pgext.'</a><br />';
  					$zipfiles .= $Fnm.'|';
  				} else {
  				  if ($htaccess4sef === true)
            $default_lg_pg_htm = $clean_contTitle;
            else {
    				$default_lg_pg_htm = space2underscore(sql_getone($tblcont,"WHERE contpg='$contPg' AND contlang='$default_lg' ","conttitle"));
    					if	($contLang == $default_lg)
              $default_lg_pg_htm = $clean_contTitle	;
  					}
  					$Fnm = $getcwd.$up.$default_lg_pg_htm.'_'.$contLang.$pgext;
  					if	($contLang == $default_lg)
            $Fnm = $getcwd.$up.$default_lg_pg_htm.'_'.$contLang.$pgext;
  					if ($html_site === false) {
              $editrapport .= (@file_exists($Fnm)?' exists ':(copy($getcwd.'_tpl_'.$pgext,$Fnm)?' copied ':' not copied! '));
            } else {
    					$inF = fopen($Fnm,"w+");
    					fwrite($inF,html_index($contLang));
    					fclose($inF);
    				}
  					$editrapport .= '<a href="'.$mainurl.$default_lg_pg_htm.'_'.$contLang.$pgext.'" target="_blank">'.$default_lg_pg_htm.'_'.$contLang.$pgext.'</a><br />';
  					$zipfiles .= $Fnm.'|';
  				}
  			}
  			$editrapport .= "<h1>JS</h1>";
  			foreach($array_lang as $keylg) {
  				$Fjsnm = $getcwd.$up.'-menu_pagine_'.$keylg.'.js';
					$injsF = fopen($Fjsnm,"w+");
					fwrite($injsF,html_js($keylg));
					fclose($injsF);
					$editrapport .= '<a href="'.$mainurl.'-menu_pagine_'.$keylg.'.js" target="_blank">-menu_pagine_'.$keylg.'.js</a><br />';
					$zipfiles .= $Fjsnm.'|';
					$Fjsnmscroller = $getcwd.$up.'-scroller_'.$keylg.'.js';
					$injsFscroller = fopen($Fjsnmscroller,"w+");
					fwrite($injsFscroller,scroller_js($keylg));
					fclose($injsFscroller);
					$editrapport .= '<a href="'.$mainurl.'-scroller_'.$keylg.'.js" target="_blank">-scroller_'.$keylg.'.js</a><br />';
					$zipfiles .= $Fjsnmscroller.'|';
				}
  /**************************************** check php.ini
  **************************************** check php.ini */
 				$zipfiles = explode("|",$zipfiles);
 				if	(!is_array($zipfiles))
        $zipfiles = array($zipfiles);
  			$zipfile = new zipfile();
  			foreach($zipfiles as $value)
  				if(@file_exists($value))
  				$zipfile -> addFile(implode("",file($value)),basename($value));
    		$Fnm = $getcwd.$up.$safedir."zip".$pgext."js.zip";
    		$inF = fopen($Fnm,"w+");
    		fwrite($inF,$zipfile -> file());
    		fclose($inF);
    	} ################################################### END IF NOT WRITABLE ROOT
  		$zipuplds = "";
			$read = @mysql_query("SELECT * FROM $tblcontphoto WHERE contphotolang='$lg' ORDER BY contphotocontid ASC ");
			$nrows = @mysql_num_rows($read);
			$editrapport .= '<div class="clear">&nbsp;</div><h1>MEDIA</h1>';
			while($row = @mysql_fetch_array($read)) {
			  $row_contphotoid = $row["contphotoid"];
        $row_contphotodesc = space2underscore($row["contphotodesc"]);
				$inst_row_contphotoimg = $row["contphotoimg"];
				$ext = explode('.',strrev($inst_row_contphotoimg),2);
				$this_filename = strrev($ext[1]);
				$ext = strrev($ext[0]);
			//	$new_filename = $filedir.$row_contphotodesc;
				$new_filename = $this_filename;
				if (!stristr($zipuplds,$getcwd.$up.$this_filename.'.'.$ext)) {
				/*
  				$zipuplds .= $getcwd.$up.$this_filename.'.'.$ext.'|';
				$editrapport .= '<b>> updated to: '.sql_updateone($tblcontphoto,"SET conphotodesc='$row_contphotodesc', contphotoimg='".$new_filename.'.'.$ext."' ","WHERE contphotoid='$row_contphotoid' ","contphotoimg").'</b> '.(isset($row["contphotolang"])?" | ".$row["contphotolang"]:'').'<br />';
				if (@file_exists($getcwd.$up.$this_filename.'.'.$ext)) {
					$editrapport .= rename($getcwd.$up.$this_filename.'.'.$ext,$getcwd.$up.$new_filename.'.'.$ext);
					if (in_array($ext,$array_img_ext)) {
						$editrapport .= rename($getcwd.$up.$this_filename.'_big.'.$ext,$getcwd.$up.$new_filename.'_big.'.$ext);
						$editrapport .= rename($getcwd.$up.$this_filename.'_ori.'.$ext,$getcwd.$up.$new_filename.'_ori.'.$ext);
					}
				}
			 */
  				$this_filename = $new_filename;
  				$editrapport .= '<a href="'.$mainurl.$this_filename.'.'.$ext.'" target="_blank">'.$this_filename.'.'.$ext.'</a><br />';
          if (in_array($ext,$array_img_ext)) {
  					$editrapport .= '<a href="'.$mainurl.$this_filename.'.'.$ext.'" target="_blank">'.$this_filename.'_big.'.$ext.'</a><br /><a href="'.$mainurl.$this_filename.'.'.$ext.'" target="_blank">'.$this_filename.'_ori.'.$ext.'</a><br />';
  					$zipuplds .= $getcwd.$up.$this_filename.'_big.'.$ext.'|';
  					$zipuplds .= $getcwd.$up.$this_filename.'_ori.'.$ext.'|';
  				}
				}
			}
			$editrapport .= '<div class="clear">&nbsp;</div><h1>DOC</h1>';
			$read = @mysql_query("SELECT * FROM $tblcontdoc WHERE contdoclang='$lg' ORDER BY contdoccontid ASC ");
			while($row = @mysql_fetch_array($read)) {
			/*
			  $row_contdocid = $row["contdocid"];
        $row_contdocdesc = space2underscore($row["contdocdesc"]);
        $row_contdoc = $row['contdoc'];
				$ext = explode('.',strrev($row_contdoc),2);
				$this_filename = strrev($ext[1]);
				$explode_underscore = explode("_",strrev($row_contdoc),2);
				$explode_underscore = strtolower(strrev($explode_underscore[0]));
				echo "$explode_underscore<hr />";
				$ext = strrev($ext[0]);
				if (stristr($this_filename,"BIBLIO_"))
        $new_filename = $filedir."BIBLIO_".$row_contdocdesc.'.'.$ext;
				else if (stristr($this_filename,"REUNION_"))
        $new_filename = $filedir."REUNION_".$row_contdocdesc.'.'.$ext;
				else if (in_array($explode_underscore,$array_modules))
        $new_filename = (in_array($explode_underscore,$array_safedir)?$safedir:$filedir).strtoupper($explode_underscore)."_".$row_contdocdesc.'.'.$ext;
				else
        $new_filename = $filedir.$row_contdocdesc.'.'.$ext;
				$editrapport .= "updated to: ".sql_updateone($tblcontdoc,"SET condocdesc='$row_contdocdesc', contdoc='".$new_filename.'.'.$ext."' ","WHERE contdocid='$row_contdocid' ","contdoc")."<br />";
				$editrapport .= rename($getcwd.$up.$this_filename.'.'.$ext,$getcwd.$up.$new_filename);
				$this_filename = $new_filename;
				$editrapport .= '<br /><a class="'.$ext.'" href="'.$mainurl.$this_filename.'" target="_blank">'.$this_filename.'</a> '.(isset($row["contdoclang"])?" | ".$row["contdoclang"]:'').'<br />';
			*/
				if (!stristr($zipuplds,$getcwd.$up.$row["contdoc"])) {
  				$editrapport .= '<a class="'.$ext.'" href="'.$mainurl.$row["contdoc"].'" target="_blank">'.$row["contdoc"].' - '.$row["contdocdesc"].'</a> '.(isset($row["contdoclang"])?" | ".$row["contdoclang"]:'').'<br />';
  				$zipuplds .= $getcwd.$up.$row["contdoc"].'|';
  			}
			}
			if (isset($tblhtaccess)) {
        $editrapport .= "<h1>HTACCESSES</h1>";
      //  sql_del($tblhtaccess,"");// this deletes all but upon remaking does not keep metas...
        foreach($array_modules as $key) {
          if (in_array($key,$array_modules_as_form))
          continue;
          $dbtable = ${"tbl".$key};
					$read = @mysql_query("SELECT * FROM $dbtable ORDER BY ".$key."date DESC ");
					while($row = @mysql_fetch_array($read)) {
          //  foreach($array_lang as $keylg) {
						  $row_date = $row[$key."date"];
						  $row_statut = $row[$key."statut"];
						  $row_lang = $row[$key."lang"];//$keylg;
						  $row_type = $key;
						  $row_item = (isset($row[$key."rid"])?$row[$key."rid"]:$row[$key."id"]);
						  if (isset($row[$key."gendre"])) $row_title = $row[$key."prenom"]." ".$row[$key."nom"];// sql_stringit('gendre',$row[$key."gendre"])." ".
						  else $row_title = (isset($row[$key."title"])?$row[$key."title"]:$row[$key."titre"]);
						  /*
						  if (preg_match("/^[0-9]+\$/",$row_title))
						  $row_title = sql_getone($tblstring,"WHERE stringlang='$keylg' AND stringtype='$key' AND stringtitle='".$row_title."' ","stringentry");
						  */
              $row_entry = (isset($row[$key."entry"])?$row[$key."entry"]:(isset($row[$key."desc"])?$row[$key."desc"]:''));
						  $row_url = space2underscore($row_title);
    					$row_metadesc = (isset($row[$key."metadesc"])&&($row[$key."metadesc"]!=$default_desc_keyw[0])?$row[$key."metadesc"]:'');
    					$row_metakeyw = (isset($row[$key."metakeyw"])&&($row[$key."metakeyw"]!=$default_desc_keyw[1])?$row[$key."metakeyw"]:'');
              /*
    					if (sql_nrows($tblhtaccess,"WHERE htaccesslang='$keylg' AND htaccessitem='$row_item' AND htaccesstype='$row_type' ")>1)
    					sql_del($tblhtaccess,"WHERE htaccesslang='$keylg' AND htaccessitem='$row_item' AND htaccesstype='$row_type' ");
    					*/
              $getthis = sql_get($tblhtaccess,"WHERE htaccesslang='$row_lang' AND htaccessitem='$row_item' AND htaccesstype='$row_type' ORDER BY htaccessdate DESC ","htaccessid,htaccessstatut,htaccesstitle,htaccessentry,htaccessurl,htaccessmetadesc,htaccessmetakeyw");
						  if ($getthis[0] == '.') {
						    $insertq = "INSERT INTO $tblhtaccess ".sql_fields($tblhtaccess,'list')." VALUES ('','$row_date','$row_statut','$row_lang','$row_item','$row_title','$row_entry','$row_url','$row_type','$row_metadesc','$row_metakeyw') ";
						    $insertquery = @mysql_query($insertq);
  							$editrapport .= (!$insertquery?$error_request."<br />$insertq<br />":$effectueString." [i]")." > <b>$row_type</b> : <i>$row_title</i> $row_lang <br />$insertq<br />";
  						} else {
      					if (sql_nrows($tblhtaccess,"WHERE htaccesslang='$row_lang' AND htaccessurl='$row_url' AND htaccesstype='$row_type' ")>1)
      					sql_del($tblhtaccess,"WHERE htaccessid!='".$getthis[0]."' AND htaccesslang='$row_lang' AND htaccessurl='$row_url' AND htaccesstype='$row_type' ");
  						  $setq = 'SET ';
    					  if ($row_statut != $getthis[1]) $setq .= "htaccessstatut='$row_statut', ";
    					  if ($row_title != $getthis[2]) $setq .= "htaccesstitle='$row_title', ";
    					  if ($row_entry != $getthis[3]) $setq .= "htaccessentry='$row_entry', ";
    					  if ($row_url != $getthis[4]) $setq .= "htaccessurl='$row_url', ";
    					  if (($row_metadesc != $getthis[5]) || ($row_metadesc != $default_desc_keyw[0])) $setq .= "htaccessmetadesc='$row_metadesc', ";
    					  if (($row_metakeyw != $getthis[6]) || ($row_metakeyw != $default_desc_keyw[1])) $setq .= "htaccessmetakeyw='$row_metakeyw', ";
    						$editrapport .= $effectueString." [u] > <b>$row_type</b> : <i>".sql_updateone($tblhtaccess,"SET $setq htaccessid='".$getthis[0]."' ","WHERE htaccessid='".$getthis[0]."' ","htaccesstitle")."</i> $row_lang <br />";
              }
					//	}
					}
				}
			}
/**************************************** check php.ini
			$zipuplds = explode("|",$zipuplds);
			if	(!is_array($zipuplds))	$zipuplds = array($zipuplds)	;
			$zipfile = new zipfile();
			foreach($zipuplds as $value)
				if(@file_exists($value))
					$zipfile -> addFile(implode("",file($value)),basename($value));
			$Fnm = "zipuplds.zip";
			$inF = fopen($Fnm,"w+");
			fwrite($inF,$zipfile -> file());
			fclose($inF);
**************************************** check php.ini */
/************************************************************************
			$zipzips = array("ziphtmjs.zip","zipuplds.zip");
			$Fnm = "zip_".date('Ymd').".zip";
			$zipfile = new zipfile();
			foreach($zipzips as $value)
				if(@file_exists($value))
					$zipfile -> addFile(implode("",file($value)),basename($value));
			$inF = fopen($Fnm,"w+");
			fwrite($inF,$zipfile -> file());
			fclose($inF);<!-- <a href="'.$Fnm.'" target="_self">'.$cliquericiString.'</a> zip containing  -->
************************************************************************/
  $content .= $editrapport.'<hr />';
/**************************************** check php.ini
**************************************** check php.ini */
  $content .= '<hr />';
  if ($root_writable === true)
  $content .= 'zips with files in respective folders:<br /> <br /> - <a href="'.$mainurl.$urladmin.'_download.php?file='.base64_encode($safedir.'zip'.$pgext.'js.zip').'" target="_blank">'.$pgext.' & js files in root directory</a>,<br />';
  $content .= ' - <a href="'.substr($filedir,0,-1).'.php" target="_blank">files in '.$filedir.' directory</a><br />';
  $content .= ' - <a href="'.substr($safedir,0,-1).'.php" target="_blank">files in '.$safedir.' directory</a><br />';
  $notice = $enregistrementString.' '.$effectueString;
#######################################################
    }
  }
$content .= '</div></div>';
?>