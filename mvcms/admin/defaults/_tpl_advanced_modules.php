<?PHP if (stristr($_SERVER['PHP_SELF'],"_tpl_advanced_modules.php") || !isset($this_is)){include '_security.php';Header("Location: $redirect");Die();}

      if ($this_is == 'event') {
        if (isset($filter_index)) {
          $show_array = array("type", "title", "from", "location");
          $array_tpl = array('<hr style="clear:right;" /><a href="', "\$local_url", '&amp;', "\$this_is", 'Id=', "\$show_id", '" target="_self"><b>', "\$show_from", ': ', "\$show_type", ', <i>', "\$show_location", '</i></b><br />', "\$show_title", '</a><br />');
        //  $array_tpl = array('<hr style="clear:right;" /><a href="', "\$local_url", '&amp;', "\$this_is", 'Id=', "\$show_id", '" target="_self"><b>', "\$show_type", ', <i>', "\$show_location", ' ', "\$show_from", '</i></b><br />', "\$show_title", '</a><br />');
          
        } else {
        //  $select_array = array("type", "from");
          $ordered_by = "type";
          if (isset($filter_archive)) {
            $ordered_by = "type,from";
            $view_see_all_button = false;
          }
          $filter_search = false;
          $index_by_alpha = true;
          $show_array = array("type", "title", "from", "until");//, "entry:cut"
          $array_tpl = array('<ul class="arrow"><li><a href="', "\$local_url", '&amp;', "\$this_is", 'Id=', "\$show_id", '" target="_self">', "\$show_title", '</a><br /><span class="square">', "\$fromString", ' ', "\$show_from", ' - ', "\$untilString", ' ', "\$show_until", '</span></li></ul>');//<b>', "\$show_type", '</b>: // ', "\$show_type", '
  
          $show_array_by_id = array("type", "title", "from", "until", "entry");
          $array_tpl_by_id = array('<h1>', "\$show_title", '</h1>', "\$show_from", ' - ', "\$show_until", '<br />', "\$show_entry");
  
        }
      } else if ($this_is == 'gallery') {
        $ordered_by = "date";
        $show_array = array("title");
        $array_tpl = array('<a href="', "\$local_url", '&amp;', "\$this_is" ,'Id=', "\$show_id", '" target="_self">', "\$show_img", "\$show_title", '</a>');
        $show_array_by_id = array("title", "date", "entry", "img");
        $array_tpl_by_id = array('<h1>', "\$show_title", '</h1><i>', "\$dateString", ': ', "\$show_date", '</i><br />', "\$show_entry", '<br /><div class="lightbox">', "\$show_img", '</div>');
      } else if ($this_is == 'forum') {//
        $ordered_by = "date";
        $show_array = array("title", "membre", "comment");
        $array_tpl = array('<span class="arrow"><a href="', "\$local_url", '&amp;', "\$this_is" ,'Id=', "\$show_id", '" target="_self">', "\$show_title", '</a>', ' ', "\$parString", ' ', "\$show_membre", '</span>');//, "\$show_comment"
        $show_array_by_id = array("title", "membre", "date", "entry", "comment");
        $array_tpl_by_id = array('<div class="centered"><h1>', "\$show_title", '</h1><b>', "\$parString", ' ', "\$show_membre", '</b><br /><i>', "\$dateString", ': ', "\$show_date", '</i><div class="centered">', "\$show_entry", '<br />', "\$show_comment", '</div></div>');
      } else if ($this_is == 'membre') {
        $ordered_by = "nom";
        $index_by_alpha = true;
        $show_array = array("gendre", "prenom", "nom", "profession");//, "img"
        $array_tpl = array('<a href="', "\$local_url", '&amp;', "\$this_is" ,'Id=', "\$show_id", '">', "\$show_gendre", ' ', "\$show_prenom", ' ', "\$show_nom", '</a>', '<br /><i>', "\$show_profession", '</i><br />');//"\$show_img", 

        $show_array_by_id = array("gendre", "prenom", "nom", "profession", "img", "ville", "pays", "user:email", "skype", "marketing1", "forum", "event");//
        $array_tpl_by_id = array('<div class="rightbox">', "\$show_img", '<p>', "\$show_ville", '<br />', "\$show_pays", '<br /> <br /><span class="email">', "\$show_user_email", '</span><br /><span class="skype">', "\$show_skype", '</span></p></div>', '<a href="', "\$local_url", "&amp;membreId=", "\$show_id", '">', "\$show_gendre", ' ', "\$show_prenom", ' ', "\$show_nom", '</a>', '<br /><i>', "\$show_profession", '</i><br />', '<br /><b>', "\$marketing1String", '</b><br />', "\$show_marketing1", '<br />');//"\$show_img", 

        $link_array_linked_by_id = array("title");
        $array_tpl_linked_by_id = array('<ul class="arrow"><li><a href="', "\$local_url", "&amp;instituteId=", "\$instituteId", '" target="_self">', "\$link_title", '</a></li></ul>');
      } else {
        # DEFAULT
        if (!in_array($this_is,$array_unmanaged_modules)) {
          $ordered_by = "date";
          $filter_search = false;
          $show_array = array("title", "titre", "nom", "name", "util");
          $array_tpl = array('<span class="arrow"><a href="', "\$local_url", '&amp;', "\$this_is" ,'Id=', "\$show_id", '" target="_self">', "\$show_title", "\$show_titre", "\$show_nom", "\$show_name", "\$show_util", '</a></span>');
  
          $show_array_by_id = array("title", "titre", "nom", "name", "util", "type", "from", "until", "entry", "desc", "marketing1", "img", "doc");
          $array_tpl_by_id = array('<div class="centered"><div style="float:right;max-width:', "\$max_width", 'px;">', "\$show_doc", "\$show_img", '</div><h3>', "\$show_type", '</h3><h1>', "\$show_title", "\$show_titre", "\$show_nom", "\$show_name", "\$show_util", '</h1><b>', "\$fromString", ' ', "\$show_from", ' ', "\$untilString", ' ', "\$show_until", '</b><br /><div class="centered">', "\$show_entry", "\$show_desc", "\$show_marketing1", '</div></div>');
        }
      }
?>