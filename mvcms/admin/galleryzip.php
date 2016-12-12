<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

//          $notice .= mvtrace(__FILE__,__LINE__)." $x<br />";

  //  include'galleryadd.php';
  if (!isset($user_name)) $user_name = $admin_name;
  if (!isset($helpUploadimg)) $helpUploadimg = "Help upload image";
  if (!isset($gallery_from_zip)) $gallery_from_zip = true;
      //  $ARCHIVE->extractZip($userfile_tmp,$nowtime); //
      /* in _incerror.php
  class zip {
    function extractZip($src,$dest) {
      global $trace,$notice,$error,$getcwd,$up,$filedir,$urladmin; 
      if (mkdir($getcwd.$dest, 0777)) $notice .= "et voila!<br />";
      else $notice .= "buh<br />";
      if (function_exists('zip_open')) {
        $zip_open = zip_open($src);
        if ($zip_open) {
        $notice .= "zip opened...<hr /><br />";
          while ($zip_open_entry = zip_read($zip_open)) {
            $complete_name = zip_entry_name($zip_open_entry);
            $notice .= $complete_name." is complete_name<br />";
            if (zip_entry_open($zip_open, $zip_open_entry, "r")) {
              if ($fd = fopen($getcwd.$dest.$complete_name, 'w+')) {
                $notice .= "now we write...<br />";
                if (fwrite($fd, zip_entry_read($zip_open_entry, zip_entry_filesize($zip_open_entry))))
                  $notice .= "written.<br />";
                else $notice .= "not written...<br />";
                fclose($fd);
              }
              zip_entry_close($zip_open_entry);
            }
          }
          zip_close($zip_open);
        } else $notice .= "not open...<br />";
      } else {
        $do = "unzip $src -d $dest";
        $notice .= exec($do,$output,$result).($result==1?'':'NOT')." extracted by exec<br />";
        //`unzip $src -d $dest`;
      }
    return true;
    }
  }
      */
      //  processZippedItem($nowtime,$filedir,$gid); //
  function processZippedItem($dir, $newdir, $gid) {
    global $notice,$error,$dbtime,$nowtime,$getcwd,$up,$filedir,$urladmin,$user_name,$array_media_ext,$array_img_ext,$this_is,$dbtable,$array_lang,$default_lg;
    $invisibleFile_names = array(".", "..", ".htaccess", ".htpasswd");
    $dir = $getcwd.$dir;
    if (is_dir($dir)) {
      $lst = scandir($dir);//getcwd()."/".
      if (is_array($lst)) {
        if (!isset($gid)||($gid==NULL)) {
          if (array_search("_description.txt",$lst)) {
            $file_temp = $dir."/_description.txt";
            $description = html_encode(nl2br(file_get_contents($file_temp)));
            if (unlink($file_temp))
            $notice .= $file_temp." -> deleted<hr />";
          } else $description = 'no description';
          $notice .= @mysql_query("INSERT INTO `$dbtable` (galleryid,gallerystatut,gallerydate,gallerylang,galleryrid,gallerytitle,galleryentry) VALUES (NULL,'Y',$dbtime,'$default_lg','','$nowtime','$description');");
          $this_galleryid = sql_updateone($dbtable,"SET galleryrid=galleryid ","WHERE gallerytitle='$nowtime' ","galleryrid");
          $notice .= "<hr />::::$ $this_galleryid -> galleryrid ,,, <br />";
          foreach($array_lang as $keylg)
            if ($keylg != $default_lg)
            $notice .= @mysql_query("INSERT INTO `_gallery` (galleryid,gallerystatut,gallerydate,gallerylang,galleryrid,gallerytitle,galleryentry) VALUES (NULL,'Y',$dbtime,'$keylg','$this_galleryid','$nowtime','$description');")." $keylg ";
        } else {
          $this_galleryid = $gid;
          $notice .= "assigned existing galleryrid<br />";
        }
        foreach($lst as $item) {
          if (!in_array($item,$invisibleFile_names)) {
            $file_temp = $dir."/".$item;
            $new_file = $newdir.'GALLERY_'.$this_galleryid.'_'.space2underscore($item);
          	$ext = explode('.',strrev($item),2);
          	$ext = strtolower(strrev($ext[0]));
          	if	(in_array($ext,$array_media_ext)) {           
              $notice .= $file_temp." -> ".$new_file." (established rel)<hr /><br />";
        			$file_name = html_encode(substr($new_file,0,-(strlen($ext)+1))); // string length of ext plus dot
        			if (in_array($ext,$array_img_ext)) {
        				$notice .= "<h1>".$file_name.".$ext</h1>";
        				upload_image($file_temp,$file_name,$ext);
        				rename($file_temp,$getcwd.$up.$file_name.'_ori.'.$ext);
        				$notice .= "<hr /><br />";
        			} else {
        // || ($ext == 'swf') || ($ext == 'flv') || ($ext == 'wmv') || ($ext == 'avi') || ($ext == 'mov') || ($ext == 'rm') || ($ext == 'mpeg') || ($ext == 'mpg') || ($ext == 'asf') //
        			//	$userfile_name = space2underscore($contphotoEntry).'.'.$ext;
        				$location = $file_name.'.'.$ext;
        				$movelocation = $getcwd.$up.$location;
        				$notice .= "$location -> $movelocation<hr /><br />";
        				rename($file_temp,$movelocation);
        			}
        			$contphoto_desc = str_replace("-"," ",substr(ucfirst($item),0,-(strlen($ext)+1)));
              $notice .= @mysql_query("INSERT INTO `_contphoto` (contphotoid,contphotostatut,contphotodate,contphotolang,contphotorid,contphotoutil,contphotocontid,contphotodesc,contphotoimg) VALUES (NULL,'Y',$dbtime,'$default_lg','','$user_name','$this_galleryid','$contphoto_desc','".$file_name.'.'.$ext."');").' == photo<br />';
              $this_photoid = sql_updateone("_contphoto","SET contphotorid=contphotoid ","WHERE contphotocontid='$this_galleryid' AND contphotodesc='$contphoto_desc' AND contphotoimg='".$file_name.'.'.$ext."' ","contphotorid");
              foreach($array_lang as $keylg)
                if ($keylg != $default_lg)
                $notice .= @mysql_query("INSERT INTO `_contphoto` (contphotoid,contphotostatut,contphotodate,contphotolang,contphotorid,contphotoutil,contphotocontid,contphotodesc,contphotoimg)  VALUES (NULL,'Y',$dbtime,'$keylg','$this_photoid','$user_name','$this_galleryid','$contphoto_desc','".$file_name.'.'.$ext."');")." $keylg ";
           //   $notice .= @mysql_query("INSERT INTO `_galleryphoto` VALUES ('','Y',$dbtime,'$user_name','$this_galleryid','".str_replace("_"," ",substr(ucfirst($item),0,-(strlen($ext)+1)))."','".$file_name.'.'.$ext."');").' == photo<br />';
            }
          }
        }
      }
      rmdir("$dir");
    } else if (is_file("$dir"))
    $notice .= '<br />file'."<hr />";
  }
  if ($gallery_from_zip === true)
  if ($lg == 'fr')
  $content .= '<b>Envoi d`un zip complet</b><br />Le zip doit contenir un fichier nomm&eacute; _description.txt contenant la description souhait&eacute;e, et aussi chaque image de la galerie.<br />'.gen_form($lg,$x,$y).'<input type="file" name="myzip" value="" /><br />'.(isset($galleryId)?'<div style="float:left;color:red;">S`assurer que le zip choisit ne contient pas de _description.txt !!!</div>':'S&eacute;lectionner une galerie existante pour y ajouter les photos contenues dans un zip sans _description.txt').' '.gen_fullselectoption(array_unique(sql_array($dbtable,"","galleryid")),(isset($galleryId)?$galleryId:''),'','galleryid').'<br /> >> <input type="submit" name="send" value="'.ucfirst($envoyerString).' ZIP" /> (peut prendre quelques instants, maximum de '.ini_get('max_execution_time').' secondes)</form><hr /><br />';
  else
  $content .= '<b>Sending a complete ZIP</b><br />When creating a new gallery the zip archive needs to contain a file called _description.txt in which the entry for the given gallery will be. Additionally, this ZIP should contain all the image files for the gallery.<br />'.gen_form($lg,$x,$y).'<input type="file" name="myzip" value="" /><br />'.(isset($galleryId)?'<div style="float:left;color:red;">Please ensure that the ZIP does NOT contain the _description.txt file !!!</div>':'Please select an existing gallery to add the images contained in your ZIP to, without any _description.txt').' '.gen_fullselectoption(array_unique(sql_array($dbtable,"","galleryid")),(isset($galleryId)?$galleryId:'ajout'),'','galleryid').'<br /> >> <input type="submit" name="send" value="'.ucfirst($envoyerString).' ZIP" /> (This can take some time, maximum of '.ini_get('max_execution_time').' seconds)</form><hr /><br />';
  if (isset($_POST['send']) && ($_POST['send'] == ucfirst($envoyerString).' ZIP')) {
    if ($_FILES) {
      $userfile_name = $_FILES['myzip']['name'];
      $userfile_tmp  = $_FILES['myzip']['tmp_name'];
      $userfile_size = $_FILES['myzip']['size'];
      $userfile_type = $_FILES['myzip']['type'];
      /*
      echo $userfile_name."<hr />";
      echo $userfile_tmp."<hr />";
      echo $userfile_size."<hr />";
      echo $userfile_type."<hr />";
      */
      if (stristr($userfile_name,'zip')) {
        if (isset($_POST['galleryid'])) $gid = $_POST['galleryid'];
        else $gid = NULL;
        $nowtime = time();
        @set_time_limit(0);
        $ARCHIVE = new zip;
        $ARCHIVE->extractZip($userfile_tmp,"$getcwd$up$filedir$nowtime/"); //
        processZippedItem("$up$filedir$nowtime",$filedir,$gid); //
        if ($gid==NULL)
        if (isset($tblhtaccess) && in_array($this_is,$array_modules)) {
          $row_date = $dbtime;
          $row_statut = 'Y';
          $row_type = $this_is;
          $row_item = ($gid!=NULL?$gid:sql_getone($dbtable,"WHERE gallerytitle='$nowtime' ","galleryrid"));
          if (isset(${$this_is."Gendre"})) $row_title = sql_stringit('gendre',${$this_is."Gendre"})." ".${$this_is."Prenom"}." ".${$this_is."Nom"};
					else $row_title = (isset($this_titre)?$this_titre:$nowtime);
					$row_entry = (isset(${$this_is."Entry"})?${$this_is."Entry"}:(isset(${$this_is."Desc"})?${$this_is."Desc"}:''));
					$row_url = space2underscore($row_title);
					$row_metadesc = (isset(${$this_is."Metadesc"})&&(${$this_is."Metadesc"}!=$default_desc_keyw[0])?${$this_is."Metadesc"}:'');
					$row_metakeyw = (isset(${$this_is."Metakeyw"})&&(${$this_is."Metakeyw"}!=$default_desc_keyw[1])?${$this_is."Metakeyw"}:'');
					foreach($array_lang as $keylg) {
            $getthis = sql_get($tblhtaccess,"WHERE htaccesslang='$keylg' AND htaccessitem='$row_item' AND htaccesstype='$row_type' ORDER BY htaccessdate DESC ","htaccessid,htaccessstatut,htaccesstitle,htaccessentry,htaccessurl,htaccessmetadesc,htaccessmetakeyw");
  				  if ($getthis[0] == '.') {
  				    $insertq = "INSERT INTO $tblhtaccess ".sql_fields($tblhtaccess,'list')." VALUES ('',$row_date,'$row_statut','$keylg','$row_item','$row_title','$row_entry','$row_url','$row_type','$row_metadesc','$row_metakeyw') ";
  				    $insertquery = @mysql_query($insertq);
  						if (!$insertquery)
              $error .= $error_request." [i] > <b>$row_type</b> : <i>$row_title</i><br />";
              else
              $notice .= $effectueString." [i] > <b>$row_type</b> : <i>$row_title</i><br />";
  					} else {
  					  $setq = "htaccessdate=$row_date, ";
  					  if ($row_statut != $getthis[1]) $setq .= "htaccessstatut='$row_statut', ";
  					  if (!isset($gid))
  					  if ($row_title != $getthis[2]) $setq .= "htaccesstitle='$row_title', ";
  					  if ($row_entry != $getthis[3]) $setq .= "htaccessentry='$row_entry', ";
  					  if ($row_url != $getthis[4]) $setq .= "htaccessurl='$row_url', ";
  					  if (($row_metadesc != $getthis[5]) || ($row_metadesc != $default_desc_keyw[0])) $setq .= "htaccessmetadesc='$row_metadesc', ";
  					  if (($row_metakeyw != $getthis[6]) || ($row_metakeyw != $default_desc_keyw[1])) $setq .= "htaccessmetakeyw='$row_metakeyw', ";
   						$notice .= $effectueString." [u] > <b>$row_type</b> : <i>".sql_updateone($tblhtaccess,"SET $setq htaccessid='".$getthis[0]."' ","WHERE htaccessid='".$getthis[0]."' ","htaccesstitle")."</i><br />"."SET $setq htaccessid='".$getthis[0]."'<br /> ";
            }
          }
        }
      }
    } else
    $content .= "no zip<br />";
  }
  if ($_SERVER['REQUEST_METHOD'] == "POST")
  if (($notice != '') || ($error != '')) {
    $_SESSION['mv_notice'] = $notice;
    $_SESSION['mv_error'] = $error;
    Header("Location: ".html_entity_decode($local_url));Die();
  }
