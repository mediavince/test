<?php #۞ #
###########################!!! ADMIN !!!

include '_incdb.php';
include '_view.php';
include '_strings.php';

###########################!!! ADMIN !!!

if (stristr($_SERVER['PHP_SELF'],$urladmin))
  include '_login.php';
else
  include '_login_root.php';
  
$login = "";

include '_query.php';

if (stristr($_SERVER['PHP_SELF'],$urladmin) && ($logged_in === true))
  include '_menu.php';
else
  include '_menu_root.php';

if ($menu_pagine)
	include '_menu_pagine_root.php';

index_generate();

################### GARBAGE EXTERMINATOR

clearstatcache();
@mysql_close($connection);
