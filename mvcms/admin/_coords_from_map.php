<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

//> <b>'.$coordsString.'</b><br />
$content .= '
<script>
//global variables
var Coordinate_X_InImage;
var Coordinate_Y_InImage;

function init(){
  if(document.addEventListener) {
    document.getElementById("idImageToMonitor").addEventListener("mousemove",TrackCoordinatesInImage, false);
  } else if(window.event && document.getElementById) {
    document.getElementById("idImageToMonitor").onmousemove = TrackCoordinatesInImage;
  }
}

function TrackCoordinatesInImage(evt){
  Coordinate_X_InImage = Coordinate_Y_InImage = 0;
  if(window.event && !window.opera && typeof event.offsetX == "number") {
  // IE 5+
    Coordinate_X_InImage = event.offsetX;
    Coordinate_Y_InImage = event.offsetY;
  } else if(document.addEventListener && evt && typeof evt.pageX == "number"){
    // Mozilla-based browsers
    var Element = evt.target;
    var CalculatedTotalOffsetLeft, CalculatedTotalOffsetTop;
    CalculatedTotalOffsetLeft = CalculatedTotalOffsetTop = 0;
    while (Element.offsetParent) {
      CalculatedTotalOffsetLeft += Element.offsetLeft ;
      CalculatedTotalOffsetTop += Element.offsetTop ;
      Element = Element.offsetParent ;
    }
    Coordinate_X_InImage = evt.pageX - CalculatedTotalOffsetLeft ;
    Coordinate_Y_InImage = evt.pageY - CalculatedTotalOffsetTop ;
  }
  document.getElementById(\'div1\').innerHTML = "X:"+Coordinate_X_InImage+" Y: "+Coordinate_Y_InImage;
}

// array to store mouse click coordinates
var coords=new Array();

function storepoint(x,y){
  //uncomment the next line to see each image click\'s coordinates
//  alert(x+\', \'+y);
  coords[coords.length]=[x,y];
  document.getElementById(\'instituteCoords\').value = x+"|"+y;
  document.getElementById(\'star_img_coords\').style.marginLeft = (x>7?x-7:x)+"px";
  document.getElementById(\'star_img_coords\').style.marginTop = (y>7?y-7:y)+"px";
}

function showarr(){
  var s1=\'\';
  for (var i=0; i<coords.length; i++)
  s1+=\'\\\n\'+coords[i][0]+\', \'+coords[i][1];
//  alert(s1);
}
</script>
<body onload="init()"></body>
<div id="img_coords"><div id="star_img_coords" class="star_img_coords"><img src="'.$mainurl.'images/star.png" border="0" /></div>
<img name="idImageToMonitor" id="idImageToMonitor" src="'.$mainurl.$map_image.'" onmousedown="storepoint(Coordinate_X_InImage,Coordinate_Y_InImage)">
</div>
<div id="div1">&nbsp;</div>';
/*
<input type="button" value="Show Contents of Array" onclick="showarr()" /><br />
*/
