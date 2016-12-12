<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include dirname(__FILE__).DIRECTORY_SEPARATOR.'admin/_security.php';Header("Location: $redirect");Die();}

$caught = imagecreatefromgif("_caught.gif");

header("Content-type: image/png");
imagepng($caught);

imagedestroy($caught);
