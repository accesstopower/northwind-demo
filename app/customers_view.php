<?php
// This script and data application were generated by AppGini 22.13
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/customers.php');
	include_once(__DIR__ . '/customers_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('customers');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'customers';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`customers`.`CustomerID`" => "CustomerID",
		"`customers`.`CompanyName`" => "CompanyName",
		"`customers`.`ContactName`" => "ContactName",
		"`customers`.`ContactTitle`" => "ContactTitle",
		"`customers`.`Address`" => "Address",
		"`customers`.`City`" => "City",
		"`customers`.`Region`" => "Region",
		"`customers`.`PostalCode`" => "PostalCode",
		"`customers`.`Country`" => "Country",
		"`customers`.`Phone`" => "Phone",
		"`customers`.`Fax`" => "Fax",
		"`customers`.`TotalSales`" => "TotalSales",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7,
		8 => 8,
		9 => 9,
		10 => 10,
		11 => 11,
		12 => '`customers`.`TotalSales`',
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`customers`.`CustomerID`" => "CustomerID",
		"`customers`.`CompanyName`" => "CompanyName",
		"`customers`.`ContactName`" => "ContactName",
		"`customers`.`ContactTitle`" => "ContactTitle",
		"`customers`.`Address`" => "Address",
		"`customers`.`City`" => "City",
		"`customers`.`Region`" => "Region",
		"`customers`.`PostalCode`" => "PostalCode",
		"`customers`.`Country`" => "Country",
		"`customers`.`Phone`" => "Phone",
		"`customers`.`Fax`" => "Fax",
		"`customers`.`TotalSales`" => "TotalSales",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`customers`.`CustomerID`" => "Customer ID",
		"`customers`.`CompanyName`" => "Company Name",
		"`customers`.`ContactName`" => "Contact Name",
		"`customers`.`ContactTitle`" => "Contact Title",
		"`customers`.`Address`" => "Address",
		"`customers`.`City`" => "City",
		"`customers`.`Region`" => "Region",
		"`customers`.`PostalCode`" => "Postal Code",
		"`customers`.`Country`" => "Country",
		"`customers`.`Phone`" => "Phone",
		"`customers`.`Fax`" => "Fax",
		"`customers`.`TotalSales`" => "Total Sales",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`customers`.`CustomerID`" => "CustomerID",
		"`customers`.`CompanyName`" => "CompanyName",
		"`customers`.`ContactName`" => "ContactName",
		"`customers`.`ContactTitle`" => "ContactTitle",
		"`customers`.`Address`" => "Address",
		"`customers`.`City`" => "City",
		"`customers`.`Region`" => "Region",
		"`customers`.`PostalCode`" => "PostalCode",
		"`customers`.`Country`" => "Country",
		"`customers`.`Phone`" => "Phone",
		"`customers`.`Fax`" => "Fax",
		"`customers`.`TotalSales`" => "TotalSales",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = [];

	$x->QueryFrom = "`customers` ";
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
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'customers_view.php';
	$x->RedirectAfterInsert = 'customers_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Customers';
	$x->TableIcon = 'resources/table_icons/account_balances.png';
	$x->PrimaryKey = '`customers`.`CustomerID`';
	$x->DefaultSortField = '2';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth = [90, 250, 200, 150, 100, 50, 120, 150, ];
	$x->ColCaption = ['Customer ID', 'Company Name', 'Contact Name', 'Address', 'City', 'Region', 'Country', 'Total Sales', ];
	$x->ColFieldName = ['CustomerID', 'CompanyName', 'ContactName', 'Address', 'City', 'Region', 'Country', 'TotalSales', ];
	$x->ColNumber  = [1, 2, 3, 5, 6, 7, 9, 12, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/customers_templateTV.html';
	$x->SelectedTemplate = 'templates/customers_templateTVS.html';
	$x->TemplateDV = 'templates/customers_templateDV.html';
	$x->TemplateDVP = 'templates/customers_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = true;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: customers_init
	$render = true;
	if(function_exists('customers_init')) {
		$args = [];
		$render = customers_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: customers_header
	$headerCode = '';
	if(function_exists('customers_header')) {
		$args = [];
		$headerCode = customers_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: customers_footer
	$footerCode = '';
	if(function_exists('customers_footer')) {
		$args = [];
		$footerCode = customers_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
