<?php if (stristr($_SERVER['PHP_SELF'],'_template.php')){include'_security.php';Header("Location: $redirect");Die();}$_template = "
$div_alogin
<div id='iefix'>

<div id='top'>
  $toplinksentry
</div>

  <div id='main'>

  <div class='contour'>
  	<div class='top-left'><em></em></div>
  	<div class='top-center'><em></em></div>
  	<div class='top-right'><em></em></div>
  	
    <div class='contour-body'>
      <div class='contour-body-left'>
      <div class='contour-body-middle'>


  	<div id='header'>
  		$cologo
  		<div class='trmenu'>
  			<ul>
  				<li><a href='".$mainurl."rss/".($lg==$default_lg?'':"?lg=".$lg)."'><img src='".$mainurl."images/rss-icon.png' border='0' title='RSS' alt='RSS' /></a></li>
  				<!--<li><a href='?lg=".$lg."&amp;x=8'>".($lg=='it'?'Mappa Google':'Google Map')."</a></li>
  				<li><a href='?lg=".$lg."&amp;x=9'>".($lg=='it'?'Contattaci':'Contact Us')."</a></li>
  				<li><a href='#'>Info sul sito</a></li>-->
  			</ul>
  		</div>
  		<h1>
  			$confinfoString
  		</h1>
  	</div>
  	<div id='navcontainer'>
  		<div class='lang'>
  			$gen_lang
  		</div>
  		$menuhori
  	</div>
  	<div id='bgleft'>
  		<div id='secondnav'>
  			<div id='lpane'>
  				<div id='sidenav'>
  					$menuleft
  				</div>
  				<div id='leftbox'>
  					$leftlinksentry
  				</div>
	".($x!='1'?$div_ulogin.'<br />':'')."
  				<div id='topics'>
  					$topicslist
  				</div>
  			</div>
  		</div>
  		<div id='content'>
  			<div class='wherearewe'>
  				<script type='text/javascript'>document.write(wherearewe);</script>
  			</div>
  			<h2 class='title'>$title</h2>
  			$content
  		</div>
  	</div>
  	<div class='clear'> 
  	</div>
  	
      </div><!-- contour-body-middle -->
      </div>
      <div class='contour-body-right'><em></em></div>
    </div>
  	<div class='bottom-left'><em></em></div>
  	<div class='bottom-center'><em></em></div>
  	<div class='bottom-right'><em></em></div>
  </div><!-- contour -->

  </div>
</div>
<div id='footer'>
	$footer
</div>

";
