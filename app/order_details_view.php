<?php
// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/order_details.php');
	include_once(__DIR__ . '/order_details_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('order_details');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'order_details';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`order_details`.`odID`" => "odID",
		"IF(    CHAR_LENGTH(`orders1`.`OrderID`), CONCAT_WS('',   `orders1`.`OrderID`), '') /* Order ID */" => "OrderID",
		"IF(    CHAR_LENGTH(`categories1`.`CategoryName`) || CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `categories1`.`CategoryName`, ' / ', `suppliers1`.`CompanyName`), '') /* Category */" => "Category",
		"IF(    CHAR_LENGTH(`products1`.`ProductName`), CONCAT_WS('',   `products1`.`ProductName`), '') /* Product */" => "ProductID",
		"CONCAT('$', FORMAT(`order_details`.`UnitPrice`, 2))" => "UnitPrice",
		"`order_details`.`Quantity`" => "Quantity",
		"CONCAT('$', FORMAT(`order_details`.`Discount`, 2))" => "Discount",
		"`order_details`.`Subtotal`" => "Subtotal",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`order_details`.`odID`',
		2 => '`orders1`.`OrderID`',
		3 => 3,
		4 => '`products1`.`ProductName`',
		5 => '`order_details`.`UnitPrice`',
		6 => '`order_details`.`Quantity`',
		7 => '`order_details`.`Discount`',
		8 => '`order_details`.`Subtotal`',
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`order_details`.`odID`" => "odID",
		"IF(    CHAR_LENGTH(`orders1`.`OrderID`), CONCAT_WS('',   `orders1`.`OrderID`), '') /* Order ID */" => "OrderID",
		"IF(    CHAR_LENGTH(`categories1`.`CategoryName`) || CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `categories1`.`CategoryName`, ' / ', `suppliers1`.`CompanyName`), '') /* Category */" => "Category",
		"IF(    CHAR_LENGTH(`products1`.`ProductName`), CONCAT_WS('',   `products1`.`ProductName`), '') /* Product */" => "ProductID",
		"CONCAT('$', FORMAT(`order_details`.`UnitPrice`, 2))" => "UnitPrice",
		"`order_details`.`Quantity`" => "Quantity",
		"CONCAT('$', FORMAT(`order_details`.`Discount`, 2))" => "Discount",
		"`order_details`.`Subtotal`" => "Subtotal",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`order_details`.`odID`" => "ID",
		"IF(    CHAR_LENGTH(`orders1`.`OrderID`), CONCAT_WS('',   `orders1`.`OrderID`), '') /* Order ID */" => "Order ID",
		"IF(    CHAR_LENGTH(`categories1`.`CategoryName`) || CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `categories1`.`CategoryName`, ' / ', `suppliers1`.`CompanyName`), '') /* Category */" => "Category",
		"IF(    CHAR_LENGTH(`products1`.`ProductName`), CONCAT_WS('',   `products1`.`ProductName`), '') /* Product */" => "Product",
		"`order_details`.`UnitPrice`" => "Unit Price",
		"`order_details`.`Quantity`" => "Quantity",
		"`order_details`.`Discount`" => "Discount",
		"`order_details`.`Subtotal`" => "Subtotal",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`order_details`.`odID`" => "odID",
		"IF(    CHAR_LENGTH(`orders1`.`OrderID`), CONCAT_WS('',   `orders1`.`OrderID`), '') /* Order ID */" => "OrderID",
		"IF(    CHAR_LENGTH(`categories1`.`CategoryName`) || CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `categories1`.`CategoryName`, ' / ', `suppliers1`.`CompanyName`), '') /* Category */" => "Category",
		"IF(    CHAR_LENGTH(`products1`.`ProductName`), CONCAT_WS('',   `products1`.`ProductName`), '') /* Product */" => "ProductID",
		"CONCAT('$', FORMAT(`order_details`.`UnitPrice`, 2))" => "UnitPrice",
		"`order_details`.`Quantity`" => "Quantity",
		"CONCAT('$', FORMAT(`order_details`.`Discount`, 2))" => "Discount",
		"`order_details`.`Subtotal`" => "Subtotal",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = ['OrderID' => 'Order ID', 'ProductID' => 'Product', ];

	$x->QueryFrom = "`order_details` LEFT JOIN `orders` as orders1 ON `orders1`.`OrderID`=`order_details`.`OrderID` LEFT JOIN `products` as products1 ON `products1`.`ProductID`=`order_details`.`ProductID` LEFT JOIN `categories` as categories1 ON `categories1`.`CategoryID`=`products1`.`CategoryID` LEFT JOIN `suppliers` as suppliers1 ON `suppliers1`.`SupplierID`=`products1`.`SupplierID` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm['view'] == 0 ? 1 : 0);
	$x->AllowDelete = $perm['delete'];
	$x->AllowMassDelete = (getLoggedAdmin() !== false);
	$x->AllowInsert = $perm['insert'];
	$x->AllowUpdate = $perm['edit'];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 1;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = (getLoggedAdmin() !== false);
	$x->AllowAdminShowSQL = showSQL();
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'order_details_view.php';
	$x->TableTitle = 'Order Items';
	$x->TableIcon = 'resources/table_icons/application_form_magnify.png';
	$x->PrimaryKey = '`order_details`.`odID`';
	$x->DefaultSortField = '2';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth = [70, 150, 350, 75, 75, 75, 150, ];
	$x->ColCaption = ['Order ID', 'Category', 'Product', 'Unit Price', 'Quantity', 'Discount', 'Subtotal', ];
	$x->ColFieldName = ['OrderID', 'Category', 'ProductID', 'UnitPrice', 'Quantity', 'Discount', 'Subtotal', ];
	$x->ColNumber  = [2, 3, 4, 5, 6, 7, 8, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/order_details_templateTV.html';
	$x->SelectedTemplate = 'templates/order_details_templateTVS.html';
	$x->TemplateDV = 'templates/order_details_templateDV.html';
	$x->TemplateDVP = 'templates/order_details_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = true;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: order_details_init
	$render = true;
	if(function_exists('order_details_init')) {
		$args = [];
		$render = order_details_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// column sums
	if(strpos($x->HTML, '<!-- tv data below -->')) {
		// if printing multi-selection TV, calculate the sum only for the selected records
		$record_selector = Request::val('record_selector');
		if(Request::val('Print_x') && is_array($record_selector)) {
			$QueryWhere = '';
			foreach($record_selector as $id) {   // get selected records
				if($id != '') $QueryWhere .= "'" . makeSafe($id) . "',";
			}
			if($QueryWhere != '') {
				$QueryWhere = 'where `order_details`.`odID` in ('.substr($QueryWhere, 0, -1).')';
			} else { // if no selected records, write the where clause to return an empty result
				$QueryWhere = 'where 1=0';
			}
		} else {
			$QueryWhere = $x->QueryWhere;
		}

		$sumQuery = "SELECT SUM(`order_details`.`Quantity`), SUM(`order_details`.`Subtotal`) FROM {$x->QueryFrom} {$QueryWhere}";
		$res = sql($sumQuery, $eo);
		if($row = db_fetch_row($res)) {
			$sumRow = '<tr class="success sum">';
			if(!Request::val('Print_x')) $sumRow .= '<th class="text-center sum">&sum;</th>';
			$sumRow .= '<td class="order_details-OrderID sum"></td>';
			$sumRow .= '<td class="order_details-Category sum"></td>';
			$sumRow .= '<td class="order_details-ProductID sum"></td>';
			$sumRow .= '<td class="order_details-UnitPrice sum"></td>';
			$sumRow .= "<td class=\"order_details-Quantity text-right sum locale-int\">{$row[0]}</td>";
			$sumRow .= '<td class="order_details-Discount sum"></td>';
			$sumRow .= "<td class=\"order_details-Subtotal text-right sum locale-float\">{$row[1]}</td>";
			$sumRow .= '</tr>';

			$x->HTML = str_replace('<!-- tv data below -->', '', $x->HTML);
			$x->HTML = str_replace('<!-- tv data above -->', $sumRow, $x->HTML);
		}
	}

	// hook: order_details_header
	$headerCode = '';
	if(function_exists('order_details_header')) {
		$args = [];
		$headerCode = order_details_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: order_details_footer
	$footerCode = '';
	if(function_exists('order_details_footer')) {
		$args = [];
		$footerCode = order_details_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
