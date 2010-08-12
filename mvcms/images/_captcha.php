<?PHP #۞ #

if (!isset($_SERVER["HTTP_REFERER"])) {

  if (!isset($urladmin))
  $urladmin = 'admin/';
  if (!isset($urlintro))// needs to be a dir
  $urlintro = 'blog/';
  // File Directory where your protected files and configs shall be uploaded
  if (!isset($safedir))
  $safedir = "SQL/"	;
  
  $up = "";
  if (stristr($_SERVER['REQUEST_URI'],$urladmin) || (stristr($_SERVER['REQUEST_URI'],$urlintro) && is_dir($urlintro))) {
    if (stristr($_SERVER['HTTP_HOST'],"mediavince.com") || stristr($_SERVER['HTTP_HOST'],"localhost"))
    $up = "../"	;
    else
    $up = $_SERVER['DOCUMENT_ROOT'].'/';
  }

	include $up.$urladmin.'_security.php';
	Header("Location: $redirect");Die();
}

$string = " ";
if (isset($_GET['string'])) $string = base64_decode($_GET['string']);
//Start the session so we can store what the code actually is.
//session_start();
/*
if (isset($_SESSION['antispam_key'])) {
  $string = $_GET['string'];
} else {
  $md5 = md5(microtime() * mktime());
  $string = substr($md5,0,rand(5,8));
  //Encrypt and store the key inside of a session
  $_SESSION['antispam_key'] = md5($string);
}
*/
//Now for the GD stuff, for ease of use lets create the image from a background image.
$captcha = imagecreatefromjpeg("_captcha.jpg");
 
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
header("Content-type: image/png");
imagepng($captcha);

imagedestroy($captcha);

?>