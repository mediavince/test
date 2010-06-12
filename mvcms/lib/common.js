/*

 $Name  : common.js, 20061017 © 1996-2007 Nodes & pf;
 $Date  : Tue, 17 Oct 2006 10:00:00 GMT;
 $Exp   : Mon, 31 Dec 2007 23:59:59 GMT;
 $Lang  : javascript;
 $Target: none;
 $Type  : text/javascript;
 $Desc  : Load Common Functions.

 Programming and graphics by pf - All Rights Reserved.
 Please send any questions to: info@nodes.it or pjoef@tiscali.it

*/

// global vars.
var nname = navigator.appName;
var nver  = parseInt(navigator.appVersion);

// fn_css - load specific CSS.
// pitdt: [string] - position in the directory tree.
function fn_css(pitdt)
{
  pitdt = '';
//  if (pitdt == null) pitdt = 'http:\/\/www.nodes.it\/';
/*
  if (window.name == 'sc' && document.bgColor == '#ffffff')
  { document.writeln('<link rel="stylesheet"' +
      ' href="' + pitdt + 'css\/sc.css" \/>');
  }
*/

  // CSS for Netscape < 5...
  if (nname == 'Netscape' && nver < 5)
  { document.writeln('<link rel="stylesheet"' +
      ' href="' + pitdt + 'css\/ns4.css" \/>');

    // Loading OSs specified CSS for Netscape < 5...

    // Netscape < 5 on Linux.
    if (navigator.userAgent.indexOf('inux') != -1)
      document.writeln('<link rel="stylesheet" href="' + pitdt +
        'css\/ns4linux.css" \/>')

  // ...end of loading CSS for Netscape < 5.
  }
  else
  { document.writeln('<link rel="stylesheet"' +
      ' href="' + pitdt + 'css\/ns5.css" \/>');
  // Internet Explorer
/*
  else if (name == 'Microsoft Internet Explorer')
    document.writeln('<link rel="stylesheet"' +
      ' href="' + pitdt + 'css\/ie.css" \/>');
  else
    document.writeln('<link rel="stylesheet"' +
      ' href="' + pitdt + 'css\/ns.css" \/>');
*/
  }
}
// end of_ fn_css

//  end of common.js javascript.