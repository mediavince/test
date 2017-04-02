<?php if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

      if ($this_is == 'event') {
        if (isset($filter_index)) {
					/*
					 * example for the case where we want to show either the future events or the archive
					 * if there are no events in the future
					 * >synchronise the modification in the page as such and in this order:
					 * [event:index|future=from]
					 * [event:index]
					 *
					 * >in _extra_routines, after having added the bogus field at the end of the table
					 *
        if (($this_is=='event')&&($key == 'bogus')) {
					if (isset($filter_index)) {
						if (isset($filter_future)) {
							$future_events_oked = true;//will get set only if there are events in future
							//
							// using the var bypass_this as true and an array with only bogus and an empty tpl
							// the next event module will be ignored, here we check first for the future and then
							// if it is empty, we fall back on the archive if future_events_oked is never set
							//
						}
						// example of css refactoring, this is needed if any of the tags around the list/article
						// are set, update also the templates main.css
						if (!isset($looping_through_items_of_events)) {
							if (count($array_getitems)==1)
							$stylesheet .= '<style type="text/css">#hsep_top_events{margin-top:-5px;}</style>';
						} else {
							$looping_through_items_of_events = true;
						}
					}
					 * in _tpl_advanced_modules, call only the bogus field to eval the situation
					if (isset($future_events_oked)) {
					//
					// by using this var, we bypass the loop to avoid presenting any events when we already
					// checked for the future module, if it is set then bypass it...
					// this has to be implemented in _extra_routines with the bogus field, where we introduce
					// the condition in order to be able to set future_events_oked if it evals to true
					//
						$bypass_this = true;
	          $show_array = array("bogus");
	          $array_tpl = array('');
					} else {
						// place the following block here
					}

					 */
          $show_array = array("type", "title", "from", "location");
          $array_tpl = array('<hr style="clear:right;" /><a href="', "\$local_url", '&amp;', "\$this_is", 'Id=', "\$show_id", '" target="_self"><b>', "\$show_from", ': ', "\$show_type", ', <i>', "\$show_location", '</i></b><br />', "\$show_title", '</a><br />');
          
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