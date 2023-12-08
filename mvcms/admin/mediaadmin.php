<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$content .= $admin_menu;

if (!isset($array_img_ext))
$array_img_ext = array("jpg","jpeg","gif","png");
if (!isset($array_swf_ext))
$array_swf_ext = array("swf","flv","wmv","avi","mp3","mov","rm","mpeg","mpg","asf");
if (!isset($array_doc_ext))
$array_doc_ext = array("doc","xls","ppt","pps","pdf");

$local_url = $local.'?lg='.$lg.'&amp;x=z&amp;y=media';

if ($logged_in === true) {

	$error_img = '';
	$notice_img = '';

	$photo_fields = sql_fields($tblcontphoto,"array");
	$doc_fields = sql_fields($tblcontdoc,"array");
  
	if (!isset($send) || (isset($send) && ($send == 'edit') && ($y != 'media'))) {
		$delete_media = "";
// ########################## PICS ###############################
		$contPhotocount = sql_nrows($tblcontphoto,(in_array("contphotolang",$photo_fields)?" WHERE contphotolang='$lg' ":''));
		if (($tinyMCE === false) && (isset($send) && ($send == 'edit') && ($y != 'media'))) {
			if ($contPhotocount > '0') {
				$photoListe = '<a href="'.$local_url.'&amp;send=upldphoto">'
							.$ajouterString.' 1 '.$photoString.'</a><hr />';
				$photoListe .= $contPhotocount.' '
							.$class_conjugaison->plural($photodispoString,'F',$contPhotocount)
							.'.<br />';
				$read = mysqli_query($connection, "SELECT * FROM $tblcontphoto ".(in_array("contphotolang",
											$photo_fields)?" WHERE contphotolang='$lg' ":''));
				if ($contPhotocount > 3) $photoListe .= '
									<div style="overflow:auto;height:170px;width:100px;">';
				for ($i=0;$i<$contPhotocount;$i++) {
					$row = mysqli_fetch_array($read);
					$ext = explode('.',strrev($row["contphotoimg"]));
					$ext = strtolower(strrev($ext[0]));
					if (in_array($ext,$array_swf_ext)) {
						$photoListe .= '<a href="#" onclick="insertFlash(\''
									.$row["contphotoimg"].'\');return(false)" title="'
									.$row["contphotodesc"].'" alt="'.$row["contphotodesc"]
									.'"><sup>'.str_replace('_',' ',$row["contphotoimg"])
									.'</sup></a> | <a href="'.$local_url
									.'&amp;send=delphoto&amp;contphotoId='
									.(isset($row["contphotorid"])?$row["contphotorid"]:
									$row["contphotoid"]).'" onclick="return confirm(\''
									.$confirmationeffacementString.'\');"><img src="'.$mainurl
									.'images/delete.gif" width="10" height="10" title="'
									.$effacerString.'" alt="'.$effacerString
									.'" border="0" /></a><br />';
					} else {
						$photoListe .= '<a href="#" onclick="insertImage(\''
									.$row["contphotoimg"].'\');return(false)" title="'
									.$row["contphotodesc"].'" alt="'.$row["contphotodesc"]
									.'"><img src="'.$mainurl.$row["contphotoimg"]
									.'" width="50" height="50" hspace="5" vspace="5" 
									border="0" alt="'.$row["contphotodesc"]
									.'" /></a> <a href="'.$local_url
									.'&amp;send=delphoto&amp;contphotoId='
									.(isset($row["contphotorid"])?$row["contphotorid"]:
									$row["contphotoid"]).'" onclick="return confirm(\''
									.$confirmationeffacementString.'\');"><img src="'.$mainurl
									.'images/delete.gif" width="10" height="10" title="'
									.$effacerString.'" alt="'.$effacerString
									.'" border="0" /></a><br />';
					}
				}
				if ($contPhotocount > 3) $photoListe .= '</div>';
			} else {
				$photoListe = '<a href="'.$local_url.'&amp;send=upldphoto">'.$ajouterString
							.' 1 '.$photoString.'</a><hr />'.$pasdeString.' '
							.$photodispoString.' !<br />';
			}
		} else { // $tinyMCE === true
			$photoListe = '<div><div style="float:left;width:49%;text-align:right;"><a href="'
						.$local_url.'&amp;send=upldphoto">'.$ajouterString.' 1 '.$photoString
						.'</a> |</div>'; // write javascript for import in tinymce.
			if (isset($send) && ($send == 'edit') && ($y != 'media'))
			$tinyMCE_photos = '<div style="float:left;overflow:auto;height:110px;width:49%;
								min-width:300px;">';
			else
			$tinyMCE_photos = '<div style="float:left;overflow:auto;height:300px;width:49%;
								min-width:300px;">';
			$tinyMCE_photos .= '<table cellspacing="0" cellpadding="2" border="0" 
								style="border:none;width: 98%;">
								<tr><td align="center" style="border:none;">';
							// '<div style="float:right;text-align:center;">';
			$tinyMCE_flashs = '<div style="float:left;overflow:auto;height:100px;width:49%;
								padding-left:30px;">'.$nonString.' '.$photoString.'<br />';
			$read = mysqli_query($connection, "SELECT * FROM $tblcontphoto ".(in_array("contphotolang",
											$photo_fields)?" WHERE contphotolang='$lg' ":''));
			$loop_tinyMCE_photos = "";
			$loop_tinyMCE_flashs = "";
			for ($i=0;$i<$contPhotocount;$i++) {
				$row = mysqli_fetch_array($read);
				$ext = explode('.',strrev($row["contphotoimg"]));
				$ext = strtolower(strrev($ext[0]));
				if (in_array($ext,$array_swf_ext)) {
					$loop_tinyMCE_flashs .= '<a href="'.$local_url
										.'&amp;send=delphoto&amp;contphotoId='
										.(isset($row["contphotorid"])?$row["contphotorid"]:
										$row["contphotoid"]).'" onclick="return confirm(\''
										.$confirmationeffacementString.'\');"><img src="'
										.$mainurl.'images/delete.gif" width="10" height="10" 
										title="'.$effacerString.'" alt="'.$effacerString.'" 
										border="0" /></a> | <a href="'.$mainurl
										.$row["contphotoimg"].'" target="_blank"><img src="'
										.$mainurl.'images/'.$ext.'logo.gif" width="16" 
										height="16" border="0" title="'.$ouvrirString.' '.$ext
										.'" alt="'.$ouvrirString.' '.$ext.'" /> '
										.str_replace("_", " ", $row["contphotodesc"]).'</a>
										<br />';
				} else {
					if ($loop_tinyMCE_photos !== "")
					if (isset($send) && ($send == 'edit') && ($y != 'media'))
					$loop_tinyMCE_photos .= '</td><td align="center" style="border:none;">';
					else {
						if (($i==ceil($contPhotocount/3)) 
							|| ($i==(2*(ceil($contPhotocount/3)))))
						$loop_tinyMCE_photos .= '</td></tr>
												<tr><td align="center" style="border:none;">';
						else
						$loop_tinyMCE_photos .= '
											</td><td align="center" style="border:none;">';
					}
					$loop_tinyMCE_photos .= '<a href="'.$mainurl.$row["contphotoimg"]
										.'" target="_blank" title="'.$row["contphotodesc"]
										.'" alt="'.$row["contphotodesc"].'"><img src="'
										.$mainurl.$row["contphotoimg"].'" width="50" 
										height="50" hspace="5" vspace="5" border="0" alt="'
										.$row["contphotodesc"].'" /></a><br /><a href="'
										.$local_url.'&amp;send=delphoto&amp;contphotoId='
										.(isset($row["contphotorid"])?$row["contphotorid"]:
										$row["contphotoid"]).'" onclick="return confirm(\''
										.$confirmationeffacementString.'\');"><img src="'
										.$mainurl.'images/delete.gif" width="10" height="10" 
										title="'.$effacerString.'" alt="'.$effacerString.'" 
										border="0" /></a>';
				}
			}
			if ($loop_tinyMCE_photos != '')
			$tinyMCE_photos .= $loop_tinyMCE_photos.'</td><tr></table></div>'; 
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
		$contDoccount = sql_nrows($tblcontdoc,(in_array("contdoclang",$doc_fields)?" WHERE contdoclang='$lg' ":''));
		if ($tinyMCE === false) {
			$docListe = '<hr /><a href="'.$local_url.'&amp;send=uplddoc">'.$ajouterString
						.' 1 '.$docString.'</a>';
			if ($contDoccount > '0') {
				$docListe .= '<hr />'.$contDoccount.' '
							.$class_conjugaison->plural($docdispoString,'M',$contDoccount)
							.'.<br />';
				$read = mysqli_query($connection, "SELECT * FROM $tblcontdoc ".(in_array("contdoclang",
											$doc_fields)?" WHERE contdoclang='$lg' ":''));
				for ($i=0;$i<$contDoccount;$i++) {
					$row = mysqli_fetch_array($read);
					$ext = explode('.',strrev($row["contdoc"]),2);
					$ext = strtolower(strrev($ext[0]));
					$docListe .= '<a href="'.$mainurl.$row["contdoc"].'" target="_blank">
								<img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" 
								height="16" border="0" alt="'.$ouvrirString.'" /></a> | 
								<a href="#" onclick="insertDoc(\''.$row["contdoc"]
								.'\');return(false)">'.str_replace("_"," ",
								$row["contdocdesc"]).'</a> | <a href="'.$local_url
								.'&amp;send=deldoc&amp;contphotoId='
								.(isset($row["contdocrid"])?$row["contdocrid"]:
								$row["contdocid"]).'" onclick="return confirm(\''
								.$confirmationeffacementString.'\');"><img src="'.$mainurl
								.'images/delete.gif" width="10" height="10" title="'
								.$effacerString.'" alt="'.$effacerString.'" border="0" /></a>
								<br />';
				}
			} else {
				$docListe .= '<hr />'.$pasdeString.' '.$docdispoString.' !';
			}
			$docListe .= '<hr />';
		} else { // $tinyMCE === true
			$docListe = '<div style="float:left;width:49%;text-align:left;">| <a href="'
						.$local_url.'&amp;send=uplddoc">'.$ajouterString.' 1 '.$docString
						.'</a></div></div>'; // write javascript for import in tinymce.
			if (isset($send) && ($send == 'edit') && ($y != 'media'))
			$tinyMCE_docs = '<div style="float:right;overflow:auto;height:110px;
							min-width:300px;width:49%;text-align:left;">';
			else
			$tinyMCE_docs = '<div style="float:right;overflow:auto;height:300px;width:49%;
							min-width:300px;text-align:left;">';
			$read = mysqli_query($connection, "SELECT * FROM $tblcontdoc ".(in_array("contdoclang",
											$doc_fields)?" WHERE contdoclang='$lg' ":''));
			for ($i=0;$i<$contDoccount;$i++) {
				$row = mysqli_fetch_array($read);
				$ext = explode('.',strrev($row["contdoc"]),2);
				$ext = strtolower(strrev($ext[0]));
				if ($tinyMCE_docs !== "") $tinyMCE_docs .= "<br />";
				$tinyMCE_docs .= '<a href="'.$local_url.'&amp;send=deldoc&amp;contdocId='
								.(isset($row["contdocrid"])?$row["contdocrid"]:
								$row["contdocid"]).'" onclick="return confirm(\''
								.$confirmationeffacementString.'\');"><img src="'.$mainurl
								.'images/delete.gif" width="10" height="10" title="'
								.$effacerString.'" alt="'.$effacerString.'" border="0" /></a>
								 | <a href="'.$mainurl.$row["contdoc"].'" target="_blank">
								 <img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" 
								 height="16" border="0" title="'.$ouvrirString.' '.$ext.'" 
								 alt="'.$ouvrirString.' '.$ext.'" /> '
								.str_replace("_"," ",$row["contdocdesc"]).'</a>';
			}
			$tinyMCE_docs .= '</div>';
			$delete_media = $tinyMCE_docs.$delete_media;
		}
// end doc list

		if ($x == 'z')
		$content .= $photoListe.$docListe.$delete_media;
    
	    // template upload
	    if (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil")
	    	== $admin_name)
		$content .= '<b>Sending a complete template in ZIP</b><br />'.gen_form($lg,$x,$y)
				.'<input type="file" name="myzip" value="" /><br /><input type="submit" 
				name="send" value="'.ucfirst($envoyerString).' ZIP" /><br />(This can take 
				some time, maximum of '.ini_get('max_execution_time').' seconds)</form><hr />
				<br />';

#######################################################FORME DE TELECHARGE PHOTO
#######################################################PIC UPLOAD FORM
	} else if ($send == "upldphoto") {
		$content .= gen_form($lg,$x,$y);
		if (isset($logo))	
		$content .= '<input type="hidden" name="logo" value="" /><b>'.$ajoutString.' '
					.$logoString.'</b><br /> <br />';
		for ($j=0;$j<$nof;$j++) {
			if ($j == 0)
			$content .= ucfirst($photoString).' ( .'.implode(" , .",$array_img_ext).' )<br />'
					.$multimediaString.' ( .'.implode(" , .",$array_swf_ext).' )<br />('
					.$max_filesizeString.$max_filesideString.')<br /> <br />';
			$content .= '<label for="contphotoDesc[]">'.$descriptionString.' '.$photoString
					.' '.($j>0?$j:'').'</label><br /><input type="text" name="contphotoDesc[]" 
					size="60" value="'.(isset($_SESSION["contphotoDesc_$j"])?
					$_SESSION["contphotoDesc_$j"]:'').'" />
					<br /><!--( a-z A-Z 0-9_- , " " > _ )<br />-->';
			$content .= '<input type="file" name="contphoto[]" /> <br />';
			$_SESSION["contphotoDesc_$j"] = null;
		}
		$content .= '<br /> <br /><input type="submit" name="send" value="'.$envoiphotoString
				.'" /> | <a href="javascript:history.back()//">'.$retourString.'</a></form>';
#######################################################ENVOI PHOTO
#######################################################SEND PIC
	} else if ($send == $envoiphotoString) {
		for ($i=0;$i<$nof;$i++) {
			if (isset($_FILES["contphoto"]["name"][$i]) 
				&& ($_FILES["contphoto"]["name"][$i] != '')
				) {
	  			$userfile_name = $_FILES["contphoto"]["name"][$i];
	  			$userfile_tmp = $_FILES["contphoto"]["tmp_name"][$i];
	  			$userfile_size = $_FILES["contphoto"]["size"][$i];
	  			$userfile_type = $_FILES["contphoto"]["type"][$i];
	  			$ext = explode('.',strrev($userfile_name),2);
	  			$filename = space2underscore(strrev($ext[1]));
	  			$ext = strtolower(strrev($ext[0]));
	  			if (isset($_POST["contphotoDesc"][$i]))
				$contphotoDesc =space2underscore(stripslashes(str_replace("'","´",html_encode(
															$_POST["contphotoDesc"][$i]))));
				if ($contphotoDesc == '')
				$contphotoDesc = $filename;
	  			if (in_array($ext,array_merge($array_img_ext,$array_swf_ext)))
	  			$valid_ext = "true"	;
	  			if (!isset($valid_ext))
	  			$valid_ext = "false";
	  			$sql = "WHERE contphotoimg='".$filedir.$contphotoDesc.'.'.$ext."' ";
	  			$check = sql_nrows($tblcontphoto,$sql);
	  			if (!$contphotoDesc 
	  				|| !preg_match("/^[a-zA-Z0-9_-]+\$/",space2underscore($contphotoDesc)) 
	  				|| ($contphotoDesc == '') || ($userfile_name == '') 
	  				|| ($valid_ext !== 'true') || ($valid_ext == 'false') || 
	  				($userfile_size > $max_filesize) || ($check > '0')
	  				) {
					$error_img .= $erreurString.'!<br />'.$listecorrectionString.'<ul>';
	  				if (!$contphotoDesc || ($contphotoDesc == '')
	  					||!preg_match("/^[a-zA-Z0-9_-]+\$/",space2underscore($contphotoDesc)))	
					$error_img .= '<li>'.$nomString.' > '.$error_invmiss.'</li>';
	  				if (($userfile_name == '') 
	  					|| ($valid_ext !== 'true') || ($valid_ext == 'false'))	
					$error_img .= '<li>'.$fichierString.' > '.$error_invmiss.'</li>';
	  				if ($userfile_size > $max_filesize)	
					$error_img .= '<li>'.$fichierString.' > '.$error_inv.' ('
								.$max_filesizeString.')</li>';
	  				if ($check > '0')
					$error_img .= '<li>'.$error_exists.'</li>';
					$error_img .= '</ul><br />';
	  			} else {
					if (in_array($ext,$array_img_ext)) {
						$filename = $filedir.space2underscore($contphotoDesc);
						$location = upload_image($userfile_tmp,$getcwd.$filename,$ext);
					} else {
						$userfile_name = space2underscore($contphotoDesc).'.'.$ext;
						//${$this_is."Desc"}.'.'.$ext;
						$location = "$filedir$userfile_name";
						$movelocation = $location;
						move_uploaded_file($userfile_tmp,$getcwd.$up.$movelocation);
					}
					if ($location == "error-too_small") {
						$error_img .= $erreurString.' > '.$max_filesizeString.'<br />';
					} else if ($location == "error-sqrt") {
						$error_img .= $erreurString.' > '.$max_filesizeString.' (max sqrt <= '
									.$max_sqrt.')<br />';
					} else if (!$location || ($location == "")) {
						$error_img .= $erreurString.' > '.$error_inv.'<br />';
					} else {
						$contphotoimg = $location;
						$insertquery = mysqli_query($connection, "INSERT INTO $tblcontphoto
                  							(`contphotoid`,`contphotostatut`,`contphotodate`,
                  							`contphotolang`,`contphotorid`,`contphotoutil`,
                  							`contphotocontid`,`contphotodesc`,`contphotoimg`)
                  									VALUES
                  							(NULL,'Y',$dbtime,'','','$admin_name','$x',
                  							'$contphotoDesc','$contphotoimg')
													");
						if (!$insertquery) {
							$error_img .= $error_request.'<br />';
						} else {
							$insertread = sql_get($tblcontphoto,"
														WHERE contphotoimg='$contphotoimg' ",
					"contphotoid,contphotoutil,contphotocontid,contphotoimg,contphotodesc");
							$values = "";
							foreach($array_lang as $keylg) {
								if ($keylg == $default_lg) {
									sql_updateone($tblcontphoto,"SET contphotorid=contphotoid, contphotolang='$default_lg' ","WHERE contphotoid='".$insertread[0]."' ");
								} else {
									$values .= ($values==''?'':',')
									."(NULL,'Y',$dbtime,'$keylg','".$insertread[0]
									."','$admin_name','$x','$contphotoDesc','$contphotoimg')";
								}
								$notice_img .= " $keylg ";
							}
							if ($values!='') {
								$insertquery = mysqli_query($connection, "INSERT INTO $tblcontphoto
											(`contphotoid`,`contphotostatut`,`contphotodate`,
											`contphotolang`,`contphotorid`,`contphotoutil`,
											`contphotocontid`,`contphotodesc`,`contphotoimg`)
															VALUES 
															$values
															");
							}
							$insert_read_3 = $insertread[3];
							$notice_img .= $enregistrementString.' '.$effectueString.'<br />';
							$_SESSION["contphotoDesc_$i"] = $contphotoDesc;
							if (in_array($ext,$array_img_ext))
							$notice_img .= '<img '.show_img_attr($insert_read_3).' src="'
										.$mainurl.$insert_read_3.'" align="center" vspace="5" 
										hspace="5" title="'.$insertread[4].'" alt="'
										.$insertread[4].'" border="0" /><br />';
							else
							$notice_img .= '<a href="'.$mainurl.$insert_read_3
										.'" target="_blank" title="'.$insertread[4].'" alt="'
										.$insertread[4].'"><img src="'.$mainurl.'images/'.$ext
										.'logo.gif" width="16" height="16" vspace="5" 
										hspace="5" title="'.$insertread[4].'" alt="'
										.$insertread[4].'" border="0" /> '.$insertread[4]
										.'</a><br />';
						}
					}
				}
			} else {
			//	$error_img .= $erreurString.' > '.$descriptionString.' '
			//	.($nof>1?'('.$i.')':'').' '
			//	.$error_invmiss.'<br />';// | <a href="'.$local_url.($x=='z'?'':
			//	'&amp;send=upldphoto').'">'.$retourString.'</a>
			}
		}
		$notice .= $notice_img;
		$error .= $error_img;
		if ($error !== '') {// && stristr($_SERVER['PHP_SELF'],$urladmin)) {
			$_SESSION['mv_error'] = $error;
			Header("Cache-Control: no-cache");
			Header("Pragma: no-cache");
			Header("Location: ".html_entity_decode($local_url.'&amp;send=upldphoto'));Die();
		}
#######################################################EFFACER PHOTO
#######################################################DELETE PIC
	} else if ($send == "delphoto") {
		if ((!isset($contphotoId)) || !preg_match("/^[0-9]+\$/", $contphotoId) 
			|| ($contphotoId == ''))
		{Header("Location: $redirect");Die();}
		$contphotoId = strip_tags($contphotoId);
		$editcontphoto = sql_get($tblcontphoto,"WHERE contphotorid='$contphotoId' 
												AND contphotolang='$lg' ",
								"contphotorid,contphotocontid,contphotoimg,contphotodesc");
		if ($editcontphoto[0] == '.')
		{Header("Location: $redirect");Die();}
		$edit_contphoto_2 = $editcontphoto[2];
		$deletequery = sql_del($tblcontphoto,"WHERE contphotorid='$contphotoId' ");
		if ($deletequery > '0') {
			$error .= '<p style="text-align:center">'.$error_request
					//."WHERE contphotorid='$contphotoId' "
					.'<br /> <br /><a href="'.$local_url.($x=='z'?'':'&amp;send=edit').'">'
					.$retourString.'</a></p>';
		} else {
			$xpldext = explode('.',strrev($edit_contphoto_2),2);
			$ext = strtolower(strrev($xpldext[0]));
			if (in_array($ext,$array_img_ext)) {
				$delurl = "../$editcontphoto[2]";
				@unlink($getcwd.$delurl);
				$bigdelurl = '../'.strrev($xpldext[1]).'_big.'.$ext;
				@unlink($getcwd.$bigdelurl);
				$oridelurl = '../'.strrev($xpldext[1]).'_ori.'.$ext;
				@unlink($getcwd.$oridelurl);
			} else {
				$delurl = "../".(stristr($editcontphoto[2],$filedir)?$filedir:'')
						.$editcontphoto[2];
				@unlink($getcwd.$delurl);
			}
			$notice .= $fichierString.' '.$effaceString.'<hr /><br />';
			if (!isset($notice)) {
				$notice .= '<br /></div><a href="'.$local_url.($x=='z'?'':'&amp;send=edit')
						.'">'.$retourString.'</a><!-- <br /> <br />S`assurer que le lien ainsi
						 que l`image sont retir&eacute;s du texte. --></p>';
				$notice .= '<div style="border:1px solid red;padding:10px;text-align:left;">
						<h1>'.$rapportString.'</h1>';
			}
			$notice .= substr($edit_contphoto_2,0,-(strlen($ext)+1)).'.'.$ext.'<br />';
			if (in_array($ext,$array_img_ext))
			$notice .= substr($edit_contphoto_2,0,-(strlen($ext)+1)).'_big.'.$ext.'<br />'
					.substr($edit_contphoto_2,0,-(strlen($ext)+1)).'_ori.'.$ext.'<br />';
		}
		if ($error == '') {// && stristr($_SERVER['PHP_SELF'],$urladmin)) {
			$_SESSION['mv_notice'] = $notice;
			Header("Cache-Control: no-cache");
			Header("Pragma: no-cache");
			Header("Location: ".html_entity_decode($local_url));Die();
		}
#######################################################FORME DE TELECHARGE doc
#######################################################PIC UPLOAD FORM
	} else if ($send == "uplddoc") {
		$content .= gen_form($lg,$x,$y);
		for ($j=0;$j<$nof;$j++) {
			if ($j == 0)
			$content .= ucfirst($documentString).' (.'.implode(", .",$array_doc_ext).' &amp; '
					.$max_filesizeString.')<br /> <br />';
			$content .= '<label for="condocDesc[]">'.$descriptionString.' '.$docString
					.' '.($j>0?$j:'').'</label><br /><input type="text" name="contdocDesc[]" 
					size="60" value="'.(isset($_SESSION["contdocDesc_$j"])?
					$_SESSION["contdocDesc_$j"]:'').'" /><br />
					<!--( a-z A-Z 0-9_- , " " > _ )<br />-->';
			$content .= '<input type="file" name="contdoc[]" /><br />';
			$_SESSION["contdocDesc_$j"] = null;
		}
		$content .= '<br /> <br /><input type="submit" name="send" value="'.$envoidocString
				.'" /> | <a href="javascript:history.back()//">'.$retourString.'</a></form>';
#######################################################ENVOI doc
#######################################################SEND PIC
	} else if ($send == $envoidocString) {
		for ($i=0;$i<$nof;$i++) {
			if (isset($_FILES["contdoc"]["name"][$i]) 
				&& ($_FILES["contdoc"]["name"][$i] != '')
				) {
				$userfile_name = $_FILES["contdoc"]["name"][$i];
				$userfile_tmp = $_FILES["contdoc"]["tmp_name"][$i];
				$userfile_size = $_FILES["contdoc"]["size"][$i];
				$userfile_type = $_FILES["contdoc"]["type"][$i];
				$ext = explode('.',strrev($userfile_name),2);
				$filename = space2underscore(strrev($ext[1]));
				$ext = strtolower(strrev($ext[0]));
				if (isset($_POST["contdocDesc"][$i]))
				$contdocDesc = space2underscore(stripslashes(str_replace("'","´",
													html_encode($_POST["contdocDesc"][$i]))));
				if ($contdocDesc == "")
				$contdocDesc = $filename;
				if (in_array($ext,$array_doc_ext))
				$valid_ext = "true";
				if (!isset($valid_ext))
				$valid_ext = "false";
				$sql = "WHERE contdoc='".$filedir.$contdocDesc.'.'.$ext."' ";
				$check = sql_nrows($tblcontdoc,$sql);
				if (!$contdocDesc || ($contdocDesc == '')|| ($userfile_name == '') 
					|| !preg_match("/^[a-zA-Z0-9_-]+\$/",space2underscore($contdocDesc))  
					|| (!$valid_ext == 'true') || ($valid_ext == 'false') 
					|| ($userfile_size > $max_filesize) || ($check > 0)
					) {
					$error_img .= $erreurString.'!<br />'.$listecorrectionString.'<ul>';
					if (!$contdocDesc || ($contdocDesc == '')
						|| !preg_match("/^[a-zA-Z0-9_-]+\$/",space2underscore($contdocDesc)))
					$error_img .= '<li>'.$nomString.' > '.$error_invmiss.'</li>';
					if (($userfile_name == '') 
						|| (!$valid_ext == 'true') || ($valid_ext == 'false'))	
					$error_img .= '<li>'.$fichierString.': '.$contdocDesc.' > '
																	.$error_invmiss.'</li>';
					if ($userfile_size > $max_filesize)
					$error_img .= '<li>'.$fichierString.' > '
												.$error_inv.' ('.$max_filesizeString.')</li>';
					if ($check > '0')
					$error_img .= '<li>'.$error_exists.'</li>'	;
					$error_img .= '</ul><br />';
				} else {
					$userfile_name = $filedir.space2underscore($contdocDesc).'.'.$ext;
					$location = "$userfile_name";
					$movelocation = "../$location";
					move_uploaded_file($userfile_tmp,$getcwd.$movelocation);
					$contdoc = $location;
					$insertquery = mysqli_query($connection, "INSERT INTO $tblcontdoc
										(`contdocid`,`contdocstatut`,`contdocdate`,
										`contdoclang`,`contdocrid`,`contdocutil`,
										`contdoccontid`,`contdocdesc`,`contdoc`)
												VALUES 
										(NULL,'Y',$dbtime,'','','$admin_name','$x',
										'$contdocDesc','$contdoc')
												");
					if (!$insertquery) {
						$error_img .= $error_request.'<br />';
					} else {
						$insertread = sql_get($tblcontdoc,"WHERE contdoc='$contdoc' ","contdocid,contdocutil,contdoccontid,contdoc,contdocdesc");
						$values = "";
						foreach($array_lang as $keylg) {
							if ($keylg == $default_lg) {
								sql_updateone($tblcontdoc,"SET contdocrid=contdocid, contdoclang='$default_lg' ","WHERE contdocid='".$insertread[0]."' ");
							} else {
								$values .= ($values==''?'':',')
										."(NULL,'Y',$dbtime,'$keylg','".$insertread[0]
										."','$admin_name','$x','$contdocDesc','$contdoc')";
							}
							$notice_img .= " $keylg ";
						}
						if ($values!='') {
							$insertquery = mysqli_query($connection, "INSERT INTO $tblcontdoc
                  						(`contdocid`,`contdocstatut`,`contdocdate`,
                  						`contdoclang`,`contdocrid`,`contdocutil`,
                  						`contdoccontid`,`contdocdesc`,`contdoc`)
														VALUES 
														$values
														");
						}
						$insert_read_3 = $insertread[3];
						$notice_img .= $enregistrementString.' '.$effectueString
									.'<!-- <br />pour le doc : "<i>'.$insertread[4].'</i>"
									<br />post&eacute; par '.$insertread[1].' --></b></font>
									<br /> <br /><a href="'.$mainurl.$insert_read_3
									.'" target="_blank" title="'.$insertread[4].'" alt="'
									.$insertread[4].'"><img src="'.$mainurl.'images/'.$ext
									.'logo.gif" width="16" height="16" vspace="5" hspace="5" 
									title="'.$insertread[4].'" alt="'.$insertread[4]
									.'" border="0" /> '.$insertread[4].'</a>';
						$_SESSION["contdocDesc_$i"] = $contdocDesc;
						if (!isset($notice))
						$notice_img .= '<br /> <br /><a href="'.$local_url
									.'&amp;send=uplddoc">'.$ajouterString.' 1 '.$docString
									.'</a><br /> <br /><div style="border:1px solid green;
									padding:10px;text-align:left;"><h1>'.$rapportString
									.'</h1><a href="'.$mainurl.$insert_read_3
									.'" target="_blank">'.$insert_read_3.'</a></div><br />';
					}
				}
			} else {
			//	$error_img .= $erreurString.' > '.$descriptionString.' '
			//				.($nof>1?'('.$i.')':'').' '.$error_invmiss.'<br />';
			}
		}
		$notice .= $notice_img;
		$error .= $error_img;
		if ($error !== '') {// && stristr($_SERVER['PHP_SELF'],$urladmin)) {
			$_SESSION['mv_error'] = $error;
			Header("Cache-Control: no-cache");
			Header("Pragma: no-cache");
			Header("Location: ".html_entity_decode($local_url.'&amp;send=uplddoc'));Die();
		}
#######################################################EFFACER doc
#######################################################DELETE PIC
	} else if ($send == "deldoc") {
		$contdocId = strip_tags($contdocId);
		$editcontdoc = sql_get($tblcontdoc,"WHERE contdocrid='$contdocId' ",
											"contdocrid,contdoccontid,contdoc,contdocdesc");
		$edit_contdoc_2 = $editcontdoc[2];
		$deletequery = sql_del($tblcontdoc,"WHERE contdocrid='$contdocId' ");
		if ($deletequery > '0') {
			$error .= $error_delete.'<br /><a href="'.$local_url.($x=='z'?'':'&amp;send=edit')
					.'">'.$retourString.'</a><br />';
		} else {
			$delurl = "../$edit_contdoc_2";
			@unlink($getcwd.$delurl);	
			$notice .= $fichierString.' '.$effaceString.'<br />';
			if (!isset($notice))
			$content .= '<a href="'.$local_url.'&amp;send=uplddoc">'.$ajouterString.' 1 '
					.$docString.'</a><br /> <br /><div style="border:1px solid red;
					padding:10px;text-align:left;"><h1>'.$rapportString.'</h1>'
					.$edit_contdoc_2.'</div><br /><a href="'.$local_url.($x=='z'?'':
					'&amp;send=edit').'">'.$retourString.'</a><br />';
		}
		if ($error == '') {// && stristr($_SERVER['PHP_SELF'],$urladmin)) {
			$_SESSION['mv_notice'] = $notice;
			Header("Cache-Control: no-cache");
			Header("Pragma: no-cache");
			Header("Location: ".html_entity_decode($local_url));Die();
		}
#######################################################ENVOYER ZIP
#######################################################SEND ZIP
	} else if ($send == ucfirst($envoyerString).' ZIP') {
		$templatesdir = $getcwd.$up.$libdir."templates/";
		if ($_FILES) {
			$userfile_name = $_FILES['myzip']['name'];
			$this_userfile_name = substr($userfile_name,0,-(strlen(".zip")));
			$userfile_tmp  = $_FILES['myzip']['tmp_name'];
			$userfile_size = $_FILES['myzip']['size'];
			$userfile_type = $_FILES['myzip']['type'];
			if (stristr($userfile_name,'zip')) {
				@set_time_limit(0);
				$src = $userfile_tmp;
				$dest = $templatesdir."$this_userfile_name/";
				$ARCHIVE = new zip;
				$ARCHIVE->extractTPLZip($src,$dest);
				if (is_dir($dest))
				$notice .= "<i>$this_userfile_name</i> ".$envoyeString.'<hr />';
			}
		} else
		$content .= "no zip<br />";
    
#######################################################
#######################################################
	} else { // send = different...
		Header("Location: $redirect");Die();
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($error == '') {// && stristr($_SERVER['PHP_SELF'],$urladmin)) {
			$_SESSION['mv_notice'] = $notice;
			Header("Cache-Control: no-cache");
			Header("Pragma: no-cache");
			Header("Location: ".html_entity_decode($local_url));Die();
		}
	}
} else { // logged_in = "false"
	Header("Location: $redirect");Die();
}
