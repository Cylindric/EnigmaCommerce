<?php

	//show some special offers and new items on the home page
	//only change them once per session
	
	$panels_to_show = 3;
	$max_img_height = 100;

	if(empty($_SESSION['promo_panels'])) {
		$_SESSION['promo_panels'] = array();			
		
		$categoryfilter = $OPT->cat_specialoffers.','.$OPT->cat_newitems;
		$rs=view_items($categoryfilter, 0, $panels_to_show, 1, 1);
		$num_specials=db_num_rows($rs);
		$counter = 0;
		while($r=db_fetch_array($rs)) {
			$_SESSION['promo_panels'][$counter]['CATID'] = $r['CategoryID'];
			$_SESSION['promo_panels'][$counter]['ITEMID'] = $r['ItemID'];
			$_SESSION['promo_panels'][$counter]['ITEMNAME'] = $r['ItemName'];
			$_SESSION['promo_panels'][$counter]['FILENAME'] = $r['Filename'];
			$_SESSION['promo_panels'][$counter]['FILENAME'] = $r['Filename'];

			$imgsize = getimagesize($_SERVER['DOCUMENT_ROOT'].$OPT->productimageroot.$r['Filename']);
			$width = $imgsize[0];
			$height = $imgsize[1];
			if($height > $max_img_height) {
				$width = $max_img_height * ($width / $height);
				$height = $max_img_height;
			}
			$_SESSION['promo_panels'][$counter]['IMGPROPS'] = " width=\"$width\" height=\"$height\"";
			
			$counter++;
			if($counter>= $panels_to_show) {
				break;
			}
		}
			
	}
	
	if(count($_SESSION['promo_panels'])>0) {
		for($i = 0; $i < count($_SESSION['promo_panels']); $i++) {
			$html->assign('PANEL_NAME', $_SESSION['promo_panels'][$i]['CATID']==$OPT->cat_specialoffers?'Special Offer':'New Item');
			$html->assign('ITEM_ID', $_SESSION['promo_panels'][$i]['ITEMID']);
			$html->assign('ITEM_NAME', $_SESSION['promo_panels'][$i]['ITEMNAME']);
			$html->assign('ITEM_IMAGE', $OPT->productimageroot.$_SESSION['promo_panels'][$i]['FILENAME']);
			$html->assign('IMAGE_PROPS', $_SESSION['promo_panels'][$i]['IMGPROPS']);
			$html->parse('main.promo_panels.panel');
		}
		
		$html->parse('main.promo_panels');
	}


?>