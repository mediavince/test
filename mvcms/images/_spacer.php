<?php #۞ #

if	(!$_SERVER["HTTP_REFERER"])	
	include '../admin/_security.php';

$caught = imagecreatefromgif("_caught.gif");

header("Content-type: image/png");
imagepng($caught);

imagedestroy($caught);
