<?PHP
      if (!stristr($_SERVER['PHP_SELF'],$urladmin)) {
          
          if (isset($_GET['item']) && is_int($_GET['item']+0)) $cartitem = $_GET['item'];
          if (isset($_GET['add-to-cart'])) $addtocart = $_GET['add-to-cart'];
          if (isset($_GET['remove-from-cart'])) $removefromcart = $_GET['remove-from-cart'];
          
          if (!isset($_SESSION['cart-item'])) $_SESSION['cart-item'] = array();
          //$_SESSION['cart-price'] = 0;
          if ($cartitem && $addtocart && in_array($addtocart,$array_shop)) {
            $_SESSION['cart-price'] += sql_getone(${"tbl".$addtocart},"WHERE ".$addtocart."rid='$cartitem' ",$addtocart."price");
            $_SESSION['cart-item'][] = $addtocart."_".$cartitem;
          }
          
          if ($cartitem && $removefromcart && in_array($removefromcart,$array_shop)) {
            //if (isset($_GET['item']))
            $_SESSION['cart-price'] -= sql_getone(${"tbl".$removefromcart},"WHERE ".$removefromcart."rid='$cartitem' ",$removefromcart."price");
            if ($_SESSION['cart-price']<0) $_SESSION['cart-price'] = 0;
            unset($_SESSION['cart-item'][array_search($removefromcart."_".$cartitem,$_SESSION['cart-item'])]);
            $_SESSION['cart-item'] = array_values($_SESSION['cart-item']);
            $numberitems = (isset($_SESSION['cart-item'][0])?count($_SESSION['cart-item']):0);
          }
          
          if (isset($_GET['empty-cart'])) {
            $_SESSION['cart-item'] = NULL;
            $_SESSION['cart-price'] = 0;
          }
          
          if (isset($_SESSION['cart-item']))
          $numberitems = count($_SESSION['cart-item']);
          if (isset($numberitems))  
          $previewcart = '<div style="text-align:center;" class="previewcart">You have '.$numberitems.' item(s)<br />in your cart!<br /> <br /><span style="font-size:x-large;">Total: €'.$_SESSION['cart-price'].'</span><br /> <br /><a href="'.lgx2readable($lg,'','cart').'"><img src="'.$mainurl.'images/icon_cart.gif" title="View" alt="View" border="0" /></a> | <a href="'.$local_url.'empty-cart">Empty</a></div><br />';
          else 
          $previewcart = '<div style="text-align:center;" class="previewcart">You have no item(s) in your cart!</div><br />';
        
        if (sql_getone($tblcont," WHERE $where_statut_lang contpg='".$x."' ","conttype") == 'cart') {
          $content = '<h2>Cart!</h2><img src="'.$mainurl.'images/cart.png" style="float:left;" title="Cart" alt="Cart" border="0" />';
          sort($_SESSION['cart-item']);
          $array_count_values = array_count_values($_SESSION['cart-item']);
          foreach(array_unique($_SESSION['cart-item']) as $cartitems) {
            $what_item = explode("_",$cartitems);
            $what_kind = $what_item[0];
            $what_id = $what_item[1];
            $get_item_for_cart = sql_get(${"tbl".$what_kind},"WHERE ".$what_kind."rid='".$what_id."' ","{$what_kind}title,{$what_kind}price");
            $content .= '<div style="width:350px;margin:0 auto;">=> '.$get_item_for_cart[0].' <div style="float:right;text-align:right"> '.$array_count_values[$cartitems].' * €'.price(round($get_item_for_cart[1],2)).' = €'.round(($array_count_values[$cartitems]*$get_item_for_cart[1]),2).'</div><br /></div>';// '.sql_stringit('general',$what_kind).'
          }
          $content .= '<h2 style="text-align:center;"><br />Total: €'.price(round($_SESSION['cart-price'],2)).'</h2>';
        } else
        $leftlinksentry .= $previewcart;  
      }
?>