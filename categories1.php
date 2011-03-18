<?php
  $section = 'main.maincats';
 	$_SESSION['maincatdesc']='';
	$mainrs=view_categories(0, false);

	if(authorised()) {
		$html->assign('MAINCAT_LABEL', 'Admin');
		$html->assign('CAT_ID', 0);
		$html->assign('CAT_NAME', 'Add New Category');
		$html->assign('ROW_CLASS', '');

		$html->assign('DESTINATION', $OPT->enigmaroot.$OPT->controlpage.'/deleted/'.($PAGEOPT['deletedvis']?'0':'1'));
		$html->assign('NAME', ($PAGEOPT['deletedvis'])?'Hide Deleted Items':'Show Deleted Items');
 		$html->parse($section.'.row');
		$html->parse($section.'.customrow');
	  $html->parse($section);
  }
 



	while($mainr=db_fetch_array($mainrs)) {
    $dbMainCategoryID = $mainr['CategoryID'];
    $dbMainCategoryType = $mainr['CategoryTypeID'];
    $dbMainCategoryName = $mainr['CategoryName'];


		$html->assign('MAINCAT_LABEL', $dbMainCategoryName);
  
    $rs=view_categories($dbMainCategoryID, false);
    while($r=db_fetch_array($rs)) {

      $dbCategoryID = $r['CategoryID'];
      $dbCategoryType = $r['CategoryTypeID'];
      $dbCategoryName = $r['CategoryName'];
      $dbCategoryDescription = $r['Description'];
		
  		//get category text from Mambo
  		$mosText = mostext($dbCategoryID);
  		if(!empty($mosText)) {
  			$dbCategoryDescription = $mosText;
  		}
  
  		$html->assign('CAT_ID', $dbCategoryID);
  		$html->assign('CAT_NAME', $dbCategoryName);
  		$html->assign('ROW_CLASS', '');
  		if($r['WebView']==0) $html->assign("ROW_CLASS", 'unavailable');
  
  		if($dbCategoryID==$_SESSION['maincatid']) {
  			$html->assign('ROW_CLASS', 'current');
  			$_SESSION['maincatdesc']=$dbCategoryDescription;
  			$_SESSION['maincatname']=$dbCategoryName;
  			$_SESSION['subcatname']='';
        	if($addlog['maincat']==true) spLogToHistory('category', $dbCategoryID, $dbCategoryName, "Viewed category \"$dbCategoryName\"");
  		}
  

  		$html->parse($section.'.row');

    }
    
  	$html->parse($section);
     
	}

?>