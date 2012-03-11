<?PHP #۞ #
include '_incdb.php';

if (!isset($array_img_ext))
	$array_img_ext = array("jpg","jpeg","gif","png");
if (!isset($array_swf_ext))
	$array_swf_ext = array("swf","flv","wmv","avi","mp3","mov","rm","mpeg","mpg","asf");
if (!isset($array_doc_ext))
	$array_doc_ext = array("doc","xls","ppt","pps","pdf");
if (!isset($invisibleFileNames))
  $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
if (!isset($tiny_mp3_inflash))
	$tiny_mp3_inflash = false;
if (!isset($mp3_logo))
	$mp3_logo = "images/mp3logo.png";
if (!isset($mp3_params))
	$mp3_params = "&vol=60";//"&autostart=false&playlist=empty_playlist.xml&action=stop&vol=50";//&color=AFED2F&textcolor=006600&loop=no&lma=yes&textcolor=006600&color=AFED2F
if (!isset($array_vars_mp3_player))
	$array_vars_mp3_player = array("playlist","autostart","vol","artist","title","url");

###########################!!! ADMIN !!!

$tmcels = $_REQUEST["tmcels"];
if (in_array($_REQUEST["lg"],$array_lang))
$lg = $_REQUEST["lg"];
else $lg = $default_lg;

if (($tmcels == "img") || ($tmcels == "swf")) {
	$contPhotocount = sql_nrows($tblcontphoto," WHERE contphotolang='$lg' ");
	$tinyMCE_photos = 'var tinyMCEImageList = new Array(';
		// var tinyMCEImageList = new Array(["test", "content/test.gif"]);
	$tinyMCE_flashs = 'var tinyMCEMediaList = new Array(';
//	$tinyMCE_flashs = 'var tinyMCEFlashList = new Array(';
		// Name, URL	["Logo 1", "example_data/logo.jpg"],["Logo 2 Over", "example_data/logo_over.jpg"]
	$read = @mysql_query("SELECT * FROM $tblcontphoto WHERE contphotolang='$lg' ");
	$loop_tinyMCE_photos = "";
	$loop_tinyMCE_flashs = "";
	for ($i=0;$i<$contPhotocount;$i++) {
		$row = @mysql_fetch_array($read);
		$bigori = strrev($row["contphotoimg"]);
		$bigori = explode('.', $bigori, 2);
		$ext = strrev($bigori[0]);
		$photoPath = strrev($bigori[1]);
		if (in_array($ext,$array_swf_ext)) {
			if ($loop_tinyMCE_flashs !== "")	$loop_tinyMCE_flashs .= ",";
			$loop_tinyMCE_flashs .= '["'.$row["contphotodesc"].' ('.$ext.')","';
		//	if ($ext == 'flv')	$loop_tinyMCE_flashs .= 'lib/mediaplayer.swf?vid=';
			if ($ext == 'flv')	$loop_tinyMCE_flashs .= $filedir.'flv_player.swf?flvToPlay=';
			if ($ext == 'mp3') {
        if ($tiny_mp3_inflash === true)	{
          $title = '';
          $artist = NULL;
          $exploded_title = explode("/",strrev($photoPath),2);
          $title = str_replace("_"," ",strrev($exploded_title[0]));
          if (in_array("artist",$array_vars_mp3_player) && strstr($title,"-")) {
            $title = explode("-",$title,2);
            $artist = trim($title[0]);
            $title = trim($title[1]);
          }
          $loop_tinyMCE_flashs .= $filedir.'mvmp3_player.swf?playlist=empty_playlist.xml&title='.$title.(isset($artist)?'&artist='.$artist:'').$mp3_params.'&url=';
        }
      }
			$loop_tinyMCE_flashs .= ($ext=='mp3'?'':$up);
			$loop_tinyMCE_flashs .= $row["contphotoimg"].'"]';
		//	$loop_tinyMCE_flashs .= str_replace("_", " ", $row["contphotoimg"]).' ('.$ext.')"]';
		} else {
			if ($loop_tinyMCE_photos !== "")	$loop_tinyMCE_photos .= ",";
			$loop_tinyMCE_photos .= '["'.str_replace("_", " ", $row["contphotodesc"]).' (sml)", "'.$row["contphotoimg"].'"],';
			$loop_tinyMCE_photos .= '["'.str_replace("_", " ", $row["contphotodesc"]).' (big)", "'.$photoPath.'_big.'.$ext.'"],';
			$loop_tinyMCE_photos .= '["'.str_replace("_", " ", $row["contphotodesc"]).' (ori)", "'.$photoPath.'_ori.'.$ext.'"]';
		}
	}
	$tinyMCE_photos .= $loop_tinyMCE_photos.');';
/*
echo '<?xml version="1.0" encoding="UTF-8"?'.'>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
  <trackList>
';
*/
	$list_in_dir = scanDirectories("../");
	if (count($list_in_dir) > 0) {
		for ($i=0;$i<count($list_in_dir);$i++) {
			if (strstr($list_in_dir[$i],"../")) $list_in_dir[$i] = substr($list_in_dir[$i],3);
			if ($list_in_dir[$i][0] == "/") $list_in_dir[$i] = substr($list_in_dir[$i],1);
			if (!strstr($loop_tinyMCE_flashs,$list_in_dir[$i]) && !strstr($list_in_dir[$i],$urladmin)) {
				$bigori = strrev($list_in_dir[$i]);
				$bigori = explode('.', $bigori, 2);
				$ext = strrev($bigori[0]);
//ok	echo $list_in_dir[$i]."\n\r";
				if (in_array($ext,$array_swf_ext)) {
					$photoPath = strrev($bigori[1]);
					if ($loop_tinyMCE_flashs !== "")	$loop_tinyMCE_flashs .= ",";
					$loop_tinyMCE_flashs .= '["'.$photoPath.' ('.$ext.')","';
					if ($ext == 'flv')	$loop_tinyMCE_flashs .= $filedir.'flv_player.swf?flvToPlay=';
					if ($ext == 'mp3') {
            if ($tiny_mp3_inflash === true)	{
              $title = '';
              $artist = NULL;
              $exploded_title = explode("/",strrev($photoPath),2);
              $title = str_replace("_"," ",strrev($exploded_title[0]));
              if (in_array("artist",$array_vars_mp3_player) && strstr($title,"-")) {
                $title = explode("-",$title,2);
                $artist = trim($title[0]);
                $title = trim($title[1]);
              }
              $url = $list_in_dir[$i];
/*
echo '
    <track>
  		<artist>'.$artist.'</artist>
  		<title>'.$title.'</title>
  		<url>'.$url.'</url>
  	</track>
';
*/
              $playlist = "";
              $potential_xml_list = explode("_",strrev($list_in_dir[$i]),2);
              $potential_xml_list = strrev($potential_xml_list[1]).".xml";
              $playlist = (file_exists($up.$potential_xml_list)?$potential_xml_list:'empty_playlist.xml');
              $loop_tinyMCE_flashs .= $filedir.'mvmp3_player.swf?playlist='.$playlist.'&title='.$title.(isset($artist)?'&artist='.$artist:'').$mp3_params.'&url=';
            }
          }
					$loop_tinyMCE_flashs .= ($ext=='mp3'?'':$up).$list_in_dir[$i].'"]';
				}
			}
		}
	}
/*
echo '
  </trackList>
</playlist>
';
*/
	$tinyMCE_flashs .= $loop_tinyMCE_flashs.');';
	if ($tmcels == "img") echo $tinyMCE_photos;
	else echo $tinyMCE_flashs;
} else if ($tmcels == "doc") {
  if (!isset($lg)) $lg = $default_lg;// || !in_array($lg,$array_lang)
	$where = "WHERE contstatut='Y' AND contlang='$lg' ";
	// Name, URL	["Logo 1", "example_data/logo.jpg"],["Logo 2 Over", "example_data/logo_over.jpg"]
	$read = @mysql_query("SELECT * FROM $tblcont $where ");
	$contPgcount = mysql_num_rows($read);
	$tinyMCE_pgs = '';
	for ($i=0;$i<$contPgcount;$i++) {
		$row = @mysql_fetch_array($read);
		if	($tinyMCE_pgs !== "")	$tinyMCE_pgs .= ","	;
		$tinyMCE_pgs .= '["'.str_replace("_", " ", $row["conttitle"]).'", "?lg='.$lg.'&amp;x='.$row["contpg"].'"]';
	//	$tinyMCE_pgs .= '["'.str_replace("_", " ", $row["conttitle"]).'", "'.$row["conturl"].'"]';
	}
	$contDoccount = sql_nrows($tblcontdoc," WHERE contdoclang='$lg' ");
	$loop_tinyMCE_docs = "";
	$tinyMCE_docs = 'var tinyMCELinkList = new Array(';
		// Name, URL	["Logo 1", "example_data/logo.jpg"],["Logo 2 Over", "example_data/logo_over.jpg"]
	$read = @mysql_query("SELECT * FROM $tblcontdoc WHERE contdoclang='$lg' ");
	for ($i=0;$i<$contDoccount;$i++) {
		$row = @mysql_fetch_array($read);
		$ext = strrev($row["contdoc"]);
		$ext = explode('.', $ext, 2);
		$ext = strrev($ext[0]);
		if	($loop_tinyMCE_docs !== "")	$loop_tinyMCE_docs .= ","	;
		$loop_tinyMCE_docs .= '["'.str_replace("_", " ", $row["contdocdesc"]).' ('.$ext.')", "'.$row["contdoc"].'"]';
	}
	if	($loop_tinyMCE_docs !== "")	$loop_tinyMCE_docs = $tinyMCE_pgs.",".$loop_tinyMCE_docs	; // ARRAY OF PAGES OF CONTENT
	else $loop_tinyMCE_docs = $tinyMCE_pgs	;
	$tinyMCE_docs .= $loop_tinyMCE_docs.');';
	echo $tinyMCE_docs;
} else {
	Header("Location: $redirect");Die();
}
?>