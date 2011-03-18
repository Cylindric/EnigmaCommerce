<?php
	/* ----------------------------------------------------------------
		    Filename:

		Requirements:

		       Notes:
	------------------------------------------------------------------- */

	require_https();

	//suppliers
	$setFocusForm='frmeditlist';
	$setFocusField='txtSupplierName';

	if( (count($_POST)>0) && ($_POST['listtype']=='supplier') ) {
		if(!empty($_POST['txtSupplierName'])) {
			spInsertSupplier($_POST['txtSupplierName'], $_POST['txtSupplierDescription']);
		}
		foreach($_POST as $key=>$value) {
			if(strcmp(substr($key,0,16),'txtSupplierName_')==0) {
				$temp = explode('_',$key);
				$id = $temp[1];
				$name = $value;
				$description = $_POST['txtSupplierDescription_'.$id];
				$primary     = $_POST['chkSupplierIsPrimary_'.$id];
				$delete      = $_POST['chkSupplierDelete_'.$id];

				if($delete==1) {
					spDeleteSupplier($id);
				} else {
					spUpdateSupplier($id, $name, $description, $primary);
				}
			}
		}
		reload_page();
	}

	//labels
	$html->assign('TEXT', 'Name'); $html->parse('main.list.heading');
	$html->assign('TEXT', 'Description'); $html->parse('main.list.heading');
	$html->assign('TEXT', 'Default'); $html->parse('main.list.heading');
	$html->assign('TEXT', 'Delete'); $html->parse('main.list.heading');
	$html->assign('TEXT', 'Linked Items'); $html->parse('main.list.heading');

	//space for new item
	$tab=1;
	$html->assign('LIST_NAME', 'Suppliers');
	$html->assign('LIST_TYPE', 'supplier');
	$html->assign('NAME', 'txtSupplierName');
	$html->assign('VALUE', '');
	$html->assign('TAB', $tab++);
	$html->parse('main.list.row.field.input');  $html->parse('main.list.row.field');
	$html->assign('NAME', 'txtSupplierDescription');
	$html->assign('VALUE', '');
	$html->assign('TAB', $tab++);
	$html->parse('main.list.row.field.input');  $html->parse('main.list.row.field');
	$html->assign('VALUE', '&nbsp;');
	$html->parse('main.list.row.field.text'); $html->parse('main.list.row.field');
	$html->assign('VALUE', '&nbsp;');
	$html->parse('main.list.row.field.text'); $html->parse('main.list.row.field');
	$html->assign('VALUE', '&nbsp;');
	$html->parse('main.list.row.field.text'); $html->parse('main.list.row.field');
	$html->parse('main.list.row');

	//existing items
	$rs = view_supplier();
	while($r = db_fetch_array($rs)) {
		$html->assign('NAME', 'txtSupplierName_'.$r['SupplierID']);
		$html->assign('VALUE', $r['SupplierName']);
		$html->assign('TAB', $tab++);
		$html->parse('main.list.row.field.input');
		$html->parse('main.list.row.field');

		$html->assign('NAME', 'txtSupplierDescription_'.$r['SupplierID']);
		$html->assign('VALUE', $r['Description']);
		$html->assign('TAB', $tab++);
		$html->parse('main.list.row.field.input');
		$html->parse('main.list.row.field');

		$html->assign('NAME', 'chkSupplierIsPrimary_'.$r['SupplierID']);
		$html->assign('VALUE', 1);
		$html->assign('CHECKED', ($r['IsPrimary']==0)?'':' checked="checked"');
		$html->assign('TAB', $tab++);
		$html->parse('main.list.row.field.checkbox');
		$html->parse('main.list.row.field');

		$html->assign('NAME', 'chkSupplierDelete_'.$r['SupplierID']);
		$html->assign('VALUE', 1);
		$html->assign('CHECKED', '');
		$html->assign('TAB', $tab++);
		$html->parse('main.list.row.field.checkbox');
		$html->parse('main.list.row.field');

		$html->assign('VALUE', $r['DetailCount']);
		$html->parse('main.list.row.field.text');
		$html->assign('TAB', $tab++);
		$html->assign('CLASS', 'class="numeric"');
		$html->parse('main.list.row.field');
		$html->assign('CLASS', '');

		$html->parse('main.list.row');
	}

	$html->parse('main.list');

?>