/*
 $Name  : mvdyn_selectable_options.js, 20090313 © MediaVince;
 $Date  : Fri, 13 Mar 2009 19:00:00 GMT;
 $Lang  : javascript;
 $Target: PHP needs to generate the array for these function to make sense;
 $Type  : text/javascript;
 $Desc  : Upon selection of the first select, the second will update options with only corresponding items.
           Programming by MediaVince  -  All rights reserved.
           Please send any questions to: developer@mediavince.com
 $Template: You just need to have a script like PHP fetching data, ie from a database with MySQL, and looping through it to produce the following output that should then be enclosed in your HTML source
 
    articleArray = [];
    articleArray[3] = 'something with id 3';
    articleArray[4] = 'something else with id 4';
    articleArray[7] = 'some other item with id 7';
    
    Array_3 = [];
    Array_3[11] = 'content matching article 3 with id 11';
    Array_4 = [];
    Array_4[7] = 'content matching article 4 with id 7';
    Array_4[9] = 'other content matching article 4 with id 9';
    Array_7 = [];
    Array_7[6] = 'content matching article 7 with id 6';
    Array_7[8] = 'more content matching article 7 with id 8';
    
  $Note: your select tags should have the following respectively
    1 -> id="something" onchange="changed(this,'otherthing')"
    2 -> id="otherthing" onchange="check_selected('otherthing','something')"
*/
function getelbyid(obj) {
  if (document.getElementById) el = document.getElementById(obj);
  else if (document.all) el = document.all[obj];
  else if (document.layers) el = document.layers[obj];
  return el;
}
function updateselectoptions(el,v,myArray) {
  function assignOption(t,v) {
    var txt = t;
    var val = v;
    var o=new Option(txt,val,false,true);
    el.options[el.options.length]=o;
  }
  el.options.length=[];
  myArray.forEach(assignOption);// bug in ie
}
function changed(el,nel){
  nel = getelbyid(nel);
  v = el.options[el.selectedIndex].value;
  if (v!='')
  updateselectoptions(nel,v,eval('Array_'+v));
  else {
    nel.options.length=[];
  }
}
function check_selected(el,nel){
  el = getelbyid(el);
  nel = getelbyid(nel);
  if (nel.options[nel.selectedIndex].value=='') {
    alert('select '+ nel.id +' first...');
  } else {
    sel = el.options[el.selectedIndex].text;
    var index = eval('Array_'+nel.options[nel.selectedIndex].value).indexOf(sel);// bug in ie
    if (index==-1) {
      alert('not valid!');
    }
  }
}

if (typeof jQuery !== 'undefined')
jQuery(document).ready(function($){
  $("#metadescZone").css('display', 'none');
  $("#metadescToggle").on('click', function(){
    $("#metadescZone").toggle();
  });
});
