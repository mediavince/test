<?php #۞ #

if (!isset($urladmin))
$urladmin = 'admin/';
if (!isset($urlintro))// needs to be a dir
$urlintro = 'blog/';
// File Directory where your protected files and configs shall be uploaded
if (!isset($safedir))
$safedir = "SQL/"	;
if (!isset($tpldir))
$tpldir = "lib/templates/"	;
  
$up = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
  
if (stristr($_SERVER['PHP_SELF'],$urladmin) 
	|| (stristr($_SERVER['PHP_SELF'],$urlintro) && is_dir($urlintro))) {
    if (stristr($_SERVER['HTTP_HOST'],"mediavince.com") 
		|| stristr($_SERVER['HTTP_HOST'],"localhost"))
    $up = "..".DIRECTORY_SEPARATOR;
    else
    $up = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;
}
  
include $up.$urladmin.'_incdb.php';

$default_img_captcha = "_captcha.jpg";

if (!isset($img_captcha)) {
	$img_captcha = $up."images".DIRECTORY_SEPARATOR.$default_img_captcha;
	// check if default file exists from config in active_template
	if (@file_exists($up.$tpldir.$active_template.DIRECTORY_SEPARATOR.$default_img_captcha))
	$img_captcha = $up.$tpldir.$active_template.DIRECTORY_SEPARATOR.$default_img_captcha;
} else {
	if (@file_exists($up.$tpldir.$active_template.DIRECTORY_SEPARATOR.$img_captcha))
	$img_captcha = $up.$tpldir.$active_template.DIRECTORY_SEPARATOR.$img_captcha;
}

// define mime_type of img_captcha
$mime_type = "image/jpeg";
if (@file_exists($img_captcha)) {
	/* avoiding unnecessary checks, we directly getimagesize()
	if (function_exists("finfo_open")) { // does not exist on 1and1
		$fi = finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = finfo_file($fi,$img_captcha);
	} else if (function_exists("mime_content_type")) { // does not exist on 1and1
		$mime_type = mime_content_type($img_captcha);
	} else if (function_exists("getimagesize")) { // works on 1and1 for images
		$gis = getimagesize($img_captcha);
		$mime_type = $gis['mime'];
	} else {
		// risky, better not using...
		$mime_type = strrchr(system('file -ib ' . escapeshellarg($img_captcha))," ");
	}
	*/
	// if returns nothing on 1and1, then it is not an image...
	$gis = getimagesize($img_captcha);
	$mime_type = $gis['mime'];
}
		
if (!stristr($mime_type,"image/")) // possible security threat
{Header("Location: $redirect");Die();}

/*
//Start the session so we can store what the code actually is.
//session_start();
*/
$string = " ";
if (isset($_SESSION['antispam_key']) && isset($_GET['string'])) {
	$string = base64_decode($_GET['string']);
} else {
	$md5 = md5(microtime() * mktime());
	$string = substr($md5,0,rand(5,8));
	//Encrypt and store the key inside of a session
	$_SESSION['antispam_key'] = md5($string);
}

//Now for the GD stuff, for ease of use lets create the image from a background image.
switch ($mime_type)
{
	case 'image/gif': $captcha = imagecreatefromgif($img_captcha);
	break;
	case 'image/png': $captcha = imagecreatefrompng($img_captcha);
	break;
	default: $captcha = imagecreatefromjpeg($img_captcha);
	break;
}

//Lets set the colours, the colour $line is used to generate lines. 
$black = imagecolorallocate($captcha,rand(0,128),rand(0,128),rand(0,224));
$line1 = imagecolorallocate($captcha,rand(128,255),rand(64,128),rand(224,255));
$line2 = imagecolorallocate($captcha,rand(128,224),rand(128,255),rand(0,255));
 
/*
Now to make it a little bit harder for any bots to break, 
assuming they can break it so far. Lets add some lines
in (static lines) to attempt to make the bots life a little harder
*/
imageline($captcha,rand(0,10),0,rand(100,150),rand(25,35),$line2);
imageline($captcha,rand(90,130),rand(0,10),rand(0,35),rand(30,50),$line1);
imageline($captcha,rand(40,70),0,rand(60,90),rand(50,70),$line1);
imageline($captcha,rand(60,90),rand(0,10),0,rand(25,35),$line2);

//Now for the all important writing of the randomly generated string to the image.
imagestring($captcha, 5, 30, 5, $string, $black);
 
//Output the image
Header("Content-type: image/png");
Header("Cache-Control: no-cache");
Header("Pragma: no-cache");

imagepng($captcha);

imagedestroy($captcha);
