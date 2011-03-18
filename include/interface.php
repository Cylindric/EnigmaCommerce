<?php
// ==================================================================
//   this include provides some interface functions that you might
//   want to use in your own pages outside of the store.  You should
//   be able to just include the application.php and then use these
//   functions.
// ==================================================================

// ==================================================================
//                              categories
// ==================================================================
	function CategoryVisible($categoryid) {
		global $OPT;

		$rs = view_categorydetails($categoryid);
		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {
			$r = db_fetch_array($rs);
			return (boolean) $r['WebView'];
		}
	}
	
	function ParentCategoryItemCount($categoryid) {
		global $OPT, $dbprefix;
		
		$categoryid = sql_html_to_int($categoryid);

		$sql  = "SELECT count(ic.itemid) AS ItemCount ";
		$sql .= "FROM {$dbprefix}category c ";
		$sql .= "LEFT JOIN {$dbprefix}itemcategory ic ON (c.categoryid = ic.categoryid) ";
		$sql .= "WHERE c.parentid = $categoryid ";
		$sql .= "GROUP BY c.parentid ";
		
		$rs = db_query($sql);
		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {
			$r = db_fetch_array($rs);
			return $r['ItemCount'];
			
		}
	}

// ==================================================================
//                              items
// ==================================================================


// ==================================================================
//                              details
// ==================================================================
   	//returns an unformatted price if successful or false if no match
	//was found.
	function GetDetailPrice($detailid) {
		global $OPT;

		$rs=view_details(0,$detailid);
		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {
			$r=db_fetch_array($rs);
			return (float) $r['RetailPrice'];
		}
	}

	//returns the cheapest supplier for the current detail.  If there
	//are no suppliers, return false
	function GetDetailCheapestSupplier($detailid) {
		global $OPT;

		$cheapestprice='';
		$cheapestsupplier=0;
		$rs = view_detailsuppliers($detailid);
		if(db_num_rows($rs)==0) {
			return (boolean)false;
		} else {
			while($r=db_fetch_array($rs)) {
				$dbsupplierid = $r['SupplierID'];
				$dbprice = $r['Price']-($r['Price']*$r['BaseDiscount']);
				if( ($cheapestprice=='') || ($dbprice<$cheapestprice) ) {
					$cheapestprice = $dbprice;
					$cheapestsupplier = $dbsupplierid;
				}
			}
		}

		return $cheapestsupplier;

	}
	

// ------------------------------------------------------------------
	//returns an unformatted price if a supplier is found for that
	//item, otherwise returns false
	function GetDetailCheapestPrice($detailid) {
		$cheapest = 0;
		$rs = view_detailsuppliers($detailid);

		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {

			while($r = db_fetch_array($rs)) {
				$gross = $r['Price'];
				$discount = $r['BaseDiscount'];
				$net=$gross-($gross*$discount);
				if(($cheapest==0)||($net<$cheapest)) {
					$cheapest = $net;
				}
			}
			return (float) $cheapest;

		}
	}


// ==================================================================
//                                stock
// ==================================================================
	function SetStockLevel($detailid, $newlevel, $preferredsupplier) {
		//this is a fairly complex routine that sets the current
		//available stock level for a given item.
		//if the specified stock level is less than the current level
		//then stock is removed from each order line until none are
		//left.
		//If the specified level is highter than the current, then
		//?
		//returns true if the update was okay, otherwise false

		global $dbprefix;

		$sql  = "SELECT SUM(QuantityRemaining) AS TotalRemaining \n";
		$sql .= "FROM {$dbprefix}stock \n";
		$sql .= "WHERE DetailID=$detailid \n";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);
		$oldlevel = $r['TotalRemaining'];
		$currentrows = db_num_rows($rs);

		if($newlevel==$oldlevel) {
			if($debug)echo("Nothing...");
			//do nothing
			return (boolean)true;


		} elseif($newlevel<$oldlevel) {
			//remove stock from table
			$delta = $oldlevel-$newlevel;
			$sql  = "SELECT StockID, QuantityRemaining \n";
			$sql .= "FROM {$dbprefix}stock \n";
			$sql .= "WHERE DetailID=$detailid \n";
			$sql .= "AND QuantityRemaining!=0 \n";
			$sql .= "ORDER BY DeliveryDate, StockID \n";
			$rs = db_query($sql);
			while($r = db_fetch_array($rs)) {
				$id = $r['StockID'];
				$amt = $r['QuantityRemaining'];

				if($amt<$delta) {
					//not enough items to carry the loss, so remove all
					$newamt = 0;
					$delta = $delta-$amt;

				} else {
					//this row will carry the rest of the loss
					$newamt = $amt-$delta;
					$delta = 0;
				}

				$sql  = "UPDATE {$dbprefix}stock \n";
				$sql .= "SET QuantityRemaining=$newamt \n";
				$sql .= "WHERE StockID=$id \n";
				db_query($sql);

				if($delta<=0) {
					//none left to remove, so finish
					return (boolean)true;
				}
			}


		} else {
			//Add stock to table
			//If a supplier was specified, only add to that supplier's lines, or
			//add a new line for that supplier.
			//If no supplier was given, add to any available line, or fail if no
			//spare capapacity.
			$delta = $newlevel-$oldlevel;
			$sql  = "SELECT StockID, SupplierID, QuantityRemaining, QuantityPurchased \n";
			$sql .= "FROM {$dbprefix}stock \n";
			$sql .= "WHERE DetailID=$detailid \n";
			$sql .= "ORDER BY DeliveryDate DESC, StockID DESC \n";
			$rs = db_query($sql);
			while($r = db_fetch_array($rs)) {
				$id = $r['StockID'];
				$amt = $r['QuantityRemaining'];
				$bought = $r['QuantityPurchased'];
				$dbsupplier = $r['SupplierID'];
				$space = $bought-$amt;

				//if supplier not given, or supplier given equals this supplier
				if( ($preferredsupplier==0) || ($preferredsupplier==$dbsupplier) ) {
					if($space<$delta) {
						//not enough room to add back all requested
						$newamt = $amt+$space;
						$delta = $delta-$space;
					} else {
						//room on this order for all the remaining changes
						$newamt = $amt+$delta;
						$delta = 0;
					}

					$sql  = "UPDATE {$dbprefix}stock \n";
					$sql .= "SET QuantityRemaining=$newamt \n";
					$sql .= "WHERE StockID=$id \n";
					db_query($sql);
				}

				if($delta<=0) {
					//none left to remove, so finish
					return (boolean)true;
				}
			}

			//if we get here then there's unallocated stock, allocate to the preferred
			//supplier if specified, otherwise dump
			if( ($delta!=0) && ($preferredsupplier!==0) ) {
				//get this suppliers price
				$price = getSupplierPrice($preferredsupplier, $detailid);
				if($price!==false) {
					echo("<ul><li>Adding stock Detail:'$detailid', Supplier:'$preferredsupplier', '', Price:'$price', Delta:'$delta', Delta:'$delta'</li></ul>");
					spInsertStock($detailid, $preferredsupplier, '', $price, $delta, $delta);
				} else {
				}
			}
		}
	}


// ==================================================================
//                              suppliers
// ==================================================================
	function GetSupplier($id, $field) {
		$rs=view_supplier($id);
		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {
			$r=db_fetch_array($rs);
			return $r[$field];
		}
	}


// ------------------------------------------------------------------
	function GetSupplierName($id) {
		return GetSupplier($id, 'SupplierName');
	}

// ------------------------------------------------------------------
	function GetDefaultSupplierID() {
		$rs = view_supplier(0, true);
		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {
			$r=db_fetch_array($rs);
			return (int)$r['SupplierID'];
		}
	}

// ------------------------------------------------------------------
	function getSupplierPrice($supplierid, $detailid) {
		$supplierfound = false;
		$supplierprice = 0;
		$rs = view_detailsuppliers($detailid);
		if(db_num_rows($rs)==0) {
			return (boolean)false;
		} else {
			while($r = db_fetch_array($rs)) {
				$dbsupplierid = $r['SupplierID'];
				$dbprice = $r['Price'];
				$value = $dbprice;

				if($dbsupplierid==$supplierid) {
					$supplierfound = true;
					$supplierprice = $value;
					break;
				}
			}

			if($supplierfound) {
				return (float)$supplierprice;
			} else {
				return (boolean)false;
			}
		}
	}

// ==================================================================
//                                units
// ==================================================================
	function GetUnit($id, $field) {
		$rs=view_units($id);
		if(db_num_rows($rs)==0) {
			return (boolean) false;

		} else {
			$r=db_fetch_array($rs);
			return $r[$field];
		}
	}
	function GetUnitName($id) {
		return GetUnit($id, 'UnitName');
	}
	function GetUnitCode($id) {
		return GetUnit($id, 'UnitCode');
	}


// ==================================================================
//                                pictures
// ==================================================================
	function getPictureFileName($pictureid) {
		global $OPT, $dbprefix;

		$pictureid=sql_html_to_int($pictureid);

		$sql  = "SELECT FileName ";
		$sql .= "FROM {$dbprefix}picture ";
		$sql .= "WHERE PictureID=$pictureid ";
		$rs = db_query($sql);
		if(db_num_rows($rs)==0) {
			return (boolean)false;
		} else {
			$r=db_fetch_array($rs);
			$dbfilename = $r['FileName'];
			return $dbfilename;
		}
	}

// ==================================================================
//                                stock codes
// ==================================================================
	function getItemFromStockCode($dirty_stockcode) {
		global $OPT, $dbprefix;
		
		$stockcode = sql_html_to_string($dirty_stockcode);
		$categorypart = sql_html_to_int(substr($stockcode,0,2));
		$detailpart = sql_html_to_int(substr($stockcode,-3));
		
		$sql  = "SELECT i.ItemID ItemID, i.ItemName ItemName \n";
		$sql .= "FROM {$dbprefix}detail d \n";
		$sql .= "INNER JOIN {$dbprefix}item i ON (d.ItemID=i.ItemID) \n";
		$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0) \n";
		$sql .= "INNER JOIN {$dbprefix}category c ON (ic.CategoryID=c.CategoryID) \n";
		$sql .= "INNER JOIN {$dbprefix}category c1 ON (c.ParentID=c1.CategoryID) \n";
		$sql .= "WHERE d.StockCode=$detailpart \n";
		$sql .= "AND c1.StockCodePrefix=$categorypart \n";
		//echo(nl2br($sql));
		$rs = db_query($sql);

		if(db_num_rows($rs)==0) {
			return (bool)false;
		} else {
			$r = db_fetch_array($rs);
			return $r;
		}
	}
	
	
	function getDetailFromStockCode($dirty_stockcode) {
		global $OPT, $dbprefix;
		
		$stockcode = sql_html_to_string($dirty_stockcode);
		$categorypart = sql_html_to_int(substr($stockcode,0,2));
		$detailpart = sql_html_to_int(substr($stockcode,-3));
		
		$sql  = "SELECT d.DetailID DetailID, d.RetailPrice RetailPrice, i.ItemID ItemID, i.ItemName ItemName \n";
		$sql .= "FROM {$dbprefix}detail d \n";
		$sql .= "INNER JOIN {$dbprefix}item i ON (d.ItemID=i.ItemID) \n";
		$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0) \n";
		$sql .= "INNER JOIN {$dbprefix}category c ON (ic.CategoryID=c.CategoryID) \n";
		$sql .= "INNER JOIN {$dbprefix}category c1 ON (c.ParentID=c1.CategoryID) \n";
		$sql .= "WHERE d.StockCode=$detailpart \n";
		$sql .= "AND c1.StockCodePrefix=$categorypart \n";
		
		$rs = db_query($sql);

		if(db_num_rows($rs)==0) {
			return (bool)false;
		} else {
			$r = db_fetch_array($rs);
			
			//Add a buy link
			$r['BuyURL'] = $OPT->enigmaroot.$OPT->controlpage.'/basket/add/'.$r['DetailID'].'/1';
			
			return $r;
		}
	}
	
	
	//Retrieve the full stock code (category and detail parts) for the item detail
	//specified
	function getStockCode($detailid) {
		global $OPT, $dbprefix;

		$detailid=sql_html_to_int($detailid);
		$dbstockcode=0;
		$sql  = "SELECT c1.StockCodePrefix, d.StockCode \n";
		$sql .= "FROM {$dbprefix}detail d \n";
		$sql .=	"INNER JOIN {$dbprefix}item i ON (d.ItemID=i.ItemID) \n";
		$sql .=	"INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0) \n";
		$sql .=	"INNER JOIN {$dbprefix}category c2 ON ic.CategoryID=c2.CategoryID \n";
		$sql .=	"INNER JOIN {$dbprefix}category c1 ON c2.ParentID=c1.CategoryID \n";
		$sql .= "WHERE d.DetailID = $detailid \n";
		$rs = db_query($sql);
		while($r = db_fetch_array($rs)) {
			$dbstockcode = format_stockcode($r['StockCodePrefix'], $r['StockCode']);
		}
		return $dbstockcode;
	}


	//get image id from stockcode
	function getPictureFromStockCode($stockcode) {
		global $OPT, $dbprefix;

		$stockcode=sql_html_to_string($stockcode);

		$categorypart = sql_html_to_int(substr($stockcode,0,2));
		$detailpart = sql_html_to_int(substr($stockcode,-3));

		$sql  = "SELECT p.FileName \n";
		$sql .= "FROM {$dbprefix}itempicture ip \n";
		$sql .= "INNER JOIN {$dbprefix}picture p ON (ip.PictureID=p.PictureID) \n";
		$sql .= "INNER JOIN {$dbprefix}detail d ON (ip.DetailID=d.DetailID) \n";
		$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0) \n";
		$sql .= "INNER JOIN {$dbprefix}category c ON (ic.CategoryID=c.CategoryID) \n";
		$sql .= "INNER JOIN {$dbprefix}category c1 ON (c.ParentID=c1.CategoryID) \n";
		$sql .= "WHERE d.StockCode=$detailpart \n";
		$sql .= "AND c1.StockCodePrefix=$categorypart \n";
		$sql .= "AND ip.DeleteDate = 0 \n";
		$sql .= "ORDER BY ip.IsPrimary DESC \n";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);

		//if this returns no image, try a more general search on the item code
		if(db_num_rows($rs)==0) {
			$sql  = "SELECT d.ItemID \n";
			$sql .= "FROM {$dbprefix}detail d \n";
			$sql .= "INNER JOIN {$dbprefix}itemcategory ic ON (d.ItemID=ic.ItemID AND ic.IsPrimary!=0) \n";
			$sql .= "INNER JOIN {$dbprefix}category c ON (ic.CategoryID=c.CategoryID) \n";
			$sql .= "INNER JOIN {$dbprefix}category c1 ON (c.ParentID=c1.CategoryID) \n";
			$sql .= "WHERE d.StockCode=$detailpart \n";
			$sql .= "AND c1.StockCodePrefix=$categorypart \n";
			$sql .= "LIMIT 1 \n";
			//echo($sql);
			$rs = db_query($sql);
			
			if(db_num_rows($rs)==0) {
				return (boolean)false;
			} else {
				$r = db_fetch_array($rs);
				$itemid = $r['ItemID'];
				$sql  = "SELECT p.FileName \n";
				$sql .= "FROM {$dbprefix}itempicture ip \n";
				$sql .= "INNER JOIN {$dbprefix}picture p ON (ip.PictureID=p.PictureID) \n";
				$sql .= "WHERE ItemID = $itemid \n";
				$sql .= "AND ip.DeleteDate = 0 \n";
				//echo($sql);
				$rs = db_query($sql);
				if(db_num_rows($rs)==0) {
					return (boolean)false;
				} else {
					$r = db_fetch_array($rs);
					return $r['FileName'];
				}
			}
		}
		return $r['FileName'];
	}

// ==================================================================
//                           missing information
// ==================================================================
	function itemMissingRRP($itemid) {
		global $OPT, $dbprefix;

		$itemid = sql_html_to_int($itemid);

		$sql  = "SELECT COUNT(d.DetailID) AS MissingCount ";
		$sql .= "FROM {$dbprefix}item AS i ";
		$sql .= "LEFT JOIN {$dbprefix}detail AS d ON (i.ItemID = d.ItemID AND d.DeleteDate = 0 AND d.RecommendedPrice=0) ";
		$sql .= "WHERE i.ItemID = $itemid ";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);

		return $r['MissingCount'];
	}

	function itemMissingSupplier($itemid) {
		global $OPT, $dbprefix;

		$itemid = sql_html_to_int($itemid);

		$sql  = "SELECT COUNT(d.DetailID) - COUNT(ds.DetailID) AS MissingCount ";
		$sql .= "FROM {$dbprefix}item AS i ";
		$sql .= "LEFT JOIN {$dbprefix}detail AS d ON (i.ItemID = d.ItemID AND d.DeleteDate = 0) ";
		$sql .= "LEFT JOIN {$dbprefix}detailsupplier AS ds ON (d.DetailID = ds.DetailID AND ds.DeleteDate = 0) ";
		$sql .= "WHERE i.ItemID = $itemid ";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);

		return $r['MissingCount'];
	}

	function itemMissingSupplierPrice($itemid) {
		global $OPT, $dbprefix;

		$itemid = sql_html_to_int($itemid);

		$sql  = "SELECT COUNT(ds.SupplierID) AS MissingCount ";
		$sql .= "FROM {$dbprefix}item AS i ";
		$sql .= "LEFT JOIN {$dbprefix}detail AS d ON (i.ItemID = d.ItemID AND d.DeleteDate = 0) ";
		$sql .= "LEFT JOIN {$dbprefix}detailsupplier AS ds ON (d.DetailID = ds.DetailID AND ds.DeleteDate = 0 AND ds.Price=0) ";
		$sql .= "WHERE i.ItemID = $itemid ";
		$rs = db_query($sql);
		$r = db_fetch_array($rs);

		return $r['MissingCount'];
	}
?>
