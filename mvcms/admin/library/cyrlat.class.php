<?PHP #۞ #
############################################################################
#CyrLat class v. 1.0.1 by Yaroslav Shapoval
#en: Class for converting Cyrillic to Latin characters in both directions.
#ru: Êëàññ äëÿ êîíâåðòèðîâàíèÿ Êèðèëèöû â Ëàòèíèöó è îáðàòíî.
#    "Privet, Mir!" <-> "Ïðèâåò, Ìèð!"
#en: See test.php for example of usage
#ru: Ôàéë test.php ïîêàçûâàåò ïðèíöèï èñïîëüçîâàíèÿ
#en: see "examples" dir for additional examples.
#ru: â ïàïêå "examples" äîïîëíèòåëüíûå ïðèìåðû
#############################################################################
class CyrLat {
    var $cyr=array(
    "Ù","Ø","×","Ö","Þ","ß","Æ","À","Á","Â","Ã","Ä","Å","¨","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Ô","Õ","Ü","Û","Ú","Ý");
    var $lat=array(
    "Sch","Sh","Ch","Ts","Yu","Ya","Zh","A","B","V","G","D","E","E","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","H","'","Y","`","E");
    var $lat_additional=array(
    "W","X","Q","Yo","Ja","Ju","'","`","y");
    var $cyr_additional=array(
    "Â","Êñ","Ê","¨","ß","Þ","ü","ú","û");
    function cyr2lat($input){
     for($i=0;$i<count($this->cyr);$i++){
       $current_cyr=$this->cyr[$i];
       $current_lat=$this->lat[$i];
       $input=str_replace($current_cyr,$current_lat,$input);
       $input=str_replace(strtolower($current_cyr),strtolower($current_lat),$input);
     }
    return($input);
    }
    function lat2cyr($input){
     for($i=0;$i<count($this->lat_additional);$i++){
       $current_cyr=$this->cyr_additional[$i];
       $current_lat=$this->lat_additional[$i];
       $input=str_replace($current_lat,$current_cyr,$input);
       $input=str_replace(strtolower($current_lat),strtolower($current_cyr),$input);
     }
     for($i=0;$i<count($this->lat);$i++){
       $current_cyr=$this->cyr[$i];
       $current_lat=$this->lat[$i];
       $input=str_replace($current_lat,$current_cyr,$input);
       $input=str_replace(strtolower($current_lat),strtolower($current_cyr),$input);
     }
    return($input);
    }
}

#Uncomment for example
#$cyrlat = new CyrLat;
#$inp="Çäðàâñòâóé, ìîé äàë¸êèé íåçíàêîìûé äðóã!";
#$out=$cyrlat->cyr2lat($inp);
#echo "!: $out <br>";
#$out2=$cyrlat->lat2cyr($out);
#echo "!: $out2 <br>";

?>