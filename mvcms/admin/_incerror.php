<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

// BACKWARD COMPATIBILITY

if(!function_exists('str_ireplace')){
    // http://us.php.net/manual/en/function.str-ireplace.php#81157
    function str_ireplace($search,$replace,$subject,&$count)
    {
        $token = chr(1);
        $haystack = strtolower($subject);
        $needle = strtolower($search);
        if (($needle===FALSE) || ($needle=='')) {
            return $subject;
        } else {
            $count = 0;
            while (($pos=strpos($haystack,$needle))!==FALSE){
                $subject = substr_replace($subject,$token,$pos,strlen($search));
                $haystack = substr_replace($haystack,$token,$pos,strlen($search));
                $count++;
            }
            $subject = str_replace($token,$replace,$subject);
            return $subject;
        }
    }
}

if(!function_exists('scandir')){
    function scandir($dir)
    {
        $dh  = opendir($dir);
        $array_files = array();
        while (false !== ($file_name = readdir($dh)))
        $array_files[] = $file_name;
        closedir($dh);
        return $array_files;
    }
}
/////////////////////////


function scanDirectories($rootDir,$allData=array())
{
    // set filenames and extensions invisible if you want
    global $invisibleFileNames,$invisibleFileExts;
    // take out last char of rootDir if it is directory_separator
    $rev_rootDir = strrev($rootDir);
    if (in_array($rev_rootDir[0],array("\\","/")))
    $rootDir = substr($rootDir,0,-1);
    // Open a known directory, and proceed to read its contents
    $dirContent = '';
    if (is_dir($rootDir)) {
        if ($dh = opendir($rootDir)) {
            while (($file = readdir($dh)) !== false) {
                if	($dirContent !== '')	$dirContent .= '|'	;
                $dirContent .= $file;
            }
            closedir($dh);
        }
    }
    //ok	echo $rootDir."\n\n".$dirContent." = dirContent ... \n\r";
    $dirContent = explode("|",$dirContent);
    if	(!is_array($dirContent))	$dirContent = array()	;

    foreach($dirContent as $key => $content) {
        // filter all files not accessible
        $path = $rootDir."/".$content;
        if(!in_array($content, $invisibleFileNames) && !stristr($content,"_backup_")) {
            // if content is file & readable, add to array
            if(is_file($path) && is_readable($path)) {
                if (isset($invisibleFileExts[0])) {
                    $path_ext = explode('.',strrev($path),2);
                    $path_ext = strrev($path_ext[0]);
                    if (!in_array($path_ext,$invisibleFileExts))
                    $allData[] = $path;
                }else
                // save file name with path
                $allData[] = $path;
                // if content is a directory and readable, add path and name
            }elseif(is_dir($path) && is_readable($path)) {
                // recursive callback to open new directory
                $allData = scanDirectories($path, $allData);
            }
        }
    }
    return $allData;
}


// http://stackoverflow.com/questions/1145775/why-does-this-simple-php-script-leak-memory
function memdiff()
{
    global $memdiff_bm;
    if (!isset($memdiff_bm))  $memdiff_bm = "";
    static $int = null;
    $current = memory_get_usage();
    $memdiff_bm .= "<hr />current memory usage: <strong>". $current . "</strong>\n<br />";
    if ($int === null) {
        $int = $current;
    } else {
        $memdiff_bm .= " -> usage since previous: <em>".($current - $int) . "</em>\n<br />";
        $int = $current;
    }
}


//CART

function calc_comm($this_facturevaleur,$minus=null)
{
    global $paypal_commission_percent,$paypalcommissionString;
    if ($paypal_commission_percent === true) {
        if ($minus == 1)
        $this_commission = round(($this_facturevaleur/(1+($paypalcommissionString/100))),2);
        else
        $this_commission = round(($this_facturevaleur*$paypalcommissionString/100),2);
    } else
    $this_commission = $paypalcommissionString;
    /*
     {
    if ($minus != null)
    $this_commission = $paypalcommissionString;
    else
    $this_commission = $this_facturevaleur;
    }
    */
    return $this_commission;
}

function price($price)
{
    $price += 0;
    if (is_float($price)) {
        $strrev = strrev($price);
        if (isset($strrev[1])&&($strrev[1]=="."))
        $price .= 0;
    }
    return $price;
}

//


function check_module_content($value_mod_array,$this_content)
{
    global $array_modules,$array_fixed_modules,$error_inv;
    $log = '';
    $array_modules = array_unique(array_merge($array_modules,$array_fixed_modules));
    $pattern = "[[".$value_mod_array."[a-zA-Z0-9,|]{0,}[:]{0,1}[a-zA-Z0-9,\-_=|]{0,}"."]]";
    preg_match($pattern,$this_content,$matches);
    if (isset($matches[0])) {
        $matches[0] = substr($matches[0],1,-1);
        if (!in_array($matches[0],$array_modules)) {
            $log = "Possible error in module <b>[".$matches[0]."] $error_inv => ".$value_mod_array."</b><br />";
            if (isset($matches[0])&&strstr($matches[0],":")) {
                $log = '';
                $passed_parameters = explode(":",$matches[0]);
                if (isset($passed_parameters[1])) {
                    $dbtables = explode(",",$passed_parameters[0]);
                    if (!in_array((isset($dbtables[0])?$dbtables[0]:$value_mod_array),$array_modules)) {
                        $log .= "Possible error in module <b>[".$matches[0]."] => <u>".$dbtables[0]."</u> $error_inv </b><br />";
                    }
                    if (isset($dbtables[1])) {
                        if (!in_array($dbtables[1],$array_modules))
                        $log .= "Possible error in module <b>[".$matches[0]."] => <u>".$dbtables[1]."</u> $error_inv </b><br />";
                    }
                }
            }
        }
        return $log;
    }
}

function img_size($image,$bigori=null)
{
    $img = explode(".",strrev($image),2);
    $imgname = strrev($img[1]);
    $ext = strrev($img[0]);
    if ($bigori)
    return $imgname.($bigori=='big'?"_big":"_ori").'.'.$ext;
    else
    return $image;
}

function mvtrace($script,$line, $message = "")
{
    \error_log(\sprintf("%s#%d: %s", basename($script), $line, $message));
    return "";
}

function connect()
{
    global $dbhost, $dbuser, $dbpass, $dbname;
    $connection = mysqli_connect("$dbhost", "$dbuser", "$dbpass", "$dbname");
    return $connection;
}

function gen_form($lg,$x=null,$y=null)
{
    global $postgetmethod,$mainurl,$local,$urladmin,$indexdotphpfroform,$htaccess4sef,$full_url;
    if (isset($x)&&!isset($y))
        return '<form enctype="multipart/form-data" name="f'.$x.'" action="'.$local
            .($htaccess4sef===true?lgx2readable($lg,$x):$indexdotphpfroform.'?lg='.$lg.'&amp;x='.$x).'" method="'.$postgetmethod.'">';//$form_method
    else if (isset($y))
        return '<form enctype="multipart/form-data" name="f'.$x.'" action="'.$local
            .$indexdotphpfroform.'?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'" method="'.$postgetmethod.'">';//$form_methody
    else
        return '<form enctype="multipart/form-data" name="f" action="'.$local
            .(!stristr($local,$urladmin)?($htaccess4sef===true?$lg:'?lg='.$lg):'').'" method="'.$postgetmethod.'">';//$form_method
    // return '<form enctype="multipart/form-data" name="'.$x.'" action="" method="'.$postgetmethod.'">';
    //$form_method_self // does not work on chrome... returns to root...
}

function delete_imgdoc($table,$imgdoc)
{
    global $trace,$getcwd,$up,$error_img,$notice_img,$array_img_ext;
    $ext = explode('.',$imgdoc);
    $ext = strtolower($ext[count($ext)-1]);
    $delurl = "$up$imgdoc";
    @unlink($getcwd.$delurl);
    if (in_array($ext,$array_img_ext)) {
        $bigdelurl = strrev($delurl);
        $bigdelurl = explode('.', $bigdelurl, 2);
        $bigdelurl = strrev($bigdelurl[1]).'_big.'.strrev($bigdelurl[0]);
        @unlink($getcwd.$bigdelurl);
        $oridelurl = strrev($delurl);
        $oridelurl = explode('.', $oridelurl, 2);
        $oridelurl = strrev($oridelurl[1]).'_ori.'.strrev($oridelurl[0]);
        @unlink($getcwd.$oridelurl);
    }
    return (@file_exists($getcwd.$delurl)?false:true);
}

function human_date($_this,$hour=null,$class=null)
{
    global $trace, $notice, $lg, $array_months, $array_days, $mainurl;
    $output = $_this;
    if (strstr($_this,"-")) {
        if (preg_match("/^[0a-zA-Z]\$/",$_this[0]))
        $output = date('Y-m-d h:i:s');
        $time = substr($output,-9);
        list($year,$month,$day) = explode('-',substr($output,0,-9));
        if (('13'>$month) && ($month>'0')) {
            $weekday = date("w", mktime(0,0,0,$month,$day,$year));
            $output = ($hour===true?$time.' ':'').$array_days[$weekday].' '.$day.' '.$array_months[$month-1].' '.$year;
            if ($class!=null)
            $output = '<span'.($class=='nl'?
                	'><img src="'.$mainurl.'images/cal_sml.png" align="left" width="17" height="20" alt=" " border="0" /> '
                :
                	' class="cal_sml">').$output.'</span>';
        }
    }
    return $output;
}

function edit_date($_this)
{
    global $trace, $lg, $array_months, $array_days, $notice, $null_time;
    $output = $_this;
    $date = explode(" ",$_this);
    $time = (isset($date[1])&&($date[1]!='')?$date[1]:$null_time);
    $time = (strlen($time)<strlen($null_time)?$time.substr($null_time,strlen($time)):$time);
    $date = $date[0];
    if (strstr($date,"-")) {
        if ($date[0]=='0')
        $output = date('Y-m-d h:i:s');
        list($year,$month,$day) = explode('-',substr($output,0,-9));
        if (('13'>$month) && ($month>'0'))
        $output = $day.'/'.$month.'/'.$year.($time!=$null_time?' '.$time:'');
    }
    if (!strstr($output,"/"))
    $output = date('d/m/Y');
    return $output;
}

function db_date($_this)
{
    global $trace, $lg, $notice, $null_time;
    $date = explode(" ",$_this);
    $time = (isset($date[1])&&($date[1]!='')?$date[1]:$null_time);
    $time = (strlen($time)<strlen($null_time)?$time.substr($null_time,strlen($time)):$time);
    $date = $date[0];
    if (!strstr($date,"/") || preg_match("/^[0a-zA-Z]\$/",$date))
    $output = date('d/m/Y');
    list($day,$month,$year) = explode('/',$date);
    $output = $year.'-'.$month.'-'.$day.($time!=$null_time?' '.$time:'');
    return $output;
}

// for flash sites
function html_entity_decode_utf8($string)
{
    static $trans_tbl;
    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'code2utf(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'code2utf(\\1)', $string);
    // replace literal entities
    if (!isset($trans_tbl)) {
        $trans_tbl = array();
        foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
        $trans_tbl[$key] = utf8_encode($val);
    }
    return strtr($string, $trans_tbl);
}


// Returns the utf string corresponding to the unicode value
function code2utf($num)
{
    if ($num < 128) return chr($num);
    if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    return '';
}

function keep_tags($text, $tags = array())
{
    $yield = $text;
    if (!empty($tags))
        $yield = preg_replace('#<(' . implode( '|', $tags) . ')>.*?<\/$1>#s', '', $text);
    return $yield;
}

function gen_lang()
{
    global $trace, $mainurl, $local, $urladmin, $url_mirror, $extractfromphpself, $x, $y, $lg, $tblcont, $tblenum, $tblstring, $default_lg,
        $htaccess4sef, $local_uri, $tblhtaccess, $array_lang, $x1subok;
    $params = "?"; // builds params for rewrite of URL
    foreach($_GET as $key => $value)
    if	($key !== 'lg')	$params .= $key."=".$value."&amp;"	;
    if	($htaccess4sef===true) {
        $local_uri = explode("?",$local_uri);
        $local_uri_path = $local_uri[0];
        if (isset($local_uri[1]))
        $local_uri_query = $local_uri[1];
        $local_uri_path_analysis = explode("/",strrev($local_uri_path));
        for($i=0;$i<count($local_uri_path_analysis);$i++)
        if ($local_uri_path_analysis[$i] != '') {
            $current_item = strrev($local_uri_path_analysis[$i]);
            $get_htaccess = sql_get($tblhtaccess,"WHERE htaccessurl='$current_item' AND htaccesslang='$lg' ","htaccesstype,htaccessitem");
            $field = $get_htaccess[0];
            $item = $get_htaccess[1];
            $current_page = space2underscore(sql_getone($tblcont,"WHERE contpg='$x' AND contlang='$lg' ","conttitle"));
            break;
        }
    }
    $tabinverted = "inverted";
    $gen_lang = "";
    if (count($array_lang) > 1) {
        //  if (!in_array(substr($local_uri,1,3),$array_lang))
        //  $local_uri = "/".$default_lg.$local_uri;
        foreach($array_lang as $key) {
            $keyid = sql_getone($tblenum,"WHERE enumtype='$key' ","enumtitre");
            if	($key == $lg)	${
"tabinverted".$key} = $tabinverted	;
                else	${
"tabinverted".$key} = ""	;
                    if	($key == $lg)	$aligntoleft = ''	;// align="left"
                    else	$aligntoleft = ''	;// align="right"
                    $gen_lang .= '<a href="';
                    $this_extracted_url = substr($_SERVER["PHP_SELF"], $extractfromphpself);
                    if	(stristr($_SERVER["PHP_SELF"],$urladmin))	$gen_lang .= $local.$params.'lg='.$key.'"'	;
                    else {
                        if	($htaccess4sef===true) {
                            $redir_uri = $key;
                            if (strlen($x)>1) {
                                for(($x1subok===true?$hj=1:$hj=2);$hj<strlen($x);$hj++){
                                    $substr_pg = substr($x,0,$hj);
                                    $redir_uri .= "/".space2underscore(sql_getone(
                                        $tblcont,
                                    	"WHERE contlang='$key' AND contpg='$substr_pg' ",
                                    	"conttitle"
                                    ));
                                }
                            }
                            $redir_uri .= "/".space2underscore(sql_getone($tblcont,"WHERE contpg='$x' AND contlang='$key' ","conttitle"));
                            if (isset($current_page) && isset($current_item) && ($current_page != $current_item))
                            $redir_uri = lgx2readable($key,'',$field,$item);
                        }
                        if	($params == "?")	$params = ""	;
                        if	($x == '1')
                            $gen_lang .= ($htaccess4sef===true?$url_mirror.$redir_uri:'index_'.$key.'.php').$params.'"'	;
                        else
                            $gen_lang .= ($htaccess4sef===true?$url_mirror.$redir_uri:substr($this_extracted_url,0,-7).'_'.$key.'.php').$params.'"'	;
                    }
                    $gen_lang .= ' class="'.${"tabinverted".$key}.'">';
                    $sql_get_lang = sql_getone($tblstring,"WHERE stringlang='$key' AND stringtype='lang' AND stringtitle='$keyid' ","stringentry");
                    $gen_lang .= '<img src="'.$mainurl.'images/'.$key.'.gif" '.$aligntoleft.' title="'.$sql_get_lang.'" alt="'.$sql_get_lang
                            .'" border="0" width="24" height="24" class="'.${"tabinverted".$key}.'" /></a>&nbsp;';
        }
    }
    return $gen_lang;
}


function fieldis($_this)
{
    global $this_is;
    $fieldisthat = $this_is.$_this;
    return $fieldisthat;
}


function sql_get($dbtable,$where,$getrow)
{
    global $connection;
    $sql = mysqli_query($connection, "SELECT $getrow FROM $dbtable $where");
    $row = mysqli_fetch_array($sql);
    if (is_array($row)) {
        foreach($row as $sql_get) {
            $sql_get = $row;
        }
    }
    if (!is_array($row)) {
        $sql_get[0] = '.';
        $array_fields = array();
        if ($getrow !== '*') {
            $fields = explode(",", $getrow);
            if (!is_array($fields))
                $fields = array($getrow);
            foreach($fields as $field)
            {
                $sql_get []= "";
                $array_fields[$field] = "";
            }
        } else $sql_get = array(".","","","","","","","","","","","","","","","","","","","","","","","");
        $sql_get = array_merge($sql_get, $array_fields);
    }
    return $sql_get;
}


function sql_getone($dbtable,$where,$getone)
{
    global $connection;
    $sql = mysqli_query($connection, "SELECT $getone FROM $dbtable $where");
    $row = mysqli_fetch_array($sql);
    return $row[0];
}

function lgx2readable($this_lg,$this_x,$mod=null,$id=null)
{
    global $trace,$x,$notice,$admin_viewing,$array_lang,$tblcont,$x1subok,$full_url,$htaccess4sef,
        $tblhtaccess,$this_is,$filter_index,$filter_map,$filter_archive,$ordered_by,${"tbl".$this_is};
    $redirtourl = '';
    if (($htaccess4sef === false) || ($admin_viewing === true)) {
            if ($admin_viewing === true) {
                $redirtourl = "?lg=$this_lg";
                if (isset($mod)) {
                    /* this works for response on forum, check how to integrate, messes when in admin
                     if ($admin_viewing === true)
                    $redirtourl .= "&amp;x=".(isset($id)?
                    		"z&amp;y=$mod&amp;".$mod."Id=$id"
                    	:
                    		($this_is==$mod&&!isset($filter_index)&&!isset($filter_map)?
                    			$x:sql_getone($tblcont,"WHERE contlang='$this_lg' AND conttype='$mod' ","contpg")
                    		)
                    	);
                    else {
                    }
                    */
                    $redirtourl .= "&amp;x=".($this_is==$mod&&!isset($filter_index)&&!isset($filter_map)?$x:sql_getone($tblcont,"WHERE contlang='$this_lg' AND conttype='$mod' ","contpg"));
                    if (isset($id))
                    $redirtourl .= "&amp;".$mod."Id=$id";
                }
            } else {
                if (isset($mod)) {
                    if (isset($id)) {
                        $this_type = sql_getone(${
"tbl".$this_is},"WHERE ".$this_is."id='$id' ",$this_is."type");
                            $this_archive = sql_getone(${
"tbl".$this_is},"WHERE ".$this_is."id='$id' ",$this_is."archive");
                                if ($this_type != '')
                                $this_type_url = sql_getone($tblcont,"WHERE contstatut='Y' AND contpg!='$x' AND conturl LIKE '%".space2underscore(sql_stringit($this_is.'type',$this_type))."_$this_lg."."%' ","conturl");
                    }
                    if ((!isset($filter_archive) || (isset($filter_archive) && ($filter_archive == 'N'))) && (!isset($this_archive) || (isset($this_archive) && in_array($this_archive,array('','N'))))
                    // && ((!isset($filter_index) && !isset($filter_map)) && (isset($ordered_by) && strstr($ordered_by,'type')))
                    ) {
                        $type_url = (isset($this_type)&&($this_type!='')?$this_type_url:'');
                    }
                    //  if (!isset($type_url) || ($type_url == ''))
                    $redirtourl .= ($this_is==$mod&&!isset($filter_index)&&!isset($filter_map)&&(isset($this_archive)&&($this_archive=='Y'))?$_SERVER['PHP_SELF']:(isset($type_url)&&($type_url!='')?$type_url:sql_getone($tblcont,"WHERE contlang='$this_lg' AND conttype='$mod' ","conturl")));
                    if (isset($id))
                    $redirtourl .= "?".$mod."Id=$id".(isset($this_type)&&($this_type!='')?"&amp;{$mod}Type=".$this_type:'');
                } else {
                    $redirtourl .= sql_getone($tblcont,"WHERE contlang='$this_lg' AND contpg='$this_x' ","conturl");
                }
            }
        } else {
            if ((($mod !== null) && ($id !== null))) {
                //	if (($full_url === true) && (($mod !== null) && ($id !== null))) {
                if (!isset($filter_archive) && (isset($ordered_by) && strstr($ordered_by,'type'))) {
                    $type_url = space2underscore(sql_stringit($this_is.'type',sql_getone(${
"tbl".$this_is},"WHERE ".$this_is."rid='$id' ",$this_is."type")));
                }
                $get_redirtourl = sql_get($tblcont,"WHERE contlang='$this_lg' AND contstatut='Y' ".($this_is==$mod&&!isset($filter_index)&&!isset($filter_map)?" AND contpg='$x' ":" AND conttype='$mod' "),"conturl,contpg");
                $geturl = '';
                if (isset($tblhtaccess))
                $geturl = sql_getone($tblhtaccess,"WHERE htaccesstype='$mod' AND htaccessitem='$id' AND htaccesslang='$this_lg' ORDER BY htaccessdate DESC ","htaccessurl");
                $redirtourl = $get_redirtourl[0].(isset($type_url)&&($type_url!='')?"/$type_url":'').($geturl!=''?"/$geturl":"/$id");
                if ($full_url === false) $redirtourl .= '?'.$mod.'Id='.$id;
                $this_x = ($this_is==$mod&&!isset($filter_index)&&!isset($filter_map)?$x:$get_redirtourl[1]);
            } else if (isset($this_lg) && ($this_x == '') && ($mod !== null)) {
                // compares against type
                $this_x = sql_getone($tblcont,"WHERE contlang='$this_lg' AND contstatut='Y' AND conttype='$mod' ","contpg");
                $redirtourl = sql_getone($tblcont,"WHERE contlang='$this_lg' AND contstatut='Y' AND contpg='$this_x' ","conturl");
            } else {
                $redirtourl = sql_getone($tblcont,"WHERE contlang='$this_lg' AND contstatut='Y' AND contpg='$this_x' ","conturl");
            }
            for(($x1subok===true?$i=1:$i=2);$i<strlen($this_x);$i++) {
                $sub_x = substr((isset($sub_x)?$sub_x:$this_x),0,-1);
                $redirtourl = sql_getone($tblcont,"WHERE contlang='$this_lg' AND contstatut='Y' AND contpg='$sub_x' ","conturl").($redirtourl==''?'':"/".$redirtourl);
            }
            $redirtourl = (count($array_lang)>1?"$this_lg/":"").$redirtourl;
            if (!stristr($redirtourl,"?")&&(substr($redirtourl,-1)!="/")&&($redirtourl!=''))
            $redirtourl .= "/";
        }
        //	$trace .= (count($array_lang)>1?"$lg/":"").$redirtourl;
        return $redirtourl;
}

function sql_array($dbtable,$where,$getrow)
{
    global $connection, $trace;
    $sql = mysqli_query($connection, "SELECT $getrow FROM $dbtable $where");
    $nrows = mysqli_num_rows($sql);
    $sql_array = "";
    for ($i=0;$i<$nrows;$i++) {
        $row = mysqli_fetch_array($sql);
        $sql_array .= $row[$getrow].'|';
    }
    if	(in_array($sql_array,array("","|")))	$sql_array = array()	;//
    else	$sql_array = explode("|",substr($sql_array,0,-1))	;
    return $sql_array;
}


function sql_stringit($type,$title)
{
    global $connection, $tblstring, $tblenum, $lg;
    if ($connection) {
        $row = sql_getone($tblstring,"WHERE stringlang='$lg' AND stringtype='$type' AND stringtitle='$title' ","stringentry");
        if ($row == '') {
            $row = sql_getone($tblenum,"WHERE enumwhat='$type' AND enumtype='$title' ","enumtitre");
            if ($row != '')
            $row = sql_getone($tblstring,"WHERE stringlang='$lg' AND stringtype='$type' AND stringtitle='$row' ","stringentry");
        }
    } else $row = $title;
    return $row;
}


function sql_update($dbtable,$setq,$where,$getrow)
{
    global $connection;
    $sql = mysqli_query($connection, "UPDATE $dbtable $setq $where");
    $sql = mysqli_query($connection, "SELECT $getrow FROM $dbtable $where ");
    $row = mysqli_fetch_array($sql);
    if (is_array($row)) {
        foreach ($row as $sql_update) {
            $sql_update = $row;
        }
    }
    if (!is_array($row)) $sql_update = array(".","","","","","","","","","","","","","","","","","","","","","","","") ;
    return $sql_update;
}

function sql_updateone($dbtable,$setq,$where,$getrow)
{
    global $connection;
    $sql = mysqli_query($connection, "UPDATE $dbtable $setq $where");
    $sql = mysqli_query($connection, "SELECT $getrow FROM $dbtable $where ");
    $row = mysqli_fetch_array($sql);
    if (is_array($row)) {
        foreach ($row as $sql_update) {
            $sql_update = $row;
        }
    }
    if (!is_array($row)) $sql_update = array(".","","","","","","","","","","","","","","","","","","","","","","","") ;
    return $sql_update[0];
}

function sql_nrows($dbtable,$where)
{
    global $connection;
    $sql = mysqli_query($connection, "SELECT * FROM $dbtable $where");
    $row = mysqli_num_rows($sql);
    return $row;
}


function sql_del($dbtable,$where)
{
    global $connection;
    $sql = mysqli_query($connection, "DELETE FROM $dbtable $where");
    $sql = mysqli_query($connection, "SELECT * FROM $dbtable $where");
    $row = mysqli_num_rows($sql);
    return $row;
}


function sql_fields($dbtable,$output)
{
    global $connection, $trace,$error,$notice;
    $sql = "SELECT * FROM $dbtable ";
    $result = mysqli_query($connection, $sql);
    $i = 0;
    $array_types = "";
    $array_fields = "";
    $list_all_fields = "";
    if ($result) {
        while ($i < mysqli_num_fields($result)) {
            $meta = mysqli_fetch_field($result);
            if (!$meta) {
                $array_fields .= "";
                $list_all_fields .= "";
            }
            if ($i == mysqli_num_fields($result)-1) {
                $array_types .= "$meta->type";
                $array_fields .= "$meta->name";
                $list_all_fields .= "`$meta->name`";
            } else {
                $array_types .= "$meta->type,";
                $array_fields .= "$meta->name,";
                $list_all_fields .= "`$meta->name`, ";
            }
            $i++;
        }
        mysqli_free_result($result);
        if ($output == 'types') {
            $batch_array_types = explode(',', $array_types);
            return $batch_array_types;
        } else if ($output == 'array') {
            $batch_array_fields = explode(',', $array_fields);
            return $batch_array_fields;
        } else if ($output == 'list') {
            $list_all_fields = '('.$list_all_fields.')';
            return $list_all_fields;
        } else {
            $count_batch_array_fields = substr_count($array_fields, ',');
            return $count_batch_array_fields;
        }
    }
}

function get_types($_this)
{
    global $connection, $trace,$notice,$redirect,${
"tbl".$_this},$dbtime,$curdate,$array_compare_by_exacttime,$this_is,$that_is,$lg;
        if (!isset($array_compare_by_exacttime)) $array_compare_by_exacttime = array();
        global $array_fields,$that_array_fields;
        global $array_fields_type,$mediumtext_array,$longtext_array,$enumYN_array,$enumtype_array,$int3_array,$datetime_array;
        $dbtable = ${
"tbl".$_this};
            $this_array_fields = sql_fields($dbtable,'array');
            if (!isset($array_fields_type))
            $array_fields_type = array();//lists all types for a given table
            if (!isset($mediumtext_array))
            $mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
            if (!isset($longtext_array))
            $longtext_array = array(); // textarea with tinyMCE: word style UI
            if (!isset($enumYN_array))
            $enumYN_array = array(); // either Y or NO: produces selectable option code
            if (!isset($enumtype_array))
            $enumtype_array = array(); // int(11) unsigned, produces selectable code for all possibilities taken from enum and assign string, then allows creation of new type, further options apply like - for deleting the selected item
            if (!isset($int3_array))
            $int3_array = array(); // int(3) unsigned, flag for fetching items from referenced table
            if (!isset($datetime_array))
            $datetime_array = array(); // datetime, flag for showing calendar
            $result = mysqli_query($connection, "SHOW FIELDS FROM $dbtable");
            if (!$result) {
                Header("Location: $redirect");Die();
            } // no table or no connection...
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
            $array_fields_type = array_unique($array_fields_type);
            $mediumtext_array = array_unique($mediumtext_array);
            $longtext_array = array_unique($longtext_array);
            $enumYN_array = array_unique($enumYN_array);
            $enumtype_array = array_unique($enumtype_array);
            $int3_array = array_unique($int3_array);
            $datetime_array = array_unique($datetime_array);
}

function filter_sql_q($_this)
{
    global $connection, $trace,$notice,$redirect,${
"tbl".$_this},$dbtime,$curdate,$array_compare_by_exacttime,$this_is,$that_is,$lg;
        if (!isset($array_compare_by_exacttime)) $array_compare_by_exacttime = array();
        global $array_fields,$that_array_fields;
        global $filter_future,$filter_past,$filter_archive,$filter_search,$filter_searchfield,$q;
        if (!isset($sql_q)) $sql_q = '';
        $dbtable = ${
"tbl".$_this};
            /*
             if (($_this == $this_is) || (isset($that_is) && ($_this == $that_is))) {
            $this_array_fields = sql_fields($dbtable,'array');
            $array_fields_type = array();//lists all types for a given table
            $mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
            $longtext_array = array(); // textarea with tinyMCE: word style UI
            $enumYN_array = array(); // either Y or NO: produces selectable option code
            $enumtype_array = array(); // int(11) unsigned, produces selectable code for all possibilities taken from enum and assign string, then allows creation of new type, further options apply like - for deleting the selected item
            $int3_array = array(); // int(3) unsigned, flag for fetching items from referenced table
            $datetime_array = array(); // datetime, flag for showing calendar
            $result = mysqli_query($connection, "SHOW FIELDS FROM $dbtable");
            if (!$result) {Header("Location: $redirect");Die();} // no table or no connection...
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
            } else
            global $array_fields_type,$mediumtext_array,$longtext_array,$enumYN_array,$enumtype_array,$int3_array,$datetime_array;
            */
            if (($_this == $this_is) || (isset($that_is) && ($_this == $that_is))) {
            global $array_fields_type,$mediumtext_array,$longtext_array,$enumYN_array,$enumtype_array,$int3_array,$datetime_array;
        } else {
            $this_array_fields = sql_fields($dbtable,'array');
            $array_fields_type = array();//lists all types for a given table
            $mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
            $longtext_array = array(); // textarea with tinyMCE: word style UI
            $enumYN_array = array(); // either Y or NO: produces selectable option code
            $enumtype_array = array(); // int(11) unsigned, produces selectable code for all possibilities taken from enum and assign string, then allows creation of new type, further options apply like - for deleting the selected item
            $int3_array = array(); // int(3) unsigned, flag for fetching items from referenced table
            $datetime_array = array(); // datetime, flag for showing calendar
            $result = mysqli_query($connection, "SHOW FIELDS FROM $dbtable");
            if (!$result) {
                Header("Location: $redirect");Die();
            } // no table or no connection...
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
        if (isset($this_array_fields) && in_array($_this."lang",$this_array_fields))
        $sql_q .= " AND ".$_this."lang='$lg' ";
        if (isset($q) && ($q != ''))
        if (isset($filter_search) && isset($filter_searchfield) && (in_array($this_is.$filter_searchfield,$array_fields) || (isset($that_is) && in_array($that_is.$filter_searchfield,$that_array_fields)))) {
            if (isset($that_is) && in_array($that_is.$filter_searchfield,$that_array_fields)) {
                // && ($_this == $that_is)
                $sql_q .= " AND ".$that_is.$filter_searchfield." LIKE '%$q%' ";
            } else {
                if (in_array($this_is.$filter_searchfield,$array_fields))
                $sql_q .= " AND ".$this_is.$filter_searchfield." LIKE '%$q%' ";
            }
        } else {

            /*

            if (isset($array_fields[0])) {
            $loop_array_q = '';
            foreach($array_fields as $key)
            // search only on this // pay attention, different from mod_generic 4 lines down @ OR
            if (isset($q) && ($q != '')) {
            if (($key != $this_is."id") && ($key != $this_is."date") && ($key != $this_is."statut")) {
            $array_q = explode(" ",$q);
            if (!isset($array_q[1])) $array_q = ($loop_array_q==''?'':" OR ").$key." LIKE '%".$q."%' ";
            else $array_q = implode("%' OR ".$key." LIKE '%",$array_q);
            if ($array_q != '') $loop_array_q .= $array_q;
            }
            $q = $q;
            }
            // end of search
            if ($loop_array_q != '') $sql_q .= " AND ( ".$loop_array_q." ) ";
            }

            if (isset($array_fields[0]))
            foreach($array_fields as $key)
            // search only on this
            if (isset($q) && ($q != '')) {
            if (($key != $this_is."id") && ($key != $this_is."date") && ($key != $this_is."statut")) {
            $array_q = explode(" ",$q);
            if (!isset($array_q[1])) $array_q = ($sql_q!=''?" OR ":' AND ').$key." LIKE '%".$q."%' ";
            else $array_q = implode("%' OR ".$key." LIKE '%",$array_q);
            if ($array_q != '') $sql_q .= $array_q;
            }
            $q = $q;
            }
            // end of search

            */
        }

        if (!isset($curdate))
        $curdate = str_replace("NOW()","CURDATE()",$dbtime);

        foreach(sql_fields($dbtable,'array') as $key_filter) {
            $key_filter = substr($key_filter,strlen($_this));
            global ${
"filter_".$key_filter};
                if (isset(${
"filter_".$key_filter}) && !in_array($_this.$key_filter,$datetime_array)) {
                    if (is_bool(${
"filter_".$key_filter}) && in_array($_this.$key_filter,$enumYN_array))
                        $sql_q .= " AND $_this$key_filter='".(${
"filter_".$key_filter}===true?'Y':'N')."' ";
                        else
                        $sql_q .= " AND $_this$key_filter='".${
"filter_".$key_filter}."' ";
                } else {
                    if (isset($filter_archive))
                    if (is_bool($filter_archive))
                    $check = " AND ".$_this."date".($filter_archive===true?' <':' >=').(in_array($_this.'date',$array_compare_by_exacttime)?" $dbtime ":" $curdate ");
                    else {
                        if (in_array($filter_archive,array($key_filter,"-$key_filter")))
                        $check = " AND ".$_this.($key_filter==$filter_archive?$filter_archive.' <':substr($filter_archive,1).' >=').(in_array($_this.($key_filter==$filter_archive?$filter_archive:substr($filter_archive,1)),$array_compare_by_exacttime)?" $dbtime ":" $curdate ");
                    }
                    else {
                        if (isset($filter_future) && ($filter_future == $key_filter) && in_array($_this.$filter_future,$datetime_array))
                        $check = " AND ".$_this.$key_filter." >= ".(in_array($_this.$key_filter,$array_compare_by_exacttime)?" $dbtime ":"$curdate ");
                        if (isset($filter_past) && ($filter_past == $key_filter) && in_array($_this.$filter_past,$datetime_array))
                        $check = " AND ".$_this.$key_filter." < ".(in_array($_this.$key_filter,$array_compare_by_exacttime)?" $dbtime ":"$curdate ");
                    }
                    if (isset($check)&&!strstr($sql_q,$check)) $sql_q .= $check;
                }
        }
        $my_q = $sql_q;
        return $my_q;
}


/*##########################################################
 domain names follow these rules :
Use only letters, numbers, or hyphen ("-")
Cannot begin or end with a hyphen
Must have less than 63* characters, not including extension
##########################################################*/
function is_email($addr)
{
    $addr = strtolower(trim($addr));
    $addr = str_replace("&#064;","@",$addr);
    if ( (strstr($addr, "..")) || (strstr($addr, ".@")) || (strstr($addr, "@.")) || (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9][a-z0-9.-]{0,61}[a-z0-9]\.[a-z]{2,6}\$/", stripslashes(trim($addr)))) ) {
        $emailvalidity = 0;
    } else {
        $emailvalidity = 1;
    }
    return $emailvalidity;
}

function is_valid_email($email)
{
    $email = strtolower(trim($email));
    $email = str_replace("&#064;","@",$email);
    return $email;//preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $email);
}

function contains_bad_str($str_to_test)
{
    $bad_strings = array(
        "content-type:",
        "mime-version:",
        "multipart/mixed",
        "Content-Transfer-Encoding:",
        "bcc:",
        "cc:",
        "to:",
    );
    if ($str_to_test != '')
    foreach($bad_strings as $bad_string) {
        if(stristr($bad_string,strtolower($str_to_test))) {
            echo "$bad_string found. Suspected injection attempt - mail not being sent.";
            exit;
        }
    }
}

function contains_newlines($str_to_test)
{
    if(preg_match("/(%0A|%0D|\\n+|\\r+)/i", $str_to_test) != 0) {
        echo "newline found in $str_to_test. Suspected injection attempt - mail not being sent.";
        exit;
    }
}



function is_url($url)
{
    $url = strtolower(trim($url));
    if ( (strstr($url, "..")) || (strstr($url, "@")) || (strstr($url, " ")) || (strstr($url, "%20")) || ( (strpos(strrev($url), ".") == '0') ) || (!preg_match("/^[[:alpha:]][a-z0-9:\/_.-]*[a-z0-9][a-z0-9.-]{0,61}[a-z0-9]\.[a-z]{2,6}[a-z0-9\/_.-@#%&()+=?]*[[:alnum:]]*\$/", stripslashes(trim($url)))) ) {
        $urlvalidity = 0;
    } else {
        $urlvalidity = 1;
    }
    return $urlvalidity;
}


function html_encode($_str)
{
    global $trace;
    if (!is_string($_str))
        error_log(print_r($_str, true));
    $str = stripslashes(trim($_str));
    $table3 = get_html_translation_table(HTML_ENTITIES);
    //	$table3[" "] = '&nbsp;'; // taken out otherwise goes off the screen
    $table3["@"] = '&#064;';
    $table3["'"] = '&acute;';
    $table3["`"] = '&acute;';
    $table3["’"] = '&acute;';
    $table3["…"] = '...';
    $table3["<"] = '<';
    $table3[">"] = '>';
    $table3["&"] = '&';
    $table3["_"] = '-';
    return strtr($str, $table3);
}


function space2underscore($_str)
{
    global $trace,$do,$lg,$charset_iso;
    $str = strtolower(trim($_str));
    if (($do == "ru") || ($lg == "ru")) {
        //  global $cyrlat;
        require_once 'library/cyrlat.class.php';
        $cyrlat = new CyrLat;
        $str = $cyrlat->cyr2lat(html_entity_decode($str,ENT-QUOTES,"cp1251"));
        //  echo space2underscore($cyrlat->cyr2lat($str));
    }
    /* probe
    if (!strstr($str, '&'))
    if ($charset_iso == 'UTF-8') {
      //  $str = htmlspecialchars(strtolower(trim($_str)));
        $str = html_entity_decode_utf8(stripslashes($str));
    } else {
        $str = html_entity_decode(stripslashes($str));
    }
    */
    $str = html_entity_decode(stripslashes($str));
    $new_str = "";
    $str_l = strlen($str);
    for ($i=0;$i<$str_l;$i++) {
        if (preg_match("/^[a-zA-Z0-9_ -]{1}\$/", $str[$i])) {
            if (in_array($str[$i],array(" ","_")))
            	$str[$i] = "-";
            $new_str .= $str[$i];
        } else {
            if (preg_match("/^[[:punct:]]{1}\$/",$str[$i])) {
                $new_str .= "-";
            } else {
                $str_ncd = html_encode($str[$i]);
                $array_codes = array("acute","grave","circ","tilde","uml","ring","elig","slash","horn","zlig","cedil","th");
                $code = substr(substr($str_ncd,2),0,-1);
                if (isset($str_ncd[1]) && preg_match("/^[a-z]\$/", $str_ncd[1]) && in_array($code,$array_codes)) {
                    $new_str .= $str_ncd[1];
                    if ($code == "elig") $new_str .= "e"; // e dans l`o, l`a...
                    if ($code == "zlig") $new_str .= "s"; // in german, szet is replaced by ss, not sz
                } else {
                    $new_str .= "-"; // other punctuation codes
                }
            }
        }
    }
    $new_str = preg_replace("/(\-)+/", "$1", $new_str);
    // next step is better than ($i+1==strlen($str)?"":"-") in case repeated punct at end of str
    if (substr(strrev($new_str),0,1) == '-')
        $new_str = substr($new_str,0,-1);

    return $new_str;
}


function show_img_attr($img_url)
{
    global $mainurl;
    if	(!stristr($img_url, "https://"))	$img_url = $mainurl.$img_url	;
    list($img_width, $img_height, $img_orient, $img_attr) = @getimagesize($img_url);
    return $img_attr;
}


function sql_nemails($email)
{
    //!!!	needs sql_fields and is_email above	//
    global $connection, $trace, $tblmembre;
    $select = "$tblmembre";
    $tables = explode(",",$select);
    $where = "";
    for ($i=0;$i<count($tables);$i++) {
        $fields_array = sql_fields($tables[$i],'array');
        for ($j=0;$j<sql_fields($tables[$i],'');$j++) {
            if (strstr($fields_array[$j], "email")) {
                if	($where !== '')	$where .= "OR"	;
                $where .= " $fields_array[$j]='$email' ";
            }
        }
    }
    $sql = mysqli_query($connection, "SELECT * FROM $select WHERE $where");
    $row = mysqli_num_rows($sql);
    return $row;
}


function mv_toggle($dbtable,$field,$item)
{
    global $trace,$notice,$tblhtaccess,$array_modules,$htaccess4sef;
    $value = sql_getone($dbtable,"WHERE $item ","$field");
    switch($value) {
        case '1': $newvalue = '0'; break;
        case '0': $newvalue = '1'; break;
        case 'Y': $newvalue = 'N'; break;
        case 'N': $newvalue = 'Y'; break;
        case '': $newvalue = 'Y'; break;
    }
    $toggled = sql_update($dbtable,"SET $field='$newvalue' ","WHERE $item ","$field");
    if (stristr($field,"statut") && ($htaccess4sef === true)) {
        $this_is = substr($dbtable,1);// _tbl
        $item = explode("=",trim($item));
        $item = substr($item[1],1,-1);
        if (in_array($this_is,$array_modules))
        $toggle_htaccess = sql_update($tblhtaccess,"SET htaccessstatut='$newvalue' ","WHERE htaccesstype='$this_is' AND htaccessitem='$item' ","htaccesssstatut");
    }
    return $toggled[0];
}


function format_edit($text,$do)
{
    global $tinyMCE, $trace, $mainurl, $filedir, $lg, $x, $y, $cliquericiString;
    if ($tinyMCE === false) {
        if ($do == "edit") {

            $text = stripslashes($text);

            $text = ereg_replace('" border="0" \/>','[/img]',$text);

            $text = ereg_replace('<img width="([^]]*)" height="([^]]*)" src="([^]]*)" align="right" hspace="5" vspace="5" alt="([^]]*)','[img=\3]\4',$text);

            $text = ereg_replace('</div>','[/div]',$text);
            $text = ereg_replace('" id="div">',']',$text);
            $text = ereg_replace('<div style="width:([^]]*);text-align:([^]]*);float:([^]]*);','[div=\1,\2,\3',$text);

            $text = ereg_replace('</a>','[/url]',$text);

            $text = ereg_replace('<a href="([^]]*)" target="_blank" name="doc">([^]]*)','[url=doc:\1]\2',$text);
            $text = ereg_replace('<a href="'.$mainurl.'([^]]*)" target="_self">([^]]*)','[url='.$mainurl.'\1]\2',$text);
            $text = ereg_replace('<a href="([^]]*)" target="_blank">([^]]*)','[url=\1]\2',$text);

            $text = ereg_replace('<i>','[i]',$text);
            $text = ereg_replace('</i>','[/i]',$text);
            if (in_array($lg, array('es','fr','it'))) {
                $text = ereg_replace('<b>','[g]',$text);
                $text = ereg_replace('</b>','[/g]',$text);
                $text = ereg_replace('<u>','[s]',$text);
                $text = ereg_replace('</u>','[/s]',$text);
            } else {
                $text = ereg_replace('<b>','[b]',$text);
                $text = ereg_replace('</b>','[/b]',$text);
                $text = ereg_replace('<u>','[u]',$text);
                $text = ereg_replace('</u>','[/u]',$text);
            }
            $text = ereg_replace('<hr />','[---]',$text);

            $formatted = stripslashes(strip_tags($text));

        } else {

            $formatted = nl2br(strip_tags($text));

            $text = ereg_replace('\[email=mailto:]\[/email]','',$text);
            $text = ereg_replace('\[email=]\[/email]','',$text);
            $text = ereg_replace('\[url=doc://]\[/url]','',$text);
            $text = ereg_replace('\[url=http://]\[/url]','',$text);
            $text = ereg_replace('\[url=https://]\[/url]','',$text);
            $text = ereg_replace('\[url=mailto:]\[/url]','',$text);
            $text = ereg_replace('\[url=]\[/url]','',$text);
            $text = ereg_replace('\[div=]','',$text);
            $text = ereg_replace('\[div=]\[/div]','',$text);
            $text = ereg_replace('\[img=]','',$text);
            $text = ereg_replace('\[img=][/img]','',$text);
            $text = ereg_replace('\[flash=]','',$text);
            $text = ereg_replace('\[i]\[/i]','',$text);
            $text = ereg_replace('\[g]\[/g]','',$text);
            $text = ereg_replace('\[b]\[/b]','',$text);
            $text = ereg_replace('\[s]\[/s]','',$text);
            $text = ereg_replace('\[u]\[/u]','',$text);

            $text = ereg_replace('\[i]','<i>',$text);
            $text = ereg_replace('\[/i]','</i>',$text);
            $text = ereg_replace('\[g]','<b>',$text);
            $text = ereg_replace('\[/g]','</b>',$text);
            $text = ereg_replace('\[b]','<b>',$text);
            $text = ereg_replace('\[/b]','</b>',$text);
            $text = ereg_replace('\[s]','<u>',$text);
            $text = ereg_replace('\[/s]','</u>',$text);
            $text = ereg_replace('\[u]','<u>',$text);
            $text = ereg_replace('\[/u]','</u>',$text);
            $text = ereg_replace('\[---]','<hr />',$text);
            $text = ereg_replace('\[/div]','</div>',$text);

            $text = ereg_replace('\[img=https://([^]]*)]([^]]*)\[/img]','<img src="https://\1" align="right" hspace="5" vspace="5" alt="\2" border="0" \/>',$text);
            $text = ereg_replace('\[img=([^]]*)]([^]]*)\[/img]','<img src="\1" align="right" hspace="5" vspace="5" alt="\2" border="0" \/>',$text);

            $text = ereg_replace('\[div=([^]]*),([^]]*),([^]]*)]','<div style="width:\1;text-align:\2;float:\3;" id="div">',$text);

            /* error with $up inserted prior content/... for admin */
            //	$text = ereg_replace('\[url=doc:([^]]*)]\[/url]','<a href="'.$mainurl.'\1" target="_blank" name="doc">'.$cliquericiString.'</a>',$text);
            //	$text = ereg_replace('\[url=doc:([^]]*)]([^]]*)\[/url]','<a href="'.$mainurl.'\1" target="_blank" name="doc">\2</a>',$text);
            $text = ereg_replace('\[url=doc:([^]]*)]\[/url]','<a href="\1" target="_blank" name="doc">'.$cliquericiString.'</a>',$text);
            $text = ereg_replace('\[url=doc:([^]]*)]([^]]*)\[/url]','<a href="\1" target="_blank" name="doc">\2</a>',$text);

            $text = ereg_replace('\[url=mailto:([^]]*)]([^]]*)\[/url]','<a href="mailto:\1" target="_blank">\2</a>',$text);
            $text = ereg_replace('\[url=mailto:([^]]*)]\[/url]','<a href="mailto:\1" target="_blank">\1</a>',$text);

            $text = ereg_replace('\[url='.$mainurl.'([^]]*)]([^]]*)\[/url]','<a href="'.$mainurl.'\1" target="_self">\2</a>',$text);
            $text = ereg_replace('\[url=([^]]*)]\[/url]','<a href="\1" target="_blank">'.$cliquericiString.'</a>',$text);

            $text = ereg_replace('\[url=([^]]*)]([^]]*)\[/url]','<a href="\1" target="_blank">\2</a>',$text);

            $formatted = stripslashes($text);

            $formattedpara = explode('<img src="',$formatted);

            $formatted = $formattedpara[0];
            for ($i=1;$i<count($formattedpara);$i++) {
                $attr = explode('"',$formattedpara[$i]);
                $attr = show_img_attr($attr[0]);
                if	($attr == '')	$attr = 'width="0" height="0"'	;
                $formatted .= '<img '.$attr.' src="'.$formattedpara[$i];
            }

        }
        return $formatted;
    } else {
        return $text;
    }
}

function upload_image($userfile_tmp,$filename,$ext)
{
    global $trace, $up, $max_sqrt, $max_sml, $max_big, $max_width, $max_height;
    $location = $filename.'.'.$ext;
    $movelocation = "$up$location";
    $bigfilename = $filename.'_big.'.$ext;
    $biglocation = $bigfilename;
    $bigmovelocation = "$up$bigfilename";
    $orifilename = $filename.'_ori.'.$ext;
    $orilocation = $orifilename;
    $orimovelocation = "$up$orifilename";
    list($width_orig, $height_orig, $orient, $attr) = getimagesize($userfile_tmp);
    if ( ($width_orig<$max_width) && ($height_orig<$max_height) ) {
        copy($userfile_tmp, $movelocation);
        copy($userfile_tmp, $bigmovelocation);
        move_uploaded_file($userfile_tmp, $orimovelocation);
    } else {
        $sqrt_tmp = $width_orig*$height_orig;
        $sqrt_tmp = sqrt($sqrt_tmp);
        ################# ### upl img square size of side > max_sqrt corresponding to servers -> error
        if ($sqrt_tmp > $max_sqrt) {
            $location = "error-sqrt";
        } else {
            if ($width_orig >= $height_orig) {
                // ############## LANDSCAPE OR SQUARE
                $sml_ratio = $height_orig/$width_orig;
                $big_ratio = $height_orig/$width_orig;
                $sml_width = $max_sml;
                $sml_height = $sml_width*$sml_ratio;
                $big_width = $max_big;
                $big_height = $big_width*$big_ratio;
            } else if ($height_orig > $width_orig) {
                // ############## PORTRAIT
                $sml_ratio = $width_orig/$height_orig;
                $big_ratio = $width_orig/$height_orig;
                $sml_height = $max_sml;
                $sml_width = $sml_height*$sml_ratio;
                $big_height = $max_big;
                $big_width = $big_height*$big_ratio;
            }
            $image_p = imagecreatetruecolor($sml_width, $sml_height);
            $bigimage_p = imagecreatetruecolor($big_width, $big_height);
            if ($ext == 'gif') {
                // medium image default 160x120, or 80x60
                $image = imagecreatefromgif( $userfile_tmp );
                // big image default 640x480
                $bigimage = imagecreatefromgif( $userfile_tmp );
                // http://it.php.net/manual/en/function.imagecopyresized.php#76648
                // if the image has transparent color, we first extract the RGB value of it,
                // then use this color to fill the thumbnail image as the background. This color
                // is safe to be assigned as the new transparent color later on because it will
                // be filtered by imagecopyresize.
                $originaltransparentcolor = imagecolortransparent( $image );
                if($originaltransparentcolor >= 0 // -1 for opaque image
                && $originaltransparentcolor < imagecolorstotal( $image )
                // for animated GIF, imagecolortransparent will return a color index larger
                // than total colors, in this case the image is treated as opaque ( actually
                // it is opaque )
                ) {
                    $transparentcolor = imagecolorsforindex( $image, $originaltransparentcolor );
                    $newtransparentcolor = imagecolorallocate(
                    $image_p,
                    $transparentcolor['red'],
                    $transparentcolor['green'],
                    $transparentcolor['blue']
                    );
                    // for true color image, we must fill the background manually
                    imagefill( $image_p, 0, 0, $newtransparentcolor );
                    // assign the transparent color in the thumbnail image
                    imagecolortransparent( $image_p, $newtransparentcolor );
                }
                // if the image has transparent color, we first extract the RGB value of it,
                // then use this color to fill the thumbnail image as the background. This color
                // is safe to be assigned as the new transparent color later on because it will
                // be filtered by imagecopyresize.
                $originaltransparentcolor = imagecolortransparent( $bigimage );
                if($originaltransparentcolor >= 0 // -1 for opaque image
                && $originaltransparentcolor < imagecolorstotal( $bigimage )
                // for animated GIF, imagecolortransparent will return a color index larger
                // than total colors, in this case the image is treated as opaque ( actually
                // it is opaque )
                ) {
                    $transparentcolor = imagecolorsforindex( $bigimage, $originaltransparentcolor );
                    $newtransparentcolor = imagecolorallocate(
                    $bigimage_p,
                    $transparentcolor['red'],
                    $transparentcolor['green'],
                    $transparentcolor['blue']
                    );
                    // for true color image, we must fill the background manually
                    imagefill( $bigimage_p, 0, 0, $newtransparentcolor );
                    // assign the transparent color in the thumbnail image
                    imagecolortransparent( $bigimage_p, $newtransparentcolor );
                }
            } else if ($ext == 'png') {
                $image = imagecreatefrompng($userfile_tmp);
                imageSaveAlpha($image, true);
                $bigimage = imagecreatefrompng($userfile_tmp);
                imageSaveAlpha($bigimage, true);
            } else {
                $image = imagecreatefromjpeg($userfile_tmp);
                $bigimage = imagecreatefromjpeg($userfile_tmp);
            }
            if ($ext == 'jpg') {
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $sml_width, $sml_height, $width_orig, $height_orig); // better quality for JPG
                imagecopyresampled($bigimage_p, $bigimage, 0, 0, 0, 0, $big_width, $big_height, $width_orig, $height_orig); // better quality for JPG
            } else {
                imagecopyresized($image_p, $image, 0, 0, 0, 0, $sml_width, $sml_height, $width_orig, $height_orig);
                imagecopyresized($bigimage_p, $bigimage, 0, 0, 0, 0, $big_width, $big_height, $width_orig, $height_orig);
            }
            if ($ext == 'gif') {
                imagegif($image_p, $movelocation, 90);
                imagegif($bigimage_p, $bigmovelocation, 90);
            } else if ($ext == 'png') {
                imagepng($image_p, $movelocation, 90);
                imagepng($bigimage_p, $bigmovelocation, 90);
            } else {
                imagejpeg($image_p, $movelocation, 90);
                imagejpeg($bigimage_p, $bigmovelocation, 90);
            }
            imagedestroy($image_p);
            imagedestroy($image);
            imagedestroy($bigimage_p);
            imagedestroy($bigimage);
            move_uploaded_file($userfile_tmp, $orimovelocation);
        }
    }
    return $location;
}

################################################### JAVASCRIPTS

$text_style_js = "<script type=\"text/javascript\"><!--
var uldescr = document.getElementById('uldescription');
function insertTag(tag, closetag) {
 if  (closetag == undefined)  closetag = tag  ;
 uldescr.focus();
 if (document.selection != undefined) { //Internet Explorer
  uldescr.focus();
  var rng = document.selection.createRange();
  rng.colapse;
  if (tag == 'membre') {
   rng.text = '|' + tag + closetag;
  } else if (closetag == '---' || closetag == 'flash') {
   rng.text = '[' + tag + ']' + rng.text;
  } else {
   rng.text = '[' + tag + ']' + rng.text + '[/' + closetag + ']';
  }
 }
 else if (uldescr.selectionEnd != undefined) { // Browsers with support for selectionStart and selectionEnd
  if (tag == 'membre') {
   uldescr.value =
    uldescr.value.substring(0, uldescr.selectionStart) + '|' + tag + closetag +
    uldescr.value.substring(uldescr.selectionEnd);
  } else if (closetag == '---' || closetag == 'flash') {
   uldescr.value =
    uldescr.value.substring(0, uldescr.selectionStart) + '[' + tag + ']' +
    uldescr.value.substring(uldescr.selectionEnd);
  } else {
   uldescr.value =
    uldescr.value.substring(0, uldescr.selectionStart) +
    '[' + tag + ']' +
    uldescr.value.substring(uldescr.selectionStart, uldescr.selectionEnd) +
    '[/' + closetag + ']' +
    uldescr.value.substring(uldescr.selectionEnd);
  }
 }
 else { // Browsers with no selection support
  if (tag == 'membre') {
   uldescr.value += '|' + tag + closetag;
  } else if (closetag == '---' || closetag == 'flash') {
   uldescr.value += '[' + tag + ']';
  } else {
   uldescr.value += '[' + tag + '][/' + closetag + ']';
  }
 }
}
function insertLink() {
 var link = prompt('Insert URL:', 'https://');
 if  (link && link != null && link != 'https://')  insertTag('url=' + link, 'url')  ;
}
function insertEmail() {
 var link = prompt('Insert Email:', 'mailto:');
 if  (link && link != null && link != 'mailto:')  insertTag('url=' + link, 'url')  ;
}
function insertDoc(doc) {
 if  (doc && doc != null)  insertTag('url=doc:' + doc, 'url')  ;
}
function insertImage(img) {
 if  (img && img != null)  insertTag('img=' + img, 'img')  ;
}
function insertFlash(swf) {
 if  (swf && swf != null)  insertTag('flash=' + swf, 'flash')  ;
}
function insertDiv() {
 var div = prompt('Insert DIV width (0-9)(% or px), text-align (left,center,right), align (left,right) + [ ; extra_css:properties ] :', '');
 if  (div && div != null && div != ',')  insertTag('div=' + div, 'div')  ;
}
//--></script>";


// ##########\/\/\/\/\/ NOT FULL  SEE BELOW \/\/\/\/\/######################## option select
function gen_selectoption($table,$selected,$type,$what,$show_count=null)
{
    global $connection, $dbtable, $trace, $lg, $optionselected, $tblenum, $tblstring, $this_is, $nRowsThis_is, $nRowsThis_isy, $nRowsThis_isn, $fieldis, $toutString, $achoisirString;
    $selectoptions = '';
    if (is_array($table)) {
        if (in_array($selected,array('ajout','0','',null)))
        $selectoptions = '<option value=""> >> '.$achoisirString.' &nbsp;</option>';
        foreach($table as $key) {
            $key = html_encode($key);
            ${
                $key."String"} = sql_getone($tblstring, " WHERE stringlang='$lg' AND stringtype='general' AND stringtitle='$key' ", "stringentry");
                if ($key == $selected) $this_select = $optionselected;
                else  $this_select = '';
                $selectoptions .= '<option value="'.$key.'"'.$this_select.'>'.(sql_stringit(($what!=''?$what:'general'),$key)!=''?sql_stringit(($what!=''?$what:'general'),$key):(${
                    $key."String"}==''?$key:${
                        $key."String"})).'</option>';
        }
    } else if ($table == $tblenum) {
        $read = mysqli_query($connection, "
						SELECT * FROM $table
						WHERE enumstatut='Y'
						AND enumtype LIKE '%$type%'
						AND enumwhat LIKE '%$what%'
						");
        $nRows = sql_nrows($table, "
							WHERE enumstatut='Y'
							AND enumtype LIKE '%$type%'
							AND enumwhat LIKE '%$what%'
							");
        if (in_array($selected,array('ajout','0','',null))) {
            $selectoptions = '<option value=""> >> '.$achoisirString.' &nbsp;</option>';
            for ($i=0;$i<$nRows;$i++) {
                $row = mysqli_fetch_array($read);
                $selectoptions .= '<option value="'.$row["enumtitre"].'"> '.sql_stringit(($what==''?'general':$what),$row["enumtitre"]).' </option>';
            }
        } else {
            if (isset($nRowsThis_is) && ($show_count!=null))
            $selectoptions = '<option'.($selected==$toutString?$optionselected:'').' value="'.$toutString.'"> ('.$nRowsThis_is.') '.$toutString.' &nbsp;</option>';
            for ($i=0;$i<$nRows;$i++) {
                $row = mysqli_fetch_array($read);
                $row_enumtitre = $row["enumtitre"];
                if ($row["enumtitre"] == $selected) $this_select = $optionselected;
                else  $this_select = '';
                $editwhat = sql_getone($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                $sql_this = " WHERE ".$fieldis.$what."='$row_enumtitre' ";
                $countwhat = sql_nrows($dbtable,$sql_this);
                $selectoptions .= '<option value="'.$row_enumtitre.'" '.$this_select.'>'.($show_count!=null?($countwhat==''?'(0)':'('.$countwhat.')'):'').' '.$editwhat.' </option>';
            }
        }
    } else if ($table == $tblstring) {
        $selectoptions = '';
        if ($selected == '')	$selectoptions = '<option value=""> >> '.$achoisirString.' &nbsp;</option>'	;
        $whereq = " WHERE stringtype='$what' $type ";
        $read = mysqli_query($connection, " SELECT * FROM $table $whereq ");
        $nRows = sql_nrows($table,$whereq);
        for ($i=0;$i<$nRows;$i++) {
            $row = mysqli_fetch_array($read);
            if ($row["stringentry"] == $selected) {
                $this_select = $optionselected;
            } else {
                $this_select = '';
            }
            $selectoptions .= '<option value="'.$row["stringentry"].'" '.$this_select.'> '.$row["stringentry"].' </option>';
        }
    } else {
        $selectoptions = '';
        if (in_array($selected,array('ajout','0','',null)))
        $selectoptions = '<option value=""> >> '.$achoisirString.' &nbsp;</option>'	;
        $rid = (in_array($what."rid",sql_fields($table,'array'))?'r':'')."id";
        $whereq = " WHERE ".$what."statut='Y' ".($rid=='rid'?" AND ".$what."lang='$lg' ":'')." $type ";
        $read = mysqli_query($connection, " SELECT * FROM $table $whereq ");
        $nRows = sql_nrows($table,$whereq);
        for ($i=0;$i<$nRows;$i++) {
            $row = mysqli_fetch_array($read);
            if ($row[$what.$rid] == $selected) {
                $this_select = $optionselected;
            } else {
                $this_select = '';
            }
            if (!isset($row[$what."util"])) {
                if (!isset($row[$what."nom"])) {
                    if (!isset($row[$what."titre"])) {
                        if (!isset($row[$what."title"])) {
                            $row_nomoutitre = $row[$what.$rid];
                        } else {
                            $row_nomoutitre = $row[$what."title"];
                        }
                    } else {
                        $row_nomoutitre = $row[$what."titre"];
                    }
                } else {
                    $row_nomoutitre = (isset($row[$what."prenom"])?(isset($row[$what."gendre"])?sql_stringit($tblenum,$row[$what."gendre"],'gendre')." ":'').$row[$what."prenom"]." ":'').$row[$what."nom"];
                }
            } else {
                $row_nomoutitre = $row[$what."util"];
            }
            $selectoptions .= '<option value="'.$row[$what.$rid].'" '.$this_select.'> '.$row_nomoutitre.' </option>';
        }
    }
    return $selectoptions;
}


// ########## full option select
function gen_fullselectoption($table,$selected,$type,$what,$str_type=null)
{
    global $connection, $dbtable, $trace, $lg, $optionselected, $tblcont, $tblenum, $tblstring, $this_is, $nRowsThis_is, $nRowsThis_isy, $nRowsThis_isn, $fieldis, $toutString, $achoisirString;
    if (!$str_type) $str_type = 'general';
    $selectoptions = '<select class="text" name="'.$what.'">';
    if (is_array($table)) {
        if (in_array($selected,array('ajout','0','',null)))
        $selectoptions .= '<option value=""> >> '.$achoisirString.' &nbsp;</option>';
        foreach($table as $key) {
            $key = html_encode($key);
            if ($key == $selected) $this_select = $optionselected;
            else  $this_select = '';
            $selectoptions .= '<option value="'.$key.'"'.$this_select.'>'.(sql_stringit($str_type,$key)!=''?sql_stringit($str_type,$key):$key).'</option>';
        }
    } else if ($table == $tblenum) {
        $read = mysqli_query($connection, "
						SELECT * FROM $table
						WHERE enumstatut='Y'
						AND enumtype LIKE '%$type%'
						AND enumwhat LIKE '%$what%'
						");
        $nRows = sql_nrows($table, "
							WHERE enumstatut='Y'
							AND enumtype LIKE '%$type%'
							AND enumwhat LIKE '%$what%'
							");
        if (in_array($selected,array('ajout','0','',null))) {
            $selectoptions .= '<option value=""> >> '.$achoisirString.' &nbsp;</option>';
            for ($i=0;$i<$nRows;$i++) {
                $row = mysqli_fetch_array($read);
                $row_enumtitre = $row["enumtitre"];
                $editwhat = sql_get($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                $selectoptions .= '<option value="'.$row_enumtitre.'"> '.$editwhat[0].' </option>';
            }
        } else {
            if (isset($nRowsThis_isy)) {
                if ($selected == $toutString) {
                    $selectoptions .= '<option'.$optionselected.' value="'.$toutString.'"> ('.$nRowsThis_isy.') '.$toutString.' &nbsp;</option>';
                } else {
                    $selectoptions .= '<option value="'.$toutString.'"> ('.$nRowsThis_isy.') '.$toutString.' &nbsp;</option>';
                }
            }
            for ($i=0;$i<$nRows;$i++) {
                $row = mysqli_fetch_array($read);
                $row_enumtitre = $row["enumtitre"];
                if ($row["enumtitre"] == $selected) {
                    $this_select = $optionselected;
                } else {
                    $this_select = '';
                }
                $editwhat = sql_get($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                $sql_this = " WHERE ".$fieldis."statut='Y' AND ".$fieldis."type='$what' AND ".$fieldis.$what."='$row_enumtitre' ";
                $countwhat = sql_nrows($dbtable,$sql_this);
                $selectoptions .= '<option value="'.$row_enumtitre.'" '.$this_select.'>';
                if	(!$countwhat == '')	$selectoptions .= '('.$countwhat.')'	;
                $selectoptions .= ' '.$editwhat[0].' </option>';
            }
        }
    } else if ($table == $tblcont) {
        $selectoptions .= '';
        if (in_array($selected,array('ajout','0','',null)))
        $selectoptions .= '<option value=""> >> '.$achoisirString.' &nbsp;</option>'	;
        $whereq = " WHERE ".$what."statut='Y' $type ";
        $read = mysqli_query($connection, " SELECT * FROM $table "); // $whereq
        $nRows = sql_nrows($table,$whereq);
        for ($i=0;$i<$nRows;$i++) {
            $row = mysqli_fetch_array($read);
            if ($row[$what."pg"] == $selected) {
                $this_select = $optionselected;
            } else {
                $this_select = '';
            }
            if (!isset($row[$what."util"])) {
                if (!isset($row[$what."nom"])) {
                    if (!isset($row[$what."titre"])) {
                        if (!isset($row[$what."title"])) {
                            $row_nomoutitre = $row[$what."id"];
                        } else {
                            $row_nomoutitre = $row[$what."title"];
                        }
                    } else {
                        $row_nomoutitre = $row[$what."titre"];
                    }
                } else {
                    $row_nomoutitre = (isset($row[$what."prenom"])?(isset($row[$what."gendre"])?sql_stringit($tblenum,$row[$what."gendre"],'gendre')." ":'').$row[$what."prenom"]." ":'').$row[$what."nom"];
                }
            } else {
                $row_nomoutitre = $row[$what."util"];
            }
            $selectoptions .= '<option value="'.$row[$what."pg"].'" '.$this_select.'>'.$row[$what."pg"].' '.$row_nomoutitre.' </option>';
        }
    } else {
        $selectoptions .= '';
        if (in_array($selected,array('ajout','0','',null)))
        $selectoptions .= '<option value=""> >> '.$achoisirString.' &nbsp;</option>'	;
        $rid = (in_array($what."rid",sql_fields($table,'array'))?'r':'')."id";
        $whereq = " WHERE ".$what."statut='Y' ".($rid=='rid'?" AND ".$what."lang='$lg' ":'')." $type ";
        $read = mysqli_query($connection, " SELECT * FROM $table $whereq ");
        $nRows = sql_nrows($table,$whereq);
        for ($i=0;$i<$nRows;$i++) {
            $row = mysqli_fetch_array($read);
            if ($row[$what.$rid] == $selected) {
                $this_select = $optionselected;
            } else {
                $this_select = '';
            }
            if (!isset($row[$what."util"])) {
                if (!isset($row[$what."nom"])) {
                    if (!isset($row[$what."titre"])) {
                        if (!isset($row[$what."title"])) {
                            $row_nomoutitre = $row[$what.$rid];
                        } else {
                            $row_nomoutitre = $row[$what."title"];
                        }
                    } else {
                        $row_nomoutitre = $row[$what."titre"];
                    }
                } else {
                    $row_nomoutitre = (isset($row[$what."prenom"])?(isset($row[$what."gendre"])?sql_stringit($tblenum,$row[$what."gendre"],'gendre')." ":'').$row[$what."prenom"]." ":'').$row[$what."nom"];
                }
            } else {
                $row_nomoutitre = $row[$what."util"];
            }
            $selectoptions .= '<option value="'.$row[$what.$rid].'" '.$this_select.'> '.$row_nomoutitre.' </option>';
        }
    }
    $selectoptions .= '</select>';
    return $selectoptions;
}


// ########## option check
function gen_inputcheck($table,$checked,$type,$what,$hide_first=null)
{
    global $connection, $dbtable, $trace, $lg, $inputchecked, $tblenum, $tblstring, $this_is, $nRowsThis_is, $nRowsThis_isy, $nRowsThis_isn, $fieldis, $toutString, $achoisirString;
    if (($checked !== 'ajout') || ($checked !== '')) {
        $checked = explode("|", $checked);
        if (!is_array($checked)) $checked = array($checked);
    }
    $checkedoptions = '';
    if ($table == $tblenum) {
        $read = mysqli_query($connection, "
						SELECT * FROM $table
						WHERE enumstatut='Y'
						AND enumtype LIKE '%$type%'
						AND enumwhat LIKE '%$what%'
						");
        $nRows = sql_nrows($table, "
							WHERE enumstatut='Y'
							AND enumtype LIKE '%$type%'
							AND enumwhat LIKE '%$what%'
							");
        if (($checked == 'ajout') || ($checked == '')) {
            //	for ($i=0;$i<$nRows;$i++) {
            $i=0;
            while($row = mysqli_fetch_array($read)) {
                $iminus = $i-1;
                //	$row = mysqli_fetch_array($read);
                $row_enumtitre = $row["enumtitre"];
                $editwhat = sql_get($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                if (($hide_first === true) && ($i==0))
                $checkedoptions .= '<input type="hidden" name="'.$what.$row_enumtitre.'" '.$inputchecked.' />';
                else
                $checkedoptions .= '<div class="inputcheck"><label for="'.$what.$row_enumtitre.'"> '.$editwhat[0].' </label><input type="checkbox" name="'.$what.$row_enumtitre.'" /></div>';//$what.($i+1)
                if	($i > '0')	$row_enumtype = $row["enumtype"][$iminus]	;
                if	(($i > '0') && ($row["enumtype"] !== $row_enumtype))
                $checkedoptions .= '<!-- <br /> -->';
                $i++;
            }
        } else {
            //	for ($i=0;$i<$nRows;$i++) {
            $i=0;
            while($row = mysqli_fetch_array($read)) {
                $iminus = $i-1;
                //	$row = mysqli_fetch_array($read);
                $row_enumtitre = $row["enumtitre"];
                $row_enumtype[$i] = $row["enumtype"];
                if	(($i > '0') && ($row_enumtype[$iminus] !== $row["enumtype"]))
                $checkedoptions .= '<!-- <br /> -->';
                if (in_array($row["enumtitre"], $checked)) {
                    $this_check = $inputchecked;
                } else {
                    $this_check = '';
                }
                $editwhat = sql_get($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                $sql_this = " WHERE ".$fieldis."statut='Y' AND ".$fieldis."type='$what' AND ".$fieldis.$what."='$row_enumtitre' ";
                $countwhat = sql_nrows($dbtable,$sql_this);
                if (($hide_first === true) && ($i==0))
                $checkedoptions .= '<input type="hidden" name="'.$what.$row_enumtitre.'" '.$inputchecked.' />';
                else
                $checkedoptions .= '<div class="inputcheck"><label for="'.$what.$row_enumtitre.'"> '.$editwhat[0].' </label><input type="checkbox" name="'.$what.$row_enumtitre.'" '.$this_check.' /></div>';//$what.($i+1)
                if	(!$countwhat == '')	$checkedoptions .= '('.$countwhat.')'	;
                $i++;
            }
        }
    } else {
        $whereq = " WHERE ".$what."statut='Y' $type ";
        $read = mysqli_query($connection, " SELECT * FROM $table $whereq ");
        $rid = (in_array($what."rid",sql_fields($table,'array'))?'r':'')."id";
        $nRows = sql_nrows($table,$whereq);
        //	for ($i=0;$i<$nRows;$i++) {
        $i=0;
        while($row = mysqli_fetch_array($read)) {
            //	$row = mysqli_fetch_array($read);
            if (in_array($row[$what.$rid], $checked)) {
                $this_check = $inputchecked;
            } else {
                $this_check = '';
            }
            if (!isset($row[$what."util"])) {
                if (!isset($row[$what."nom"])) {
                    if (!isset($row[$what."titre"])) {
                        if (!isset($row[$what."title"])) {
                            $row_nomoutitre = $row[$what.$rid];
                        } else {
                            $row_nomoutitre = $row[$what."title"];
                        }
                    } else {
                        $row_nomoutitre = $row[$what."titre"];
                    }
                } else {
                    $row_nomoutitre = (isset($row[$what."prenom"])?(isset($row[$what."gendre"])?sql_stringit($tblenum,$row[$what."gendre"],'gendre')." ":'').$row[$what."prenom"]." ":'').$row[$what."nom"];
                }
            } else {
                $row_nomoutitre = $row[$what."util"];
            }
            $checkedoptions .= '<div class="inputcheck"><label for="'.$what.$row_enumtitre.'"> '.$row_nomoutitre.' </label><input type="checkbox" name="'.$what.$row_enumtitre.'" '.$this_check.' /></div>';//$what.($i+1)
            $i++;
        }
    }
    return $checkedoptions;
}


// ########## input radio
function gen_inputradio($table,$checked,$type,$what)
{
    global $connection, $error_invmiss, $dbtable, $trace, $lg, $inputchecked, $tblenum, $tblmembre, $tblstring, $this_is, $nRowsThis_is, $nRowsThis_isy, $nRowsThis_isn, $fieldis, $toutString, $achoisirString;
    if (strstr($checked, "|")) {
        $radiooptions = $error_invmiss.' array passed...<br />';
    } else {
        $radiooptions = '';
        if ($table == $tblenum) {
            $read = mysqli_query($connection, "
							SELECT * FROM $table
							WHERE enumstatut='Y'
							AND enumtype LIKE '%$type%'
							AND enumwhat LIKE '%$what%'
							");
            $nRows = sql_nrows($table, "
								WHERE enumstatut='Y'
								AND enumtype LIKE '%$type%'
								AND enumwhat LIKE '%$what%'
								");
            if (($checked == 'ajout') || ($checked == '')) {
                //	for ($i=0;$i<$nRows;$i++) {
                $i=0;
                while($row = mysqli_fetch_array($read)) {
                    //	$row = mysqli_fetch_array($read);
                    $row_enumtitre = $row["enumtitre"];
                    $editwhat = sql_get($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                    $radiooptions .= '<label for="'.$what.'"> '.$editwhat[0].' </label><input type="radio" name="'.$what.'" value="'.$row_enumtitre.'" />';//($i+1)
                    $i++;
                }
            } else {
                //	for ($i=0;$i<$nRows;$i++) {
                $i=0;
                while($row = mysqli_fetch_array($read)) {
                    //	$row = mysqli_fetch_array($read);
                    $iminus = $i-1;
                    $row_enumtitre = $row["enumtitre"];
                    $row_enumtype[$i] = $row["enumtype"];
                    if	(($i > '0') && ($row_enumtype[$iminus] !== $row["enumtype"]))
                    $radiooptions .= '<br />';
                    if (in_array($row["enumtitre"], $checked)) {
                        $this_check = $inputchecked;
                    } else {
                        $this_check = '';
                    }
                    $editwhat = sql_get($tblstring, " WHERE stringlang='$lg' AND stringtype='$what' AND stringtitle='$row_enumtitre' ", "stringentry");
                    $sql_this = " WHERE ".$fieldis."statut='Y' AND ".$fieldis."type='$what' AND ".$fieldis.$what."='$row_enumtitre' ";
                    $countwhat = sql_nrows($dbtable,$sql_this);
                    $radiooptions .= '<label for="'.$what.'"> '.$editwhat[0].' </label><input type="radio" name="'.$what.'" value="'.$row_enumtitre.'" '.$this_check.' />';//($i+1)
                    if	(!$countwhat == '')	$radiooptions .= '('.$countwhat.')'	;
                    $i++;
                }
            }
        } else {
            $whereq = " WHERE ".$what."statut='Y' $type ";
            $read = mysqli_query($connection, " SELECT * FROM $table $whereq ");
            $rid = (in_array($what."rid",sql_fields($table,'array'))?'r':'')."id";
            $nRows = sql_nrows($table,$whereq);
            //	for ($i=0;$i<$nRows;$i++) {
            $i=0;
            while($row = mysqli_fetch_array($read)) {
                //	$row = mysqli_fetch_array($read);
                $row_enumtitre = $row["enumtitre"];
                if (in_array($row[$what.$rid], $checked)) {
                    $this_check = $inputchecked;
                } else {
                    $this_check = '';
                }
                if (!isset($row[$what."util"])) {
                    if (!isset($row[$what."nom"])) {
                        if (!isset($row[$what."titre"])) {
                            if (!isset($row[$what."title"])) {
                                $row_nomoutitre = $row[$what.$rid];
                            } else {
                                $row_nomoutitre = $row[$what."title"];
                            }
                        } else {
                            $row_nomoutitre = $row[$what."titre"];
                        }
                    } else {
                        $row_nomoutitre = (isset($row[$what."prenom"])?(isset($row[$what."gendre"])?sql_stringit($tblenum,$row[$what."gendre"],'gendre')." ":'').$row[$what."prenom"]." ":'').$row[$what."nom"];
                    }
                } else {
                    $row_nomoutitre = $row[$what."util"];
                }
                $radiooptions .= '<label for="'.$what.'"> '.$row_nomoutitre.' </label><input type="radio" name="'.$what.'" value="'.$row_enumtitre.'" '.$this_check.' />';//($i+1)
                $i++;
            }
        }
    }
    return $radiooptions;
}

class Conjugaison
{
    var $plural = '';
    function plural($text,$genre,$count)
    {
        global $lg;
        if ($count > '1') {
            if ($lg == 'fr') {
                // puts s at every words end
                $s = "s"	;
                if	($genre == 'F')	$fem = "e"	;
                if	($genre == 'M')	$fem = ""	;
                if ($genre == '') {
                    $_this->plural = $text;
                } else {
                    if (strstr($text, ' ')) {
                        $text = explode(' ', $text);
                        $newtext = "";
                        for ($i=0;$i<count($text);$i++) {
                            if ($i == count($text)-1) {
                                $s = "s";
                                $revtext = strrev($text[$i]);
                                if	((count($text) > '2') && (($revtext[2] == 'e') && ($revtext[1] == 'a') && ($revtext[0] == 'u')))	$s = "x"	;
                                if (($revtext[1] == 'a') && (($revtext[0] == 'l') || ($revtext[0] == 'u'))) {
                                    $s = "aux"	;
                                    $text[$i] = substr($text[$i], 0, -2);
                                }
                                $newtext .= $text[$i].$s.' ';
                            } else {
                                $s = "s";
                                $revtext = strrev($text[$i]);
                                if	((count($text) > '2') && (($revtext[2] == 'e') && ($revtext[1] == 'a') && ($revtext[0] == 'u')))	$s = "x"	;
                                if (($revtext[1] == 'a') && (($revtext[0] == 'l') || ($revtext[0] == 'u'))) {
                                    $s = "aux"	;
                                    $text[$i] = substr($text[$i], 0, -2);
                                }
                                $newtext .= $text[$i].$s.' ';
                            }
                        }
                        $_this->plural = $newtext;
                    } else {
                        $revtext = strrev($text);
                        if	((count($text) > '2') && (($revtext[2] == 'e') && ($revtext[1] == 'a') && ($revtext[0] == 'u')))	$s = "x"	;
                        if (($revtext[1] == 'a') && (($revtext[0] == 'l') || ($revtext[0] == 'u'))) {
                            $s = "aux"	;
                            $text = substr($text, 0, -2);
                        }
                        if	(($genre == 'F') && ($revtext[0] !== 'e') && ($revtext[0] !== 'n'))	$text .= $fem	;
                        $_this->plural = $text.$s;
                    }
                }
            } else if ($lg == 'en') {
                // puts s only on last words end
                $s = "s";
                $fem = "";
                if (strstr(trim($text),' ')) {
                    $text = explode(' ',strrev($text),2);
                    $revtext = $text[0];
                    if ((($revtext[1] == 'e') && ($revtext[0] == 'd')) || ($revtext[0] == 's')) {
                        $s = ""	;
                    } else if ($revtext[0] == 'y') {
                        $text[0] = substr($revtext,1);
                        $s = "ies"	;
                    }
                    $_this->plural = strrev($text[1]).' '.strrev($text[0]).$s;
                } else {
                    $revtext = strrev($text);
                    if ((($revtext[1] == 'e') && ($revtext[0] == 'd')) || ($revtext[0] == 's')) {
                        $s = ""	;
                    } else if ($revtext[0] == 'y') {
                        $text = substr($text,0,-1);
                        $s = "ies"	;
                    }
                    $array_inv = array('Dear');
                    $_this->plural = $text.(in_array($text,$array_inv)?'':$s);
                }
            } else if (($lg == 'it') || ($lg == 'es')) {
                // puts i or e at every words end according to genre M or F
                $s = ""	;
                $fem = "";
                if	($genre == 'M')	$s = "i"	;
                if	($genre == 'F')	$s = "e"	;
                if (strstr($text, " ")) {
                    $text = explode(" ", $text);
                    $newtext = "";
                    for ($i=0;$i<count($text);$i++) {
                        $chop = "-1";
                        $revtext = strrev($text[$i]);
                        if ($i==0) {
                            if (in_array($revtext[0],array('o','e'))) {
                                $s = 'i';
                            }
                            if (in_array($revtext[0],array('a'))) {
                                $s = 'e';
                            }
                        }
                        if (($revtext[0] == ';') || !in_array($revtext[0],array('a','e','o'))) {
                            $newtext .= $text[$i].' ';
                        } else {
                            if	((($revtext[1] == $s) && ($revtext[0] == 'o')) || (strrev($text[$i][2]) == $s))
                            $chop = "-2"	;
                            $text[$i] = substr($text[$i], 0, $chop);
                            $newtext .= $text[$i].$s.' ';
                        }
                    }
                    $_this->plural = $newtext;
                } else {
                    $chop = "-1";
                    $revtext = strrev($text);
                    if (in_array($revtext[0],array('o','e'))) {
                        $s = 'i';
                    }
                    if (in_array($revtext[0],array('a'))) {
                        $s = 'e';
                    }
                    if (($revtext[0] == ';') || !in_array($revtext[0],array('a','e','o'))) {
                        $_this->plural = $text;
                    } else {
                        if	(($revtext[1] == $s) && ($revtext[0] == 'o'))	$chop = "-2"	;
                        $text = substr($text, 0, $chop);
                        $_this->plural = $text.$s;
                    }
                }
            } else {
                $_this->plural = $text;
            }
        } else {
            if ($lg == 'fr') {
                // puts s at every words end
                if	($genre == 'F')	$fem = "e"	;
                if	($genre == 'M')	$fem = ""	;
                if ($genre == '') {
                    $_this->plural = $text;
                } else {
                    $revtext = strrev($text);
                    if	(($genre == 'F') && ($revtext[0] !== 'e') && ($revtext[0] !== 'n'))	$text .= $fem	;
                    $_this->plural = $text;
                }
            } else {
                $_this->plural = $text;
            }
        }
        return $_this->plural;
    }
}

$class_conjugaison = new Conjugaison;

function class_conjugaison_plural($text,$genre,$count)
{
    /*
     global $class_conjugaison;
    return $class_conjugaison->plural($text,$genre,$count);
    */
    return $text;
}


class zip
{
    function extractZip($src,$dest)
    {
        global $trace,$notice,$error,$getcwd,$up,$filedir,$urladmin;
        if (mkdir($getcwd.$dest)) $notice .= "et voila!<br />";
        else $notice .= "dir not created<br />";
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
    function extractTPLZip($src,$dest)
    {
        global $trace,$notice,$error,$getcwd,$up,$filedir,$urladmin;
        if (function_exists('zip_open')) {
            function unzip($file,$dest=null)
            {
                //  $zip=zip_open(realpath(".")."/".$file);
                $zip = zip_open($file);
                if (!$zip) {
                    return "Unable to proccess file '{$file}'";
                }
                $e = '';
                while($zip_entry = zip_read($zip)) {
                    $zdir = dirname($dest.zip_entry_name($zip_entry));
                    $zname = $dest.zip_entry_name($zip_entry);
                    if (!zip_entry_open($zip,$zip_entry,"r")) {
                        $e .= "Unable to proccess file '{$zname}'";
                        continue;
                    }
                    if (!is_dir($zdir))
                        mkdirr($zdir);
                    $zip_fs = zip_entry_filesize($zip_entry);
                    if (empty($zip_fs))
                        continue;
                    $zz = zip_entry_read($zip_entry,$zip_fs);
                    $z = fopen($zname,"w");
                    fwrite($z,$zz);
                    fclose($z);
                    zip_entry_close($zip_entry);
                }
                zip_close($zip);
                return $e;
            }
            function mkdirr($dest,$mode=null)
            {
                if (is_dir($dest)||empty($dest))
                    return true;
                $dest = str_replace(array('/', ''),DIRECTORY_SEPARATOR,$dest);
                if (is_file($dest)) {
                    trigger_error('mkdirr() File exists', E_USER_WARNING);
                    return false;
                }
                $next_pathname = substr($dest,0,strrpos($dest,DIRECTORY_SEPARATOR));
                if (mkdirr($next_pathname,$mode)) {
                    if (!file_exists($dest)) {
                        return mkdir($dest,$mode);
                    }
                }
                return false;
            }
            unzip($src,$dest);
        } else {
            $do = "unzip $src -d $dest";
            $notice .= exec($do,$output,$result).($result==1?'':'NOT')." extracted by exec<br />"; // `unzip $src -d $dest`;
        }
        return true;
    }
}
